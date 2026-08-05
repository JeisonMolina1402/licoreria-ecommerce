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
     * MÉTODO INDEX mostramos la tabla del inventario con filtros.
     */
    public function index(Request $request)
    {
        // 1. Traemos todas las categorías para llenar el desplegable Select de los filtros.
        $categorias = Categoria::all();
        
        // 2. Extraemos una lista única de los nombres de los productos para el autocompletado del buscador.
        $nombresProductos = Producto::select('nombre')->distinct()->pluck('nombre');
        
        // 3. Iniciamos el Constructor de Consultas de Eloquent.
        $query = Producto::query();

        // 4. APLICACIÓN DE FILTROS DINÁMICOS:
        // Verificamos si el usuario envió algún filtro en la URL y ajustamos la consulta SQL automáticamente.
        if ($request->filled('nombre')) {
            // Búsqueda por coincidencia de texto es como un LIKE en SQL
            $query->where('nombre', 'LIKE', '%' . $request->nombre . '%');
        }
        if ($request->filled('categoria_id')) {
            // Filtro exacto por el ID de la categoría
            $query->where('categoria_id', $request->categoria_id);
        }
        if ($request->filled('orden_stock')) {
            // Ordenamiento ascendente o descendente según la cantidad física
            $query->orderBy('stock', $request->orden_stock);
        }
        if ($request->filled('orden_precio')) {
            // Ordenamiento por precio
            $query->orderBy('precio', $request->orden_precio);
        }
        
        // Si no hay un filtro  mostramos los productos más nuevos primero.
        if (!$request->filled('orden_stock') && (!$request->filled('orden_precio'))) {
            $query->latest();
        }

        // 5. PAGINACIÓN: traemos bloques de 10.
        // con $request->all() es para que memorize los filtros en la URL 
        // para que al pasar de paginano se pierda la búsqueda actual.
        $productos = $query->paginate(10)->appends($request->all());

        // 6. Enviamos todos los datos a la vista .
        return view('inventario.inventario', compact('productos', 'categorias', 'nombresProductos'));
    }

    /**
     * MÉTODO STORE (CREAR / CREATE): Guarda un nuevo producto en la base de datos y sube su imagen.
     */
    public function store(Request $request)
    {
        // 1. Reemplazamos las comas por puntos en los precios para evitar errores matemáticos en MySQL.
        $request->merge([
            'precio_compra' => str_replace(',', '.', $request->precio_compra),
            'precio' => str_replace(',', '.', $request->precio),
        ]);

        // 2. REGLAS DE VALIDACIÓN 
        // para proteger la bd exigiendo tipos de datos especificos (strings, números, imágenes permitidas).
        $reglas = [
            'nombre' => 'required|string|max:255',
            'categoria_id' => 'required',
            'precio' => 'required|numeric|min:0',
            'precio_compra' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', 
        ];

        // Mensajes para mejorar la experiencia del usuario cuando haya errires .
        $mensajes = [
            'nombre.required' => 'El nombre del licor es obligatorio.',
            'categoria_id.required' => 'Debes seleccionar una categoría.',
            'precio.required' => 'El precio de venta es obligatorio.',
            'precio.min' => 'El precio de venta no puede ser negativo.',
            'precio_compra.min' => 'El precio de compra no puede ser negativo.',
            'stock.required' => 'La cantidad en stock es obligatoria.',
            'stock.min' => 'El stock no puede ser menor a 0.',
            'imagen.image' => 'El archivo debe ser una imagen válida.',
            'imagen.mimes' => 'La imagen debe ser formato jpeg, png, jpg o webp.',
            'imagen.max' => 'La imagen es muy pesada. Máximo 2MB permitidos.',
        ];

        // Ejecutamos la validación. Si falla vuelve al formulario automáticamente mostrando los errores.
        $request->validate($reglas, $mensajes);

        // 3. CONTROL DE CATEGORÍAS HUÉRFANAS
        // Si se envían un ID de categoría que no existe la creamos  para evitar que el sistema colapse.
        Categoria::firstOrCreate(
            ['id' => $request->categoria_id],
            ['nombre' => 'Categoría General', 'descripcion' => 'Generada automáticamente por el sistema']
        );

        // 4. GESTIÓN DE LA IMAGEN
        $rutaImagen = null;
        if ($request->hasFile('imagen')) {
            // Renombramos el archivo usando la hora actual con (time()) para evitar que dos imágenes se llamen igual y se sobrescriban.
            $nombreImagen = time() . '.' . $request->imagen->extension();
            // Movemos la imagen de la memoria temporal del servidor a la carpeta pública.
            $request->imagen->move(public_path('uploads/productos'), $nombreImagen);
            $rutaImagen = 'uploads/productos/' . $nombreImagen; // Guardamos la ruta de texto para la base de datos.
        }

        // 5. GUARDAMOS EN LA BASE DE DATOS 
        // Instanciamos un nuevo objeto Producto y le asignamos los valores limpios.
        $producto = new Producto();
        $producto->categoria_id = $request->categoria_id;
        $producto->nombre = $request->nombre;
        $producto->descripcion = $request->descripcion;
        $producto->precio_compra = $request->precio_compra ?: 0; // Si no hay precio de compra, guardamos 0
        $producto->precio = $request->precio;
        $producto->stock = $request->stock;
        $producto->imagen = $rutaImagen;
        $producto->save(); 

        // Refrescamos la pantalla con un mensaje verde de éxito.
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
            'precio' => 'required|numeric|min:0',
            'precio_compra' => 'nullable|numeric|min:0',
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
     * MÉTODO DESTROY para borrar un producto del sistema tanto en BD y Archivos.
     */
    public function destroy($id)
    {
        // 1. Buscamos el producto asegurándonos de que exista.
        $producto = Producto::findOrFail($id);
        
        // 2. LIMPIEZA DE SERVIDOR: 
        // Verificamos si el producto tiene una imagen vinculada y comprobamos que el archivo físico realmente exista.
        if ($producto->imagen && file_exists(public_path($producto->imagen))) {
            // con unlink() es para que borreel archivo físico del disco duro
            unlink(public_path($producto->imagen));
        }
        
        // 3. Borramos el registro de la base de datos es como un DELETE FROM ENSQL.
        $producto->delete();
        
        return redirect()->back()->with('success', '¡Producto eliminado correctamente del inventario!');
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

