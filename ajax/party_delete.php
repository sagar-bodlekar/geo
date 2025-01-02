<?php
include '../config/database.php';

$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = mysqli_real_escape_string($conn, $_POST['id']);

    // Check if party has any associated sales orders
    $check_query = "SELECT COUNT(*) as count FROM sales_orders WHERE party_id = $id";
    $check_result = mysqli_query($conn, $check_query);
    $check_row = mysqli_fetch_assoc($check_result);

    if ($check_row['count'] > 0) {
        $response['status'] = 'error';
        $response['message'] = 'Cannot delete party. There are sales orders associated with this party.';
    } else {
        $query = "DELETE FROM parties WHERE id = $id";
        if (mysqli_query($conn, $query)) {
            $response['status'] = 'success';
            $response['message'] = 'Party deleted successfully';
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