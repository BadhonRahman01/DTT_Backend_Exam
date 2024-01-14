<?php

namespace App\Controllers;

use PDO;


class FacilityController
{
    private $pdo; // PDO object to handle database connection

    // Constructor to initialize the database connection
    public function __construct($host, $dbname, $username, $password) {
        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);

            $name = $data['name'];
            $creation_date = $data['creation_date'];
            $location_id = $data['location_id'];
            $tags = $data['tags'];

            // Insert into Facility table
            $query = "INSERT INTO Facility (name, creation_date, location_id) VALUES (:name, :creation_date, :location_id)";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':creation_date', $creation_date, PDO::PARAM_STR);
            $stmt->bindParam(':location_id', $location_id, PDO::PARAM_INT);
            $stmt->execute();
            $facility_id = $this->pdo->lastInsertId();

            // Insert into FacilityTags junction table
            foreach ($tags as $tag_id) {
                $query = "INSERT INTO FacilityTags (facility_id, tag_id) VALUES (:facility_id, :tag_id)";
                $stmt = $this->pdo->prepare($query);
                $stmt->bindParam(':facility_id', $facility_id, PDO::PARAM_INT);
                $stmt->bindParam(':tag_id', $tag_id, PDO::PARAM_INT);
                $stmt->execute();
            }

            echo json_encode(['message' => 'Facility created successfully']);
        } else {
            // Handle error: Invalid request method
            echo json_encode(['error' => 'Invalid request method']);
        }
    }
    

    public function readOne($id)
    {
                // Fetch facility details using prepared statement
                $query = "SELECT * FROM Facility WHERE facility_id = ?";
                $stmt = $this->pdo->prepare($query);
                $stmt->bindParam(1, $id, PDO::PARAM_INT);
                $stmt->execute();
                $facility = $stmt->fetch(PDO::FETCH_ASSOC);
        
                if (!$facility) {
                    // Handle error: Facility not found
                    echo json_encode(["error" => "Facility not found"]);
                    return;
                }
        
                // Fetch location details using prepared statement
                $query = "SELECT * FROM Location WHERE location_id = ?";
                $stmt = $this->pdo->prepare($query);
                $stmt->bindParam(1, $facility['location_id'], PDO::PARAM_INT);
                $stmt->execute();
                $location = $stmt->fetch(PDO::FETCH_ASSOC);
        
                // Fetch tags using prepared statement
                $query = "SELECT Tag.name FROM Tag
                          JOIN FacilityTags ON Tag.tag_id = FacilityTags.tag_id
                          WHERE FacilityTags.facility_id = ?";
                $stmt = $this->pdo->prepare($query);
                $stmt->bindParam(1, $id, PDO::PARAM_INT);
                $stmt->execute();
                $tags = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
                // Combine results
                $facility['location'] = $location;
                $facility['tags'] = $tags;
        
                echo json_encode($facility);
    }

    public function readAll()
    {
        $query = "SELECT * FROM Facility";
        $stmt = $this->pdo->query($query);
        $facilities = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $facility_id = $row['facility_id'];
            $location_id = $row['location_id'];

            // Fetch location details using prepared statement
            $query = "SELECT * FROM Location WHERE location_id = ?";
            $stmt_location = $this->pdo->prepare($query);
            $stmt_location->bindParam(1, $location_id, PDO::PARAM_INT);
            $stmt_location->execute();
            $location = $stmt_location->fetch(PDO::FETCH_ASSOC);

            // Fetch tags using prepared statement
            $query = "SELECT Tag.name FROM Tag
                      JOIN FacilityTags ON Tag.tag_id = FacilityTags.tag_id
                      WHERE FacilityTags.facility_id = ?";
            $stmt_tags = $this->pdo->prepare($query);
            $stmt_tags->bindParam(1, $facility_id, PDO::PARAM_INT);
            $stmt_tags->execute();
            $tags = $stmt_tags->fetchAll(PDO::FETCH_COLUMN);

            // Combine results
            $row['location'] = $location;
            $row['tags'] = $tags;

            $facilities[] = $row;
        }

        echo json_encode($facilities);
    }


    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            // Sanitize and validate the input
            $facility_id = filter_var($id, FILTER_VALIDATE_INT);
    
            if ($facility_id !== false) {
                // Get JSON data from the request body
                $data = json_decode(file_get_contents("php://input"), true);
    
                // Build the SQL query and parameter bindings dynamically
                $updateFields = [];
                $bindParams = [':facility_id' => $facility_id];
    
                if (isset($data['name'])) {
                    $updateFields[] = 'name = :name';
                    $bindParams[':name'] = $data['name'];
                }
    
                if (isset($data['creation_date'])) {
                    $updateFields[] = 'creation_date = :creation_date';
                    $bindParams[':creation_date'] = $data['creation_date'];
                }
    
                if (isset($data['location_id'])) {
                    $updateFields[] = 'location_id = :location_id';
                    $bindParams[':location_id'] = $data['location_id'];
                }
    
                // Update Facility table
                if (!empty($updateFields)) {
                    $updateFieldsStr = implode(', ', $updateFields);
                    $queryFacility = "UPDATE Facility SET $updateFieldsStr WHERE facility_id = :facility_id";
                    $stmtFacility = $this->pdo->prepare($queryFacility);
    
                    foreach ($bindParams as $param => &$value) {
                        $stmtFacility->bindParam($param, $value);
                    }
    
                    $stmtFacility->execute();
                }
    
                // Update tags for the facility
                if (isset($data['tags']) && is_array($data['tags'])) {
                    // Delete existing tags for the facility
                    $queryDeleteTags = "DELETE FROM FacilityTags WHERE facility_id = :facility_id";
                    $stmtDeleteTags = $this->pdo->prepare($queryDeleteTags);
                    $stmtDeleteTags->bindParam(':facility_id', $facility_id, PDO::PARAM_INT);
                    $stmtDeleteTags->execute();
    
                    // Insert new tags for the facility
                    $queryInsertTags = "INSERT INTO FacilityTags (facility_id, tag_id) VALUES (:facility_id, :tag_id)";
                    $stmtInsertTags = $this->pdo->prepare($queryInsertTags);
                    $stmtInsertTags->bindParam(':facility_id', $facility_id, PDO::PARAM_INT);
                    $stmtInsertTags->bindParam(':tag_id', $tag_id, PDO::PARAM_INT);
    
                    foreach ($data['tags'] as $tag_id) {
                        $stmtInsertTags->execute();
                    }
                }
    
                echo json_encode(['message' => 'Facility updated successfully']);
            } else {
                // Handle error: Invalid facility ID
                echo json_encode(['error' => 'Invalid facility ID']);
            }
        } else {
            // Handle error: Invalid request method
            echo json_encode(['error' => 'Invalid request method']);
        }
    }

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            // Sanitize and validate the input
            $facility_id = filter_var($id, FILTER_VALIDATE_INT);
    
            if ($facility_id !== false) {
                // Delete from FacilityTags junction table first
                $queryTags = "DELETE FROM FacilityTags WHERE facility_id = :facility_id";
                $stmtTags = $this->pdo->prepare($queryTags);
                $stmtTags->bindParam(':facility_id', $facility_id, PDO::PARAM_INT);
                $stmtTags->execute();
    
                // Delete from Facility table
                $queryFacility = "DELETE FROM Facility WHERE facility_id = :facility_id";
                $stmtFacility = $this->pdo->prepare($queryFacility);
                $stmtFacility->bindParam(':facility_id', $facility_id, PDO::PARAM_INT);
                $stmtFacility->execute();
    
                echo json_encode(['message' => 'Facility deleted successfully']);
            } else {
                // Handle error: Invalid facility ID
                echo json_encode(['error' => 'Invalid facility ID']);
            }
        } else {
            // Handle error: Invalid request method
            echo json_encode(['error' => 'Invalid request method']);
        }
    }

    public function facilitysearch($search)
    {
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
                  WHERE Facility.name LIKE :search
                     OR Tag.name LIKE :search
                     OR Location.city LIKE :search
                  GROUP BY Facility.facility_id
                  LIMIT :offset, :pageSize";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':pageSize', $pageSize, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $facilities = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
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
            if (count($facilities) === $pageSize) {
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

}

