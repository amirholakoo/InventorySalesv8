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

    <!-- Select Loaded Trucks and Generate Packing List -->
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
        <input type="submit" name="generatePackingList" value="Generate Packing List">
    </form>

</body>
</html>
