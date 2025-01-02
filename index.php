<?php
session_start();
include 'config/database.php';
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard</h1>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Main row -->
            <div class="row">
                <!-- Quick Stats -->
                <div class="col-12">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-box custom-shadow hover-shadow">
                                <span class="info-box-icon bg-info"><i class="fas fa-truck"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Suppliers</span>
                                    <span class="info-box-number"><?php echo $suppliers_count; ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box custom-shadow hover-shadow">
                                <span class="info-box-icon bg-success"><i class="fas fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Parties</span>
                                    <span class="info-box-number"><?php echo $parties_count; ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box custom-shadow hover-shadow">
                                <span class="info-box-icon bg-warning"><i class="fas fa-box"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Products</span>
                                    <span class="info-box-number"><?php echo $products_count; ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box custom-shadow hover-shadow">
                                <span class="info-box-icon bg-danger"><i class="fas fa-shopping-cart"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Sales Orders</span>
                                    <span class="info-box-number"><?php echo $sales_count; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Today's Overview -->
                <div class="col-md-6">
                    <div class="card custom-shadow hover-shadow">
                        <div class="card-header">
                            <h3 class="card-title">Today's Overview</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-box bg-gradient-success">
                                        <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Today's Sales</span>
                                            <span class="info-box-number" id="todaySales">₹0.00</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-box bg-gradient-danger">
                                        <span class="info-box-icon"><i class="fas fa-shopping-basket"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Today's Purchase</span>
                                            <span class="info-box-number" id="todayPurchase">₹0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Sales & Purchase -->
                <div class="col-md-6">
                    <div class="card custom-shadow hover-shadow">
                        <div class="card-header">
                            <h3 class="card-title">Overall Performance</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-box bg-gradient-success">
                                        <span class="info-box-icon"><i class="fas fa-chart-pie"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Sales</span>
                                            <span class="info-box-number" id="totalSales">₹0.00</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-box bg-gradient-danger">
                                        <span class="info-box-icon"><i class="fas fa-cart-plus"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Purchase</span>
                                            <span class="info-box-number" id="totalPurchase">₹0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="progress-group">
                                        <span class="progress-text">Sales vs Purchase Ratio</span>
                                        <span class="float-right" id="salesPurchaseRatio">0%</span>
                                        <div class="progress progress-sm">
                                            <div class="progress-bar bg-success" id="salesPurchaseProgress" style="width: 0%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="col-12">
                    <div class="row">
                        <!-- Expenses Statement -->
                        <div class="col-md-4">
                            <div class="card custom-shadow hover-shadow">
                                <div class="card-header">
                                    <h3 class="card-title">Expenses Statement</h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="expensesChart" style="min-height: 200px; max-height: 200px;"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Best Selling Products -->
                        <div class="col-md-8">
                            <div class="card custom-shadow hover-shadow">
                                <div class="card-header">
                                    <h3 class="card-title">Best Selling Products</h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="bestSellingChart" style="min-height: 200px; max-height: 200px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sales & Purchase Report -->
                <div class="col-12">
                    <div class="card custom-shadow-lg">
                        <div class="card-header">
                            <h3 class="card-title">Sales & Purchase Report</h3>
                            <div class="card-tools">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-primary active" data-range="day">Daily</button>
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-range="week">Weekly</button>
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-range="month">Monthly</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="salesPurchaseChart" style="min-height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'includes/footer.php'; ?>

<style>
.custom-shadow {
    box-shadow: 0 0 1rem rgba(0,0,0,.15);
    transition: all 0.3s ease;
}
.custom-shadow-lg {
    box-shadow: 0 0 2rem rgba(0,0,0,.15);
    transition: all 0.3s ease;
}
.hover-shadow:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 2rem rgba(0,0,0,.15);
}
.info-box {
    min-height: 100px;
}
.progress-sm {
    height: 0.5rem;
}
.btn-group .btn.active {
    background-color: #007bff;
    color: white;
}
</style>

<script>
// Global variables for charts
let expensesChart = null;
let bestSellingChart = null;
let salesPurchaseChart = null;

// Function to update dashboard data
function updateDashboard() {
    const range = $('.btn-group .btn.active').data('range');
    $.ajax({
        url: 'ajax/get_dashboard_data.php',
        type: 'GET',
        data: { range: range },
        success: function(response) {
            const data = JSON.parse(response);
            
            if (data.status === 'success') {
                updateCounts(data.data.counts);
                updateTodayOverview(data.data.today);
                updateExpensesChart(data.data.expenses);
                updateBestSellingChart(data.data.best_selling);
                updateSalesPurchaseChart(data.data.monthly_data, data.data.range);
            }
        }
    });
}

// Function to update count boxes
function updateCounts(counts) {
    $('.info-box-number').each(function() {
        const box = $(this);
        if (box.closest('.info-box').find('.fas.fa-truck').length) {
            box.text(counts.suppliers);
        } else if (box.closest('.info-box').find('.fas.fa-users').length) {
            box.text(counts.parties);
        } else if (box.closest('.info-box').find('.fas.fa-box').length) {
            box.text(counts.products);
        } else if (box.closest('.info-box').find('.fas.fa-shopping-cart').length) {
            box.text(counts.sales);
        }
    });
}

