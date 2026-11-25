export default function monitoringAndAnalyticDashboard(trends = { labels: [], datasets: { incoming: [], completed: [] } }) {
    return {
        chart: null,
        trends,

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
            const successColor = styles.getPropertyValue('--success').trim() || '#10B981';
            const borderColor = styles.getPropertyValue('--border').trim() || '#E8E8EC';
            const mutedColor = styles.getPropertyValue('--muted-foreground').trim() || '#6B6B6B';
            const foregroundColor = styles.getPropertyValue('--foreground').trim() || '#0A0A0A';

            const labels = Array.isArray(this.trends?.labels) ? this.trends.labels : [];
            const incoming = Array.isArray(this.trends?.datasets?.incoming) ? this.trends.datasets.incoming : [];
            const completed = Array.isArray(this.trends?.datasets?.completed) ? this.trends.datasets.completed : [];

            const ctx = canvas.getContext('2d');
            this.chart = new window.Chart(ctx, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [
                        {
                            label: 'Laporan Masuk',
                            data: incoming,
                            backgroundColor: primaryColor,
                            borderRadius: 6,
                            borderSkipped: false,
                            barPercentage: 0.6,
                            categoryPercentage: 0.65,
                        },
                        {
                            label: 'Selesai Dikerjakan',
                            data: completed,
                            backgroundColor: successColor,
                            borderRadius: 6,
                            borderSkipped: false,
                            barPercentage: 0.6,
                            categoryPercentage: 0.65,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
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
                    scales: {
                        x: {
                            grid: {
                                display: false,
                            },
                            ticks: {
                                color: mutedColor,
                                font: {
                                    size: 11,
                                },
                            },
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                color: mutedColor,
                                font: {
                                    size: 11,
                                },
                            },
                            grid: {
                                color: borderColor,
                            },
                        },
                    },
                },
            });
        },
    };
}
