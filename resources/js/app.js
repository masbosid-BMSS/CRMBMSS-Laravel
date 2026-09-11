import './bootstrap';
import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

// Global Toast helper using custom event
window.showToast = function(message, type = 'success') {
    window.dispatchEvent(new CustomEvent('bmss-toast', { detail: { message, type } }));
};

Alpine.start();
