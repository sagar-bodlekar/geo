<?php
session_start();
include 'config/database.php';
include 'includes/header.php';
include 'includes/sidebar.php';

// Get all parties
$parties_query = "SELECT id, name FROM parties";
$parties_result = mysqli_query($conn, $parties_query);

// Get all sales orders
$orders_query = "SELECT so.id, so.order_date, so.total_amount, p.name as party_name, 
                (so.total_amount - COALESCE((SELECT SUM(amount) FROM sales_transactions WHERE sales_order_id = so.id), 0)) as balance_amount
                FROM sales_orders so
                LEFT JOIN parties p ON so.party_id = p.id
                WHERE so.payment_status != 'paid'
                ORDER BY so.order_date DESC";
$orders_result = mysqli_query($conn, $orders_query);
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Sales Transactions</h1>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Add Transaction Form -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Add New Transaction</h3>
                </div>
                <div class="card-body">
                    <form id="transactionForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Party</label>
                                    <select class="form-control" name="party_id" id="party_id" required>
                                        <option value="">Select Party</option>
                                        <?php 
                                        mysqli_data_seek($parties_result, 0);
                                        while ($party = mysqli_fetch_assoc($parties_result)) { 
                                        ?>
                                            <option value="<?php echo $party['id']; ?>"><?php echo $party['name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Sales Order</label>
                                    <select class="form-control" name="sales_order_id" id="sales_order_id" required>
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
                        <button type="submit" class="btn btn-primary">Save Transaction</button>
                    </form>
                </div>
            </div>

            <!-- Transactions List -->
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">Recent Transactions</h3>
                </div>
                <div class="card-body">
                    <table id="transactionsTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Party</th>
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
                            $transactions_query = "SELECT t.*, p.name as party_name, so.payment_status 
                                                 FROM sales_transactions t
                                                 LEFT JOIN parties p ON t.party_id = p.id
                                                 LEFT JOIN sales_orders so ON t.sales_order_id = so.id
                                                 ORDER BY t.payment_date DESC";
                            $transactions_result = mysqli_query($conn, $transactions_query);
                            
                            while ($transaction = mysqli_fetch_assoc($transactions_result)) {
                                // Get badge class based on status
                                switch($transaction['payment_status']) {
                                    case 'completed':
                                        $badge_class = 'badge badge-success';
                                        break;
                                    case 'partial':
                                        $badge_class = 'badge badge-info';
                                        break;
                                    case 'pending':
                                        $badge_class = 'badge badge-warning';
                                        break;
                                    default:
                                        $badge_class = 'badge badge-secondary';
                                }
                            ?>
                                <tr>
                                    <td><?php echo date('d M Y', strtotime($transaction['payment_date'])); ?></td>
                                    <td><?php echo $transaction['party_name']; ?></td>
                                    <td><?php echo $transaction['sales_order_id']; ?></td>
                                    <td>₹<?php echo number_format($transaction['amount'], 2); ?></td>
                                    <td><?php echo ucfirst($transaction['payment_mode']); ?></td>
                                    <td><?php echo $transaction['reference_no']; ?></td>
                                    <td><?php echo $transaction['notes']; ?></td>
                                    <td><span class="<?php echo $badge_class; ?>"><?php echo ucfirst($transaction['payment_status']); ?></span></td>
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

<script>
$(function() {
    // Debug log to check if script is loaded
    console.log('Script loaded');

    // Initialize DataTable
    try {
        var table = $('#transactionsTable').DataTable({
            "responsive": true,
            "autoWidth": false,
            "order": [[0, 'desc']],
            "language": {
                "search": "Search:",
                "lengthMenu": "Show _MENU_ entries",
                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                "infoEmpty": "No entries available",
                "infoFiltered": "(filtered from _MAX_ total entries)",
                "paginate": {
                    "first": "First",
                    "last": "Last",
                    "next": "Next",
                    "previous": "Previous"
                }
            }
        });
        console.log('DataTable initialized successfully');
    } catch (e) {
        console.error('Error initializing DataTable:', e);
    }

    // When party is selected, load their orders
    $('#party_id').on('change', function() {
        var partyId = $(this).val();
        console.log('Party changed, ID:', partyId);
        
        if (partyId) {
            // Show loading message
            $('#sales_order_id').html('<option value="">Loading orders...</option>');
            
            // Make AJAX call
            $.ajax({
                type: 'POST',
                url: 'ajax/get_party_orders.php',
                data: { party_id: partyId },
                success: function(response) {
                    console.log('Raw Response:', response);
                    
                    try {
                        // Parse response if it's a string
                        var data = typeof response === 'string' ? JSON.parse(response) : response;
                        console.log('Parsed Response:', data);
                        
                        var options = '<option value="">Select Order</option>';
                        if (data.status === 'success' && data.orders && data.orders.length > 0) {
                            data.orders.forEach(function(order) {
                                options += `<option value="${order.id}" data-balance="${order.balance_amount}">
                                            Order #${order.id} (${order.order_date}) - Balance: ₹${order.balance_amount}
                                          </option>`;
                            });
                        } else {
                            options = '<option value="">No pending orders found</option>';
                        }
                        $('#sales_order_id').html(options);
                    } catch (e) {
                        console.error('Error parsing response:', e);
                        $('#sales_order_id').html('<option value="">Error loading orders</option>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    console.error('Status:', status);
                    console.error('Response:', xhr.responseText);
                    $('#sales_order_id').html('<option value="">Error loading orders</option>');
                }
            });
        } else {
            $('#sales_order_id').html('<option value="">Select Order</option>');
        }
    });

    // When order is selected, set max amount
    $('#sales_order_id').change(function() {
        var selected = $(this).find(':selected');
        var balanceAmount = selected.data('balance');
        if (balanceAmount) {
            $('#amount').attr('max', balanceAmount);
            $('#amount').val(balanceAmount);
        }
    });

    // Form submission
    $('#transactionForm').on('submit', function(e) {
        e.preventDefault();
        console.log('Form Data:', $(this).serialize());
        
        $.ajax({
            type: 'POST',
            url: 'ajax/sales_transaction_add.php',
            data: $(this).serialize(),
            success: function(response) {
                console.log('Raw Response:', response);
                
                try {
                    var data = typeof response === 'string' ? JSON.parse(response) : response;
                    if (data.status === 'success') {
                        Swal.fire({
                            title: 'Success!',
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
                            text: data.message || 'Unknown error occurred',
                            icon: 'error',
                            confirmButtonText: 'Ok'
                        });
                    }
                } catch (e) {
                    console.error('Error parsing response:', e);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to process server response',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                console.error('Status:', status);
                console.error('Response:', xhr.responseText);
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to save transaction. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'Ok'
                });
            }
        });
    });
});
</script> 