<?php
// Database connection
$host = 'localhost';
$db = 'dtt_exam';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Delete a facility and its tags
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents("php://input"), true);
    $facility_id = $data['facility_id'];

    // Delete from FacilityTags junction table first
    $query = "DELETE FROM FacilityTags WHERE facility_id = $facility_id";
    $conn->query($query);

    // Delete from Facility table
    $query = "DELETE FROM Facility WHERE facility_id = $facility_id";
    $conn->query($query);

    echo json_encode(['message' => 'Facility deleted successfully']);
}


// Close database connection
$conn->close();
?>