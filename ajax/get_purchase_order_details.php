<?php
include '../config/database.php';

if (isset($_POST['order_id'])) {
    $order_id = mysqli_real_escape_string($conn, $_POST['order_id']);
    
    // Get order and supplier details
    $order_query = "SELECT po.*, s.* 
                    FROM purchase_orders po
                    LEFT JOIN suppliers s ON po.supplier_id = s.id
                    WHERE po.id = '$order_id'";
    $order_result = mysqli_query($conn, $order_query);
    
    if ($row = mysqli_fetch_assoc($order_result)) {
        $response = [
            'status' => 'success',
            'order' => [
                'id' => $row['id'],
                'order_date' => date('d M Y', strtotime($row['order_date'])),
                'total_amount' => $row['total_amount'],
                'payment_status' => $row['payment_status']
            ],
            'supplier' => [
                'name' => $row['name'],
                'contact_person' => $row['contact_person'],
                'phone' => $row['phone'],
                'email' => $row['email']
            ]
        ];
        
        // Get order items
        $items_query = "SELECT poi.*, p.name as product_name, u.name as unit_name 
                       FROM purchase_order_items poi
                       LEFT JOIN products p ON poi.product_id = p.id
                       LEFT JOIN units u ON poi.unit_id = u.id
                       WHERE poi.purchase_order_id = '$order_id'";
        $items_result = mysqli_query($conn, $items_query);
        
        $items = [];
        while ($item = mysqli_fetch_assoc($items_result)) {
            $items[] = [
                'product_name' => $item['product_name'],
                'quantity' => $item['quantity'],
                'unit_name' => $item['unit_name'],
                'unit_price' => $item['unit_price'],
                'total_price' => $item['total_price']
            ];
        }
        $response['items'] = $items;
        
        // Get payment history
        $payments_query = "SELECT * FROM purchase_receipts 
                          WHERE purchase_order_id = '$order_id'
                          ORDER BY payment_date DESC";
        $payments_result = mysqli_query($conn, $payments_query);
        
        $payments = [];
        while ($payment = mysqli_fetch_assoc($payments_result)) {
            $payments[] = [
                'payment_date' => date('d M Y', strtotime($payment['payment_date'])),
                'amount' => $payment['amount'],
                'payment_mode' => ucfirst($payment['payment_mode']),
                'reference_no' => $payment['reference_no'] ?: '-',
                'notes' => $payment['notes'] ?: '-'
            ];
        }
        $response['payments'] = $payments;
        
    } else {
        $response = [
            'status' => 'error',
            'message' => 'Order not found'
        ];
    }
} else {
    $response = [
        'status' => 'error',
        'message' => 'Order ID not provided'
    ];
}

echo json_encode($response); 