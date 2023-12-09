<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
<h2>Welcome to work,</h2>
<?php
session_start();

if (isset($_SESSION['username'])) {
    echo "<h2>" . $_SESSION['username'] . "</h2>";

    // Connect to your database
    $servername = 'localhost';
    $db_username = 'root';
    $db_password = '202003';
    $dbname = 'project';

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $db_username, $db_password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Check if form submitted for editing
        if (isset($_POST['edit_submit'])) {
            $id = $_POST['edit_id'];
            $description = $_POST['edit_description'];
            $status = $_POST['edit_status'];
            $quantity = $_POST['edit_quantity'];

            // Update the record in the database
            $stmt = $conn->prepare("UPDATE comenzi SET descriere = :description, status = :status, cantitate = :quantity WHERE idcomenzi = :id");
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':quantity', $quantity);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        }

        // Check if form submitted for deletion
        if (isset($_POST['delete_submit'])) {
            $id = $_POST['delete_id'];

            // Delete the record from the database
            $stmt = $conn->prepare("DELETE FROM comenzi WHERE idcomenzi = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            // Reset the auto-increment index in the table
            $stmt = $conn->prepare("ALTER TABLE comenzi AUTO_INCREMENT = 1");
            $stmt->execute();
        }
        // Perform the database query
        $stmt = $conn->query("SELECT * FROM comenzi");

        // Fetch data from the result set
        echo "<h3>Comenzi in curs!</h3>";
        echo "<table>";
        echo "<tr>";
        echo "<th>ID Comenzi</th>";
        echo "<th>ID Preparat</th>";
        echo "<th>ID Masa</th>";
        echo "<th>Cantitate</th>";
        echo "<th>Descriere</th>";
        echo "<th>Status</th>";
        echo "</tr>";

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . $row['idcomenzi'] . "</td>";
            echo "<td>" . $row['idpreparat'] . "</td>";
            echo "<td>" . $row['idmasa'] . "</td>";
            echo "<td>" . $row['cantitate'] . "</td>";
            echo "<td>" . $row['descriere'] . "</td>";
            echo "<td>" . $row['status'] . "</td>";
            echo "</tr>";
        }

        echo "</table>";

        // Add search form
        echo "<br>";
        echo "<h3>Search by ID Comenzi:</h3>";
        echo "<form method='post' action=''>";
        echo "<input type='text' name='search_id' placeholder='Enter ID Comenzi' required>";
        echo "<input type='submit' name='search_submit' value='Search'>";
        echo "</form>";

        if (isset($_POST['search_submit'])) {
            $search_id = $_POST['search_id'];

            // Retrieve the matching record from the database
            $stmt = $conn->prepare("SELECT * FROM comenzi WHERE idcomenzi = :id");
            $stmt->bindParam(':id', $search_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                echo "<br>";
                echo "<h3>Edit/Delete Comenzi:</h3>";
                echo "<table>";
                echo "<tr>";
                echo "<th>ID Comenzi</th>";
                echo "<th>ID Preparat</th>";
                echo "<th>ID Masa</th>";
                echo "<th>Cantitate</th>";
                echo "<th>Descriere</th>";
                echo "<th>Status</th>";
                echo "</tr>";

                echo "<tr>";
                echo "<td>" . $result['idcomenzi'] . "</td>";
                echo "<td>" . $result['idpreparat'] . "</td>";
                echo "<td>" . $result['idmasa'] . "</td>";
                echo "<td>" . $result['cantitate'] . "</td>";
                echo "<td>" . $result['descriere'] . "</td>";
                echo "<td>" . $result['status'] . "</td>";
                echo "</tr>";

                echo "</table>";

                // Add edit and delete options
                echo "<br>";
                echo "<h3>Edit/Delete Options:</h3>";
                echo "<form method='post' action=''>";
                echo "<input type='hidden' name='edit_id' value='" . $result['idcomenzi'] . "'/>";
                echo "Description: <input type='text' name='edit_description' value='" . $result['descriere'] . "'/><br>";
                echo "Status: <input type='text' name='edit_status' value='" . $result['status'] . "'/><br>";
                echo "Quantity: <input type='text' name='edit_quantity' value='" . $result['cantitate'] . "'/><br>";
                echo "<input type='submit' name='edit_submit' value='Edit'/>";
                echo "</form>";
                echo "<form method='post' action=''>";
                echo "<input type='hidden' name='delete_id' value='" . $result['idcomenzi'] . "'/>";
                echo "<input type='submit' name='delete_submit' value='Delete'/>";
                echo "</form>";
            } else {
                echo "<p>No matching records found.</p>";
            }
        }
    } catch (PDOException $e) {
        // Error occurred during database connection or query
        echo "Error: " . $e->getMessage();
    }

    // Close the database connection
    $conn = null;
} else {
    header('Location: login.html');
    exit();
}
?>
</body>
</html>
