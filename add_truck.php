<!DOCTYPE html>
<html>
<head>
    <title>Add Truck Info</title>
</head>
<body>
    <h2>Add Truck Information</h2>
    <form action="submit_truck.php" method="post">
        Driver Name: <input type="text" name="driverName"><br>
        License Plate: <input type="text" name="licensePlate"><br>
        Unloaded Weight (kg): <input type="number" name="unloadedWeight"><br>
        Loaded Weight (kg): <input type="number" name="loadedWeight"><br>
        <input type="submit" value="Submit">
    </form>
</body>
</html>
