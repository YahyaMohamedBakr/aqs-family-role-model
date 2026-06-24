/**
 * AQS Admin - Charts (Chart.js)
 */
document.addEventListener('DOMContentLoaded', function () {
    const distCanvas = document.getElementById('aqs-dist-chart');
    const axisCanvas = document.getElementById('aqs-axis-chart');

    if (distCanvas && window.aqsAdminData) {
        const data = window.aqsAdminData;
        new Chart(distCanvas, {
            type: 'doughnut',
            data: {
                labels: data.distLabels,
                datasets: [{
                    data: data.distCounts,
                    backgroundColor: data.distColors.map(function(c) {
                        return c || '#2090b0';
                    }),
                    borderWidth: 2,
                    borderColor: '#fff',
                }],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        rtl: true,
                        labels: {
                            font: { family: 'Cairo, sans-serif' },
                            padding: 16,
                        },
                    },
                },
            },
        });
    }

    if (axisCanvas && window.aqsAdminData) {
        const data = window.aqsAdminData;
        new Chart(axisCanvas, {
            type: 'bar',
            data: {
                labels: data.axisLabels,
                datasets: [{
                    label: 'متوسط النتيجة',
                    data: data.axisAvgs,
                    backgroundColor: 'rgba(32, 144, 176, 0.7)',
                    borderColor: '#2090b0',
                    borderWidth: 2,
                    borderRadius: 8,
                }],
            },
            options: {
                responsive: true,
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: false,
                    },
                },
                scales: {
                    x: {
                        max: 25,
                        grid: {
                            display: false,
                        },
                    },
                    y: {
                        grid: {
                            display: false,
                        },
                        ticks: {
                            font: {
                                family: 'Cairo, sans-serif',
                            },
                        },
                    },
                },
            },
        });
    }
});
