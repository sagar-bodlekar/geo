<?php
include '../config/database.php';

$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Insert sales order
        $party_id = mysqli_real_escape_string($conn, $_POST['party_id']);
        $order_date = mysqli_real_escape_string($conn, $_POST['order_date']);
        $total_amount = mysqli_real_escape_string($conn, $_POST['total_amount']);
        
        $query = "INSERT INTO sales_orders (party_id, order_date, total_amount, status, payment_status) 
                  VALUES ('$party_id', '$order_date', '$total_amount', 'pending', 'pending')";
        
        if (!mysqli_query($conn, $query)) {
            throw new Exception("Error creating sales order: " . mysqli_error($conn));
        }
        
        $sales_order_id = mysqli_insert_id($conn);
        
        // Insert sales order items
        foreach ($_POST['items'] as $item) {
            $product_id = mysqli_real_escape_string($conn, $item['product_id']);
            $quantity = mysqli_real_escape_string($conn, $item['quantity']);
            $unit_id = mysqli_real_escape_string($conn, $item['unit_id']);
            $unit_price = mysqli_real_escape_string($conn, $item['unit_price']);
            $total_price = mysqli_real_escape_string($conn, $item['total_price']);
            
            $query = "INSERT INTO sales_order_items (sales_order_id, product_id, quantity, unit_id, unit_price, total_price) 
                      VALUES ('$sales_order_id', '$product_id', '$quantity', '$unit_id', '$unit_price', '$total_price')";
            
            if (!mysqli_query($conn, $query)) {
                throw new Exception("Error adding sales order item: " . mysqli_error($conn));
            }
        }
        
        // Commit transaction
        mysqli_commit($conn);
        
        $response['status'] = 'success';
        $response['message'] = 'Sales order created successfully';
        $response['sales_order_id'] = $sales_order_id;
        $response['redirect'] = 'sales.php';
        
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