<?php
session_start();

// Retrieve the entered username and password
$username = $_POST['username'];
$password = $_POST['password'];

// Connect to your database
$servername = 'localhost';
$db_username = 'root';
$db_password = '202003';
$dbname = 'project';

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $db_username, $db_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Perform the database query
    $stmt = $conn->prepare("SELECT password FROM login WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    // Fetch the password from the database
    $storedPassword = $stmt->fetchColumn();

    // Check if the entered password matches the stored password
    if ($storedPassword==$password) {
        // Authentication successful, set session variables
        $_SESSION['authenticated'] = true;
        $_SESSION['username'] = $username;

        // Redirect to the protected page (e.g., dashboard.html)
        header('Location: dashboard.php');
        exit();
    } else {
        // Invalid username or password, display error message
        $errorMessage = "Invalid username or password. Please try again.";
    }
} catch (PDOException $e) {
    // Error occurred during database connection or query
    echo "Error: " . $e->getMessage();
}

// Close the database connection
$conn = null;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
<h2>Login</h2>
<form action="authenticate.php" method="POST">
    <div>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
    </div>
    <div>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
    </div>
    <div>
        <input type="submit" value="Login">
    </div>
</form>

<?php if (isset($errorMessage)) { ?>
    <p><?php echo $errorMessage; ?></p>
<?php } ?>
</body>
</html>
