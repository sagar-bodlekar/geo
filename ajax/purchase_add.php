<?php
include '../config/database.php';

$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Insert purchase order
        $supplier_id = mysqli_real_escape_string($conn, $_POST['supplier_id']);
        $order_date = mysqli_real_escape_string($conn, $_POST['order_date']);
        $total_amount = mysqli_real_escape_string($conn, $_POST['total_amount']);
        
        $query = "INSERT INTO purchase_orders (supplier_id, order_date, total_amount, status, payment_status) 
                  VALUES ('$supplier_id', '$order_date', '$total_amount', 'pending', 'pending')";
        
        if (!mysqli_query($conn, $query)) {
            throw new Exception("Error creating purchase order: " . mysqli_error($conn));
        }
        
        $purchase_order_id = mysqli_insert_id($conn);
        
        // Insert purchase order items
        foreach ($_POST['items'] as $item) {
            $product_id = mysqli_real_escape_string($conn, $item['product_id']);
            $quantity = mysqli_real_escape_string($conn, $item['quantity']);
            $unit_id = mysqli_real_escape_string($conn, $item['unit_id']);
            $unit_price = mysqli_real_escape_string($conn, $item['unit_price']);
            $total_price = mysqli_real_escape_string($conn, $item['total_price']);
            
            $query = "INSERT INTO purchase_order_items (purchase_order_id, product_id, quantity, unit_id, unit_price, total_price) 
                      VALUES ('$purchase_order_id', '$product_id', '$quantity', '$unit_id', '$unit_price', '$total_price')";
            
            if (!mysqli_query($conn, $query)) {
                throw new Exception("Error adding purchase order item: " . mysqli_error($conn));
            }
        }
        
        // Commit transaction
        mysqli_commit($conn);
        
        $response['status'] = 'success';
        $response['message'] = 'Purchase order created successfully';
        $response['purchase_order_id'] = $purchase_order_id;
        $response['redirect'] = 'purchase.php';
        
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($conn);
        
        $response['status'] = 'error';
        $response['message'] = $e->getMessage();
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request method';
}

echo json_encode($response); 