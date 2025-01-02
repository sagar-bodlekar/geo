<?php
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $party_id = mysqli_real_escape_string($conn, $_POST['party_id']);
    $sales_order_id = mysqli_real_escape_string($conn, $_POST['sales_order_id']);
    $amount = mysqli_real_escape_string($conn, $_POST['amount']);
    $payment_date = mysqli_real_escape_string($conn, $_POST['payment_date']);
    $payment_mode = mysqli_real_escape_string($conn, $_POST['payment_mode']);
    $reference_no = mysqli_real_escape_string($conn, $_POST['reference_no']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);

    // Start transaction
    mysqli_begin_transaction($conn);
    try {
        // Insert transaction
        $insert_query = "INSERT INTO sales_transactions (party_id, sales_order_id, amount, payment_date, payment_mode, reference_no, notes) 
                        VALUES ('$party_id', '$sales_order_id', '$amount', '$payment_date', '$payment_mode', '$reference_no', '$notes')";
        mysqli_query($conn, $insert_query);

        // Get total amount and paid amount
        $order_query = "SELECT total_amount FROM sales_orders WHERE id = '$sales_order_id'";
        $order_result = mysqli_query($conn, $order_query);
        $order = mysqli_fetch_assoc($order_result);
        $total_amount = $order['total_amount'];

        $paid_query = "SELECT SUM(amount) as paid_amount FROM sales_transactions WHERE sales_order_id = '$sales_order_id'";
        $paid_result = mysqli_query($conn, $paid_query);
        $paid = mysqli_fetch_assoc($paid_result);
        $paid_amount = $paid['paid_amount'];

        // Update payment status
        if ($paid_amount >= $total_amount) {
            $payment_status = 'completed';
        } elseif ($paid_amount > 0) {
            $payment_status = 'partial';
        } else {
            $payment_status = 'pending';
        }

        $update_query = "UPDATE sales_orders SET 
                        payment_status = '$payment_status'
                        WHERE id = '$sales_order_id'";
        mysqli_query($conn, $update_query);

        mysqli_commit($conn);
        
        $response = [
            'status' => 'success',
            'message' => 'Transaction added successfully',
            'payment_status' => $payment_status
        ];
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $response = [
            'status' => 'error',
            'message' => 'Error adding transaction: ' . $e->getMessage()
        ];
    }
} else {
    $response = [
        'status' => 'error',
        'message' => 'Invalid request method'
    ];
}

header('Content-Type: application/json');
echo json_encode($response);
?> 