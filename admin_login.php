<?php
header("Content-Type: application/json; charset=UTF-8");
require_once "db.php";

/* ---------- READ RAW JSON ---------- */
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

/* ---------- VALIDATE JSON ---------- */
if (!is_array($data)) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid JSON input"
    ]);
    exit;
}

/* ---------- READ INPUT ---------- */
$username = trim($data["username"] ?? "");
$password = trim($data["password"] ?? "");

/* ---------- VALIDATE INPUT ---------- */
if ($username === "" || $password === "") {
    echo json_encode([
        "status" => "error",
        "message" => "Username and password are required"
    ]);
    exit;
}

/* ---------- FETCH ADMIN ---------- */
$stmt = $conn->prepare(
    "SELECT password FROM admins WHERE username = ? LIMIT 1"
);

if (!$stmt) {
    echo json_encode([
        "status" => "error",
        "message" => "Query preparation failed"
    ]);
    exit;
}

$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

/* ---------- CHECK USER ---------- */
if ($result->num_rows === 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Admin login failed"
    ]);
    exit;
}

$row = $result->fetch_assoc();

/* ---------- PASSWORD CHECK (PLAIN TEXT) ---------- */
if (trim($row["password"]) !== $password) {
    echo json_encode([
        "status" => "error",
        "message" => "Admin login failed"
    ]);
    exit;
}

/* ---------- SUCCESS ---------- */
echo json_encode([
    "status" => "success",
    "message" => "Admin login successful"
]);

$stmt->close();
$conn->close();
