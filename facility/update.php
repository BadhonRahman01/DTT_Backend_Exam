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

// Update a facility and its tags
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);

    $facility_id = $data['facility_id'];
    $name = $data['name'];
    $creation_date = $data['creation_date'];
    $location_id = $data['location_id'];
    $tags = $data['tags'];

    // Update Facility table
    $query = "UPDATE Facility SET name = '$name', creation_date = '$creation_date', location_id = $location_id WHERE facility_id = $facility_id";
    $conn->query($query);

    // Delete existing tags for the facility
    $query = "DELETE FROM FacilityTags WHERE facility_id = $facility_id";
    $conn->query($query);

    // Insert new tags for the facility
    foreach ($tags as $tag_id) {
        $query = "INSERT INTO FacilityTags (facility_id, tag_id) VALUES ($facility_id, $tag_id)";
        $conn->query($query);
    }

    echo json_encode(['message' => 'Facility updated successfully']);
}


// Close database connection
$conn->close();
?>