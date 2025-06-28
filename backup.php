<?php
session_start();
include 'config.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("Access Denied. You must be an administrator to access this page.");
}

$tables = array();
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_row()) {
    $tables[] = $row[0];
}

$sqlScript = "";
foreach ($tables as $table) {
    // Prepare SQL create table statement
    $result = $conn->query("SHOW CREATE TABLE $table");
    $row = $result->fetch_row();
    $sqlScript .= "\n\n" . $row[1] . ";\n\n";

    $result = $conn->query("SELECT * FROM $table");
    $columnCount = $result->field_count;

    // Prepare SQL insert statement
    for ($i = 0; $i < $result->num_rows; $i++) {
        $row = $result->fetch_row();
        $sqlScript .= "INSERT INTO $table VALUES(";
        for ($j = 0; $j < $columnCount; $j++) {
            $row[$j] = $row[$j];
            
            if (isset($row[$j])) {
                $sqlScript .= '"' . $conn->real_escape_string($row[$j]) . '"';
            } else {
                $sqlScript .= '""';
            }
            if ($j < ($columnCount - 1)) {
                $sqlScript .= ',';
            }
        }
        $sqlScript .= ");\n";
    }
}

if(!empty($sqlScript))
{
    // Save the SQL script to a backup file
    $backup_file_name = DB_NAME . '_backup_' . time() . '.sql';
    
    header('Content-type: application/octet-stream');
    header("Content-Disposition: attachment; filename=\"$backup_file_name\"");
    echo $sqlScript;
    exit();
}
?> 