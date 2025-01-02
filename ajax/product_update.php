<?php
include '../config/database.php';

$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $sku = mysqli_real_escape_string($conn, $_POST['sku']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $unit_id = mysqli_real_escape_string($conn, $_POST['unit_id']);
    $purchase_price = mysqli_real_escape_string($conn, $_POST['purchase_price']);
    $selling_price = mysqli_real_escape_string($conn, $_POST['selling_price']);

    // Check if SKU already exists for other products
    $check_query = "SELECT COUNT(*) as count FROM products WHERE sku = '$sku' AND id != '$id'";
    $check_result = mysqli_query($conn, $check_query);
    $check_row = mysqli_fetch_assoc($check_result);

    if ($check_row['count'] > 0) {
        $response['status'] = 'error';
        $response['message'] = 'SKU already exists';
    } else {
        $query = "UPDATE products SET 
                  name = '$name',
                  sku = '$sku',
                  category = '$category',
                  unit_id = '$unit_id',
                  purchase_price = '$purchase_price',
                  selling_price = '$selling_price'
                  WHERE id = '$id'";

        if (mysqli_query($conn, $query)) {
            $response['status'] = 'success';
            $response['message'] = 'Product updated successfully';
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