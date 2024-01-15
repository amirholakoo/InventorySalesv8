<?php
require('fpdf.php'); // Path to FPDF library

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["generatePackingList"])) {
    $selectedTruck = $_POST["selectedTruck"];

    // Create instance of FPDF class
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 12);

    // Connect to database
    include 'connect_db.php';

    // Fetch rolls data
    $rollsQuery = "SELECT RollID, ReelNumber, GSM, Grade, Width, Length FROM Rolls WHERE CurrentLocation = '$selectedTruck' AND Status = 'Loaded'";
    $rollsResult = $conn->query($rollsQuery);

    // Check if there are rolls loaded on the truck
    if ($rollsResult->num_rows > 0) {
        $pdf->Cell(40, 10, 'Packing List for Truck: ' . $selectedTruck);
        $pdf->Ln(20);

        $pdf->Cell(30, 10, 'Roll ID', 1);
        $pdf->Cell(40, 10, 'Reel Number', 1);
        $pdf->Cell(20, 10, 'GSM', 1);
        $pdf->Cell(20, 10, 'Grade', 1);
        $pdf->Cell(20, 10, 'Width', 1);
        $pdf->Cell(20, 10, 'Length', 1);
        $pdf->Ln();

        while ($rollRow = $rollsResult->fetch_assoc()) {
            $pdf->Cell(30, 10, $rollRow["RollID"], 1);
            $pdf->Cell(40, 10, $rollRow["ReelNumber"], 1);
            $pdf->Cell(20, 10, $rollRow["GSM"], 1);
            $pdf->Cell(20, 10, $rollRow["Grade"], 1);
            $pdf->Cell(20, 10, $rollRow["Width"], 1);
            $pdf->Cell(20, 10, $rollRow["Length"], 1);
            $pdf->Ln();
        }

        // Output the PDF
        $pdf->Output('D', 'PackingList_Truck_' . $selectedTruck . '.pdf');
    } else {
        echo "No rolls loaded on this truck.";
    }
}
?>




<!DOCTYPE html>
<html>
<head>
    <title>Packing List and Invoice Generation</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <h1>Packing List and Invoice Generation</h1>

    <!-- Select Loaded Trucks -->
    <form action="packing_invoice.php" method="post">
        Select Truck for Shipment: <select name="selectedTruck">
            <?php
            include 'connect_db.php';
            $query = "SELECT TruckID FROM Trucks WHERE LoadedWeight IS NOT NULL";
            $result = $conn->query($query);
            while ($row = $result->fetch_assoc()) {
                echo "<option value='".$row["TruckID"]."'>".$row["TruckID"]."</option>";
            }
            ?>
        </select><br>
        <input type="submit" name="viewDetails" value="View Shipment Details">
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["viewDetails"])) {
        $selectedTruck = $_POST["selectedTruck"];

        // Display Rolls on Selected Truck
        echo "<h2>Rolls Loaded on Truck $selectedTruck:</h2>";
        $rollsQuery = "SELECT RollID, ReelNumber, GSM, Grade, Width, Length FROM Rolls WHERE CurrentLocation = '$selectedTruck' AND Status = 'Loaded'";
        $rollsResult = $conn->query($rollsQuery);
        if ($rollsResult->num_rows > 0) {
            echo "<table><tr><th>Roll ID</th><th>Reel Number</th><th>GSM</th><th>Grade</th><th>Width</th><th>Length</th></tr>";
            while ($rollRow = $rollsResult->fetch_assoc()) {
                echo "<tr><td>".$rollRow["RollID"]."</td><td>".$rollRow["ReelNumber"]."</td><td>".$rollRow["GSM"]."</td><td>".$rollRow["Grade"]."</td><td>".$rollRow["Width"]."</td><td>".$rollRow["Length"]."</td></tr>";
            }
            echo "</table>";
            echo "<button onclick='generatePackingList(\"$selectedTruck\")'>Generate Packing List</button>";
            echo "<button onclick='generateInvoice(\"$selectedTruck\")'>Generate Invoice</button>";
        } else {
            echo "No rolls loaded on this truck.";
        }
    }

    // JavaScript functions for generating packing list and invoice
    echo "<script>
    function generatePackingList(truckId) {
        // Code to generate packing list
        alert('Generating packing list for ' + truckId);
        // Implement the generation logic
    }

    function generateInvoice(truckId) {
        // Code to generate invoice
        alert('Generating invoice for ' + truckId);
        // Implement the generation logic
    }
    </script>";

    $conn->close();
    ?>

</body>
</html>
