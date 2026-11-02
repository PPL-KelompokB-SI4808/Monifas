import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';
import materialRepeater from './components/repair-materials';

window.Alpine = Alpine;
window.Chart = Chart;

Alpine.data('materialRepeater', materialRepeater);

Alpine.start();