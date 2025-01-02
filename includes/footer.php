    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
    $(document).ready(function() {
        // Check if we're on the dashboard page
        if ($('#salesPurchaseChart').length > 0) {
            // Dashboard specific code
            let expensesChart = null;
            let bestSellingChart = null;
            let salesPurchaseChart = null;

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

            function updateTodayOverview(today) {
                $('#todaySales').text('₹' + parseFloat(today.sales).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                $('#todayPurchase').text('₹' + parseFloat(today.purchase).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                
                $('#totalSales').text('₹' + parseFloat(today.total_sales).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                $('#totalPurchase').text('₹' + parseFloat(today.total_purchase).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                
                const totalSales = parseFloat(today.total_sales) || 0;
                const totalPurchase = parseFloat(today.total_purchase) || 0;
                const ratio = totalPurchase > 0 ? (totalSales / totalPurchase) * 100 : 0;
                
                $('#salesPurchaseRatio').text(ratio.toFixed(1) + '%');
                $('#salesPurchaseProgress').css('width', Math.min(ratio, 100) + '%');
            }

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
            updateDashboard();
            
            // Update every 15 seconds
            setInterval(updateDashboard, 15000);
            
            // Update on report range button click
            $('.btn-group .btn').click(function() {
                $('.btn-group .btn').removeClass('active');
                $(this).addClass('active');
                updateDashboard();
            });
        }

        // Check if we're on a page with product selection
        if ($('.product-select').length > 0) {
            // Common Select2 configuration
            const select2Config = {
                theme: 'bootstrap4',
                width: '100%',
                allowClear: true,
                placeholder: 'Type to search...',
                escapeMarkup: function(markup) {
                    return markup;
                },
                templateResult: function(data) {
                    if (data.loading) return data.text;
                    var markup = "<div class='select2-result-item'>" + data.text + "</div>";
                    return markup;
                },
                templateSelection: function(data) {
                    return data.text;
                }
            };

            // Initialize supplier dropdown
            $.ajax({
                url: 'ajax/get_all_suppliers.php',
                dataType: 'json',
                success: function(data) {
                    $('#supplier_id').select2({
                        ...select2Config,
                        placeholder: 'Search supplier...',
                        data: data,
                        matcher: function(params, data) {
                            if ($.trim(params.term) === '') {
                                return data;
                            }
                            if (typeof data.text === 'undefined') {
                                return null;
                            }
                            if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
                                return data;
                            }
                            return null;
                        }
                    });
                }
            });

            // Initialize party dropdown
            $.ajax({
                url: 'ajax/get_all_parties.php',
                dataType: 'json',
                success: function(data) {
                    $('#party_id').select2({
                        ...select2Config,
                        placeholder: 'Search party...',
                        data: data,
                        matcher: function(params, data) {
                            if ($.trim(params.term) === '') {
                                return data;
                            }
                            if (typeof data.text === 'undefined') {
                                return null;
                            }
                            if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
                                return data;
                            }
                            return null;
                        }
                    });
                }
            });

            // Load all products data
            let allProducts = [];
            $.ajax({
                url: 'ajax/get_all_products.php',
                dataType: 'json',
                async: false,
                success: function(data) {
                    allProducts = data;
                }
            });

            // Function to initialize product select2
            function initializeProductSelect(element) {
                $(element).select2({
                    ...select2Config,
                    placeholder: 'Search product...',
                    data: allProducts,
                    matcher: function(params, data) {
                        if ($.trim(params.term) === '') {
                            return data;
                        }
                        if (typeof data.text === 'undefined') {
                            return null;
                        }
                        if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
                            return data;
                        }
                        return null;
                    }
                });
            }

            // Initialize existing product selects
            $('.product-select').each(function() {
                initializeProductSelect(this);
            });

            // Function to add new product row
            function addProductRow(tableId) {
                const rowCount = $(`#${tableId} tbody tr`).length;
                const newRow = `
                    <tr class="product-row">
                        <td>
                            <select class="form-control select2 product-select" name="products[${rowCount}][product_id]" id="product_${rowCount}" required>
                                <option value="">Select Product</option>
                            </select>
                        </td>
                        <td>
                            <input type="number" class="form-control quantity" name="products[${rowCount}][quantity]" min="1" required>
                        </td>
                        <td>
                            <input type="number" class="form-control price" name="products[${rowCount}][price]" min="0" step="0.01" required>
                        </td>
                        <td>
                            <input type="number" class="form-control total" name="products[${rowCount}][total]" readonly>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm remove-row">
                                <i class="fas fa-times"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $(`#${tableId} tbody`).append(newRow);
                initializeProductSelect(`#product_${rowCount}`);
            }

            // Add product row buttons
            $('#add_product_purchase').on('click', function() {
                addProductRow('purchase_products');
            });

            $('#add_product_sale').on('click', function() {
                addProductRow('sale_products');
            });

            // Remove product row
            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
                calculateTotal();
            });

            // Calculate row total
            $(document).on('input', '.quantity, .price', function() {
                const row = $(this).closest('tr');
                const quantity = parseFloat(row.find('.quantity').val()) || 0;
                const price = parseFloat(row.find('.price').val()) || 0;
                row.find('.total').val((quantity * price).toFixed(2));
                calculateTotal();
            });

            // Calculate grand total
            function calculateTotal() {
                let grandTotal = 0;
                $('.total').each(function() {
                    grandTotal += parseFloat($(this).val()) || 0;
                });
                $('#grand_total').val(grandTotal.toFixed(2));
            }

            // Product selection change handler
            $(document).on('select2:select', '.product-select', function(e) {
                const selectedProduct = e.params.data;
                const row = $(this).closest('tr');
                // You can add additional logic here if needed
                // For example, auto-fill price based on selected product
            });
        }
    });
    </script>
</body>
</html> 