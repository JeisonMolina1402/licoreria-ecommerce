// resources/js/permisos.js

const basePermisoUrl = window.urlBasePermisos || '/permisos';

window.prepararModalPermisoCrear = function () {
    const title = document.getElementById('modalPermisoTitle');
    const form = document.getElementById('formPermiso');
    const methodContainer = document.getElementById('metodoPermisoPutContainer');
    const inputName = document.getElementById('permisoName');
    const btnSubmit = document.getElementById('btnSubmitPermiso');

    if(title) title.innerHTML = '<i class="fa-solid fa-key me-2"></i>Nuevo Permiso';
    if(form) form.action = basePermisoUrl;
    if(methodContainer) methodContainer.innerHTML = ''; 
    if(inputName) inputName.value = '';
    if(btnSubmit) btnSubmit.innerText = 'Guardar Permiso';
};

window.prepararModalPermisoEditar = function (btn) {
    let id = btn.getAttribute('data-id');
    let name = btn.getAttribute('data-name');

    const title = document.getElementById('modalPermisoTitle');
    const form = document.getElementById('formPermiso');
    const methodContainer = document.getElementById('metodoPermisoPutContainer');
    const inputName = document.getElementById('permisoName');
    const btnSubmit = document.getElementById('btnSubmitPermiso');

    if(title) title.innerHTML = '<i class="fa-solid fa-pen me-2"></i>Editar Permiso';
    if(form) form.action = `${basePermisoUrl}/${id}`;
    if(methodContainer) methodContainer.innerHTML = '<input type="hidden" name="_method" value="PUT">';
    if(inputName) inputName.value = name;
    if(btnSubmit) btnSubmit.innerText = 'Actualizar Cambios';
};