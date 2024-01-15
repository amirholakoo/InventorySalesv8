<!DOCTYPE html>
<html>
<head>
    <title>Truck Loading Management</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <h1>Truck Loading Management</h1>

    <!-- Truck Information Section -->
    <h2>Truck Information</h2>
    <form action="truck_loading.php" method="post">
        Truck ID: <input type="text" name="truckId"><br>
        Driver Name: <input type="text" name="driverName"><br>
        License Plate: <input type="text" name="licensePlate"><br>
        Unloaded Weight: <input type="number" name="unloadedWeight"><br>
        <input type="submit" name="addTruck" value="Add Truck">
    </form>

    <!-- Weight Station Section -->
    <h2>Weight Station</h2>
    <form action="truck_loading.php" method="post">
        Truck ID: <input type="text" name="weightTruckId"><br>
        Loaded Weight: <input type="number" name="loadedWeight"><br>
        <input type="submit" name="recordWeight" value="Record Weight">
    </form>

    <!-- Loading Section -->
    <h2>Loading Rolls onto Trucks</h2>
    <form action="truck_loading.php" method="post">
        Select Truck: <select name="loadingTruckId">
            <?php
            // Populate trucks from the database
            include 'connect_db.php';
            $query = "SELECT TruckID FROM Trucks";
            $result = $conn->query($query);
            while ($row = $result->fetch_assoc()) {
                echo "<option value='".$row["TruckID"]."'>".$row["TruckID"]."</option>";
            }
            ?>
        </select><br>
        Rolls Ready for Loading:
        <ul>
            <?php
            // List rolls ready for loading
            $rollQuery = "SELECT RollID FROM Rolls WHERE Status = 'Sold'";
            $rollResult = $conn->query($rollQuery);
            while ($rollRow = $rollResult->fetch_assoc()) {
                echo "<li><input type='checkbox' name='selectedRolls[]' value='".$rollRow["RollID"]."'> Roll ID: ".$rollRow["RollID"]."</li>";
            }
            ?>
        </ul>
        <input type="submit" name="loadRolls" value="Load Selected Rolls">
    </form>

    <?php
    // Handling Truck Information Submission
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["addTruck"])) {
        $truckId = $_POST["truckId"];
        $driverName = $_POST["driverName"];
        $licensePlate = $_POST["licensePlate"];
        $unloadedWeight = $_POST["unloadedWeight"];
        $insertTruck = "INSERT INTO Trucks (TruckID, DriverName, LicensePlate, UnloadedWeight) VALUES ('$truckId', '$driverName', '$licensePlate', $unloadedWeight)";
        if ($conn->query($insertTruck) === TRUE) {
            echo "New truck added successfully.";
        } else {
            echo "Error adding truck: " . $conn->error;
        }
    }

    // Handling Weight Station Submission
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["recordWeight"])) {
        $weightTruckId = $_POST["weightTruckId"];
        $loadedWeight = $_POST["loadedWeight"];
        $updateWeight = "UPDATE Trucks SET LoadedWeight = $loadedWeight WHERE TruckID = '$weightTruckId'";
        if ($conn->query($updateWeight) === TRUE) {
            echo "Truck loaded weight recorded successfully.";
        } else {
            echo "Error recording weight: " . $conn->error;
        }
    }

    // Handling Loading Submission
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["loadRolls"])) {
        $selectedRolls = $_POST["selectedRolls"];
        $loadingTruckId = $_POST["loadingTruckId"];
        foreach ($selectedRolls as $rollId) {
            $updateRoll = "UPDATE Rolls SET Status = 'Loaded', CurrentLocation = '$loadingTruckId' WHERE RollID = $rollId";
            $conn->query($updateRoll);
        }
        echo "Selected rolls loaded onto truck " . $loadingTruckId;
    }

    $conn->close();
    ?>

</body>
</html>