// Function to update today's overview
function updateTodayOverview(today) {
    $('#todaySales').text('₹' + parseFloat(today.sales).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    $('#todayPurchase').text('₹' + parseFloat(today.purchase).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    
    // Update total sales and purchase
    $('#totalSales').text('₹' + parseFloat(today.total_sales).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    $('#totalPurchase').text('₹' + parseFloat(today.total_purchase).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    
    // Calculate and update ratio
    const totalSales = parseFloat(today.total_sales) || 0;
    const totalPurchase = parseFloat(today.total_purchase) || 0;
    const ratio = totalPurchase > 0 ? (totalSales / totalPurchase) * 100 : 0;
    
    $('#salesPurchaseRatio').text(ratio.toFixed(1) + '%');
    $('#salesPurchaseProgress').css('width', Math.min(ratio, 100) + '%');
}

// Function to update expenses chart
function updateExpensesChart(expenses) {
    const labels = expenses.map(item => item.category);
    const values = expenses.map(item => item.total);
    const colors = ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de'];
    
    if (expensesChart) {
        expensesChart.data.labels = labels;
        expensesChart.data.datasets[0].data = values;
        expensesChart.data.datasets[0].backgroundColor = colors.slice(0, labels.length);
        expensesChart.update();
    } else {
        const ctx = document.getElementById('expensesChart').getContext('2d');
        expensesChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: colors.slice(0, labels.length)
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });
    }
}

// Function to update best selling products chart
function updateBestSellingChart(products) {
    const labels = products.map(item => item.product);
    const values = products.map(item => item.quantity);
    
    if (bestSellingChart) {
        bestSellingChart.data.labels = labels;
        bestSellingChart.data.datasets[0].data = values;
        bestSellingChart.update();
    } else {
        const ctx = document.getElementById('bestSellingChart').getContext('2d');
        bestSellingChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Quantity Sold',
                    data: values,
                    backgroundColor: '#007bff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    },
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                }
            }
        });
    }
}

// Function to update sales & purchase chart
function updateSalesPurchaseChart(monthlyData, range) {
    const labels = monthlyData.map(item => item.label);
    const salesData = monthlyData.map(item => parseFloat(item.sales) || 0);
    const purchaseData = monthlyData.map(item => parseFloat(item.purchase) || 0);
    
    let chartTitle = '';
    let xAxisLabel = '';
    let tickRotation = 0;
    
    switch(range) {
        case 'day':
            chartTitle = 'Daily Sales & Purchase (Last 7 Days)';
            xAxisLabel = 'Date';
            tickRotation = 30;
            break;
        case 'week':
            chartTitle = 'Weekly Sales & Purchase (Last 4 Weeks)';
            xAxisLabel = 'Week';
            tickRotation = 0;
            break;
        case 'month':
            chartTitle = 'Monthly Sales & Purchase (Last 6 Months)';
            xAxisLabel = 'Month';
            tickRotation = 0;
            break;
    }
    
    const chartConfig = {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Sales',
                data: salesData,
                borderColor: '#00a65a',
                backgroundColor: '#00a65a20',
                fill: true,
                tension: 0.4,
                borderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }, {
                label: 'Purchase',
                data: purchaseData,
                borderColor: '#f56954',
                backgroundColor: '#f5695420',
                fill: true,
                tension: 0.4,
                borderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                title: {
                    display: true,
                    text: chartTitle,
                    font: {
                        size: 16,
                        weight: 'bold'
                    },
                    padding: {
                        top: 10,
                        bottom: 30
                    }
                },
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.9)',
                    titleColor: '#000',
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyColor: '#666',
                    bodyFont: {
                        size: 12
                    },
                    borderColor: '#ddd',
                    borderWidth: 1,
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += '₹' + context.parsed.y.toLocaleString();
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₹' + value.toLocaleString();
                        },
                        font: {
                            size: 11
                        }
                    },
                    title: {
                        display: true,
                        text: 'Amount (₹)',
                        font: {
                            size: 12,
                            weight: 'bold'
                        }
                    },
                    grid: {
                        color: '#f0f0f0'
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: xAxisLabel,
                        font: {
                            size: 12,
                            weight: 'bold'
                        }
                    },
                    ticks: {
                        maxRotation: tickRotation,
                        minRotation: tickRotation,
                        autoSkip: false,
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    };
    
    if (salesPurchaseChart) {
        salesPurchaseChart.data = chartConfig.data;
        salesPurchaseChart.options = chartConfig.options;
        salesPurchaseChart.update('none');
    } else {
        const ctx = document.getElementById('salesPurchaseChart').getContext('2d');
        salesPurchaseChart = new Chart(ctx, chartConfig);
    }
}

// Initial load
$(document).ready(function() {
    // First load
    updateDashboard();
    
    // Update every 15 seconds
    setInterval(updateDashboard, 15000);
    
    // Update on report range button click
    $('.btn-group .btn').click(function() {
        $('.btn-group .btn').removeClass('active');
        $(this).addClass('active');
        updateDashboard();
    });
});
</script>