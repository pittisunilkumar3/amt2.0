<?php
/**
 * Test to identify the date format issue
 */

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'amt';

// Connect to database
$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "=== Date Format Issue Test ===\n\n";

// Get sample records with amount_detail
$sql = "SELECT id, amount_detail FROM student_fees_deposite WHERE amount_detail IS NOT NULL LIMIT 10";
$result = $conn->query($sql);

if ($result) {
    echo "Sample records and their date formats:\n\n";
    
    while ($row = $result->fetch_assoc()) {
        echo "Record ID: " . $row['id'] . "\n";
        
        $details = json_decode($row['amount_detail'], true);
        if ($details && is_array($details)) {
            foreach ($details as $key => $detail) {
                if (isset($detail['date'])) {
                    $original_date = $detail['date'];
                    $timestamp = strtotime($original_date);
                    $converted_date = $timestamp ? date('Y-m-d', $timestamp) : 'INVALID';
                    
                    echo "  Payment $key:\n";
                    echo "    Original date: $original_date\n";
                    echo "    Timestamp: $timestamp\n";
                    echo "    Converted (Y-m-d): $converted_date\n";
                    echo "    Amount: " . ($detail['amount'] ?? 0) . "\n";
                    echo "    Fine: " . ($detail['amount_fine'] ?? 0) . "\n";
                }
            }
        }
        echo "\n";
    }
}

// Test date range filtering
echo "\n=== Test Date Range Filtering ===\n";
$date_from = '2025-10-01';
$date_to = '2025-10-31';
$formated_date_from = strtotime($date_from);
$formated_date_to = strtotime($date_to);

echo "Filter range: $date_from to $date_to\n";
echo "Timestamp range: $formated_date_from to $formated_date_to\n";
echo "Readable range: " . date('Y-m-d', $formated_date_from) . " to " . date('Y-m-d', $formated_date_to) . "\n\n";

// Test various date formats
echo "=== Testing strtotime() with different formats ===\n";
$test_dates = [
    '30-10-2021',      // DD-MM-YYYY
    '2021-10-30',      // YYYY-MM-DD
    '10-30-2021',      // MM-DD-YYYY
    '30/10/2021',      // DD/MM/YYYY
    '2021/10/30',      // YYYY/MM/DD
    '2025-10-15',      // Target format
    '15-10-2025',      // DD-MM-YYYY in target month
];

foreach ($test_dates as $test_date) {
    $timestamp = strtotime($test_date);
    $converted = $timestamp ? date('Y-m-d', $timestamp) : 'INVALID';
    $in_range = ($timestamp >= $formated_date_from && $timestamp <= $formated_date_to) ? 'YES' : 'NO';
    echo "  $test_date -> $converted (timestamp: $timestamp) [In Oct 2025 range: $in_range]\n";
}

// Check if there are any records with dates in October 2025
echo "\n=== Searching for any October 2025 dates ===\n";
$sql = "SELECT id, amount_detail FROM student_fees_deposite WHERE amount_detail LIKE '%10-2025%' OR amount_detail LIKE '%2025-10%' LIMIT 5";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo "Found records with October 2025 dates:\n";
    while ($row = $result->fetch_assoc()) {
        echo "  Record ID: " . $row['id'] . "\n";
        $details = json_decode($row['amount_detail'], true);
        if ($details && is_array($details)) {
            foreach ($details as $detail) {
                if (isset($detail['date']) && (strpos($detail['date'], '10-2025') !== false || strpos($detail['date'], '2025-10') !== false)) {
                    echo "    Date: " . $detail['date'] . ", Amount: " . ($detail['amount'] ?? 0) . "\n";
                }
            }
        }
    }
} else {
    echo "No records found with October 2025 dates.\n";
    echo "This explains why the API returns zero values!\n\n";
    
    // Find what date ranges actually exist
    echo "=== Finding actual date ranges in database ===\n";
    $sql = "SELECT amount_detail FROM student_fees_deposite WHERE amount_detail IS NOT NULL LIMIT 100";
    $result = $conn->query($sql);
    
    $all_dates = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $details = json_decode($row['amount_detail'], true);
            if ($details && is_array($details)) {
                foreach ($details as $detail) {
                    if (isset($detail['date'])) {
                        $timestamp = strtotime($detail['date']);
                        if ($timestamp) {
                            $all_dates[] = date('Y-m-d', $timestamp);
                        }
                    }
                }
            }
        }
    }
    
    if (!empty($all_dates)) {
        sort($all_dates);
        echo "Earliest date: " . $all_dates[0] . "\n";
        echo "Latest date: " . end($all_dates) . "\n";
        echo "Total unique dates found: " . count(array_unique($all_dates)) . "\n";
        
        // Show date distribution
        $date_counts = array_count_values($all_dates);
        arsort($date_counts);
        echo "\nTop 10 dates with most transactions:\n";
        $count = 0;
        foreach ($date_counts as $date => $cnt) {
            echo "  $date: $cnt transactions\n";
            if (++$count >= 10) break;
        }
    }
}

$conn->close();

echo "\n=== End of Date Format Test ===\n";

