import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';
import usersNotifications from './components/users-notifications';
import supportingFeedback from './components/supporting-feedback';

window.Alpine = Alpine;
window.Chart = Chart;

Alpine.data('usersNotifications', usersNotifications);
Alpine.data('supportingFeedback', supportingFeedback);

Alpine.start();
