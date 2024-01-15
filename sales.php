<!DOCTYPE html>
<html>
<head>
    <title>Sales Tracking</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <h1>Sales Tracking</h1>

    <!-- Dropdown for selecting roll width -->
    <form action="sales.php" method="post">
        <label for="width">Select Roll Width:</label>
        <select name="width" onchange="this.form.submit()">
            <option value="">Choose a Width</option>
            <?php
            include 'connect_db.php';

            // Get unique widths
            $widthSql = "SELECT DISTINCT Width FROM Rolls WHERE Status = 'In-stock' ORDER BY Width";
            $widthResult = $conn->query($widthSql);
            while ($row = $widthResult->fetch_assoc()) {
                echo "<option value='".$row["Width"]."'>".$row["Width"]." cm</option>";
            }
            ?>
        </select>
    </form>

    <!-- Displaying in-stock rolls based on selected width -->
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["width"])) {
        $selectedWidth = $_POST["width"];

        // Query to get in-stock rolls of selected width
        $rollsSql = "SELECT RollID, ReelNumber, GSM, Grade, Length FROM Rolls WHERE Width = $selectedWidth AND Status = 'In-stock'";
        $rollsResult = $conn->query($rollsSql);

        if ($rollsResult->num_rows > 0) {
            echo "<h2>In-Stock Rolls (Width: $selectedWidth cm):</h2>";
            echo "<form action='sales.php' method='post'>";
            echo "<input type='hidden' name='width' value='$selectedWidth'>";
            echo "<table><tr><th>Select</th><th>Roll ID</th><th>Reel Number</th><th>GSM</th><th>Grade</th><th>Length</th></tr>";

            // Output data of each roll
            while ($row = $rollsResult->fetch_assoc()) {
                echo "<tr><td><input type='checkbox' name='selectedRolls[]' value='".$row["RollID"]."'></td><td>".$row["RollID"]."</td><td>".$row["ReelNumber"]."</td><td>".$row["GSM"]."</td><td>".$row["Grade"]."</td><td>".$row["Length"]."</td></tr>";
            }
            echo "</table>";
            echo "Customer Info: <textarea name='customerInfo'></textarea><br>";
            echo "Truck Info: <input type='text' name='truckInfo'><br>";
            echo "<input type='submit' name='recordSale' value='Record Sale'>";
            echo "</form>";
        } else {
            echo "No in-stock rolls for selected width.";
        }
    }

    // Handling sale record submission
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["recordSale"])) {
        $selectedRolls = $_POST["selectedRolls"];
        $customerInfo = $_POST["customerInfo"];
        $truckInfo = $_POST["truckInfo"];

        // Transaction for recording sales
        $conn->begin_transaction();

        $saleRecorded = true;

        foreach ($selectedRolls as $rollId) {
            // Inserting sale record
            $saleSql = "INSERT INTO SalesShipment (RollID, CustomerInfo, TruckInfo)
                        VALUES ($rollId, '$customerInfo', '$truckInfo')";

            if (!$conn->query($saleSql)) {
                $saleRecorded = false;
                echo "Error: " . $saleSql . "<br>" . $conn->error;
                break;
            }

            // Update roll status in inventory
            $updateRollSql = "UPDATE Rolls SET Status = 'Sold' WHERE RollID = $rollId";
            if (!$conn->query($updateRollSql)) {
                $saleRecorded = false;
                echo "Error: " . $updateRollSql . "<br>" . $conn->error;
                break;
            }
        }

        if ($saleRecorded) {
            $conn->commit();
            echo "Sale recorded successfully.";
        } else {
            $conn->rollback();
            echo "Sale recording failed.";
        }
    }
    // Displaying current sales orders
    $sql = "SELECT OrderID, RollID, CustomerInfo, TruckInfo, Status FROM SalesShipment";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<h2>Current Sales Orders:</h2>";
        echo "<table><tr><th>Order ID</th><th>Roll ID</th><th>Customer Info</th><th>Truck Info</th><th>Status</th></tr>";
        // Output data of each row
        while($row = $result->fetch_assoc()) {
            echo "<tr><td>".$row["OrderID"]."</td><td>".$row["RollID"]."</td><td>".$row["CustomerInfo"]."</td><td>".$row["TruckInfo"]."</td><td>".$row["Status"]."</td></tr>";
        }
        echo "</table>";
    } else {
        echo "0 sales orders";
    }

    $conn->close();
    ?>

</body>
</html>
