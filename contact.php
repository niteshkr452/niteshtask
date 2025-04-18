<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "contact";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate inputs
    $errors = [];
    if (empty($_POST['name'])) $errors[] = "Name is required";
    if (empty($_POST['email'])) $errors[] = "Email is required";
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email";
    if (empty($_POST['message'])) $errors[] = "Message is required";

    if (!empty($errors)) {
        http_response_code(400);
        die(json_encode(["success" => false, "errors" => $errors]));
    }

    // Sanitize inputs
    $name = $conn->real_escape_string(htmlspecialchars($_POST['name']));
    $email = $conn->real_escape_string(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));
    $subject = $conn->real_escape_string(htmlspecialchars($_POST['subject'] ?? 'No subject'));
    $message = $conn->real_escape_string(htmlspecialchars($_POST['message']));

    // Insert into database
    $sql = "INSERT INTO messages (name, email, subject, message) VALUES ('$name', '$email', '$subject', '$message')";
    
    if ($conn->query($sql) {
        echo json_encode(["success" => true, "message" => "Form submitted!"]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "error" => "Database error: " . $conn->error]);
    }

    $conn->close();
} else {
    http_response_code(405);
    echo json_encode(["success" => false, "error" => "Method not allowed"]);
}
?>