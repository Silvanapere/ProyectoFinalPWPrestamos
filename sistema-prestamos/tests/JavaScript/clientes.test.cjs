const { test } = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');

class Element {
    constructor(options = {}) {
        Object.assign(this, { value: '', textContent: '', hidden: false, disabled: false, dataset: {}, listeners: {}, attributes: {}, checked: false, type: 'text', name: '' }, options);
        this.classes = new Set();
        this.classList = { toggle: (name, active) => active ? this.classes.add(name) : this.classes.delete(name), contains: name => this.classes.has(name) };
    }
    addEventListener(type, fn) { (this.listeners[type] ??= []).push(fn); }
    async emit(type) { await Promise.all((this.listeners[type] ?? []).map(fn => fn.call(this, { target: this, preventDefault() {} }))); }
    setAttribute(name, value) { this.attributes[name] = value; }
    querySelectorAll() { return []; }
    scrollIntoView() {}
}
function scripts(file) {
    return [...fs.readFileSync(file, 'utf8').matchAll(/<script>([\s\S]*?)<\/script>/g)].map(match => match[1]);
}
function listado() {
    const ids = Object.fromEntries(['buscar-cliente', 'filtro-estado', 'sin-resultados-clientes', 'buscar-cliente-modal', 'lista-clientes-modal', 'seleccionar-cliente', 'modalSeleccionarCliente', 'sin-resultados-modal'].map(id => [id, new Element()]));
    ids['filtro-estado'].value = 'Todos';
    const filas = [new Element({ textContent: 'Ana 001-0000000-1 Activo', style: {} }), new Element({ textContent: 'Luis 002-0000000-2 Inactivo', style: {} })];
    filas.forEach((fila, i) => { fila.querySelectorAll = () => [new Element(), new Element(), new Element(), new Element({ textContent: i ? 'Inactivo' : 'Activo' }), new Element(), new Element()]; });
    const clientes = [new Element({ textContent: 'Ana 001-0000000-1', dataset: { url: '/clientes/1/editar' }, style: {} }), new Element({ textContent: 'Luis 002-0000000-2', dataset: { url: '/clientes/2/editar' }, style: {} })];
    ids['lista-clientes-modal'].querySelectorAll = () => clientes;
    const window = { location: { href: '' } };
    const context = vm.createContext({ document: { getElementById: id => ids[id], querySelectorAll: () => filas }, window });
    scripts('resources/views/clientes/index.blade.php').forEach(script => vm.runInContext(script, context));
    return { ids, filas, clientes, window };
}

test('modal: un clic selecciona, Seleccionar navega y doble clic abre directamente', async () => {
    const { ids, clientes, window } = listado();
    await clientes[0].emit('click');
    assert.equal(window.location.href, '');
    assert.equal(clientes[0].attributes['aria-pressed'], 'true');
    assert.equal(ids['seleccionar-cliente'].disabled, false);
    await ids['seleccionar-cliente'].emit('click');
    assert.equal(window.location.href, '/clientes/1/editar');
    await clientes[1].emit('dblclick');
    assert.equal(window.location.href, '/clientes/2/editar');
});

test('modal: filtrar sin coincidencias informa y descarta la selección oculta', async () => {
    const { ids, clientes } = listado();
    await clientes[0].emit('click');
    ids['buscar-cliente-modal'].value = 'Inexistente';
    await ids['buscar-cliente-modal'].emit('input');
    assert.equal(ids['sin-resultados-modal'].hidden, false);
    assert.equal(ids['seleccionar-cliente'].disabled, true);
    assert.equal(clientes[0].attributes['aria-pressed'], 'false');
});

test('listado: combina nombre/documento y estado, mostrando ausencia de resultados', async () => {
    const { ids, filas } = listado();
    ids['buscar-cliente'].value = '001-0000000-1';
    await ids['buscar-cliente'].emit('input');
    assert.equal(filas[0].style.display, '');
    assert.equal(filas[1].style.display, 'none');
    ids['filtro-estado'].value = 'Inactivo';
    await ids['filtro-estado'].emit('change');
    assert.equal(ids['sin-resultados-clientes'].hidden, false);
    ids['buscar-cliente'].value = 'Luis';
    await ids['buscar-cliente'].emit('input');
    assert.equal(filas[1].style.display, '');
    assert.equal(ids['sin-resultados-clientes'].hidden, true);
});

