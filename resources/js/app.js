import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';
import usersNotifications from './components/users-notifications';

window.Alpine = Alpine;
window.Chart = Chart;

Alpine.data('usersNotifications', usersNotifications);

Alpine.start();
