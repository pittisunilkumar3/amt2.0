<!DOCTYPE html>
<html>
<head>
    <title>Debug Fee Collection</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>Debug Fee Collection Error</h1>
    <button id="testBtn">Test Fee Collection Call</button>
    <div id="result"></div>
    
    <script>
    $('#testBtn').click(function() {
        $('#result').html('<div style="color: blue;">Testing...</div>');
        
        $.ajax({
            url: 'studentfee/addstudentfee',
            type: 'POST',
            data: {
                'fee_category': 'regular',
                'student_fees_master_id': '1',
                'fee_groups_feetype_id': '1', 
                'date': '2025-09-05',
                'amount': '100',
                'amount_discount': '0',
                'amount_fine': '0',
                'payment_mode': 'cash',
                'accountname': 'test',
                'collect_from_advance': '0'
            },
            dataType: 'json',
            timeout: 30000,
            success: function(response) {
                $('#result').html('<div style="color: green;">Success: ' + JSON.stringify(response, null, 2) + '</div>');
            },
            error: function(xhr, status, error) {
                $('#result').html('<div style="color: red;">Error Status: ' + status + '<br>Error: ' + error + '<br>Status Code: ' + xhr.status + '<br><br>Response Text:<br><pre>' + xhr.responseText + '</pre></div>');
            }
        });
    });
    </script>
</body>
</html>
