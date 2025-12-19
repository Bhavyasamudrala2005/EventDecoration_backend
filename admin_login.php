<?php
header("Content-Type: application/json");

// MySQL database connection
$host = "localhost";
$dbname = "eventdecoration";
$user = "root";      // your DB username
$pass = "";          // your DB password

$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode([
        "status" => "error",
        "message" => "Database connection failed"
    ]));
}

// Get POSTed JSON data
$data = json_decode(file_get_contents("php://input"), true);

$username = trim($data["username"] ?? "");
$password = trim($data["password"] ?? "");

if (empty($username) || empty($password)) {
    echo json_encode([
        "status" => "error",
        "message" => "Username and password are required"
    ]);
    exit;
}

// Query the admin table
$stmt = $conn->prepare("SELECT * FROM admins WHERE username = ? LIMIT 1");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();

    if ($admin["password"] === $password) {  // ✅ In production: use password_verify()
        echo json_encode([
            "status" => "success",
            "message" => "Admin login successful"
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Invalid password"
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Admin not found"
    ]);
}

$stmt->close();
$conn->close();
?>
