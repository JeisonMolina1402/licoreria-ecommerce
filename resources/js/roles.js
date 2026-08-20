// resources/js/roles.js

// Usamos el objeto window (si lo configuramos en Blade) o armamos la ruta estática
const baseRoleUrl = window.urlBaseRoles || '/roles';

window.prepararModalRolCrear = function () {
    const title = document.getElementById('modalRolTitle');
    const form = document.getElementById('formRol');
    const methodContainer = document.getElementById('metodoRolPutContainer');
    const inputName = document.getElementById('rolName');
    const btnSubmit = document.getElementById('btnSubmitRol');

    if(title) title.innerHTML = '<i class="fa-solid fa-shield me-2"></i>Nuevo Rol';
    if(form) form.action = baseRoleUrl; // POST a /roles
    if(methodContainer) methodContainer.innerHTML = ''; 
    if(inputName) inputName.value = '';
    if(btnSubmit) btnSubmit.innerText = 'Guardar Rol';
};

window.prepararModalRolEditar = function (btn) {
    let id = btn.getAttribute('data-id');
    let name = btn.getAttribute('data-name');

    const title = document.getElementById('modalRolTitle');
    const form = document.getElementById('formRol');
    const methodContainer = document.getElementById('metodoRolPutContainer');
    const inputName = document.getElementById('rolName');
    const btnSubmit = document.getElementById('btnSubmitRol');

    if(title) title.innerHTML = '<i class="fa-solid fa-pen me-2"></i>Editar Rol';
    // Generar ruta dinámica (ej: /roles/5)
    if(form) form.action = `${baseRoleUrl}/${id}`;
    if(methodContainer) methodContainer.innerHTML = '<input type="hidden" name="_method" value="PUT">';
    if(inputName) inputName.value = name;
    if(btnSubmit) btnSubmit.innerText = 'Actualizar Cambios';
};

// Función para el modal de guardar/actualizar rol
window.confirmarCancelacionRol = function () {
    Swal.fire({
        title: '¿Cancelar edición?',
        text: 'Los datos que no hayas guardado se perderán.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, cancelar',
        cancelButtonText: 'Volver al formulario'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById("btnCerrarModalFantasmaRol").click();
            
            const form = document.getElementById("formRol");
            form.reset();
            form.action = window.urlBaseRoles; 
            
            form.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
            form.querySelectorAll(".texto-error").forEach(el => el.remove());
            document.getElementById("metodoRolPutContainer").innerHTML = '';
            document.getElementById("modalRolTitle").innerHTML = '<i class="fa-solid fa-shield me-2"></i>Nuevo Rol';
        }
    });
};