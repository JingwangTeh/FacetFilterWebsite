<?php
	include 'php-commons/database.php';
	include 'php-commons/constants.php';
	include 'php-commons/filters.php';
	include 'php-commons/functions.php';
	
	/* Connect to database */
	$DB_connect = @new mysqli ($db_connect_var['host'], $db_connect_var['username'], $db_connect_var['password'], $db_connect_var['database']);
	/* Database Connection Errors */
	if ($DB_connect->connect_errno > 0){	die();	}
	
	/* get json_encoded api_response from POST, and Decode it into an array */
	$record_array = json_decode($_POST['api_response'], true);
	
	/* API Input Validation */
	$inputValues = array();
	foreach ($dbInfoArray as $dbInfo)
	{
		// isCommaDelimited
		if ($dbInfo[5]){ 
			$inputValues[$dbInfo[1]] = str_replace(' ', '', $record_array[$dbInfo[0]]);
			if (count(explode(',', $inputValues[$dbInfo[1]])) > 5) { 
				$cd_array = explode(',', $inputValues[$dbInfo[1]]);
				$inputValues[$dbInfo[1]] = '';
				$prefix = '';
				for ($cd_array_i = 0; $cd_array_i < 5; $cd_array_i++){
					$inputValues[$dbInfo[1]] .= (empty($prefix))?"":$prefix;
					$inputValues[$dbInfo[1]] .= $cd_array[$cd_array_i];
					$prefix = ',';
				}
			}
		}
		// isDate
		else if ($dbInfo[6]){ $inputValues[$dbInfo[1]] = date('d/m/Y', strtotime( $record_array[$dbInfo[0]] )); }
		// isNum
		else if ($dbInfo[7])
		{
			// default num
			if ( empty($record_array[$dbInfo[0]]) || ($record_array[$dbInfo[0]] == "N/A") )
			{ $inputValues[$dbInfo[1]] = 0; }
			// isNumSpaceSep
			else if ($dbInfo[8])
			{ $inputValues[$dbInfo[1]] = explode(" ", $record_array[$dbInfo[0]])[0]; }
			// isNumCommaSep
			else if ($dbInfo[9])
			{	// get rid of comma
				if( strpos($record_array[$dbInfo[0]], ',') !== false )
				{ $inputValues[$dbInfo[1]] = str_replace(',', '', $record_array[$dbInfo[0]]); }
				else { $inputValues[$dbInfo[1]] = $record_array[$dbInfo[0]]; }
			}
			else { $inputValues[$dbInfo[1]] = $record_array[$dbInfo[0]]; }
		}
		else { $inputValues[$dbInfo[1]] = $record_array[$dbInfo[0]]; }
		
		// intervals
		if ($dbInfo[2])
		{	// isDate
			if ($dbInfo[6]){ $inputValues[$dbInfo[3]] = filter_valueToInterval($dbInfo[4], explode('/', $inputValues[ $dbInfo[1] ])[2]); }
			else { $inputValues[$dbInfo[3]] = filter_valueToInterval($dbInfo[4], $inputValues[ $dbInfo[1] ]); }
		}
	}
	
	/* Query : Insert Query String */
	// Query First Half (Columns)
	$prefix = '';
	$queryString = "INSERT INTO movie (";
	foreach ($inputValues as $key => $value){		
		$queryString .= (empty($prefix))? '':$prefix;
		$queryString .= $key;
		$prefix = ', ';
	}
	$prefix = '';
	$queryString .= ")";
	// Query Second Half (Values)
	$queryString .= " VALUES (";
	foreach ($inputValues as $key => $value){
		$queryString .= (empty($prefix))? '':$prefix;
		$queryString .= '"'. $value . '"';
		$prefix = ', ';
	}
	$queryString .= ")";
	
	// Execute Insert Query
	if ($DB_connect->query($queryString) === TRUE) { echo "true"; }
	else { echo "Error: " . $DB_connect->error;	}
	
	// Close database connection
	$DB_connect->close();
?>