import {
    Chart,
    ArcElement,
    BarElement,
    CategoryScale,
    LinearScale,
    DoughnutController,
    BarController,
    Tooltip,
    Legend,
} from 'chart.js';

Chart.register(ArcElement, BarElement, CategoryScale, LinearScale, DoughnutController, BarController, Tooltip, Legend);

const stats = JSON.parse(document.getElementById('dashboard-stats').textContent);

new Chart(document.getElementById('status-chart'), {
    type: 'doughnut',
    data: {
        labels: Object.keys(stats.byStatus),
        datasets: [{ data: Object.values(stats.byStatus) }],
    },
});

new Chart(document.getElementById('category-chart'), {
    type: 'bar',
    data: {
        labels: Object.keys(stats.byCategory),
        datasets: [{ data: Object.values(stats.byCategory) }],
    },
    options: {
        scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
        plugins: { legend: { display: false } },
    },
});
