<?php
session_start();
include 'config/database.php';
include 'includes/header.php';
include 'includes/sidebar.php';

// Get all suppliers
$suppliers_query = "SELECT id, name FROM suppliers";
$suppliers_result = mysqli_query($conn, $suppliers_query);

// Get all purchase orders
$orders_query = "SELECT po.id, po.order_date, po.total_amount, s.name as supplier_name, 
                (po.total_amount - COALESCE((SELECT SUM(amount) FROM purchase_receipts WHERE purchase_order_id = po.id), 0)) as balance_amount
                FROM purchase_orders po
                LEFT JOIN suppliers s ON po.supplier_id = s.id
                ORDER BY po.order_date DESC";
$orders_result = mysqli_query($conn, $orders_query);
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Purchase Receipts</h1>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Add Receipt Form -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Add New Receipt</h3>
                </div>
                <div class="card-body">
                    <form id="receiptForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Supplier</label>
                                    <select class="form-control" name="supplier_id" id="supplier_id" required>
                                        <option value="">Select Supplier</option>
                                        <?php 
                                        mysqli_data_seek($suppliers_result, 0);
                                        while ($supplier = mysqli_fetch_assoc($suppliers_result)) { 
                                        ?>
                                            <option value="<?php echo $supplier['id']; ?>"><?php echo $supplier['name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Purchase Order</label>
                                    <select class="form-control" name="purchase_order_id" id="purchase_order_id" required>
                                        <option value="">Select Order</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Amount</label>
                                    <input type="number" step="0.01" class="form-control" name="amount" id="amount" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Payment Date</label>
                                    <input type="date" class="form-control" name="payment_date" required value="<?php echo date('Y-m-d'); ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Payment Mode</label>
                                    <select class="form-control" name="payment_mode" required>
                                        <option value="cash">Cash</option>
                                        <option value="cheque">Cheque</option>
                                        <option value="upi">UPI</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Reference No.</label>
                                    <input type="text" class="form-control" name="reference_no">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Notes</label>
                                    <textarea class="form-control" name="notes" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Receipt</button>
                    </form>
                </div>
            </div>

            <!-- Receipts List -->
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">Recent Receipts</h3>
                </div>
                <div class="card-body">
                    <table id="receiptsTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Supplier</th>
                                <th>Order #</th>
                                <th>Amount</th>
                                <th>Payment Mode</th>
                                <th>Reference No.</th>
                                <th>Notes</th>
                                <th>Payment Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $receipts_query = "SELECT r.*, s.name as supplier_name, po.payment_status, po.total_amount,
                                             (SELECT SUM(amount) FROM purchase_receipts WHERE purchase_order_id = r.purchase_order_id) as total_paid 
                                             FROM purchase_receipts r
                                             LEFT JOIN suppliers s ON r.supplier_id = s.id
                                             LEFT JOIN purchase_orders po ON r.purchase_order_id = po.id
                                             ORDER BY r.payment_date DESC";
                            $receipts_result = mysqli_query($conn, $receipts_query);
                            
                            while ($receipt = mysqli_fetch_assoc($receipts_result)) {
                                // Calculate payment status based on total paid amount
                                $total_amount = $receipt['total_amount'];
                                $total_paid = $receipt['total_paid'];
                                
                                if ($total_paid >= $total_amount) {
                                    $badge_class = 'badge badge-success';
                                    $status_text = 'Completed';
                                } elseif ($total_paid > 0) {
                                    $badge_class = 'badge badge-info';
                                    $status_text = 'Partial';
                                } else {
                                    $badge_class = 'badge badge-warning';
                                    $status_text = 'Pending';
                                }
                            ?>
                                <tr>
                                    <td><?php echo date('d M Y', strtotime($receipt['payment_date'])); ?></td>
                                    <td><?php echo $receipt['supplier_name']; ?></td>
                                    <td><?php echo $receipt['purchase_order_id']; ?></td>
                                    <td>₹<?php echo number_format($receipt['amount'], 2); ?></td>
                                    <td><?php echo ucfirst($receipt['payment_mode']); ?></td>
                                    <td><?php echo $receipt['reference_no']; ?></td>
                                    <td><?php echo $receipt['notes']; ?></td>
                                    <td><span class="<?php echo $badge_class; ?>"><?php echo $status_text; ?></span></td>
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
    $('#receiptsTable').DataTable({
        "responsive": true,
        "autoWidth": false,
        "order": [[0, 'desc']], // Sort by date descending
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

    // When supplier is selected, load their orders
    $('#supplier_id').change(function() {
        var supplierId = $(this).val();
        console.log('Selected Supplier ID:', supplierId); // Debug log
        
        if (supplierId) {
            $.ajax({
                type: 'POST',
                url: 'ajax/get_supplier_orders.php',
                data: { supplier_id: supplierId },
                dataType: 'json',
                success: function(response) {
                    console.log('Server Response:', response); // Debug log
                    
                    var options = '<option value="">Select Order</option>';
                    if (response.status == 'success' && response.orders.length > 0) {
                        response.orders.forEach(function(order) {
                            options += `<option value="${order.id}" data-balance="${order.balance_amount}">
                                        Order #${order.id} (${order.order_date}) - Balance: ₹${parseFloat(order.balance_amount).toFixed(2)}
                                      </option>`;
                        });
                    } else {
                        options = '<option value="">No pending orders found</option>';
                    }
                    $('#purchase_order_id').html(options);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error); // Debug log
                    $('#purchase_order_id').html('<option value="">Error loading orders</option>');
                }
            });
        } else {
            $('#purchase_order_id').html('<option value="">Select Order</option>');
        }
    });

    // When order is selected, set max amount
    $('#purchase_order_id').change(function() {
        var selected = $(this).find(':selected');
        var balanceAmount = selected.data('balance');
        if (balanceAmount) {
            $('#amount').attr('max', balanceAmount);
            $('#amount').val(balanceAmount);
        }
    });

    // Form submission
    $('#receiptForm').on('submit', function(e) {
        e.preventDefault();
        
        // Debug log
        console.log('Form Data:', $(this).serialize());
        
        $.ajax({
            type: 'POST',
            url: 'ajax/purchase_receipt_add.php',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                console.log('Server Response:', response); // Debug log
                
                if (response.status == 'success') {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
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
                        text: response.message,
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error); // Debug log
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to save receipt. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'Ok'
                });
            }
        });
    });
});
</script>
</body>
</html> 