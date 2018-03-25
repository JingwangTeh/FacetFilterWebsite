<?php
	include 'php-commons/database.php';
	include 'php-commons/constants.php';
	include 'php-commons/filters.php';
	include 'php-commons/functions.php';
	
	/* Connect to database */
	$DB_connect = @new mysqli ($db_connect_var['host'], $db_connect_var['username'], $db_connect_var['password'], $db_connect_var['database']);
	/* Database Connection Errors */
	if ($DB_connect->connect_errno > 0){	die();	}
	
	/* get RefID from POST */
	$refID = (inputNotEmpty($_POST['RefID']))? sanitize_input($_POST['RefID']) : null;
	
	if (!empty($refID)){
		/* Query : Get Details for Record specified by RefID */
		$query = "SELECT ";
		foreach ($dbInfoArray as $dbInfo){
			$query .= (empty($prefix))? '':$prefix;
			$query .= $dbInfo[1];
			$prefix = ', ';
		} $prefix = '';
		$query .= " FROM movie WHERE ".$dbName_searchRefID."='".$refID."'";
		
		if ($pQuery = $DB_connect->prepare($query)){
			$pQuery->execute();
			
			// my_sqli result Object
			$result = $pQuery->get_result();
			// array of records
			$table = $result->fetch_all();
			
			if(!empty($table[0])){ 
			
				$record = array();
				for ($dbi_i = 0; $dbi_i < count($dbInfoArray); $dbi_i++)
				{ $record[$dbInfoArray[$dbi_i][4]] = $table[0][$dbi_i]; }
			
				echo json_encode($record);
			}
			else { echo ""; }
			
			// Close Connection
			$pQuery->free_result();
			$pQuery->close();
		}
	} else { echo ""; }
		
	// Close database connection
	$DB_connect->close();
?>