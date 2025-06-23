<?php

$conn = new mysqli('localhost', 'root', '', 'mind healer');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>