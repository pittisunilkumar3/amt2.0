<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2025-10-10 07:56:25 --> Error loading models: The model name you are loading is the name of a resource that is already being used: customlib
ERROR - 2025-10-10 11:26:25 --> Severity: error --> Exception: Call to undefined method Onlinestudent_model::get() C:\xampp\htdocs\amt\api\application\controllers\Online_admission_api.php 462
ERROR - 2025-10-10 08:00:01 --> Error loading models: The model name you are loading is the name of a resource that is already being used: customlib
ERROR - 2025-10-10 12:44:02 --> Error loading models: Unable to locate the model you have specified: Sidebarmenu_model
ERROR - 2025-10-10 12:44:03 --> Error loading models: Unable to locate the model you have specified: Sidebarmenu_model
ERROR - 2025-10-10 12:44:03 --> Error loading models: Unable to locate the model you have specified: Sidebarmenu_model
ERROR - 2025-10-10 12:44:03 --> Error loading models: Unable to locate the model you have specified: Sidebarmenu_model
ERROR - 2025-10-10 12:44:09 --> Error loading models: Unable to locate the model you have specified: Sidebarmenu_model
ERROR - 2025-10-10 12:44:35 --> Error loading models: Unable to locate the model you have specified: Sidebarmenu_model
ERROR - 2025-10-10 16:49:46 --> Error loading models: Unable to locate the model you have specified: Sidebarmenu_model
ERROR - 2025-10-10 16:49:56 --> Error loading models: Unable to locate the model you have specified: Sidebarmenu_model
ERROR - 2025-10-10 16:50:00 --> Error loading models: Unable to locate the model you have specified: Sidebarmenu_model
ERROR - 2025-10-10 16:54:53 --> Error loading models: Unable to locate the model you have specified: Sidebarmenu_model
ERROR - 2025-10-10 23:59:00 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near '.`id`, `classes`.`class` as `name`
FROM `student_session`
JOIN `classes` ON `...' at line 1 - Invalid query: SELECT `DISTINCT` `classes`.`id`, `classes`.`class` as `name`
FROM `student_session`
JOIN `classes` ON `student_session`.`class_id` = `classes`.`id`
WHERE `student_session`.`session_id` = '25'
ORDER BY `classes`.`id` ASC
