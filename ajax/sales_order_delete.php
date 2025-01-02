<?php
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'])) {
    $order_id = mysqli_real_escape_string($conn, $_POST['order_id']);
    $confirmed = isset($_POST['confirmed']) ? $_POST['confirmed'] : false;
    
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Check if there are any transactions
        $check_transactions = "SELECT COUNT(*) as count FROM sales_transactions WHERE sales_order_id = '$order_id'";
        $transaction_result = mysqli_query($conn, $check_transactions);
        $transaction_count = mysqli_fetch_assoc($transaction_result)['count'];
        
        if ($transaction_count > 0 && !$confirmed) {
            echo json_encode([
                'status' => 'confirm',
                'message' => "This sales order has $transaction_count transaction(s) associated with it. Are you sure you want to delete the order and all its transactions?",
                'transaction_count' => $transaction_count
            ]);
            exit;
        }
        
        // If confirmed or no transactions, proceed with deletion
        if ($transaction_count > 0) {
            // Delete associated transactions first
            $delete_transactions = "DELETE FROM sales_transactions WHERE sales_order_id = '$order_id'";
            if (!mysqli_query($conn, $delete_transactions)) {
                throw new Exception("Error deleting transactions: " . mysqli_error($conn));
            }
        }
        
        // Delete order items
        $delete_items = "DELETE FROM sales_order_items WHERE sales_order_id = '$order_id'";
        if (!mysqli_query($conn, $delete_items)) {
            throw new Exception("Error deleting order items: " . mysqli_error($conn));
        }
        
        // Delete the order
        $delete_order = "DELETE FROM sales_orders WHERE id = '$order_id'";
        if (!mysqli_query($conn, $delete_order)) {
            throw new Exception("Error deleting order: " . mysqli_error($conn));
        }
        
        mysqli_commit($conn);
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Sales order and all associated data deleted successfully'
        ]);
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request'
    ]);
} 