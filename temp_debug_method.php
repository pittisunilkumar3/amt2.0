<?php
// This is the fixed addstudentfee method to replace the broken one

    public function addstudentfee()
    {
        // Immediate debug response - remove after testing
        error_log("DEBUGGING: addstudentfee method called at " . date('Y-m-d H:i:s'));
        header('Content-Type: application/json');
        
        $debug_response = array(
            'status' => 'debug_success',
            'message' => 'Method is being called successfully',
            'post_data' => $this->input->post(),
            'timestamp' => date('Y-m-d H:i:s')
        );
        echo json_encode($debug_response);
        return;
    }
?>
