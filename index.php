<h1> Hello World <h1>

<!-- $apiToken = 'your_secret_token';

// Verify token function
function verifyToken($token)
{
    global $apiToken;
    return $token === $apiToken;
}

// Check for the presence of the "Authorization" header
if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

// Extract the token from the "Authorization" header
$token = trim(str_replace("Bearer", "", $_SERVER['HTTP_AUTHORIZATION']));

// Verify the token
if (!verifyToken($token)) {
    http_response_code(401); // Unauthorized
    echo json_encode(["error" => "Invalid token"]);
    exit;
} -->