<!DOCTYPE html>
<html>
<head>
    <title>Test Fee Collection</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>Test Fee Collection Processing</h1>
    <div id="result"></div>
    
    <script>
    // Test making a simple AJAX call to see if the server responds
    $.ajax({
        url: '<?php echo base_url(); ?>studentfee/test_connection',
        type: 'POST',
        data: {
            test: 'connection'
        },
        dataType: 'json',
        timeout: 30000,
        success: function(response) {
            $('#result').html('<div style="color: green;">Server connection successful: ' + JSON.stringify(response) + '</div>');
        },
        error: function(xhr, status, error) {
            $('#result').html('<div style="color: red;">Error: ' + status + ' - ' + error + '<br>Response: ' + xhr.responseText + '</div>');
        }
    });
    </script>
</body>
</html>
