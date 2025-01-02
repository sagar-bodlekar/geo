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
                    <h1 class="m-0">Purchase Orders</h1>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Add Purchase Form -->
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
                                <th>Paid Amount</th>
                                <th>Balance</th>
                                <th>Payment Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $orders_query = "SELECT po.*, s.name as supplier_name,
                                           COALESCE((SELECT SUM(amount) FROM purchase_receipts WHERE purchase_order_id = po.id), 0) as paid_amount,
                                           (po.total_amount - COALESCE((SELECT SUM(amount) FROM purchase_receipts WHERE purchase_order_id = po.id), 0)) as balance_amount
                                           FROM purchase_orders po 
                                           LEFT JOIN suppliers s ON po.supplier_id = s.id 
                                           ORDER BY po.order_date DESC";
                            $orders_result = mysqli_query($conn, $orders_query);

                            while ($order = mysqli_fetch_assoc($orders_result)) {
                                // Calculate payment status based on paid amount
                                $total_amount = $order['total_amount'];
                                $paid_amount = $order['paid_amount'];
                                $balance = $order['balance_amount'];
                                
                                if ($paid_amount >= $total_amount) {
                                    $badge_class = 'badge badge-success';
                                    $status_text = 'Completed';
                                } elseif ($paid_amount > 0) {
                                    $badge_class = 'badge badge-warning';
                                    $status_text = 'Partial';
                                } else {
                                    $badge_class = 'badge badge-warning';
                                    $status_text = 'Pending';
                                }
                            ?>
                                <tr>
                                    <td><?php echo $order['id']; ?></td>
                                    <td><?php echo date('d M Y', strtotime($order['order_date'])); ?></td>
                                    <td><?php echo $order['supplier_name']; ?></td>
                                    <td>₹<?php echo number_format($total_amount, 2); ?></td>
                                    <td>₹<?php echo number_format($paid_amount, 2); ?></td>
                                    <td>₹<?php echo number_format($balance, 2); ?></td>
                                    <td><span class="<?php echo $badge_class; ?>"><?php echo $status_text; ?></span></td>
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
        "responsive": true,
        "autoWidth": false,
        "order": [[1, 'desc']], // Sort by date descending
        "language": {
            "search": "Search:",
            "lengthMenu": "Show _MENU_ entries",
            "info": "Showing _START_ to _END_ of _TOTAL_ entries",
            "infoEmpty": "No records available",
            "infoFiltered": "(filtered from _MAX_ total records)",
            "paginate": {
                "first": "First",
                "last": "Last",
                "next": "Next",
                "previous": "Previous"
            }
        }
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

    // Delete Order
    $(document).on('click', '.delete-order', function() {
        var orderId = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to delete this order?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: 'ajax/purchase_delete.php',
                    data: { order_id: orderId },
                    success: function(response) {
                        var data = JSON.parse(response);
                        if (data.status == 'success') {
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Purchase order has been deleted.',
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
                    }
                });
            }
        });
    });
});
</script>