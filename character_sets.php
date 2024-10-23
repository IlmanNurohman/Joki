<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "posmap";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to get character set information
$sql = "SELECT `CHARACTER_SET_NAME` AS `Charset`, 
               `DEFAULT_COLLATE_NAME` AS `Default collation`, 
               `DESCRIPTION` AS `Description`, 
               `MAXLEN` AS `Maxlen` 
        FROM `information_schema`.`CHARACTER_SETS`";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        echo "Charset: " . $row["Charset"] . " - Default collation: " . $row["Default collation"] . " - Description: " . $row["Description"] . " - Maxlen: " . $row["Maxlen"] . "<br>";
    }
} else {
    echo "0 results";
}

$conn->close();
?>