function alta() {
    const ids = {};
    const get = id => ids[id] ??= new Element();
    for (const id of ['tipo_documento_id', 'garante_tipo_documento_id']) Object.assign(get(id), { value: '1', selectedOptions: [{ textContent: 'Cédula' }] });
    const names = {
        tipo_documento_id: 'tipo_documento_id', numero_documento: 'numero_documento',
        garante_tipo_documento_id: 'garante[tipo_documento_id]', garante_numero_documento: 'garante[numero_documento]',
    };
    const fields = Object.entries(names).map(([id, name]) => Object.assign(get(id), { name }));
    const detalles = ['nombres', 'apellidos', 'correo', 'nacionalidad_id'].map(name => new Element({ name: `garante[${name}]`, required: true }));
    detalles.push(new Element({ name: 'documento_garante', type: 'file' }));
    fields.push(...detalles);
    get('formCliente').elements = fields;
    get('datosGarante').querySelectorAll = () => detalles;
    const timers = new Map(); let nextTimer = 0;
    const peticiones = [];
    const context = vm.createContext({
        document: { getElementById: get, querySelectorAll: () => [] },
        window: { location: { origin: 'http://localhost' } }, URL,
        alert() {},
        setTimeout(fn) { timers.set(++nextTimer, fn); return nextTimer; },
        clearTimeout(id) { timers.delete(id); },
        fetch(url) { return new Promise(resolve => peticiones.push({ url, resolve })); },
    });
    let script = scripts('resources/views/clientes/create.blade.php')[0];
    script = script.replace(/@json\(route\('clientes.buscar-documento'\)\)/, JSON.stringify('http://localhost/clientes/buscar-documento'));
    script = script.split('// Si hubo errores de validación')[0];
    vm.runInContext(script, context);
    return { get, detalles, timers, peticiones };
}
const respuesta = datos => ({ ok: true, json: async () => datos });

test('búsqueda automática: espera, agrupa escritura y no consulta documentos incompletos', async () => {
    const { get, timers, peticiones } = alta();
    get('garante_numero_documento').value = '001';
    await get('garante_numero_documento').emit('input');
    assert.equal(timers.size, 0);
    for (const numero of ['001-0000000-1', '002-0000000-2']) {
        get('garante_numero_documento').value = numero;
        await get('garante_numero_documento').emit('input');
    }
    assert.equal(timers.size, 1);
    assert.equal(peticiones.length, 0);
    const terminar = [...timers.values()][0]();
    assert.equal(peticiones.length, 1);
    assert.match(peticiones[0].url, /002-0000000-2/);
    peticiones[0].resolve(respuesta({ encontrado: false }));
    await terminar;
});

test('búsqueda: una respuesta obsoleta no muestra ni habilita datos de otro documento', async () => {
    const { get, peticiones } = alta();
    get('garante_numero_documento').value = '001-0000000-1';
    const terminar = get('buscarGarante').emit('click');
    get('garante_numero_documento').value = '002-0000000-2';
    await get('garante_numero_documento').emit('input');
    peticiones[0].resolve(respuesta({ encontrado: true, persona: { nombres: 'Persona obsoleta' } }));
    await terminar;
    assert.equal(get('datosGarante').hidden, true);
    assert.notEqual(get('mensajeGarante').textContent, 'Garante encontrado. Sus datos se muestran en solo lectura.');
});

test('garante: existente es solo lectura y documento del nuevo sigue siendo opcional', async () => {
    const { get, peticiones, detalles } = alta();
    get('garante_numero_documento').value = '001-0000000-1';
    let terminar = get('buscarGarante').emit('click');
    peticiones[0].resolve(respuesta({ encontrado: true, persona: { nombres: 'Persona existente' } }));
    await terminar;
    assert.equal(detalles[0].value, 'Persona existente');
    assert.ok(detalles.every(campo => campo.disabled));
    get('garante_numero_documento').value = '002-0000000-2';
    await get('garante_numero_documento').emit('input');
    terminar = get('buscarGarante').emit('click');
    peticiones[1].resolve(respuesta({ encontrado: false }));
    await terminar;
    assert.equal(detalles[0].required, true);
    assert.equal(detalles.at(-1).required, false);
    assert.equal(detalles.at(-1).disabled, false);
});

test('teléfonos: la validación del navegador admite formato RD y rechaza letras', async () => {
    const formulario = new Element();
    const campo = new Element({ name: 'telefonos[0][numero]', value: '+1 (809) 555-0101' });
    campo.setCustomValidity = message => { campo.error = message; };
    formulario.querySelectorAll = () => [campo];
    const context = vm.createContext({ document: { getElementById: id => id === 'formCliente' ? formulario : null } });
    vm.runInContext(fs.readFileSync('public/js/clientes-telefonos.js', 'utf8'), context);
    assert.equal(campo.error, '');
    campo.value = 'abc';
    await Promise.all(formulario.listeners.input.map(fn => fn({ target: campo })));
    assert.match(campo.error, /teléfono dominicano válido/);
});
