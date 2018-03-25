<?php
	/* Root Directory */
	$dir_root = realpath($_SERVER["DOCUMENT_ROOT"]);
	
	/* Log Directory & File Name */
	$dir_log_folder = "log";
	$logFileName = "movie_log.txt";
	
	$db_connect_var = array(
		'host' => 'localhost',
		'username' => 'adminU',
		'password' => 'adminUser',
		'database' => 'php_assessment_major7'
	);
?>