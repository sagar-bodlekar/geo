<?php
include '../config/database.php';

if (isset($_POST['party_id'])) {
    $party_id = mysqli_real_escape_string($conn, $_POST['party_id']);
    
    $query = "SELECT so.id, so.order_date, 
             (so.total_amount - COALESCE((SELECT SUM(amount) FROM sales_transactions WHERE sales_order_id = so.id), 0)) as balance_amount
             FROM sales_orders so
             WHERE so.party_id = '$party_id' 
             AND so.payment_status != 'paid'
             ORDER BY so.order_date DESC";
             
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        $orders = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $orders[] = [
                'id' => $row['id'],
                'order_date' => date('d M Y', strtotime($row['order_date'])),
                'balance_amount' => number_format($row['balance_amount'], 2)
            ];
        }
        echo json_encode(['status' => 'success', 'orders' => $orders]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to fetch orders']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Party ID not provided']);
}
?> 