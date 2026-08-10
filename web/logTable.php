<?php
$conn = mysqli_connect("localhost", "iot", "pwiot", "iotdb");

if (!$conn) {
    die("DB Connection failed: " . mysqli_connect_error());
}

$result = mysqli_query($conn, "SELECT * FROM `log` ORDER BY log_id DESC LIMIT 20");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="5">
    <title>Umbrella Logs</title>
</head>
<body>
    <h1>Umbrella Event Logs</h1>
    <h3># Recent event log</h3>

    <table border="1" cellpadding="8">
        <tr>
            <th>LOG ID</th>
            <th>TIME</th>
            <th>SLOT</th>
            <th>CARD UID</th>
            <th>ACTION</th>
            <th>DETAIL</th>
        </tr>

        <?php
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['log_id'] . "</td>";
            echo "<td>" . $row['timestamp'] . "</td>";
            echo "<td>" . $row['slot_id'] . "</td>";
            echo "<td>" . $row['card_uid'] . "</td>";
            echo "<td>" . $row['action'] . "</td>";
            echo "<td>" . $row['detail'] . "</td>";
            echo "</tr>";
        }
        ?>
    </table>
</body>
</html>

<?php
mysqli_close($conn);
?>
