<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2025-10-11 09:02:56 --> Query error: Unknown column 'DISTINCT' in 'field list' - Invalid query: SELECT `DISTINCT` `received_by`
FROM `student_fees_depositeadding`
WHERE `received_by` IS NOT NULL
AND `received_by` != ''
ERROR - 2025-10-11 09:25:29 --> Query error: Unknown column 'received_by' in 'field list' - Invalid query: SELECT DISTINCT `received_by`
FROM `student_fees_depositeadding`
WHERE `received_by` IS NOT NULL
AND `received_by` != ''
ERROR - 2025-10-11 10:05:33 --> Severity: error --> Exception: Unable to locate the model you have specified: Studentfeemasteradding_model C:\xampp\htdocs\amt\api\system\core\Loader.php 349
ERROR - 2025-10-11 11:06:17 --> Severity: Warning --> require_once(C:\xampp\htdocs\amt\api\application/models/Studentfeemasteradding_model.php): Failed to open stream: No such file or directory C:\xampp\htdocs\amt\api\application\controllers\Other_collection_report_api.php 44
ERROR - 2025-10-11 11:06:17 --> Severity: error --> Exception: Failed opening required 'C:\xampp\htdocs\amt\api\application/models/Studentfeemasteradding_model.php' (include_path='C:\xampp\php\PEAR') C:\xampp\htdocs\amt\api\application\controllers\Other_collection_report_api.php 44
