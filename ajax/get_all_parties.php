<?php
include '../config/database.php';

$query = "SELECT id, name as text 
          FROM parties 
          ORDER BY name ASC";

$result = mysqli_query($conn, $query);
$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode($data); 