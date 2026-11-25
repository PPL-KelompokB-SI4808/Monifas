export default function monitoringAndAnalyticMaintenanceReport(comparison = { preventive: { count: 0 }, reactive: { count: 0 } }) {
    return {
        chart: null,
        comparison,

        init() {
            this.$nextTick(() => {
                this.renderChart();
            });
        },

        destroy() {
            if (this.chart) {
                this.chart.destroy();
                this.chart = null;
            }
        },

        renderChart() {
            const canvas = this.$refs.canvas;
            if (!canvas || typeof window.Chart === 'undefined') {
                return;
            }

            if (this.chart) {
                this.chart.destroy();
            }

            const styles = getComputedStyle(document.documentElement);
            const primaryColor = styles.getPropertyValue('--primary').trim() || '#6366F1';
            const warningColor = styles.getPropertyValue('--warning').trim() || '#F59E0B';
            const foregroundColor = styles.getPropertyValue('--foreground').trim() || '#0A0A0A';

            const preventiveCount = Number(this.comparison?.preventive?.count ?? 0);
            const reactiveCount = Number(this.comparison?.reactive?.count ?? 0);

            const ctx = canvas.getContext('2d');
            this.chart = new window.Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Preventive Maintenance', 'Reaktif / Darurat'],
                    datasets: [
                        {
                            data: [preventiveCount, reactiveCount],
                            backgroundColor: [primaryColor, warningColor],
                            borderWidth: 0,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',
                    plugins: {
                        legend: {
                            display: false,
                        },
                        tooltip: {
                            backgroundColor: foregroundColor,
                            padding: 10,
                            cornerRadius: 8,
                        },
                    },
                },
            });
        },
    };
}
