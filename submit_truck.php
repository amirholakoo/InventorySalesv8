<?php
include 'connect_db.php';

$driverName = $_POST['driverName'];
$licensePlate = $_POST['licensePlate'];
$unloadedWeight = $_POST['unloadedWeight'];
$loadedWeight = $_POST['loadedWeight'];

$sql = "INSERT INTO Trucks (DriverName, LicensePlate, UnloadedWeight, LoadedWeight)
VALUES ('$driverName', '$licensePlate', $unloadedWeight, $loadedWeight)";

if ($conn->query($sql) === TRUE) {
  echo "New record created successfully";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
