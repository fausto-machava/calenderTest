<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and decode JSON data from the request
    $jsonData = isset($_POST['data']) ? $_POST['data'] : '';
    $data = json_decode($jsonData, true);

    if (json_last_error() === JSON_ERROR_NONE) {
        // Define file path
        $file = 'data.txt';

        // Write the data to the file
        file_put_contents($file, $jsonData);

        echo 'Success';
    } else {
        echo 'Failed to decode JSON';
    }
}
?>
