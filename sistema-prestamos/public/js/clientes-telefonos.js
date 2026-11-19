(function () {
    const formulario = document.getElementById('formCliente') || document.getElementById('formEditarCliente');
    if (!formulario) return;
    function validarTelefono(campo) {
        const nombre = campo.name || '';
        if (!campo.setCustomValidity || !(nombre.includes('telefonos[') && nombre.endsWith('[numero]') || nombre.endsWith('[telefono]') || nombre.endsWith('[telefono_empresa]') || campo.dataset.campo === 'telefono')) return;
        const valor = campo.value.trim();
        const valido = !valor || (/^\+?[0-9 ()-]+$/.test(valor) && /^(?:1)?(?:809|829|849)[0-9]{7}$/.test(valor.replace(/\D/g, '')));
        campo.setCustomValidity(valido ? '' : 'Ingresa un teléfono dominicano válido: 809, 829 o 849 y siete dígitos; puedes usar +1, espacios, paréntesis y guiones.');
    }
    formulario.addEventListener('input', evento => validarTelefono(evento.target));
    formulario.querySelectorAll('input').forEach(validarTelefono);
})();
