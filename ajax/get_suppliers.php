<?php
include '../config/database.php';

$search = isset($_GET['q']) ? $_GET['q'] : '';

$query = "SELECT id, name as text 
          FROM suppliers 
          WHERE name LIKE '%$search%' 
          ORDER BY name ASC 
          LIMIT 10";

$result = mysqli_query($conn, $query);
$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode($data); 