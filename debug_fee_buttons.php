<!DOCTYPE html>
<html>
<head>
    <title>Debug Fee Buttons</title>
    <link rel="stylesheet" href="backend/bootstrap/css/bootstrap.min.css">
    <style>
        .ss-none { display: none; }
    </style>
    <script src="backend/custom/jquery.min.js"></script>
    <script src="backend/bootstrap/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container">
        <h1>Fee Button Debug Test</h1>
        
        <h3>Test 1: Visible Button</h3>
        <button type="button" class="btn btn-xs btn-default myCollectFeeBtn" 
                data-toggle="modal" data-target="#myFeesModal"
                data-student_session_id="123"
                data-fee-category="fees">
            <i class="fa fa-plus"></i> Collect Fee (Visible)
        </button>
        
        <h3>Test 2: Hidden Button (ss-none class)</h3>
        <button type="button" class="btn btn-xs btn-default myCollectFeeBtn ss-none" 
                data-toggle="modal" data-target="#myFeesModal"
                data-student_session_id="123"
                data-fee-category="fees">
            <i class="fa fa-plus"></i> Collect Fee (Hidden)
        </button>
        
        <h3>Test 3: Collect Selected Button</h3>
        <button type="button" class="btn btn-sm btn-warning collectSelected">
            <i class="fa fa-money"></i> Collect Selected
        </button>
        
        <h3>Test 4: Print Selected Button</h3>
        <button type="button" class="btn btn-sm btn-info printSelected">
            <i class="fa fa-print"></i> Print Selected
        </button>
        
        <div id="console-output"></div>
    </div>

    <!-- Simple Modal for Testing -->
    <div class="modal fade" id="myFeesModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Test Modal</h4>
                </div>
                <div class="modal-body">
                    <p>This is a test modal to verify the buttons work.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            var output = $('#console-output');
            
            function log(message) {
                console.log(message);
                output.append('<div>' + message + '</div>');
            }
            
            log('Document ready - JavaScript is working');
            log('Number of .myCollectFeeBtn buttons found: ' + $('.myCollectFeeBtn').length);
            log('Number of visible .myCollectFeeBtn buttons: ' + $('.myCollectFeeBtn:visible').length);
            log('Number of .collectSelected buttons found: ' + $('.collectSelected').length);
            log('Number of .printSelected buttons found: ' + $('.printSelected').length);
            
            // Add click handlers
            $(document).on('click', '.myCollectFeeBtn', function(e) {
                log('myCollectFeeBtn clicked! Data: ' + JSON.stringify($(this).data()));
            });
            
            $(document).on('click', '.collectSelected', function(e) {
                log('collectSelected clicked!');
            });
            
            $(document).on('click', '.printSelected', function(e) {
                log('printSelected clicked!');
            });
            
            // Modal events
            $('#myFeesModal').on('show.bs.modal', function() {
                log('Modal showing...');
            });
            
            $('#myFeesModal').on('shown.bs.modal', function() {
                log('Modal shown!');
            });
        });
    </script>
</body>
</html>
