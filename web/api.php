<?php
header("Content-Type: text/plain; charset=UTF-8");

$conn = mysqli_connect("localhost", "iot", "pwiot", "iotdb");

if (!$conn) {
    echo "ERROR: DB connection failed";
    exit;
}

$action = isset($_GET['action']) ? $_GET['action'] : '';
$slot   = isset($_GET['slot']) ? intval($_GET['slot']) : 0;
$uid    = isset($_GET['uid']) ? mysqli_real_escape_string($conn, $_GET['uid']) : '';
$dry    = isset($_GET['dry']) ? intval($_GET['dry']) : 0;

if ($action == '') {
    echo "ERROR: action is required";
    mysqli_close($conn);
    exit;
}

if ($slot <= 0) {
    echo "ERROR: invalid slot";
    mysqli_close($conn);
    exit;
}

/*
    우산 보관
    예:
    /api.php?action=store&slot=1&uid=A3F20C11&dry=0
*/
if ($action == "store") {
    if ($uid == '') {
        echo "ERROR: uid is required";
        mysqli_close($conn);
        exit;
    }

    $sql = "
        INSERT INTO `user`(card_uid, user_type)
        VALUES('$uid', 'NORMAL')
        ON DUPLICATE KEY UPDATE card_uid=card_uid
    ";
    mysqli_query($conn, $sql);

    $sql = "
        UPDATE `slot`
        SET status='USING',
            assigned_uid='$uid',
            dry_level=$dry,
            locked=1,
            updated_at=NOW()
        WHERE slot_id=$slot
    ";

    if (!mysqli_query($conn, $sql)) {
        echo "ERROR: slot update failed";
        mysqli_close($conn);
        exit;
    }

    $sql = "
        INSERT INTO `log`(slot_id, card_uid, action, detail)
        VALUES($slot, '$uid', 'STORE', 'Umbrella stored')
    ";
    mysqli_query($conn, $sql);

    echo "OK: STORE";
}

/*
    우산 회수
    예:
    /api.php?action=retrieve&slot=1&uid=A3F20C11
*/
else if ($action == "retrieve") {
    if ($uid == '') {
        echo "ERROR: uid is required";
        mysqli_close($conn);
        exit;
    }

    $sql = "SELECT assigned_uid FROM `slot` WHERE slot_id=$slot";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    if (!$row) {
        echo "ERROR: slot not found";
        mysqli_close($conn);
        exit;
    }

    $assigned_uid = $row['assigned_uid'];

    if ($assigned_uid == $uid) {
        $sql = "
            UPDATE `slot`
            SET status='EMPTY',
                assigned_uid=NULL,
                dry_level=0,
                locked=1,
                updated_at=NOW()
            WHERE slot_id=$slot
        ";
        mysqli_query($conn, $sql);

        $sql = "
            INSERT INTO `log`(slot_id, card_uid, action, detail)
            VALUES($slot, '$uid', 'PICKUP', 'Umbrella retrieved')
        ";
        mysqli_query($conn, $sql);

        echo "OK: RETRIEVE";
    } else {
        $sql = "
            INSERT INTO `log`(slot_id, card_uid, action, detail)
            VALUES($slot, '$uid', 'AUTH_FAIL', 'UID mismatch')
        ";
        mysqli_query($conn, $sql);

        echo "FAIL: UID_MISMATCH";
    }
}

/*
    건조 상태 업데이트
    예:
    /api.php?action=wet&slot=1&dry=45
*/
else if ($action == "wet") {
    if ($dry <= 0) {
        $status = "DRY_DONE";
        $log_action = "DRY_DONE";
        $detail = "Drying completed";
    } else {
        $status = "DRYING";
        $log_action = "DRY_UPDATE";
        $detail = "Dry level updated";
    }

    $sql = "
        UPDATE `slot`
        SET status='$status',
            dry_level=$dry,
            updated_at=NOW()
        WHERE slot_id=$slot
    ";
    mysqli_query($conn, $sql);

    $sql = "
        INSERT INTO `log`(slot_id, card_uid, action, detail)
        VALUES($slot, NULL, '$log_action', '$detail')
    ";
    mysqli_query($conn, $sql);

    echo "OK: WET";
}

/*
    도난 감지
    예:
    /api.php?action=theft&slot=1
*/
else if ($action == "theft") {
    $sql = "
        UPDATE `slot`
        SET status='THEFT',
            locked=1,
            updated_at=NOW()
        WHERE slot_id=$slot
    ";
    mysqli_query($conn, $sql);

    $sql = "
        INSERT INTO `log`(slot_id, card_uid, action, detail)
        VALUES($slot, NULL, 'THEFT', 'Forced removal detected')
    ";
    mysqli_query($conn, $sql);

    echo "OK: THEFT";
}

else {
    echo "ERROR: unknown action";
}

mysqli_close($conn);
?>
