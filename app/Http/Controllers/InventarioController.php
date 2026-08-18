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
     * MÉTODO STORE (CREAR / CREATE): Guarda un nuevo producto en la base de datos y sube su imagen.
     */
    public function store(Request $request)
    {
        // 1. Reemplazamos las comas por puntos en los precios
        $request->merge([
            'precio_compra' => str_replace(',', '.', $request->precio_compra),
            'precio' => str_replace(',', '.', $request->precio),
        ]);

        // 2. REGLAS DE VALIDACIÓN 
        $request->validate([
            'nombre' => 'required|string|max:255',
            'categoria_id' => 'required',
            'descripcion' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
            'precio_compra' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', 
        ]);

        // 3. CONTROL DE CATEGORÍAS HUÉRFANAS
        Categoria::firstOrCreate(
            ['id' => $request->categoria_id],
            ['nombre' => 'Categoría General', 'descripcion' => 'Generada automáticamente por el sistema']
        );

        // 4. GESTIÓN DE LA IMAGEN
        $rutaImagen = null;
        if ($request->hasFile('imagen')) {
            $nombreImagen = time() . '.' . $request->imagen->extension();
            $request->imagen->move(public_path('uploads/productos'), $nombreImagen);
            $rutaImagen = 'uploads/productos/' . $nombreImagen; 
        }

        // 5. GUARDAMOS EN LA BASE DE DATOS 
        $producto = new Producto();
        $producto->categoria_id = $request->categoria_id;
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
        // 1. Buscamos el producto. Si el ID no existe en la BD, lanza un error 404 automático.
        $producto = Producto::findOrFail($id);

        // 2. Misma sanitización de store para los precios
        $request->merge([
            'precio_compra' => str_replace(',', '.', $request->precio_compra),
            'precio' => str_replace(',', '.', $request->precio),
        ]);

        // 3. Reglas de validación 
        $reglas = [
            'nombre' => 'required|string|max:255',
            'categoria_id' => 'required',
            'descripcion' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
            'precio_compra' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];

        $mensajes = [ /* ... Mensajes definidos anteriormente ... */ ];
        $request->validate($reglas, $mensajes);

        // 4. ACTUALIZACIÓN DE IMAGEN
        // mantenemos la ruta de la imagen que el producto ya tenía.
        $rutaImagen = $producto->imagen;
        
        // Si el usuario subió una imagen nueva en el formulario...
        if ($request->hasFile('imagen')) {
            $nombreImagen = time() . '.' . $request->imagen->extension();
            $request->imagen->move(public_path('uploads/productos'), $nombreImagen);
            $rutaImagen = 'uploads/productos/' . $nombreImagen; // Sobrescribimos la variable con la nueva ruta.
            
            // Nota:  añadir código para borrar la imagen vieja del servidor y ahorrar espacio.
        }

        // 5. Asignación y guardamos.
        $producto->categoria_id = $request->categoria_id;
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
     * MÉTODO DESTROY (Ahora hace un Borrado Lógico / Desactivación)
     */
    public function destroy($id)
    {
        $producto = Producto::findOrFail($id);
        
        // Alternamos el estado como si fuera un interruptor
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

