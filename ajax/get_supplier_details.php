<?php
include '../config/database.php';

if (isset($_POST['supplier_id'])) {
    $supplier_id = $_POST['supplier_id'];
    
    // Get supplier details
    $supplier_query = "SELECT * FROM suppliers WHERE id = $supplier_id";
    $supplier_result = mysqli_query($conn, $supplier_query);
    $supplier = mysqli_fetch_assoc($supplier_result);
    
    // Get total purchases
    $total_purchases_query = "SELECT COALESCE(SUM(total_amount), 0) as total FROM purchase_orders WHERE supplier_id = $supplier_id";
    $total_purchases_result = mysqli_query($conn, $total_purchases_query);
    $total_purchases = mysqli_fetch_assoc($total_purchases_result)['total'];
    
    // Get total paid amount
    $total_paid_query = "SELECT COALESCE(SUM(amount), 0) as total FROM purchase_receipts WHERE supplier_id = $supplier_id";
    $total_paid_result = mysqli_query($conn, $total_paid_query);
    $total_paid = mysqli_fetch_assoc($total_paid_result)['total'];
    
    // Calculate outstanding amount
    $outstanding_amount = $total_purchases - $total_paid;
    
    // Get recent purchases
    $recent_purchases_query = "SELECT id, order_date, total_amount, payment_status 
                             FROM purchase_orders 
                             WHERE supplier_id = $supplier_id 
                             ORDER BY order_date DESC LIMIT 5";
    $recent_purchases_result = mysqli_query($conn, $recent_purchases_query);
    $recent_purchases = [];
    while ($purchase = mysqli_fetch_assoc($recent_purchases_result)) {
        $recent_purchases[] = $purchase;
    }
    
    // Get recent receipts
    $recent_receipts_query = "SELECT id, payment_date, amount, payment_mode, reference_no 
                             FROM purchase_receipts 
                             WHERE supplier_id = $supplier_id 
                             ORDER BY payment_date DESC LIMIT 5";
    $recent_receipts_result = mysqli_query($conn, $recent_receipts_query);
    $recent_receipts = [];
    while ($receipt = mysqli_fetch_assoc($recent_receipts_result)) {
        $recent_receipts[] = $receipt;
    }
    
    $response = [
        'status' => 'success',
        'supplier' => $supplier,
        'total_purchases' => $total_purchases,
        'outstanding_amount' => $outstanding_amount,
        'recent_purchases' => $recent_purchases,
        'recent_receipts' => $recent_receipts
    ];
} else {
    $response = [
        'status' => 'error',
        'message' => 'Supplier ID not provided'
    ];
}

echo json_encode($response);
?> 