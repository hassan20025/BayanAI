<?php
session_start();

// Include DB connection file
require_once '../db/db.php';

// Get user ID from session or request
$user_id = $_SESSION['user_id'] ?? $_GET['user_id'] ?? null;

if (!$user_id) {
    die("User not identified.");
}

// Check upload access
$sql = "SELECT can_upload FROM users WHERE id = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("User not found.");
}

$row = $result->fetch_assoc();

if ($row['can_upload'] != 1) {
    die("You do not have permission to upload documents.");
}

// Access granted — show upload form
?>

<!DOCTYPE html>
<html>
<head>
  <title>Upload Document</title>
</head>
<body>
  <h2>Upload your document</h2>
  <form action="handleUpload.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
    <input type="file" name="document" required>
    <button type="submit">Upload</button>
  </form>
</body>
</html>
