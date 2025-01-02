<?php
include '../config/database.php';

$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $contact_person = mysqli_real_escape_string($conn, $_POST['contact_person']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    $query = "INSERT INTO parties (name, contact_person, phone, email, address) 
              VALUES ('$name', '$contact_person', '$phone', '$email', '$address')";

    if (mysqli_query($conn, $query)) {
        $response['status'] = 'success';
        $response['message'] = 'Party added successfully';
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Error: ' . mysqli_error($conn);
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request method';
}

echo json_encode($response); 