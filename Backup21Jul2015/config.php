<?php

/*
 * examples/mysql/config.php
 * 
 * This file is part of EditableGrid.
 * http://editablegrid.net
 *
 * Copyright (c) 2011 Webismymind SPRL
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://editablegrid.net/license
 */
        

    // Ubuntu Hyper V
	// $db_hostname = '192.168.1.12:3306';
	// $db_username = 'danielgufc_user';
	// $db_password = 'danielgufc_password';
	// $db_database = 'gufcdraws';

// Define here you own values
// $config = array(
	// "db_name" => "gufcweb_player",
	// "db_user" => "gufcweb_dev",
	// "db_password" => "deve!oper",
	// "db_host" => "gungahlinunitedfc.org.au"
	// );                

$config = array(
	"db_name" => "gufcdraws",
	"db_user" => "danielgufc_user",
	"db_password" => "danielgufc_password",
	"db_host" => "192.168.1.12:3306"
	);                
	
error_reporting(E_ALL);
ini_set('display_errors', '1');

?>
