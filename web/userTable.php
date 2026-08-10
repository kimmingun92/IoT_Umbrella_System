<?php
$conn = mysqli_connect("localhost", "iot", "pwiot", "iotdb");

if (!$conn) {
    die("DB Connection failed: " . mysqli_connect_error());
}

$result = mysqli_query($conn, "SELECT * FROM `user` ORDER BY registered_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="5">
    <title>Umbrella Users</title>
</head>
<body>
    <h1>Umbrella Users</h1>
    <h3># Registered RFID users</h3>

    <table border="1" cellpadding="8">
        <tr>
            <th>UID</th>
            <th>TYPE</th>
            <th>REGISTERED</th>
        </tr>

        <?php
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['card_uid'] . "</td>";
            echo "<td>" . $row['user_type'] . "</td>";
            echo "<td>" . $row['registered_at'] . "</td>";
            echo "</tr>";
        }
        ?>
    </table>
</body>
</html>

<?php
mysqli_close($conn);
?>
