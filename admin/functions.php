<?php
function addNotification($conn, $message, $type='info', $link=null) {
    $message = mysqli_real_escape_string($conn, $message);
    $link = $link ? mysqli_real_escape_string($conn, $link) : null;
    $sql = "INSERT INTO notifications (message, type, link) VALUES ('$message', '$type', '$link')";
    mysqli_query($conn, $sql);
}
?>
