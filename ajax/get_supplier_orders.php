<?php
include '../config/database.php';

if (isset($_POST['supplier_id'])) {
    $supplier_id = mysqli_real_escape_string($conn, $_POST['supplier_id']);
    
    // Get pending orders with balance amount
    $orders_query = "SELECT po.id, po.order_date, po.total_amount,
                    (po.total_amount - COALESCE(
                        (SELECT SUM(amount) FROM purchase_receipts WHERE purchase_order_id = po.id), 
                        0
                    )) as balance_amount
                    FROM purchase_orders po
                    WHERE po.supplier_id = '$supplier_id'
                    AND (po.payment_status = 'pending' OR po.payment_status = 'partial')
                    ORDER BY po.order_date DESC";
    
    $orders_result = mysqli_query($conn, $orders_query);
    $orders = [];
    
    if ($orders_result) {
        while ($order = mysqli_fetch_assoc($orders_result)) {
            if ($order['balance_amount'] > 0) {
                $orders[] = [
                    'id' => $order['id'],
                    'order_date' => date('d M Y', strtotime($order['order_date'])),
                    'total_amount' => $order['total_amount'],
                    'balance_amount' => $order['balance_amount']
                ];
            }
        }
        
        $response = [
            'status' => 'success',
            'orders' => $orders
        ];
    } else {
        $response = [
            'status' => 'error',
            'message' => 'Error fetching orders: ' . mysqli_error($conn)
        ];
    }
} else {
    $response = [
        'status' => 'error',
        'message' => 'Supplier ID not provided'
    ];
}

echo json_encode($response);
?> 