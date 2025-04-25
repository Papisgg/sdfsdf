<?php
$servername = "localhost";
$username = "u2950585_detecti";
$password = "Egor200544";
$dbname = "u2950585_user";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$data = json_decode(file_get_contents("php://input"), true);
$comment = $data['comment'];
$amount = $data['amount'];

$sql = "UPDATE transactions SET amount=? WHERE comment=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("di", $amount, $comment);

if ($stmt->execute()) {
    echo json_encode(array("message" => "Record updated successfully."));
} else {
    echo json_encode(array("message" => "Error updating record."));
}

$stmt->close();
$conn->close();
?>
