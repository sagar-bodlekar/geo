<?php
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'])) {
    $order_id = mysqli_real_escape_string($conn, $_POST['order_id']);
    $confirmed = isset($_POST['confirmed']) ? $_POST['confirmed'] : false;
    
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Check if there are any receipts
        $check_receipts = "SELECT COUNT(*) as count FROM purchase_receipts WHERE purchase_order_id = '$order_id'";
        $receipt_result = mysqli_query($conn, $check_receipts);
        $receipt_count = mysqli_fetch_assoc($receipt_result)['count'];
        
        if ($receipt_count > 0 && !$confirmed) {
            echo json_encode([
                'status' => 'confirm',
                'message' => "This purchase order has $receipt_count receipt(s) associated with it. Are you sure you want to delete the order and all its receipts?",
                'receipt_count' => $receipt_count
            ]);
            exit;
        }
        
        // If confirmed or no receipts, proceed with deletion
        if ($receipt_count > 0) {
            // Delete associated receipts first
            $delete_receipts = "DELETE FROM purchase_receipts WHERE purchase_order_id = '$order_id'";
            if (!mysqli_query($conn, $delete_receipts)) {
                throw new Exception("Error deleting receipts: " . mysqli_error($conn));
            }
        }
        
        // Delete order items
        $delete_items = "DELETE FROM purchase_order_items WHERE purchase_order_id = '$order_id'";
        if (!mysqli_query($conn, $delete_items)) {
            throw new Exception("Error deleting order items: " . mysqli_error($conn));
        }
        
        // Delete the order
        $delete_order = "DELETE FROM purchase_orders WHERE id = '$order_id'";
        if (!mysqli_query($conn, $delete_order)) {
            throw new Exception("Error deleting order: " . mysqli_error($conn));
        }
        
        mysqli_commit($conn);
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Purchase order and all associated data deleted successfully'
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