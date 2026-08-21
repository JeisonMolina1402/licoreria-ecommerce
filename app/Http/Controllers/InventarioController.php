<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Categoria;

class InventarioController extends Controller
{
    /**
     * Seguridad del Controlador.
     * Al inyectar el middleware 'auth', garantizamos que ninguna persona en internet 
     * pueda acceder a estas funciones ni por URL si no ha iniciado sesión como administrador.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * MÉTODO INDEX mostramos la tabla del inventario con filtros de livewire.
     */
   public function index(Request $request)
    {
        return view('inventario.inventario');
    }

    /**
     * MÉTODO STORE (CREAR / CREATE): Guarda un nuevo producto.
     */
    public function store(Request $request)
    {
       $request->merge([
            'precio_compra' => str_replace(',', '.', $request->precio_compra ?? ''),
            'precio' => str_replace(',', '.', $request->precio ?? ''),
        ]);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'categoria_id' => 'required',
            'nueva_categoria' => 'nullable|required_if:categoria_id,nueva|string|max:255', // <-- REGLA NUEVA
            'descripcion' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
            'precio_compra' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'imagen' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048', 
        ], [
            'nueva_categoria.required_if' => 'Debes escribir el nombre de la nueva categoría.'
        ]);

        // ==========================================
        // LA MAGIA DE LA NUEVA CATEGORÍA
        // ==========================================
        $idCategoriaFinal = $request->categoria_id;

        if ($request->categoria_id === 'nueva') {
            // Creamos la categoría sobre la marcha
            $nuevaCat = Categoria::create([
                'nombre' => ucfirst(strtolower($request->nueva_categoria)),
                'descripcion' => 'Creada automáticamente desde el inventario.'
            ]);
            $idCategoriaFinal = $nuevaCat->id; // Tomamos el ID recién creado
        }
        // ==========================================

        $rutaImagen = null;
        if ($request->hasFile('imagen')) {
            $nombreImagen = time() . '.' . $request->imagen->extension();
            $request->imagen->move(public_path('uploads/productos'), $nombreImagen);
            $rutaImagen = 'uploads/productos/' . $nombreImagen; 
        }

        $producto = new Producto();
        $producto->categoria_id = $idCategoriaFinal; // <--- USAMOS EL ID FINAL
        $producto->nombre = $request->nombre;
        $producto->descripcion = $request->descripcion;
        $producto->precio_compra = $request->precio_compra ?: 0; 
        $producto->precio = $request->precio;
        $producto->stock = $request->stock;
        $producto->imagen = $rutaImagen;
        $producto->save(); 

        return redirect()->back()->with('success', '¡Producto agregado exitosamente al inventario!');
    }

    /**
     * MÉTODO UPDATE Modificamos los datos de un producto existente.
     */
    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

       $request->merge([
            'precio_compra' => str_replace(',', '.', $request->precio_compra ?? ''),
            'precio' => str_replace(',', '.', $request->precio ?? ''),
        ]);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'categoria_id' => 'required',
            'nueva_categoria' => 'nullable|required_if:categoria_id,nueva|string|max:255', // <-- REGLA NUEVA
            'descripcion' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
            'precio_compra' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'nueva_categoria.required_if' => 'Debes escribir el nombre de la nueva categoría.'
        ]);

        // ==========================================
        // LA MAGIA DE LA NUEVA CATEGORÍA (También al editar)
        // ==========================================
        $idCategoriaFinal = $request->categoria_id;

        if ($request->categoria_id === 'nueva') {
            $nuevaCat = Categoria::create([
                'nombre' => ucfirst(strtolower($request->nueva_categoria)),
                'descripcion' => 'Creada automáticamente desde el inventario.'
            ]);
            $idCategoriaFinal = $nuevaCat->id; 
        }
        // ==========================================

        $rutaImagen = $producto->imagen;
        if ($request->hasFile('imagen')) {
            $nombreImagen = time() . '.' . $request->imagen->extension();
            $request->imagen->move(public_path('uploads/productos'), $nombreImagen);
            $rutaImagen = 'uploads/productos/' . $nombreImagen; 
        }

        $producto->categoria_id = $idCategoriaFinal; // <--- USAMOS EL ID FINAL
        $producto->nombre = $request->nombre;
        $producto->descripcion = $request->descripcion;
        $producto->precio_compra = $request->precio_compra ?: 0;
        $producto->precio = $request->precio;
        $producto->stock = $request->stock;
        $producto->imagen = $rutaImagen;
        $producto->save();

        return redirect()->back()->with('success', '¡Producto actualizado correctamente!');
    }
    /**
     * MÉTODO TOGGLE (BOTÓN NARANJA): Alterna entre Activo e Inactivo.
     */
    public function toggleStatus($id)
    {
        $producto = Producto::findOrFail($id);
        
        if ($producto->estado === 'activo' || $producto->estado === null) {
            $producto->estado = 'inactivo';
            $accion = 'desactivado (Oculto del catálogo)';
        } else {
            $producto->estado = 'activo';
            $accion = 'activado (Visible en el catálogo)';
        }
        
        $producto->save();
        
        return redirect()->back()->with('success', "¡El producto fue $accion correctamente!");
    }

    /**
     * MÉTODO DESTROY (BOTÓN ROJO): Borrado físico y definitivo.
     */
    public function destroy($id)
    {
        try {
            $producto = Producto::findOrFail($id);
            
            // 1. Borramos la imagen del servidor para no acumular basura
            if ($producto->imagen && file_exists(public_path($producto->imagen))) {
                unlink(public_path($producto->imagen));
            }

            // 2. Eliminamos de la base de datos
            $producto->delete();
            
            return redirect()->back()->with('success', "¡El producto fue eliminado definitivamente de la base de datos!");
            
        } catch (\Illuminate\Database\QueryException $e) {
            // ESCUDO ANTI-ERRORES: Si el producto ya tiene ventas, SQL bloqueará el borrado.
            return redirect()->back()->with('error', "No puedes eliminar este producto porque ya tiene tickets de venta asociados. Te recomendamos usar el botón 'Desactivar'.");
        }
    }

    public function exportarPdf(Request $request)
    {
        // 1. Iniciamos la consulta base con su relación
        $query = Producto::with('categoria');

        // 2. usamos LOS MISMOS FILTROS que usamos en método index/inventario
        if ($request->filled('nombre')) {
            $query->where('nombre', 'LIKE', '%' . $request->nombre . '%');
        }

        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        if ($request->filled('orden_stock')) {
            $query->orderBy('stock', $request->orden_stock);
        }

        if ($request->filled('orden_precio')) {
            $query->orderBy('precio', $request->orden_precio);
        }

        // Si no hay orden específico ordenamos alfabeticamente
        if (!$request->filled('orden_stock') && !$request->filled('orden_precio')) {
            $query->orderBy('nombre', 'asc');
        }

        // 3. Obtenemos TODOS los productos filtrados 
        $productos = $query->get();

        // 4. Generamos del PDF 
        $pdf = \PDF::loadView('inventario.pdf', compact('productos'));

        
        return $pdf->download('reporte-inventario-' . date('Y-m-d') . '.pdf');
    }
}

