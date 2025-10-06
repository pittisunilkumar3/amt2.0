<?php
// Script to fix the broken Studentfee.php file

echo "Fixing Studentfee.php file...\n";

$file_path = 'application/controllers/Studentfee.php';
$content = file_get_contents($file_path);

// Find the start of addstudentfee method
$start_pattern = '/public function addstudentfee\(\)/';
$end_pattern = '/public function printFeesByName\(\)/';

if (preg_match($start_pattern, $content, $start_matches, PREG_OFFSET_CAPTURE) &&
    preg_match($end_pattern, $content, $end_matches, PREG_OFFSET_CAPTURE)) {
    
    $start_pos = $start_matches[0][1];
    $end_pos = $end_matches[0][1];
    
    echo "Found addstudentfee at position: $start_pos\n";
    echo "Found printFeesByName at position: $end_pos\n";
    
    // Create the new simple debug method
    $new_method = '    public function addstudentfee()
    {
        error_log("DEBUGGING: addstudentfee method called at " . date(\'Y-m-d H:i:s\'));
        header(\'Content-Type: application/json\');
        
        $debug_response = array(
            \'status\' => \'debug_success\',
            \'message\' => \'Method is being called successfully\',
            \'post_data\' => $this->input->post(),
            \'timestamp\' => date(\'Y-m-d H:i:s\')
        );
        echo json_encode($debug_response);
        return;
    }

    ';
    
    // Replace the broken method with the new one
    $before = substr($content, 0, $start_pos);
    $after = substr($content, $end_pos);
    $new_content = $before . $new_method . $after;
    
    // Write the fixed content back
    file_put_contents($file_path, $new_content);
    echo "File fixed successfully!\n";
    
} else {
    echo "Could not find method boundaries!\n";
}
?>
