<?php
require_once "config.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = getDBConnection();
    echo "DB CONNECTED ✅";
    } catch (Throwable $e) {
    http_response_code(500);
    echo "DB ERROR ❌<br>";
    echo $e->getMessage();
}
