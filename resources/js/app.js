import './bootstrap';

// 1. Importamos jQuery primero
import $ from 'jquery';
window.$ = window.jQuery = $;

// 2. Importamos Bootstrap completo
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

// 3. Forzamos a que Bootstrap reconozca la instancia global de jQuery para los modales
if (typeof window !== 'undefined' && window.$) {
    window.$.fn.modal = bootstrap.Modal.prototype;
}