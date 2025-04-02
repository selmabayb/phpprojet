<?php
header("Content-Type: application/json");
include 'database.php';

$sql = "SELECT * FROM films";
$result = $conn->query($sql);

$films = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $films[] = $row;
    }
}

echo json_encode($films);
?>
