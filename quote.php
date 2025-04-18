<?php
// ✅ Enable error reporting for debugging (good for development only)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ✅ Database Connection (Replace with your credentials)
$servername = "localhost";  // Typically "localhost" for local development
$username = "root";         // Default MySQL username for XAMPP
$password = "";             // Default MySQL password for XAMPP (empty by default)
$dbname = "contact";        // The name of the database you created

// ✅ Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// ✅ Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ✅ Replace with your real receiving email address
$receiving_email_address = 'contact@example.com';  // Replace this with your actual email address

// ✅ Path to the PHP Email Form library
$php_email_form_path = '../assets/vendor/php-email-form/php-email-form.php';

// ✅ Include the email form library if it exists
if (file_exists($php_email_form_path)) {
    include($php_email_form_path);
} else {
    die('Unable to load the "PHP Email Form" Library!');
}

// ✅ Create a new instance of the form
$contact = new PHP_Email_Form;

// ✅ Enable AJAX form submission (you can disable if not needed)
$contact->ajax = true;

// ✅ Input validation before processing
$errors = [];
if (empty($_POST['name'])) $errors[] = "Name is required";
if (empty($_POST['email'])) $errors[] = "Email is required";
if (!filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";
if (empty($_POST['message'])) $errors[] = "Message is required";

if (!empty($errors)) {
    http_response_code(400);
    die(json_encode(['success' => false, 'errors' => $errors]));
}

// ✅ Set form details with sanitized input
$contact->to = $receiving_email_address;
$contact->from_name = htmlspecialchars(trim($_POST['name']));
$contact->from_email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
$contact->subject = htmlspecialchars(trim($_POST['subject'] ?? 'New message from contact form'));

// ✅ SMTP Configuration for Gmail (Updated correct settings)
$contact->smtp = array(
    'host' => 'smtp.gmail.com',
    'username' => 'your@gmail.com',    // Your actual Gmail address
    'password' => 'your-app-password', // Use App Password (not your regular password)
    'port' => 5500,                     // Correct port for TLS
    'encryption' => 'tls'              // Correct encryption type
);

// ✅ Add message content for the email
$contact->add_message($_POST['name'], 'From');
$contact->add_message($_POST['email'], 'Email');
$contact->add_message($_POST['message'], 'Message', 10);

// ✅ Insert the form data into the database using prepared statements
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    
    // Bind parameters
    $stmt->bind_param("ssss", 
        htmlspecialchars(trim($_POST['name'])),
        filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL),
        htmlspecialchars(trim($_POST['subject'] ?? 'No subject')),
        htmlspecialchars(trim($_POST['message']))
    );

    // Execute and check result
    if ($stmt->execute()) {
        $db_success = true;
    } else {
        $db_success = false;
        error_log("Database error: " . $stmt->error);
    }
    $stmt->close();
}

// ✅ Send the email and capture result
$email_result = $contact->send();

// ✅ Return proper JSON response
header('Content-Type: application/json');
echo json_encode([
    'success' => $email_result && ($db_success ?? false),
    'message' => $email_result ? 'Message sent successfully' : 'Failed to send message',
    'email_sent' => $email_result,
    'db_stored' => $db_success ?? false
]);

// ✅ Close the database connection
$conn->close();
?>