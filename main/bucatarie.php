<!DOCTYPE html>
<html>
<head>
    <title>Bucatarie</title>
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
<h2>A wonderful day!!</h2>
<?php
session_start();

// Connect to your database
$servername = 'localhost';
$db_username = 'root';
$db_password = '202003';
$dbname = 'project';

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $db_username, $db_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Calculate the total quantity for each idprodus
    $stmt = $conn->query("SELECT idpreparat, SUM(cantitate) AS total_quantity FROM project.comenzi WHERE status = 'bucatarie' GROUP BY idpreparat");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $idprodus = $row['idpreparat'];
        $totalQuantity = $row['total_quantity'];
        // Check if the item already exists in the 'bucatarie' table
        $existsStmt = $conn->prepare("SELECT COUNT(*) FROM project.bucatarie WHERE idprodus = $idprodus");
        $existsStmt->execute();
        $count = $existsStmt->fetchColumn();

        if ($count > 0) {
            // Item exists, update the quantity
            $updateStmt = $conn->prepare("UPDATE project.bucatarie SET bucati = :total_quantity WHERE idprodus = :idprodus");
            $updateStmt->bindParam(':idprodus', $idprodus);
            $updateStmt->bindParam(':total_quantity', $totalQuantity);
            $updateStmt->execute();
        } else {
            // Item does not exist, insert it into 'bucatarie' table
            $insertStmt = $conn->prepare("INSERT INTO project.bucatarie (idordine, idprodus, nameprodus, bucati) VALUES (:idordine, :idprodus, :nameprodus, :total_quantity)");
            $stmt = $conn->prepare("SELECT idordine FROM project.bucatarie ORDER BY idordine DESC LIMIT 1");
            $stmt->execute();
            $lastId = $stmt->fetchColumn();
            $idordine = $lastId + 1;
            $insertStmt->bindParam(':idordine', $idordine);
            $insertStmt->bindParam(':idprodus', $idprodus);
            $insertStmt->bindParam(':nameprodus', $idprodus);
            $insertStmt->bindParam(':total_quantity', $totalQuantity);
            $insertStmt->execute();
        }
    }

    echo "<h3>Bucatarie Data</h3>";
    echo "<table>";
    echo "<tr>";
    echo "<th>Id Ordine</th>";
    echo "<th>ID Produs</th>";
    echo "<th>Name</th>";
    echo "<th>Bucati</th>";
    echo "</tr>";

    // Fetch data from the 'bucatarie' table
    $stmt = $conn->query("SELECT idordine,idprodus,nameprodus, bucati FROM bucatarie");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . $row['idordine'] . "</td>";
        echo "<td>" . $row['idprodus'] . "</td>";
        echo "<td>" . $row['nameprodus'] . "</td>";
        echo "<td>" . $row['bucati'] . "</td>";
        echo "</tr>";
    }

    echo "</table>";
} catch (PDOException $e) {
    // Error occurred during database connection or query
    echo "Error: " . $e->getMessage();
}

// Close the database connection
$conn = null;
?>
</body>
</html>
