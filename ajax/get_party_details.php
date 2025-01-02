<?php
include '../config/database.php';

if (isset($_POST['party_id'])) {
    $party_id = $_POST['party_id'];
    
    // Get party details
    $party_query = "SELECT * FROM parties WHERE id = $party_id";
    $party_result = mysqli_query($conn, $party_query);
    $party = mysqli_fetch_assoc($party_result);
    
    // Get total sales
    $total_sales_query = "SELECT COALESCE(SUM(total_amount), 0) as total FROM sales_orders WHERE party_id = $party_id";
    $total_sales_result = mysqli_query($conn, $total_sales_query);
    $total_sales = mysqli_fetch_assoc($total_sales_result)['total'];
    
    // Get total paid amount
    $total_paid_query = "SELECT COALESCE(SUM(amount), 0) as total FROM sales_transactions WHERE party_id = $party_id";
    $total_paid_result = mysqli_query($conn, $total_paid_query);
    $total_paid = mysqli_fetch_assoc($total_paid_result)['total'];
    
    // Calculate outstanding amount
    $outstanding_amount = $total_sales - $total_paid;
    
    // Get recent sales
    $recent_sales_query = "SELECT id, order_date, total_amount, payment_status 
                          FROM sales_orders 
                          WHERE party_id = $party_id 
                          ORDER BY order_date DESC LIMIT 5";
    $recent_sales_result = mysqli_query($conn, $recent_sales_query);
    $recent_sales = [];
    while ($sale = mysqli_fetch_assoc($recent_sales_result)) {
        $recent_sales[] = $sale;
    }
    
    // Get recent transactions
    $recent_transactions_query = "SELECT id, payment_date, amount, payment_mode, reference_no 
                                FROM sales_transactions 
                                WHERE party_id = $party_id 
                                ORDER BY payment_date DESC LIMIT 5";
    $recent_transactions_result = mysqli_query($conn, $recent_transactions_query);
    $recent_transactions = [];
    while ($transaction = mysqli_fetch_assoc($recent_transactions_result)) {
        $recent_transactions[] = $transaction;
    }
    
    $response = [
        'status' => 'success',
        'party' => $party,
        'total_sales' => $total_sales,
        'outstanding_amount' => $outstanding_amount,
        'recent_sales' => $recent_sales,
        'recent_transactions' => $recent_transactions
    ];
} else {
    $response = [
        'status' => 'error',
        'message' => 'Party ID not provided'
    ];
}

echo json_encode($response);
?> 