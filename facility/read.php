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

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $search = $_GET['search'];

    // Pagination parameters
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $pageSize = 5; // Number of results per page

    // Calculate offset for traditional pagination
    $offset = ($page - 1) * $pageSize;

    // Search facilities by facility name, tag name, or location city with pagination
    $query = "SELECT Facility.*, Location.*, GROUP_CONCAT(DISTINCT Tag.name) AS tag_names
              FROM Facility
              LEFT JOIN Location ON Facility.location_id = Location.location_id
              LEFT JOIN FacilityTags ON Facility.facility_id = FacilityTags.facility_id
              LEFT JOIN Tag ON FacilityTags.tag_id = Tag.tag_id
              WHERE Facility.name LIKE '%$search%'
                 OR Tag.name LIKE '%$search%'
                 OR Location.city LIKE '%$search%'
              GROUP BY Facility.facility_id
              LIMIT $offset, $pageSize";

    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $facilities = [];
        while ($row = $result->fetch_assoc()) {
            // Combine results
            $location = [
                'city' => $row['city'],
                'address' => $row['address'],
                'zip_code' => $row['zip_code'],
                'country_code' => $row['country_code'],
                'phone_number' => $row['phone_number']
            ];

            $tags = !empty($row['tag_names']) ? explode(',', $row['tag_names']) : [];

            $row['location'] = $location;
            $row['tags'] = $tags;

            $facilities[] = $row;
        }

        // Calculate the next cursor for cursor pagination
        $nextCursor = null;
        if ($result->num_rows === $pageSize) {
            $lastFacility = end($facilities);
            $nextCursor = base64_encode(json_encode(['facility_id' => $lastFacility['facility_id']]));
        }

        // Response data
        $response = [
            'facilities' => $facilities,
            'next_cursor' => $nextCursor
        ];

        echo json_encode($response);
    } else {
        // Handle error: No matching facilities found
        echo json_encode(["error" => "No matching facilities found"]);
    }
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