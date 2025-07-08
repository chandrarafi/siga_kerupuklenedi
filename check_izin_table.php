<?php

// Load the framework
require 'vendor/autoload.php';

// Create a new instance of CodeIgniter
$app = \Config\Services::codeigniter();
$app->initialize();

// Connect to the database
$db = \Config\Database::connect();

// Check if the izin table exists
$tables = $db->listTables();
if (!in_array('izin', $tables)) {
    echo "Table 'izin' does not exist.\n";
    exit;
}

// Get the structure of the izin table
$fields = $db->getFieldData('izin');
echo "Table 'izin' structure:\n";
foreach ($fields as $field) {
    echo "- {$field->name} ({$field->type}";
    if (isset($field->max_length) && $field->max_length) {
        echo ", {$field->max_length}";
    }
    echo ")\n";
}

// Count the records in the izin table
$count = $db->table('izin')->countAllResults();
echo "\nTotal records in 'izin' table: {$count}\n";

// If there are records, show the first 5
if ($count > 0) {
    $records = $db->table('izin')->limit(5)->get()->getResultArray();
    echo "\nFirst 5 records:\n";
    foreach ($records as $record) {
        echo json_encode($record, JSON_PRETTY_PRINT) . "\n";
    }
}

// Check the date format in the table
if ($count > 0) {
    $dateFormatCheck = $db->query("SELECT idizin, tanggalmulaiizin, tanggalselesaiizin FROM izin LIMIT 5")->getResultArray();
    echo "\nDate format check:\n";
    foreach ($dateFormatCheck as $record) {
        echo "ID: {$record['idizin']}, Start: {$record['tanggalmulaiizin']}, End: {$record['tanggalselesaiizin']}\n";
    }
}
