<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2025-10-03 01:26:40 --> Severity: Warning --> Undefined property: Teacher_webservice::$teacher_permission_model C:\xampp\htdocs\amt\api\application\controllers\Teacher_webservice.php 118
ERROR - 2025-10-03 01:26:40 --> Severity: error --> Exception: Call to a member function getTeacherRole() on null C:\xampp\htdocs\amt\api\application\controllers\Teacher_webservice.php 118
ERROR - 2025-10-03 01:26:49 --> Severity: Warning --> Undefined property: Teacher_webservice::$teacher_permission_model C:\xampp\htdocs\amt\api\application\controllers\Teacher_webservice.php 118
ERROR - 2025-10-03 01:26:49 --> Severity: error --> Exception: Call to a member function getTeacherRole() on null C:\xampp\htdocs\amt\api\application\controllers\Teacher_webservice.php 118
ERROR - 2025-10-03 01:27:04 --> Severity: Warning --> Undefined property: Teacher_webservice::$teacher_permission_model C:\xampp\htdocs\amt\api\application\controllers\Teacher_webservice.php 118
ERROR - 2025-10-03 01:27:04 --> Severity: error --> Exception: Call to a member function getTeacherRole() on null C:\xampp\htdocs\amt\api\application\controllers\Teacher_webservice.php 118
ERROR - 2025-10-03 01:27:39 --> Severity: Warning --> Undefined property: Teacher_webservice::$teacher_permission_model C:\xampp\htdocs\amt\api\application\controllers\Teacher_webservice.php 118
ERROR - 2025-10-03 01:27:39 --> Severity: error --> Exception: Call to a member function getTeacherRole() on null C:\xampp\htdocs\amt\api\application\controllers\Teacher_webservice.php 118
ERROR - 2025-10-03 01:30:12 --> Severity: Warning --> Undefined property: Teacher_webservice::$teacher_permission_model C:\xampp\htdocs\amt\api\application\controllers\Teacher_webservice.php 118
ERROR - 2025-10-03 01:30:12 --> Severity: error --> Exception: Call to a member function getTeacherRole() on null C:\xampp\htdocs\amt\api\application\controllers\Teacher_webservice.php 118
ERROR - 2025-10-03 01:42:04 --> Severity: Warning --> Undefined property: Teacher_webservice::$teacher_permission_model C:\xampp\htdocs\amt\api\application\controllers\Teacher_webservice.php 118
ERROR - 2025-10-03 01:42:04 --> Severity: error --> Exception: Call to a member function getTeacherRole() on null C:\xampp\htdocs\amt\api\application\controllers\Teacher_webservice.php 118
ERROR - 2025-10-03 01:42:04 --> Severity: Warning --> Cannot modify header information - headers already sent by (output started at C:\xampp\htdocs\amt\api\system\core\Exceptions.php:272) C:\xampp\htdocs\amt\api\system\core\Common.php 571
ERROR - 2025-10-03 01:42:52 --> Severity: Warning --> Undefined property: Teacher_webservice::$teacher_permission_model C:\xampp\htdocs\amt\api\application\controllers\Teacher_webservice.php 120
ERROR - 2025-10-03 01:42:52 --> Severity: error --> Exception: Call to a member function getTeacherRole() on null C:\xampp\htdocs\amt\api\application\controllers\Teacher_webservice.php 120
ERROR - 2025-10-03 01:42:52 --> Severity: Warning --> Cannot modify header information - headers already sent by (output started at C:\xampp\htdocs\amt\api\system\core\Exceptions.php:272) C:\xampp\htdocs\amt\api\system\core\Common.php 571
ERROR - 2025-10-03 01:46:39 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near '.*
FROM `sidebar_menus` `sm`
JOIN `permission_category_mapping` `pcm` ON `sm`...' at line 1 - Invalid query: SELECT `DISTINCT` `sm`.*
FROM `sidebar_menus` `sm`
JOIN `permission_category_mapping` `pcm` ON `sm`.`id` = `pcm`.`sidebar_menu_id`
JOIN `staff_designation_permissions` `sdp` ON `pcm`.`permission_category_id` = `sdp`.`permission_category_id`
JOIN `staff` `s` ON `s`.`designation` = `sdp`.`designation`
WHERE `s`.`id` = 24
AND `sm`.`status` = 1
ORDER BY `sm`.`sort_order`
ERROR - 2025-10-03 01:47:18 --> Query error: Table 'amt.permission_category_mapping' doesn't exist - Invalid query: SELECT DISTINCT `sm`.*
FROM `sidebar_menus` `sm`
JOIN `permission_category_mapping` `pcm` ON `sm`.`id` = `pcm`.`sidebar_menu_id`
JOIN `staff_designation_permissions` `sdp` ON `pcm`.`permission_category_id` = `sdp`.`permission_category_id`
JOIN `staff` `s` ON `s`.`designation` = `sdp`.`designation`
WHERE `s`.`id` = 24
AND `sm`.`status` = 1
ORDER BY `sm`.`sort_order`
ERROR - 2025-10-03 01:49:56 --> Query error: Unknown column 'rp.perm_group_id' in 'on clause' - Invalid query: SELECT DISTINCT `sm`.*
FROM `sidebar_menus` `sm`
JOIN `roles_permissions` `rp` ON `sm`.`permission_group_id` = `rp`.`perm_group_id`
WHERE `rp`.`role_id` = '2'
AND `sm`.`is_active` = 1
ORDER BY `sm`.`level`
ERROR - 2025-10-03 11:23:56 --> Error loading models: Unable to locate the model you have specified: Role_model
ERROR - 2025-10-03 11:24:21 --> Error loading models: Unable to locate the model you have specified: Role_model
ERROR - 2025-10-03 12:31:37 --> Error loading models: Unable to locate the model you have specified: Role_model
ERROR - 2025-10-03 12:32:05 --> Error loading models: Unable to locate the model you have specified: Role_model
ERROR - 2025-10-03 12:32:27 --> Error loading models: Unable to locate the model you have specified: Role_model
ERROR - 2025-10-03 12:42:07 --> Error loading models: Unable to locate the model you have specified: Role_model
ERROR - 2025-10-03 12:42:07 --> Error loading models: Unable to locate the model you have specified: Role_model
ERROR - 2025-10-03 12:42:07 --> Error loading models: Unable to locate the model you have specified: Role_model
ERROR - 2025-10-03 12:42:40 --> Error loading models: Unable to locate the model you have specified: Role_model
ERROR - 2025-10-03 12:44:18 --> Error loading models: Unable to locate the model you have specified: Role_model
ERROR - 2025-10-03 12:45:26 --> Error loading models: Unable to locate the model you have specified: Role_model
ERROR - 2025-10-03 12:45:51 --> Error loading models: Unable to locate the model you have specified: Role_model
ERROR - 2025-10-03 12:56:40 --> Error loading models: Unable to locate the model you have specified: Role_model
ERROR - 2025-10-03 15:48:28 --> Error loading models: Unable to locate the model you have specified: Role_model
ERROR - 2025-10-03 15:49:21 --> Error loading models: Unable to locate the model you have specified: Role_model
ERROR - 2025-10-03 16:04:46 --> Error loading models: Unable to locate the model you have specified: Role_model
ERROR - 2025-10-03 19:34:46 --> PHP Error: Undefined property: Teacher_webservice::$rolepermission_model in C:\xampp\htdocs\amt\api\application\controllers\Teacher_webservice.php on line 1872
ERROR - 2025-10-03 19:34:46 --> Exception: Call to a member function getPermissionByRoleandCategory() on null in C:\xampp\htdocs\amt\api\application\controllers\Teacher_webservice.php on line 1872
ERROR - 2025-10-03 16:04:59 --> Error loading models: Unable to locate the model you have specified: Role_model
ERROR - 2025-10-03 19:34:59 --> PHP Error: Undefined property: Teacher_webservice::$rolepermission_model in C:\xampp\htdocs\amt\api\application\controllers\Teacher_webservice.php on line 1872
ERROR - 2025-10-03 19:34:59 --> Exception: Call to a member function getPermissionByRoleandCategory() on null in C:\xampp\htdocs\amt\api\application\controllers\Teacher_webservice.php on line 1872
ERROR - 2025-10-03 16:10:02 --> Error loading models: Unable to locate the model you have specified: Role_model
ERROR - 2025-10-03 16:10:02 --> Error loading models: Unable to locate the model you have specified: Role_model
ERROR - 2025-10-03 16:12:38 --> Error loading models: Unable to locate the model you have specified: Role_model
ERROR - 2025-10-03 16:14:09 --> Error loading models: Unable to locate the model you have specified: Role_model
ERROR - 2025-10-03 16:14:22 --> Error loading models: Unable to locate the model you have specified: Role_model
ERROR - 2025-10-03 16:16:42 --> Error loading models: Unable to locate the model you have specified: Role_model
ERROR - 2025-10-03 16:18:13 --> Error loading models: Unable to locate the model you have specified: Role_model
ERROR - 2025-10-03 18:35:11 --> Error loading models: Unable to locate the model you have specified: Role_model
