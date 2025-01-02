<?php
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = mysqli_real_escape_string($conn, $_POST['id']);

    $query = "DELETE FROM expenses WHERE id = '$id'";
    
    if (mysqli_query($conn, $query)) {
        $response = [
            'status' => 'success',
            'message' => 'Expense deleted successfully'
        ];
    } else {
        $response = [
            'status' => 'error',
            'message' => 'Error: ' . mysqli_error($conn)
        ];
    }
} else {
    $response = [
        'status' => 'error',
        'message' => 'Invalid request method'
    ];
}

echo json_encode($response); 