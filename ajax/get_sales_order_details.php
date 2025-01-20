<?php
include '../config/database.php';

if (isset($_POST['order_id'])) {
    $order_id = mysqli_real_escape_string($conn, $_POST['order_id']);
    
    // Get order and party details
    $order_query = "SELECT so.*, p.* 
                    FROM sales_orders so
                    LEFT JOIN parties p ON so.party_id = p.id
                    WHERE so.id = '$order_id'";
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
            'party' => [
                'name' => $row['name'],
                'contact_person' => $row['contact_person'],
                'phone' => $row['phone'],
                'email' => $row['email'],
                'address' => $row['address']
            ]
        ];
        
        // Get order items
        $items_query = "SELECT soi.*, p.name as product_name, p.sku as sku, u.name as unit_name 
                       FROM sales_order_items soi
                       LEFT JOIN products p ON soi.product_id = p.id
                       LEFT JOIN units u ON soi.unit_id = u.id
                       WHERE soi.sales_order_id = '$order_id'";
        $items_result = mysqli_query($conn, $items_query);
        
        $items = [];
        while ($item = mysqli_fetch_assoc($items_result)) {
            $items[] = [
                'product_name' => $item['product_name'],
                'sku' => $item['sku'],
                'quantity' => $item['quantity'],
                'unit_name' => $item['unit_name'],
                'unit_price' => $item['unit_price'],
                'total_price' => $item['total_price']
            ];
        }
        $response['items'] = $items;
        
        // Get payment history
        $payments_query = "SELECT * FROM sales_transactions 
                          WHERE sales_order_id = '$order_id'
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