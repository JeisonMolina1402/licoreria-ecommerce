<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Categoria;

class InventarioController extends Controller
{
    /**
     * CONSTRUCTOR: Seguridad del Controlador.
     * Al inyectar el middleware 'auth', garantizamos que ninguna persona en internet 
     * pueda acceder a estas funciones (ni por URL) si no ha iniciado sesión como administrador.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * MÉTODO INDEX (LEER / READ): Muestra la tabla del inventario con filtros.
     */
    public function index(Request $request)
    {
        // 1. Traemos todas las categorías para llenar el menú desplegable (Select) de los filtros.
        $categorias = Categoria::all();
        
        // 2. Extraemos una lista única de los nombres de los productos para el autocompletado del buscador.
        $nombresProductos = Producto::select('nombre')->distinct()->pluck('nombre');
        
        // 3. Iniciamos el Constructor de Consultas (Query Builder) de Eloquent.
        $query = Producto::query();

        // 4. APLICACIÓN DE FILTROS DINÁMICOS:
        // Verificamos si el usuario envió algún filtro en la URL y ajustamos la consulta SQL automáticamente.
        if ($request->filled('nombre')) {
            // Búsqueda por coincidencia de texto (LIKE en SQL)
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
        
        // Si no se aplicó ningún filtro de orden, por defecto mostramos los productos más nuevos primero.
        if (!$request->filled('orden_stock') && (!$request->filled('orden_precio'))) {
            $query->latest();
        }

        // 5. PAGINACIÓN: En lugar de traer todo el catálogo (get), traemos bloques de 10.
        // El método appends($request->all()) es crucial: memoriza los filtros en la URL 
        // para que al pasar a la página 2, no se pierda la búsqueda actual.
        $productos = $query->paginate(10)->appends($request->all());

        // 6. Enviamos todos los datos empaquetados a la vista Blade.
        return view('inventario', compact('productos', 'categorias', 'nombresProductos'));
    }

    /**
     * MÉTODO STORE (CREAR / CREATE): Guarda un nuevo producto en la base de datos y sube su imagen.
     */
    public function store(Request $request)
    {
        // 1. PRE-PROCESAMIENTO: Seguridad financiera.
        // Reemplazamos las comas por puntos en los precios para evitar errores matemáticos en MySQL.
        $request->merge([
            'precio_compra' => str_replace(',', '.', $request->precio_compra),
            'precio' => str_replace(',', '.', $request->precio),
        ]);

        // 2. REGLAS DE VALIDACIÓN (Server-side validation)
        // Protegemos la base de datos exigiendo tipos de datos estrictos (strings, números, imágenes permitidas).
        $reglas = [
            'nombre' => 'required|string|max:255',
            'categoria_id' => 'required',
            'precio' => 'required|numeric|min:0',
            'precio_compra' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Límite de peso: 2MB
        ];

        // Mensajes para mejorar la experiencia del usuario (UX).
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

        // Ejecutamos la validación. Si falla, Laravel devuelve al usuario al formulario automáticamente mostrando los errores.
        $request->validate($reglas, $mensajes);

        // 3. CONTROL DE CATEGORÍAS HUÉRFANAS
        // Si por alguna razón envían un ID de categoría que no existe la creamos  para evitar que el sistema colapse.
        Categoria::firstOrCreate(
            ['id' => $request->categoria_id],
            ['nombre' => 'Categoría General', 'descripcion' => 'Generada automáticamente por el sistema']
        );

        // 4. GESTIÓN DEL ARCHIVO FÍSICO (LA IMAGEN)
        $rutaImagen = null;
        if ($request->hasFile('imagen')) {
            // Renombramos el archivo usando la hora actual (time()) para evitar que dos imágenes se llamen igual y se sobrescriban.
            $nombreImagen = time() . '.' . $request->imagen->extension();
            // Movemos la imagen de la memoria temporal del servidor a la carpeta pública.
            $request->imagen->move(public_path('uploads/productos'), $nombreImagen);
            $rutaImagen = 'uploads/productos/' . $nombreImagen; // Guardamos la ruta de texto para la base de datos.
        }

        // 5. INSERCIÓN EN BASE DE DATOS (Active Record)
        // Instanciamos un nuevo objeto Producto y le asignamos los valores limpios.
        $producto = new Producto();
        $producto->categoria_id = $request->categoria_id;
        $producto->nombre = $request->nombre;
        $producto->descripcion = $request->descripcion;
        $producto->precio_compra = $request->precio_compra ?: 0; // Si no hay precio de compra, guardamos 0
        $producto->precio = $request->precio;
        $producto->stock = $request->stock;
        $producto->imagen = $rutaImagen;
        $producto->save(); // Ejecuta el INSERT INTO SQL

        // Refrescamos la pantalla con un mensaje verde de éxito.
        return redirect()->back()->with('success', '¡Producto agregado exitosamente al inventario!');
    }

    /**
     * MÉTODO UPDATE (ACTUALIZAR / UPDATE): Modifica los datos de un producto existente.
     */
    public function update(Request $request, $id)
    {
        // 1. Buscamos el producto. Si el ID no existe en la BD, lanza un error 404 automático (findOrFail).
        $producto = Producto::findOrFail($id);

        // 2. Misma sanitización de precios que en el método store.
        $request->merge([
            'precio_compra' => str_replace(',', '.', $request->precio_compra),
            'precio' => str_replace(',', '.', $request->precio),
        ]);

        // 3. Reglas de validación (Idénticas a la creación)
        $reglas = [
            'nombre' => 'required|string|max:255',
            'categoria_id' => 'required',
            'precio' => 'required|numeric|min:0',
            'precio_compra' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];

        $mensajes = [ /* ... Mensajes definidos ... */ ];
        $request->validate($reglas, $mensajes);

        // 4. LÓGICA DE ACTUALIZACIÓN DE IMAGEN
        // Por defecto, mantenemos la ruta de la imagen que el producto ya tenía.
        $rutaImagen = $producto->imagen;
        
        // Si el usuario subió una imagen nueva en el formulario...
        if ($request->hasFile('imagen')) {
            $nombreImagen = time() . '.' . $request->imagen->extension();
            $request->imagen->move(public_path('uploads/productos'), $nombreImagen);
            $rutaImagen = 'uploads/productos/' . $nombreImagen; // Sobrescribimos la variable con la nueva ruta.
            
            // Nota: Aquí podríamos añadir código para borrar la imagen vieja del servidor y ahorrar espacio.
        }

        // 5. Asignación y guardado (Ejecuta el UPDATE SQL).
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
     * MÉTODO DESTROY (ELIMINAR / DELETE): Borra un producto del sistema (BD y Archivos).
     */
    public function destroy($id)
    {
        // 1. Buscamos el producto asegurándonos de que exista.
        $producto = Producto::findOrFail($id);
        
        // 2. LIMPIEZA DE SERVIDOR: 
        // Verificamos si el producto tiene una imagen vinculada y comprobamos que el archivo físico realmente exista.
        if ($producto->imagen && file_exists(public_path($producto->imagen))) {
            // unlink() es la función de PHP que destruye/borra el archivo físico del disco duro para no dejar basura.
            unlink(public_path($producto->imagen));
        }
        
        // 3. Borramos el registro de la base de datos (Ejecuta DELETE FROM SQL).
        $producto->delete();
        
        return redirect()->back()->with('success', '¡Producto eliminado correctamente del inventario!');
    }
}