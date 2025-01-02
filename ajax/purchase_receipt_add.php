<?php
include '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $supplier_id = $_POST['supplier_id'] ?? '';
    $purchase_order_id = $_POST['purchase_order_id'] ?? '';
    $amount = $_POST['amount'] ?? 0;
    $payment_date = $_POST['payment_date'] ?? '';
    $payment_mode = $_POST['payment_mode'] ?? '';
    $reference_no = $_POST['reference_no'] ?? '';
    $notes = $_POST['notes'] ?? '';

    // Validate required fields
    if (empty($supplier_id) || empty($purchase_order_id) || empty($amount) || empty($payment_date) || empty($payment_mode)) {
        echo json_encode(['status' => 'error', 'message' => 'All required fields must be filled']);
        exit;
    }

    // Start transaction
    mysqli_begin_transaction($conn);

    try {
        // Get purchase order details
        $order_query = "SELECT total_amount, 
                              COALESCE((SELECT SUM(amount) FROM purchase_receipts WHERE purchase_order_id = ?) , 0) as paid_amount 
                       FROM purchase_orders WHERE id = ?";
        $stmt = mysqli_prepare($conn, $order_query);
        mysqli_stmt_bind_param($stmt, "ii", $purchase_order_id, $purchase_order_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $order = mysqli_fetch_assoc($result);

        if (!$order) {
            throw new Exception("Purchase order not found");
        }

        $total_amount = $order['total_amount'];
        $current_paid = $order['paid_amount'];
        $new_paid_amount = $current_paid + $amount;

        // Validate if new amount doesn't exceed total amount
        if ($new_paid_amount > $total_amount) {
            throw new Exception("Receipt amount exceeds remaining balance");
        }

        // Insert receipt
        $insert_query = "INSERT INTO purchase_receipts (supplier_id, purchase_order_id, amount, payment_date, payment_mode, reference_no, notes) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insert_query);
        mysqli_stmt_bind_param($stmt, "iidssss", $supplier_id, $purchase_order_id, $amount, $payment_date, $payment_mode, $reference_no, $notes);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Failed to add receipt");
        }

        // Update purchase order payment status
        $payment_status = 'pending';
        if ($new_paid_amount >= $total_amount) {
            $payment_status = 'completed';
        } elseif ($new_paid_amount > 0) {
            $payment_status = 'partial';
        }

        $update_query = "UPDATE purchase_orders SET payment_status = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt, "si", $payment_status, $purchase_order_id);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Failed to update order status");
        }

        // Commit transaction
        mysqli_commit($conn);
        
        echo json_encode([
            'status' => 'success', 
            'message' => 'Receipt added successfully',
            'payment_status' => $payment_status,
            'paid_amount' => $new_paid_amount,
            'balance_amount' => $total_amount - $new_paid_amount
        ]);

    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?> 