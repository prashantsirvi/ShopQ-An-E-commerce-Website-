(function () {
    'use strict';

    function appUrl(path) {
        var meta = document.querySelector('meta[name="app-url"]');
        var base = meta ? meta.getAttribute('content') : '';
        return base.replace(/\/$/, '') + path;
    }

    function initAnalyticsCharts() {
        if (!document.querySelector('[data-analytics-chart]') || typeof Chart === 'undefined') {
            return;
        }

        fetch(appUrl('/admin/api/analytics'))
            .then(function (response) { return response.json(); })
            .then(function (data) {
                var revenueCanvas = document.getElementById('revenueChart');
                if (revenueCanvas && data.revenue) {
                    new Chart(revenueCanvas, {
                        type: 'line',
                        data: {
                            labels: data.revenue.map(function (row) { return row.day; }),
                            datasets: [{
                                label: 'Revenue',
                                data: data.revenue.map(function (row) { return Number(row.revenue); }),
                                borderColor: '#2874f0',
                                tension: 0.3,
                            }],
                        },
                        options: { responsive: true, maintainAspectRatio: false },
                    });
                }

                var statusCanvas = document.getElementById('statusChart');
                if (statusCanvas && data.orders_by_status) {
                    new Chart(statusCanvas, {
                        type: 'doughnut',
                        data: {
                            labels: data.orders_by_status.map(function (row) { return row.status; }),
                            datasets: [{
                                data: data.orders_by_status.map(function (row) { return Number(row.total); }),
                                backgroundColor: ['#2874f0', '#2ecc71', '#ff6b35', '#9b59b6', '#e74c3c', '#95a5a6'],
                            }],
                        },
                        options: { responsive: true, maintainAspectRatio: false },
                    });
                }
            })
            .catch(function () { /* silent */ });
    }

    initAnalyticsCharts();
})();
