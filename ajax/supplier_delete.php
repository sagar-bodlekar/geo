<?php
include '../config/database.php';

$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = mysqli_real_escape_string($conn, $_POST['id']);

    // Check if supplier has any associated products
    $check_query = "SELECT COUNT(*) as count FROM purchase_orders WHERE supplier_id = $id";
    $check_result = mysqli_query($conn, $check_query);
    $check_row = mysqli_fetch_assoc($check_result);

    if ($check_row['count'] > 0) {
        $response['status'] = 'error';
        $response['message'] = 'Cannot delete supplier. There are products associated with this supplier.';
    } else {
        $query = "DELETE FROM suppliers WHERE id = $id";
        if (mysqli_query($conn, $query)) {
            $response['status'] = 'success';
            $response['message'] = 'Supplier deleted successfully';
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