<?php
// Validate input data
function test_input($data) {
    $data = strtolower($data);
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }
?>