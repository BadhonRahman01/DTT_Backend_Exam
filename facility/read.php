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
// Read one facility, its location, and its tags
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['facility_id'])) {
        $facility_id = $_GET['facility_id'];

        // Fetch facility details using prepared statement
        $query = "SELECT * FROM Facility WHERE facility_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $facility_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $facility = $result->fetch_assoc();

        // Fetch location details using prepared statement
        $location_id = $facility['location_id'];
        $query = "SELECT * FROM Location WHERE location_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $location_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $location = $result->fetch_assoc();

        // Fetch tags using prepared statement
        $query = "SELECT Tag.name FROM Tag
                  JOIN FacilityTags ON Tag.tag_id = FacilityTags.tag_id
                  WHERE FacilityTags.facility_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $facility_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $tags = [];
        while ($row = $result->fetch_assoc()) {
            $tags[] = $row['name'];
        }

        // Combine results
        $facility['location'] = $location;
        $facility['tags'] = $tags;

        echo json_encode($facility);
    } else {
        // Read multiple facilities, their location, and their tags
        $query = "SELECT * FROM Facility";
        $result = $conn->query($query);
        $facilities = [];
        while ($row = $result->fetch_assoc()) {
            $facility_id = $row['facility_id'];
            $location_id = $row['location_id'];

            // Fetch location details using prepared statement
            $query = "SELECT * FROM Location WHERE location_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $location_id);
            $stmt->execute();
            $result_location = $stmt->get_result();
            $location = $result_location->fetch_assoc();

            // Fetch tags using prepared statement
            $query = "SELECT Tag.name FROM Tag
                      JOIN FacilityTags ON Tag.tag_id = FacilityTags.tag_id
                      WHERE FacilityTags.facility_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $facility_id);
            $stmt->execute();
            $result_tags = $stmt->get_result();
            $tags = [];
            while ($row_tag = $result_tags->fetch_assoc()) {
                $tags[] = $row_tag['name'];
            }

            // Combine results
            $row['location'] = $location;
            $row['tags'] = $tags;

            $facilities[] = $row;
        }

        echo json_encode($facilities);
    }
}

// Close database connection
$conn->close();
?>