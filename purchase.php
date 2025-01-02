<?php
session_start();
include 'config/database.php';
include 'includes/header.php';
include 'includes/sidebar.php';

// Get all suppliers
$suppliers_query = "SELECT id, name FROM suppliers";
$suppliers_result = mysqli_query($conn, $suppliers_query);

// Get all products
$products_query = "SELECT p.id, p.name, p.sku, p.purchase_price, u.id as unit_id, u.name as unit_name, u.short_name as unit_short_name 
                  FROM products p
                  LEFT JOIN units u ON p.unit_id = u.id";
$products_result = mysqli_query($conn, $products_query);

// Get all units
$units_query = "SELECT id, name, short_name FROM units";
$units_result = mysqli_query($conn, $units_query);
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Purchase Order</h1>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">New Purchase Order</h3>
                </div>
                <div class="card-body">
                    <form id="purchaseForm">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Supplier</label>
                                    <select class="form-control" name="supplier_id" id="supplier_id" required>
                                        <option value="">Select Supplier</option>
                                        <?php while ($supplier = mysqli_fetch_assoc($suppliers_result)) { ?>
                                            <option value="<?php echo $supplier['id']; ?>"><?php echo $supplier['name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Order Date</label>
                                    <input type="date" class="form-control" name="order_date" required value="<?php echo date('Y-m-d'); ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Purchase Items and Supplier Details -->
                        <div class="row">
                            <div class="col-md-8">
                                <!-- Purchase Items Table -->
                                <div class="table-responsive mt-4">
                                    <table class="table table-bordered" id="purchaseItems">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Quantity</th>
                                                <th>Unit</th>
                                                <th>Unit Price</th>
                                                <th>Total</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <select class="form-control product-select" name="items[0][product_id]" required>
                                                        <option value="">Select Product</option>
                                                        <?php 
                                                        mysqli_data_seek($products_result, 0);
                                                        while ($product = mysqli_fetch_assoc($products_result)) { 
                                                        ?>
                                                            <option value="<?php echo $product['id']; ?>" 
                                                                    data-price="<?php echo $product['purchase_price']; ?>"
                                                                    data-unit-id="<?php echo $product['unit_id']; ?>">
                                                                <?php echo $product['name'] . ' (' . $product['sku'] . ')'; ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" class="form-control quantity" name="items[0][quantity]" required>
                                                </td>
                                                <td>
                                                    <select class="form-control unit-select" name="items[0][unit_id]" required>
                                                        <option value="">Select Unit</option>
                                                        <?php 
                                                        mysqli_data_seek($units_result, 0);
                                                        while ($unit = mysqli_fetch_assoc($units_result)) { 
                                                        ?>
                                                            <option value="<?php echo $unit['id']; ?>">
                                                                <?php echo $unit['name'] . ' (' . $unit['short_name'] . ')'; ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" class="form-control price" name="items[0][unit_price]" required>
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" class="form-control total" name="items[0][total_price]" readonly>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger btn-sm remove-item"><i class="fas fa-trash"></i></button>
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="4" class="text-right"><strong>Grand Total:</strong></td>
                                                <td>
                                                    <input type="number" step="0.01" class="form-control" id="grandTotal" name="total_amount" readonly>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-primary btn-sm" id="addItem">
                                                        <i class="fas fa-plus"></i> Add Item
                                                    </button>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <!-- Supplier Profile Widget -->
                                <div class="card card-widget" id="supplierDetails" style="display: none;">
                                    <div class="card-header bg-primary py-3">
                                        <h3 class="card-title mb-2" id="supplierName" style="font-size: 2.5rem;"></h3><br><br><br>
                                        <div class="text-white mb-0" id="supplierContact" style="font-size: 1.1rem;"></div>
                                    </div>
                                    <div class="card-body">
                                        <div class="contact-info mb-3">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-envelope mr-2"></i>
                                                <span id="supplierEmail" class="text-muted"></span>
                                            </div>
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-phone mr-2"></i>
                                                <span id="supplierPhone" class="text-muted"></span>
                                            </div>
                                            <div class="d-flex align-items-start">
                                                <i class="fas fa-map-marker-alt mr-2 mt-1"></i>
                                                <span id="supplierAddress" class="text-muted"></span>
                                            </div>
                                        </div>
                                        <div class="row mt-4">
                                            <div class="col-sm-6 border-right">
                                                <div class="description-block">
                                                    <h5 class="description-header text-primary" id="totalPurchases">₹0.00</h5>
                                                    <span class="description-text">TOTAL PURCHASES</span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="description-block">
                                                    <h5 class="description-header text-danger" id="outstandingAmount">₹0.00</h5>
                                                    <span class="description-text">OUTSTANDING</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Recent Purchase History -->
                                <div class="card card-outline card-primary" id="purchaseHistory" style="display: none;">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-history mr-2"></i>Recent Purchase History
                                        </h3>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <tbody id="recentPurchases">
                                                    <!-- Recent purchases will be loaded here -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Recent Receipts -->
                                <div class="card card-outline card-success" id="receiptHistory" style="display: none;">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-receipt mr-2"></i>Recent Receipts
                                        </h3>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <tbody id="recentReceipts">
                                                    <!-- Recent receipts will be loaded here -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary float-right">Save Purchase Order</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Purchase Orders List -->
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">Recent Purchase Orders</h3>
                </div>
                <div class="card-body">
                    <table id="purchaseOrdersTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Supplier</th>
                                <th>Total Amount</th>
                                <th>Payment Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $orders_query = "SELECT po.*, s.name as supplier_name 
                                           FROM purchase_orders po 
                                           LEFT JOIN suppliers s ON po.supplier_id = s.id 
                                           ORDER BY po.order_date DESC";
                            $orders_result = mysqli_query($conn, $orders_query);
                            
                            while ($order = mysqli_fetch_assoc($orders_result)) {
                                $payment_badge = '';
                                switch($order['payment_status']) {
                                    case 'pending':
                                        $payment_badge = 'badge-warning';
                                        break;
                                    case 'completed':
                                        $payment_badge = 'badge-success';
                                        break;
                                    case 'partial':
                                        $payment_badge = 'badge-info';
                                        break;
                                }
                            ?>
                                <tr>
                                    <td><?php echo $order['id']; ?></td>
                                    <td><?php echo date('d M Y', strtotime($order['order_date'])); ?></td>
                                    <td><?php echo $order['supplier_name']; ?></td>
                                    <td>₹<?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td><span class="badge <?php echo $payment_badge; ?>"><?php echo ucfirst($order['payment_status']); ?></span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info view-order" data-id="<?php echo $order['id']; ?>">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-primary edit-order" data-id="<?php echo $order['id']; ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger delete-order" data-id="<?php echo $order['id']; ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- View Order Modal -->
<div class="modal fade" id="viewOrderModal" tabindex="-1" role="dialog" aria-labelledby="viewOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewOrderModalLabel">Purchase Order Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Order Information</h6>
                        <table class="table table-bordered">
                            <tr>
                                <th>Order ID</th>
                                <td id="view_order_id"></td>
                            </tr>
                            <tr>
                                <th>Order Date</th>
                                <td id="view_order_date"></td>
                            </tr>
                            <tr>
                                <th>Total Amount</th>
                                <td id="view_total_amount"></td>
                            </tr>
                            <tr>
                                <th>Payment Status</th>
                                <td id="view_payment_status"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Supplier Information</h6>
                        <table class="table table-bordered">
                            <tr>
                                <th>Supplier Name</th>
                                <td id="view_supplier_name"></td>
                            </tr>
                            <tr>
                                <th>Contact Person</th>
                                <td id="view_contact_person"></td>
                            </tr>
                            <tr>
                                <th>Phone</th>
                                <td id="view_phone"></td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td id="view_email"></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <h6 class="mt-4">Order Items</h6>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                                <th>Unit Price</th>
                                <th>Total Price</th>
                            </tr>
                        </thead>
                        <tbody id="view_order_items">
                        </tbody>
                    </table>
                </div>

                <h6 class="mt-4">Payment History</h6>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Payment Mode</th>
                                <th>Reference No</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody id="view_payment_history">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#purchaseOrdersTable').DataTable({
        responsive: true,
        order: [[1, 'desc']], // Sort by date descending
        language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "No records available",
            infoFiltered: "(filtered from _MAX_ total records)",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        }
    });

    // View Order Details
    $(document).on('click', '.view-order', function() {
        var orderId = $(this).data('id');
        $.ajax({
            type: 'POST',
            url: 'ajax/get_purchase_order_details.php',
            data: { order_id: orderId },
            success: function(response) {
                var data = JSON.parse(response);
                if (data.status == 'success') {
                    // Fill order information
                    $('#view_order_id').text(data.order.id);
                    $('#view_order_date').text(data.order.order_date);
                    $('#view_total_amount').text('₹' + parseFloat(data.order.total_amount).toFixed(2));
                    
                    // Set payment status with badge
                    var statusBadgeClass = '';
                    switch(data.order.payment_status) {
                        case 'pending': statusBadgeClass = 'badge-warning'; break;
                        case 'completed': statusBadgeClass = 'badge-success'; break;
                        case 'partial': statusBadgeClass = 'badge-info'; break;
                    }
                    $('#view_payment_status').html(`<span class="badge ${statusBadgeClass}">${data.order.payment_status}</span>`);
                    
                    // Fill supplier information
                    $('#view_supplier_name').text(data.supplier.name);
                    $('#view_contact_person').text(data.supplier.contact_person);
                    $('#view_phone').text(data.supplier.phone);
                    $('#view_email').text(data.supplier.email);
                    
                    // Fill order items
                    var itemsHtml = '';
                    data.items.forEach(function(item) {
                        itemsHtml += `
                            <tr>
                                <td>${item.product_name}</td>
                                <td>${item.quantity}</td>
                                <td>${item.unit_name}</td>
                                <td>₹${parseFloat(item.unit_price).toFixed(2)}</td>
                                <td>₹${parseFloat(item.total_price).toFixed(2)}</td>
                            </tr>
                        `;
                    });
                    $('#view_order_items').html(itemsHtml);
                    
                    // Fill payment history
                    var paymentsHtml = '';
                    if (data.payments.length > 0) {
                        data.payments.forEach(function(payment) {
                            paymentsHtml += `
                                <tr>
                                    <td>${payment.payment_date}</td>
                                    <td>₹${parseFloat(payment.amount).toFixed(2)}</td>
                                    <td>${payment.payment_mode}</td>
                                    <td>${payment.reference_no}</td>
                                    <td>${payment.notes}</td>
                                </tr>
                            `;
                        });
                    } else {
                        paymentsHtml = '<tr><td colspan="5" class="text-center">No payment records found</td></tr>';
                    }
                    $('#view_payment_history').html(paymentsHtml);
                    
                    $('#viewOrderModal').modal('show');
                }
            }
        });
    });

    // Edit Order
    $(document).on('click', '.edit-order', function() {
        var orderId = $(this).data('id');
        // Add your edit logic here
    });

    // Delete Order
    $(document).on('click', '.delete-order', function() {
        var orderId = $(this).data('id');
        $.ajax({
            type: 'POST',
            url: 'ajax/purchase_order_delete.php',
            data: { order_id: orderId },
            success: function(response) {
                var data = JSON.parse(response);
                if (data.status == 'success') {
                    Swal.fire({
                        title: 'Deleted!',
                        text: data.message,
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 1500,
                        didClose: () => {
                            location.reload();
                        }
                    });
                } else if (data.status == 'confirm') {
                    Swal.fire({
                        title: 'Warning!',
                        text: data.message,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete all!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                type: 'POST',
                                url: 'ajax/purchase_order_delete.php',
                                data: { 
                                    order_id: orderId,
                                    confirmed: true
                                },
                                success: function(response) {
                                    var data = JSON.parse(response);
                                    if (data.status == 'success') {
                                        Swal.fire({
                                            title: 'Deleted!',
                                            text: data.message,
                                            icon: 'success',
                                            showConfirmButton: false,
                                            timer: 1500,
                                            didClose: () => {
                                                location.reload();
                                            }
                                        });
                                    } else {
                                        Swal.fire({
                                            title: 'Error!',
                                            text: data.message,
                                            icon: 'error',
                                            confirmButtonText: 'Ok'
                                        });
                                    }
                                },
                                error: function() {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: 'Failed to delete order. Please try again.',
                                        icon: 'error',
                                        confirmButtonText: 'Ok'
                                    });
                                }
                            });
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message,
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to delete order. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'Ok'
                });
            }
        });
    });

    // Add new item row
    $('#addItem').click(function() {
        var index = $('#purchaseItems tbody tr').length;
        var newRow = $('#purchaseItems tbody tr:first').clone();
        
        // Update name attributes
        newRow.find('.product-select').attr('name', 'items[' + index + '][product_id]').val('');
        newRow.find('.quantity').attr('name', 'items[' + index + '][quantity]').val('');
        newRow.find('.unit-select').attr('name', 'items[' + index + '][unit_id]').val('');
        newRow.find('.price').attr('name', 'items[' + index + '][unit_price]').val('');
        newRow.find('.total').attr('name', 'items[' + index + '][total_price]').val('');
        
        $('#purchaseItems tbody').append(newRow);
    });

    // Remove item row
    $(document).on('click', '.remove-item', function() {
        if ($('#purchaseItems tbody tr').length > 1) {
            $(this).closest('tr').remove();
            calculateGrandTotal();
        }
    });

    // Auto-fill price and unit when product is selected
    $(document).on('change', '.product-select', function() {
        var selected = $(this).find(':selected');
        var price = selected.data('price');
        var unitId = selected.data('unit-id');
        var row = $(this).closest('tr');
        
        row.find('.price').val(price);
        row.find('.unit-select').val(unitId);
        calculateRowTotal(row);
    });

    // Calculate row total when quantity or price changes
    $(document).on('input', '.quantity, .price', function() {
        calculateRowTotal($(this).closest('tr'));
    });

    // Calculate row total
    function calculateRowTotal(row) {
        var quantity = row.find('.quantity').val() || 0;
        var price = row.find('.price').val() || 0;
        var total = quantity * price;
        row.find('.total').val(total.toFixed(2));
        calculateGrandTotal();
    }

    // Calculate grand total
    function calculateGrandTotal() {
        var grandTotal = 0;
        $('.total').each(function() {
            grandTotal += parseFloat($(this).val() || 0);
        });
        $('#grandTotal').val(grandTotal.toFixed(2));
    }

    // Form submission
    $('#purchaseForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: 'ajax/purchase_add.php',
            data: $(this).serialize(),
            success: function(response) {
                var data = JSON.parse(response);
                if (data.status == 'success') {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Purchase order created successfully!',
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 1500,
                        didClose: () => {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            }
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message,
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                }
            }
        });
    });

    // Update the JavaScript part for loading supplier details
    $('#supplier_id').change(function() {
        var supplierId = $(this).val();
        if (supplierId) {
            $.ajax({
                type: 'POST',
                url: 'ajax/get_supplier_details.php',
                data: { supplier_id: supplierId },
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data.status == 'success') {
                        // Update supplier details widget
                        $('#supplierName').text(data.supplier.name);
                        $('#supplierContact').text(data.supplier.contact_person);
                        $('#supplierEmail').text(data.supplier.email || 'N/A');
                        $('#supplierPhone').text(data.supplier.phone || 'N/A');
                        $('#supplierAddress').text(data.supplier.address || 'N/A');
                        $('#totalPurchases').text('₹' + parseFloat(data.total_purchases).toFixed(2));
                        $('#outstandingAmount').text('₹' + parseFloat(data.outstanding_amount).toFixed(2));

                        // Update recent purchase history
                        var purchasesHtml = '';
                        data.recent_purchases.forEach(function(purchase) {
                            var statusClass = '';
                            switch(purchase.status) {
                                case 'completed': statusClass = 'success'; break;
                                case 'pending': statusClass = 'warning'; break;
                                case 'cancelled': statusClass = 'danger'; break;
                            }
                            purchasesHtml += `
                                <tr>
                                    <td>
                                        <span class="text-bold">Order #${purchase.id}</span><br>
                                        <small class="text-muted">${purchase.order_date}</small>
                                    </td>
                                    <td class="text-right">
                                        <span class="badge badge-${statusClass}">${purchase.status}</span><br>
                                        <span class="text-bold">₹${parseFloat(purchase.total_amount).toFixed(2)}</span>
                                    </td>
                                </tr>`;
                        });
                        $('#recentPurchases').html(purchasesHtml || '<tr><td colspan="2" class="text-center">No recent purchases</td></tr>');

                        // Update recent receipts
                        var receiptsHtml = '';
                        data.recent_receipts.forEach(function(receipt) {
                            receiptsHtml += `
                                <tr>
                                    <td>
                                        <span class="text-bold">${receipt.payment_mode}</span><br>
                                        <small class="text-muted">${receipt.payment_date}</small>
                                    </td>
                                    <td class="text-right">
                                        <small class="text-muted">${receipt.reference_no}</small><br>
                                        <span class="text-bold text-success">₹${parseFloat(receipt.amount).toFixed(2)}</span>
                                    </td>
                                </tr>`;
                        });
                        $('#recentReceipts').html(receiptsHtml || '<tr><td colspan="2" class="text-center">No recent receipts</td></tr>');

                        // Show all widgets with animation
                        $('#supplierDetails, #contactInfo, #purchaseHistory, #receiptHistory').hide().fadeIn(500);
                    }
                }
            });
        } else {
            // Hide all widgets if no supplier is selected
            $('#supplierDetails, #contactInfo, #purchaseHistory, #receiptHistory').fadeOut(300);
        }
    });
});
</script> 