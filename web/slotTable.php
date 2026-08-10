<?php
$conn = mysqli_connect("localhost", "iot", "pwiot", "iotdb");

if (!$conn) {
    die("DB Connection failed: " . mysqli_connect_error());
}

$result = mysqli_query($conn, "SELECT * FROM `slot` ORDER BY slot_id");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="5">
    <title>Umbrella Slot Status</title>
</head>
<body>
    <h1>Umbrella Slot Status</h1>
    <h3># Slot status description</h3>

    <table border="1" cellpadding="8">
        <tr>
            <th>SLOT</th>
            <th>STATUS</th>
            <th>CARD UID</th>
            <th>DRY LEVEL</th>
            <th>LOCKED</th>
            <th>UPDATED</th>
        </tr>

        <?php
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['slot_id'] . "</td>";
            echo "<td>" . $row['status'] . "</td>";
            echo "<td>" . $row['assigned_uid'] . "</td>";
            echo "<td>" . $row['dry_level'] . "</td>";
            echo "<td>" . $row['locked'] . "</td>";
            echo "<td>" . $row['updated_at'] . "</td>";
            echo "</tr>";
        }
        ?>
    </table>
</body>
</html>

<?php
mysqli_close($conn);
?>
