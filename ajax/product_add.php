<?php
include '../config/database.php';

$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $sku = mysqli_real_escape_string($conn, $_POST['sku']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $unit_id = mysqli_real_escape_string($conn, $_POST['unit_id']);
    $purchase_price = mysqli_real_escape_string($conn, $_POST['purchase_price']);
    $selling_price = mysqli_real_escape_string($conn, $_POST['selling_price']);

    // Check if SKU already exists
    $check_query = "SELECT COUNT(*) as count FROM products WHERE sku = '$sku'";
    $check_result = mysqli_query($conn, $check_query);
    $check_row = mysqli_fetch_assoc($check_result);

    if ($check_row['count'] > 0) {
        $response['status'] = 'error';
        $response['message'] = 'SKU already exists';
    } else {
        $query = "INSERT INTO products (name, sku, category, unit_id, purchase_price, selling_price) 
                  VALUES ('$name', '$sku', '$category', '$unit_id', '$purchase_price', '$selling_price')";

        if (mysqli_query($conn, $query)) {
            $response['status'] = 'success';
            $response['message'] = 'Product added successfully';
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