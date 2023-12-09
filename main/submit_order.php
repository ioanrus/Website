<?php
// Retrieve the order summary data sent from the JavaScript
$orderSummary = json_decode(file_get_contents('php://input'), true);

// Process the order summary data
if (!empty($orderSummary)) {
    // Connect to your database
    $servername = 'localhost';
    $username = 'root';
    $password = '202003';
    $dbname = 'project';

    // Create a new PDO instance
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);

    // Insert the order summary into the database
    foreach ($orderSummary as $item) {
        $stmt = $conn->prepare("SELECT idcomenzi FROM project.comenzi ORDER BY idcomenzi DESC LIMIT 1");
        $stmt->execute();
        $lastId = $stmt->fetchColumn();
// Increment the lastId by 1
        $idcomanda = $lastId + 1;
        $itemName = $item['name'];
        $stmt = $conn->prepare("SELECT idpreparate FROM project.preparate where descriere='$itemName';");
        $stmt->execute();
        $idproduct=$stmt->fetchColumn();

        $itemQuantity = $item['quantity'];
        $itemDescription = $item['description'];
        $idMasa = $item['idMasa'];
        $status = $item['status'];

        // Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO project.comenzi (idcomenzi, idpreparat, idmasa,cantitate,descriere ,status) VALUES (?, ?, ?, ?, ?,?)");
        $stmt->bindParam(1, $idcomanda);
        $stmt->bindParam(2, $idproduct);
        $stmt->bindParam(3, $idMasa);
        $stmt->bindParam(4, $itemQuantity);
        $stmt->bindParam(5, $itemDescription);
        $stmt->bindParam(6, $status);
         // Execute the SQL statement
        $stmt->execute();
    }

    // Close the database connection
    $conn = null;

    // Send a response back to the JavaScript
    $response = ['status' => 'success'];
    echo json_encode($response);
} else {
    // Send an error response back to the JavaScript
    $response = ['status' => 'error', 'message' => 'No order summary data received'];
    echo json_encode($response);
}
?>
// names of the studends that  didn't sustained any exam