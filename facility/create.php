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

// Create a facility and its tags
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $name = $data['name'];
    $creation_date = $data['creation_date'];
    $location_id = $data['location_id'];
    $tags = $data['tags'];

    // Insert into Facility table
    $query = "INSERT INTO Facility (name, creation_date, location_id) VALUES ('$name', '$creation_date', $location_id)";
    $conn->query($query);
    $facility_id = $conn->insert_id;

    // Insert into FacilityTags junction table
    foreach ($tags as $tag_id) {
        $query = "INSERT INTO FacilityTags (facility_id, tag_id) VALUES ($facility_id, $tag_id)";
        $conn->query($query);
    }

    echo json_encode(['message' => 'Facility created successfully']);
}




// Close database connection
$conn->close();
?>
