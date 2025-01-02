<?php
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $amount = mysqli_real_escape_string($conn, $_POST['amount']);
    $expense_date = mysqli_real_escape_string($conn, $_POST['expense_date']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    $query = "INSERT INTO expenses (category, amount, expense_date, description) 
              VALUES ('$category', '$amount', '$expense_date', '$description')";

    if (mysqli_query($conn, $query)) {
        $response = [
            'status' => 'success',
            'message' => 'Expense added successfully'
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