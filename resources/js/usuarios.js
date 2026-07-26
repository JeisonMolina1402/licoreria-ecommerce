/**
 * ========================================================
 * LÓGICA DEL MÓDULO DE USUARIOS (ADMINISTRACIÓN)
 * ========================================================
 */

// Prepara el modal para CREAR un nuevo usuario
window.prepararModalCrear = function() {
    // 1. Limpiamos el formulario
    document.getElementById('formUsuario').reset();
    
    // 2. Configuramos la URL para crear (POST)
    document.getElementById('formUsuario').action = window.urlBaseUsuarios;
    document.getElementById('metodoPutContainer').innerHTML = ''; // Limpiamos cualquier rastro de PUT
    
    // 3. Cambiamos los textos visuales
    document.getElementById('modalUsuarioTitle').innerHTML = '👤 Nuevo Usuario';
    document.getElementById('btnSubmitUsuario').innerHTML = 'Crear Usuario';
    
    // 4. La contraseña es OBLIGATORIA al crear
    document.getElementById('userPassword').required = true;
    document.getElementById('helpPassword').classList.add('d-none'); // Ocultamos el mensaje de ayuda
};

// Prepara el modal para EDITAR un usuario existente
window.prepararModalEditar = function(btn) {
    // 1. Obtenemos el ID y los datos inyectados en los data-attributes del botón
    const id = btn.getAttribute('data-id');
    
    // 2. Llenamos los inputs con la información actual
    document.getElementById('userName').value = btn.getAttribute('data-nombre');
    document.getElementById('userEmail').value = btn.getAttribute('data-email');
    document.getElementById('userCedula').value = btn.getAttribute('data-cedula') || '';
    document.getElementById('userTelefono').value = btn.getAttribute('data-telefono') || '';
    document.getElementById('userRol').value = btn.getAttribute('data-rol');
    
    // Limpiamos el input de contraseña por seguridad
    document.getElementById('userPassword').value = ''; 
    
    // 3. Configuramos la URL para actualizar (Inyectamos el método PUT)
    document.getElementById('formUsuario').action = `${window.urlBaseUsuarios}/${id}`;
    document.getElementById('metodoPutContainer').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    
    // 4. Cambiamos los textos visuales
    document.getElementById('modalUsuarioTitle').innerHTML = '✏️ Editar Usuario';
    document.getElementById('btnSubmitUsuario').innerHTML = 'Actualizar Cambios';
    
    // 5. La contraseña NO es obligatoria al editar (solo si quiere cambiarla)
    document.getElementById('userPassword').required = false;
    document.getElementById('helpPassword').classList.remove('d-none'); // Mostramos el mensaje de ayuda
};