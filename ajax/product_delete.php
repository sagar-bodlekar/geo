<?php
include '../config/database.php';

$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = mysqli_real_escape_string($conn, $_POST['id']);

    // Check if product has any associated sales or purchase orders
    $check_sales_query = "SELECT COUNT(*) as count FROM sales_order_items WHERE product_id = $id";
    $check_sales_result = mysqli_query($conn, $check_sales_query);
    $check_sales_row = mysqli_fetch_assoc($check_sales_result);

    $check_purchase_query = "SELECT COUNT(*) as count FROM purchase_order_items WHERE product_id = $id";
    $check_purchase_result = mysqli_query($conn, $check_purchase_query);
    $check_purchase_row = mysqli_fetch_assoc($check_purchase_result);

    if ($check_sales_row['count'] > 0 || $check_purchase_row['count'] > 0) {
        $response['status'] = 'error';
        $response['message'] = 'Cannot delete product. There are sales or purchase orders associated with this product.';
    } else {
        $query = "DELETE FROM products WHERE id = $id";
        if (mysqli_query($conn, $query)) {
            $response['status'] = 'success';
            $response['message'] = 'Product deleted successfully';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Error: ' . mysqli_error($conn);
        }
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request method';
}

echo json_encode($response); 