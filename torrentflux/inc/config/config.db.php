<?php

/*******************************************************************************

 LICENSE

 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License (GPL)
 as published by the Free Software Foundation; either version 2
 of the License, or (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 GNU General Public License for more details.

 To read the license please visit http://www.gnu.org/copyleft/gpl.html

*******************************************************************************/

/******************************************************************************/
// YOUR DATABASE CONNECTION INFORMATION
/******************************************************************************/
$cfg["db_type"] = "mysql"; // Database-Type : mysql/sqlite/postgres
$cfg["db_host"] = getenv('DB_HOST'); // Database host computer name or IP
$cfg["db_name"] = getenv('DB_NAME'); // Name of the Database
$cfg["db_user"] = getenv('DB_USER'); // Username for Database
$cfg["db_pass"] = getenv('DB_PASS'); // Password for Database
$cfg["db_pcon"] = false; // Persistent Connection enabled : true/false
/******************************************************************************/

?>