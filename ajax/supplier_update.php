<?php
include '../config/database.php';

$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $contact_person = mysqli_real_escape_string($conn, $_POST['contact_person']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    $query = "UPDATE suppliers SET 
              name = '$name',
              contact_person = '$contact_person',
              phone = '$phone',
              email = '$email',
              address = '$address'
              WHERE id = $id";

    if (mysqli_query($conn, $query)) {
        $response['status'] = 'success';
        $response['message'] = 'Supplier updated successfully';
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Error: ' . mysqli_error($conn);
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request method';
}

echo json_encode($response); 