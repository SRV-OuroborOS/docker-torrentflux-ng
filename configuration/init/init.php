<?php
error_reporting(E_ERROR | E_PARSE);
$db_host = getenv('DB_HOST');
$db_name = getenv('DB_NAME');
$db_user = getenv('DB_USER');
$db_pass = getenv('DB_PASS');
try {
    $conn = new mysqli($db_host, $db_user, $db_pass);
} catch (Exception $e) {
    fwrite(STDERR, 'DB connection failed !' . PHP_EOL);
    die(1);
}

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if (mysqli_connect_errno()) {
    unset($conn);
    fwrite(STDOUT, 'Creating database' . PHP_EOL);
    $conn = new mysqli($db_host, $db_user, $db_pass);
    $sql = 'create database ' . $db_name . ';';
    $result = $conn->query($sql);
    $conn->close();
}

try {
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    $sql_numbers_tables = 'show tables from ' . $db_name . ';';
    $result = $conn->query($sql_numbers_tables);
    if ($result->num_rows == 0) {
        fwrite(STDOUT, 'Insert tables' . PHP_EOL);
        $command = 'mysql -h '. $db_host .' -u ' . $db_user . ' -p'.$db_pass . ' ' . $db_name . ' < /init/init_db.sql';
        $last_line = exec($command, $output, $return_var);
        if ($return_var !== 0) {
            throw new Exception('connexion OK');
        }
    } else if ($result->num_rows != 15) {
        fwrite(STDERR, 'Remove existent database' . PHP_EOL);
        die(1);
    }
} catch (Exception $e) {
    fwrite(STDERR, 'DB connection failed !' . PHP_EOL);
    die(1);
}

fwrite(STDOUT, 'DB OK :)' . PHP_EOL);