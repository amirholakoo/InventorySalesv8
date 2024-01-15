<!DOCTYPE html>
<html>
<head>
    <title>Inventory Management</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <h1>Inventory Management</h1>

    <!-- Form for adding new inventory -->
    <h2>Add New Inventory Item</h2>
    <form action="inventory.php" method="post">
        Reel Number: <input type="text" name="reelNumber"><br>
        GSM: <input type="number" name="gsm"><br>
        Grade: <input type="text" name="grade"><br>
        Width: <input type="number" name="width"><br>
        Length: <input type="number" name="length"><br>
        <input type="submit" name="submit" value="Add Inventory">
    </form>

    <?php
    include 'connect_db.php';
    include 'phpqrcode/qrlib.php'; // Path to PHP QR Code library

    // Handling form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $reelNumber = $_POST["reelNumber"];
        $gsm = $_POST["gsm"];
        $grade = $_POST["grade"];
        $width = $_POST["width"];
        $length = $_POST["length"];

        $sql = "INSERT INTO Rolls (ReelNumber, GSM, Grade, Width, Length)
        VALUES ('$reelNumber', $gsm, '$grade', $width, $length)";

        if ($conn->query($sql) === TRUE) {
            $last_id = $conn->insert_id;
            echo "New record created successfully. Roll ID is: " . $last_id;

            // Generate QR Code
            $qrData = "RollID: $last_id, ReelNumber: $reelNumber, GSM: $gsm, Width: $width";

            $qrCodePath = 'qrcodes/'.$last_id.'.png';
            QRcode::png($qrData, $qrCodePath);

            echo "<p>QR Code for Roll ID $last_id:</p>";
            echo "<img src='$qrCodePath' />";
            echo "<br><button onclick='window.print()'>Print QR Code</button>";

        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    // Rest of the inventory display code...
    // Displaying current inventory
    $sql = "SELECT RollID, ReelNumber, GSM, Grade, Width, Length, CurrentLocation, Status FROM Rolls";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<h2>Current Inventory:</h2>";
        echo "<table><tr><th>ID</th><th>Reel Number</th><th>GSM</th><th>Grade</th><th>Width</th><th>Length</th><th>Location</th><th>Status</th></tr>";
        // Output data of each row
        while($row = $result->fetch_assoc()) {
            echo "<tr><td>".$row["RollID"]."</td><td>".$row["ReelNumber"]."</td><td>".$row["GSM"]."</td><td>".$row["Grade"]."</td><td>".$row["Width"]."</td><td>".$row["Length"]."</td><td>".$row["CurrentLocation"]."</td><td>".$row["Status"]."</td></tr>";
        }
        echo "</table>";
    } else {
        echo "0 results";
    }

    $conn->close();
    ?>

</body>
</html>
