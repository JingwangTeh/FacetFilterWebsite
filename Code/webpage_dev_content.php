<!-- Process Form Submission -->
<?php
	// Initialize Empty Sticky Fields and Error Values
	foreach ($dbInfoArray as $dbInfo){
		// (Creation Form) Sticky Field Variables, Error Variables, isFacetValid Variables
		${'CForm_'.$dbInfo[4].'_Val'} = '';
		${'CForm_'.$dbInfo[4].'_Err'} = '';
		${'CForm_is'.$dbInfo[4].'Valid'} = false;
		
		// (Update Form) Sticky Field Variables, Error Variables, isFacetValid Variables
		${'UForm_'.$dbInfo[4].'_Val'} = '';
		${'UForm_'.$dbInfo[4].'_Err'} = '';
		${'UForm_is'.$dbInfo[4].'Valid'} = false;
	}
	$UForm_search_Val = $DForm_search_Val = '';
	
	/*
	 * Handle Form Submission (Creation/Update/Delete)
	 */
	$isSubmittedDataValid = true; // if any input has error, submission becomes invalid (false)
	// Creation Form
	if (array_key_exists("creation_form", $_POST))
	{	$text_formType	= 'CForm_';

		// Sanitize & Validate Input
		foreach ($dbInfoArray as $dbInfo)
		{	$text_inputName = $text_formType.$dbInfo[4];
			
			/* Sanitize Input */
			$_POST[$text_inputName] = (inputNotEmpty($_POST[$text_inputName]))?	sanitize_input($_POST[$text_inputName]) : null;
		
			/*
			 * Validate Input, and
			 * If Invalid:
			 * - Set Error Strings
			 * - Set Boolean for Invalid Field
			 */
			// isEmpty
			if (!inputNotEmpty($_POST[$text_inputName])){
				${$text_formType.$dbInfo[4].'_Err'} = "Please fill in the field for ".$dbInfo[4].".";
				$isSubmittedDataValid = false;
			}
			// isCommaDelimited
			else if ($dbInfo[5]){
				$_POST[$text_inputName] = str_replace(' ', '', $_POST[$text_inputName]);
				// Invalid Comma Delimited Text
				if( in_array("", explode(',', $_POST[$text_inputName])) ){
					${$text_formType.$dbInfo[4].'_Err'} = "Please provide a valid comma delimited text";
					$isSubmittedDataValid = false;
				} // Limit comma delimited
				else if (count(explode(',', $_POST[$text_inputName])) > 5){
					${$text_formType.$dbInfo[4].'_Err'} = "Please provide less than or equal to 5 ".$dbInfo[4];
					$isSubmittedDataValid = false;					
				} else { ${$text_formType.'is'.$dbInfo[4].'Valid'} = true; }
			}
			// isDate
			else if ($dbInfo[6])
			{	// Invalid Date Format
				if (count(explode('/', $_POST[$text_inputName])) != 3){
					${$text_formType.$dbInfo[4].'_Err'} = "Date format should be DD/MM/YYYY.";
					$isSubmittedDataValid = false;
				} // Invalid Type
				else if (!is_numeric(explode('/', $_POST[$text_inputName])[1]) || 
						 !is_numeric(explode('/', $_POST[$text_inputName])[0]) || 
						 !is_numeric(explode('/', $_POST[$text_inputName])[2]) ){
					${$text_formType.$dbInfo[4].'_Err'} = "Please provide a valid date.";
					$isSubmittedDataValid = false;
				} // Invalid Date
				else if (! (checkdate( explode('/', $_POST[$text_inputName])[1], 
									   explode('/', $_POST[$text_inputName])[0], 
									   explode('/', $_POST[$text_inputName])[2] )) ){
					${$text_formType.$dbInfo[4].'_Err'} = "Please provide a valid date.";
					$isSubmittedDataValid = false;
				} else { ${$text_formType.'is'.$dbInfo[4].'Valid'} = true; }
			}
			// isNum
			else if ($dbInfo[7])
			{	// isNumeric
				if (is_numeric($_POST[$text_inputName])){
					// isInt
					if ($dbInfo[10]){
						if (filter_var($_POST[$text_inputName], FILTER_VALIDATE_INT) || filter_var($_POST[$text_inputName], FILTER_VALIDATE_INT) === 0){
							// hasRange
							if ($dbInfo[12]){
								// inRange
								if ($_POST[$text_inputName] < $dbInfo[13] || $_POST[$text_inputName] > $dbInfo[14]){
									${$text_formType.$dbInfo[4].'_Err'} = $dbInfo[4]." needs to be within range of ".$dbInfo[13]." to ".$dbInfo[14].".";
									$isSubmittedDataValid = false;
								} else { ${$text_formType.'is'.$dbInfo[4].'Valid'} = true; }
							} // not valid number (isNegative)
							else if ($_POST[$text_inputName] < 0){
								${$text_formType.$dbInfo[4].'_Err'} = $dbInfo[4]." needs to be a positive number.";
								$isSubmittedDataValid = false;
							} else { ${$text_formType.'is'.$dbInfo[4].'Valid'} = true; }
						} // not Int
						else {
							${$text_formType.$dbInfo[4].'_Err'} = $dbInfo[4]." needs to be an integer number.";
							$isSubmittedDataValid = false;
						}
					} // isFloat
					else if ($dbInfo[11]){
						// hasRange
						if ($dbInfo[12]){
							// inRange
							if ($_POST[$text_inputName] < $dbInfo[13] || $_POST[$text_inputName] > $dbInfo[14]){
								${$text_formType.$dbInfo[4].'_Err'} = $dbInfo[4]." needs to be within range of ".$dbInfo[13]." to ".$dbInfo[14].".";
								$isSubmittedDataValid = false;
							} else { ${$text_formType.'is'.$dbInfo[4].'Valid'} = true; }
						} // not valid number (isNegative)
						else if ($_POST[$text_inputName] < 0){
							${$text_formType.$dbInfo[4].'_Err'} = $dbInfo[4]." needs to be a positive number.";
							$isSubmittedDataValid = false;
						} else { ${$text_formType.'is'.$dbInfo[4].'Valid'} = true; }
					} else { ${$text_formType.'is'.$dbInfo[4].'Valid'} = true; }
				} // not a number
				else {
					${$text_formType.$dbInfo[4].'_Err'} = $dbInfo[4]." is not a number.";
					$isSubmittedDataValid = false;
				}
			} else { ${$text_formType.'is'.$dbInfo[4].'Valid'} = true; }
		}
		
		/*
		 * Store entries and log into file if no errors
		 */
		if ($isSubmittedDataValid)
		{	/*
			 * Store Entries
			 */
			
		 	// Connect to database
			$DB_connect = @new mysqli ($db_connect_var['host'], $db_connect_var['username'], $db_connect_var['password'], $db_connect_var['database']);
			// Database Connection Errors
			if ($DB_connect->connect_errno > 0){	die();	}
			
			/* Query : Insert Query String */
			// Query First Half (Columns)
			$prefix = '';
			$queryString = "INSERT INTO movie (";
			foreach ($dbInfoArray as $dbInfo){
				$queryString .= (empty($prefix))? '':$prefix;
				$queryString .= $dbInfo[1];
				$prefix = ', ';
			}
			$prefix = '';
			$queryString .= ")";
			// Query Second Half (Values)
			$queryString .= " VALUES (";
			foreach ($dbInfoArray as $dbInfo){
				$queryString .= (empty($prefix))? '':$prefix;
				$queryString .= '"'. $_POST[$text_formType.$dbInfo[4]] . '"';
				$prefix = ', ';
			}
			$queryString .= ")";
			
			// Execute Insert Query
			if ($DB_connect->query($queryString) === TRUE){
				// update intervals for the record specified by imdbRefID
				db_updateIntervals($_POST[$text_formType.$dbInfoArray[1][4]]);
				
				/*
				 * Store Log
				 * Format : operation type, formValid & formInvalid, fieldsValid & fieldsInvalid
				 */

				// Set Log Values
				$log_time = date("Y-m-d");
				$log_operationType = "insert";
				$log_formValid = "true";
				
				// Store Log
				$prefix = '';
				$logString = $log_time .','. $log_operationType .','. $log_formValid .',';
				foreach ($dbInfoArray as $dbInfo){ 
					$logString .= empty($prefix)?'':$prefix;
					$logString .= 'true';
					$prefix = ',';
				}
				
				// add in a new log for SUCCESSFUL form submission into log file
				is_dir($dir_root.'/'.$dir_log_folder) || @mkdir($dir_root.'/'.$dir_log_folder) || die("Can't Create folder");
				file_put_contents($dir_root.'/'.$dir_log_folder.'/'.$logFileName, stripslashes($logString)."\r\n", FILE_APPEND);
				
				$globalMsg = "<span class='success'>Creation Form Submission Completed</span>";
			} else {
				$globalMsg = "<span class='fail'>Creation Form Submission Incomplete - Database Insert Error (".$DB_connect->error.")</span>";

				// Update Sticky Fields with error messages for user input
				foreach ($dbInfoArray as $dbInfo)
				{	$text_inputName = $text_formType.$dbInfo[4];
					${$text_formType.$dbInfo[4].'_Val'}	= (${$text_formType.'is'.$dbInfo[4].'Valid'})?	$_POST[$text_inputName]: '';
				}
			}
			
			// Close database connection
			$DB_connect->close();
		}
		else
		{
			// Update Sticky Fields with error messages for user input
			foreach ($dbInfoArray as $dbInfo)
			{	$text_inputName = $text_formType.$dbInfo[4];
				${$text_formType.$dbInfo[4].'_Val'}	= (${$text_formType.'is'.$dbInfo[4].'Valid'})?	$_POST[$text_inputName]: '';
			}
			
			// Set Log Values
			$log_time = date("Y-m-d");
			$log_operationType = "insert";
			$log_formValid = "false";
			
			// Store Log
			$prefix = '';
			$logString = $log_time .','. $log_operationType .','. $log_formValid .',';
			foreach ($dbInfoArray as $dbInfo){ 
				$logString .= empty($prefix)?'':$prefix;
				$logString .= (${$text_formType.'is'.$dbInfo[4].'Valid'})? 'true':'false';
				$prefix = ',';
			}
			
			// add in a new log for UNSUCCESSFUL form submission into log file
			is_dir($dir_root.'/'.$dir_log_folder) || @mkdir($dir_root.'/'.$dir_log_folder) || die("Can't Create folder");
			file_put_contents($dir_root.'/'.$dir_log_folder.'/'.$logFileName, stripslashes($logString)."\r\n", FILE_APPEND);
			
			$globalMsg = "<span class='fail'>Creation Form Submission Invalid</span>";
		}
	}
	// Update Form
	else if (array_key_exists("update_form", $_POST))
	{	$text_formType	= 'UForm_';

		// Sanitize & Validate Input
		foreach ($dbInfoArray as $dbInfo)
		{	$text_inputName = $text_formType.$dbInfo[4];
			
			/* Sanitize Input */
			$_POST[$text_inputName] = (inputNotEmpty($_POST[$text_inputName]))?	sanitize_input($_POST[$text_inputName]) : null;
		
			/*
			 * Validate Input, and
			 * If Invalid:
			 * - Set Error Strings
			 * - Set Boolean for Invalid Field
			 */
			// isEmpty
			if (!inputNotEmpty($_POST[$text_inputName])){
				${$text_formType.$dbInfo[4].'_Err'} = "Please fill in the field for ".$dbInfo[4].".";
				$isSubmittedDataValid = false;
			}
			// isCommaDelimited
			else if ($dbInfo[5]){
				$_POST[$text_inputName] = str_replace(' ', '', $_POST[$text_inputName]);
				// Invalid Comma Delimited Text
				if( in_array("", explode(',', $_POST[$text_inputName])) ){
					${$text_formType.$dbInfo[4].'_Err'} = "Please provide a valid comma delimited text";
					$isSubmittedDataValid = false;
				} // Limit comma delimited
				else if (count(explode(',', $_POST[$text_inputName])) > 5){
					${$text_formType.$dbInfo[4].'_Err'} = "Please provide less than or equal to 5 ".$dbInfo[4];
					$isSubmittedDataValid = false;					
				} else { ${$text_formType.'is'.$dbInfo[4].'Valid'} = true; }
			}
			// isDate
			else if ($dbInfo[6])
			{	// Invalid Date Format
				if (count(explode('/', $_POST[$text_inputName])) != 3){
					${$text_formType.$dbInfo[4].'_Err'} = "Date format should be DD/MM/YYYY.";
					$isSubmittedDataValid = false;
				} // Invalid Type
				else if (!is_numeric(explode('/', $_POST[$text_inputName])[1]) || 
						 !is_numeric(explode('/', $_POST[$text_inputName])[0]) || 
						 !is_numeric(explode('/', $_POST[$text_inputName])[2]) ){
					${$text_formType.$dbInfo[4].'_Err'} = "Please provide a valid date.";
					$isSubmittedDataValid = false;
				} // Invalid Date
				else if (! (checkdate( explode('/', $_POST[$text_inputName])[1], 
									   explode('/', $_POST[$text_inputName])[0], 
									   explode('/', $_POST[$text_inputName])[2] )) ){
					${$text_formType.$dbInfo[4].'_Err'} = "Please provide a valid date.";
					$isSubmittedDataValid = false;
				} else { ${$text_formType.'is'.$dbInfo[4].'Valid'} = true; }
			}
			// isNum
			else if ($dbInfo[7])
			{	// isNumeric
				if (is_numeric($_POST[$text_inputName])){
					// isInt
					if ($dbInfo[10]){
						if (filter_var($_POST[$text_inputName], FILTER_VALIDATE_INT) || filter_var($_POST[$text_inputName], FILTER_VALIDATE_INT) === 0){
							// hasRange
							if ($dbInfo[12]){
								// inRange
								if ($_POST[$text_inputName] < $dbInfo[13] || $_POST[$text_inputName] > $dbInfo[14]){
									${$text_formType.$dbInfo[4].'_Err'} = $dbInfo[4]." needs to be within range of ".$dbInfo[13]." to ".$dbInfo[14].".";
									$isSubmittedDataValid = false;
								} else { ${$text_formType.'is'.$dbInfo[4].'Valid'} = true; }
							} // not valid number (isNegative)
							else if ($_POST[$text_inputName] < 0){
								${$text_formType.$dbInfo[4].'_Err'} = $dbInfo[4]." needs to be a positive number.";
								$isSubmittedDataValid = false;
							} else { ${$text_formType.'is'.$dbInfo[4].'Valid'} = true; }
						} // not Int
						else {
							${$text_formType.$dbInfo[4].'_Err'} = $dbInfo[4]." needs to be an integer number.";
							$isSubmittedDataValid = false;
						}
					} // isFloat
					else if ($dbInfo[11]){
						// hasRange
						if ($dbInfo[12]){
							// inRange
							if ($_POST[$text_inputName] < $dbInfo[13] || $_POST[$text_inputName] > $dbInfo[14]){
								${$text_formType.$dbInfo[4].'_Err'} = $dbInfo[4]." needs to be within range of ".$dbInfo[13]." to ".$dbInfo[14].".";
								$isSubmittedDataValid = false;
							} else { ${$text_formType.'is'.$dbInfo[4].'Valid'} = true; }
						} // not valid number (isNegative)
						else if ($_POST[$text_inputName] < 0){
							${$text_formType.$dbInfo[4].'_Err'} = $dbInfo[4]." needs to be a positive number.";
							$isSubmittedDataValid = false;
						} else { ${$text_formType.'is'.$dbInfo[4].'Valid'} = true; }
					} else { ${$text_formType.'is'.$dbInfo[4].'Valid'} = true; }
				} // not a number
				else {
					${$text_formType.$dbInfo[4].'_Err'} = $dbInfo[4]." is not a number.";
					$isSubmittedDataValid = false;
				}
			} else { ${$text_formType.'is'.$dbInfo[4].'Valid'} = true; }
		}
		
		/*
		 * Update record and log into file if no errors
		 */
		$_POST['UForm_search'] = (inputNotEmpty($_POST['UForm_search']))?	sanitize_input($_POST['UForm_search']) : null;
		if ($isSubmittedDataValid)
		{	/*
			 * Update Record
			 */
			
		 	// Connect to database
			$DB_connect = @new mysqli ($db_connect_var['host'], $db_connect_var['username'], $db_connect_var['password'], $db_connect_var['database']);
			// Database Connection Errors
			if ($DB_connect->connect_errno > 0){	die();	}
			
			if (inputNotEmpty($_POST['UForm_search'])){
				/* Query : Select Record by IMDbRefID to check if it exists */
				$query = "SELECT * FROM movie WHERE ".$dbName_searchRefID."='".$_POST['UForm_search']."'";;
				if ($pQuery = $DB_connect->prepare($query)){
					$pQuery->execute();
				
					// my_sqli result Object
					$result = $pQuery->get_result();
					// array of records
					$table = $result->fetch_all();
				
					if (!empty($table)){
						/* Query : Update Query String */
						$prefix = '';
						$queryString = "UPDATE movie SET ";
						foreach ($dbInfoArray as $dbInfo){
							$queryString .= (empty($prefix))? '':$prefix;
							$queryString .= $dbInfo[1]."='".$_POST[$text_formType.$dbInfo[4]]."'";
							$prefix = ', ';
						}
						$queryString .= " WHERE ".$dbName_searchRefID."='".$_POST['UForm_search']."'";

						// Execute Update Query
						if ($DB_connect->query($queryString) === TRUE){
							// update intervals for the record specified by imdbRefID
							db_updateIntervals($_POST[$text_formType.$dbInfoArray[1][4]]);
				
							/*
							 * Store Log
							 * Format : operation type, formValid & formInvalid, fieldsValid & fieldsInvalid
							 */

							// Set Log Values
							$log_time = date("Y-m-d");
							$log_operationType = "update";
							$log_formValid = "true";
							
							// Store Log
							$prefix = '';
							$logString = $log_time .','. $log_operationType .','. $log_formValid .',';
							foreach ($dbInfoArray as $dbInfo){ 
								$logString .= empty($prefix)?'':$prefix;
								$logString .= 'true';
								$prefix = ',';
							}
							
							// add in a new log for SUCCESSFUL form submission into log file
							is_dir($dir_root.'/'.$dir_log_folder) || @mkdir($dir_root.'/'.$dir_log_folder) || die("Can't Create folder");
							file_put_contents($dir_root.'/'.$dir_log_folder.'/'.$logFileName, stripslashes($logString)."\r\n", FILE_APPEND);
							
							$globalMsg = "<span class='success'>Update Form Submission Completed</span>";
						} else {
							$globalMsg = "<span class='fail'>Update Form Submission Incomplete - Database Update Error (".$DB_connect->error.")</span>";

							// Update Sticky Fields with error messages for user input
							foreach ($dbInfoArray as $dbInfo)
							{	$text_inputName = $text_formType.$dbInfo[4];
								${$text_formType.$dbInfo[4].'_Val'}	= (${$text_formType.'is'.$dbInfo[4].'Valid'})?	$_POST[$text_inputName]: '';
							} $UForm_search_Val = $_POST['UForm_search'];
						}
					} // No Record to Delete
					else {
						$globalMsg = "<span class='fail'>Update Form Incomplete - Target Record to Update Does Not Exist</span>";

						// Update Sticky Fields with error messages for user input
						foreach ($dbInfoArray as $dbInfo)
						{	$text_inputName = $text_formType.$dbInfo[4];
							${$text_formType.$dbInfo[4].'_Val'}	= (${$text_formType.'is'.$dbInfo[4].'Valid'})?	$_POST[$text_inputName]: '';
						} $UForm_search_Val = $_POST['UForm_search'];
					}
				}
			} // Target Record not Specified
			else {
				$globalMsg = "<span class='fail'>Update Form Submission Incomplete - Missing Detail(RefID) for Target Record to Update</span>";

				// Update Sticky Fields with error messages for user input
				foreach ($dbInfoArray as $dbInfo)
				{	$text_inputName = $text_formType.$dbInfo[4];
					${$text_formType.$dbInfo[4].'_Val'}	= (${$text_formType.'is'.$dbInfo[4].'Valid'})?	$_POST[$text_inputName]: '';
				} $UForm_search_Val = $_POST['UForm_search'];
			}

			// Close database connection
			$DB_connect->close();
		}
		else
		{
			// Update Sticky Fields with error messages for user input
			foreach ($dbInfoArray as $dbInfo)
			{	$text_inputName = $text_formType.$dbInfo[4];
				${$text_formType.$dbInfo[4].'_Val'}	= (${$text_formType.'is'.$dbInfo[4].'Valid'})?	$_POST[$text_inputName]: '';
			} $UForm_search_Val = $_POST['UForm_search'];
			
			// Set Log Values
			$log_time = date("Y-m-d");
			$log_operationType = "update";
			$log_formValid = "false";
			
			// Store Log
			$prefix = '';
			$logString = $log_time .','. $log_operationType .','. $log_formValid .',';
			foreach ($dbInfoArray as $dbInfo){ 
				$logString .= empty($prefix)?'':$prefix;
				$logString .= (${$text_formType.'is'.$dbInfo[4].'Valid'})? 'true':'false';
				$prefix = ',';
			}
			
			// add in a new log for UNSUCCESSFUL form submission into log file
			is_dir($dir_root.'/'.$dir_log_folder) || @mkdir($dir_root.'/'.$dir_log_folder) || die("Can't Create folder");
			file_put_contents($dir_root.'/'.$dir_log_folder.'/'.$logFileName, stripslashes($logString)."\r\n", FILE_APPEND);
			
			$globalMsg = "<span class='fail'>Update Form Submission Invalid</span>";
		}
	}
	// Delete Form
	else if (array_key_exists("delete_form", $_POST))
	{	$text_formType	= 'DForm_';

		// Sanitize Input
		$_POST['DForm_search'] = (inputNotEmpty($_POST['DForm_search']))?	sanitize_input($_POST['DForm_search']) : null;
		
		/*
		 * Delete record and log into file if no errors
		 */

		// Connect to database
		$DB_connect = @new mysqli ($db_connect_var['host'], $db_connect_var['username'], $db_connect_var['password'], $db_connect_var['database']);
		// Database Connection Errors
		if ($DB_connect->connect_errno > 0){	die();	}
		
		if (inputNotEmpty($_POST['DForm_search']))
		{
			/* Query : Select Record by IMDbRefID to check if it exists */
			$query = "SELECT * FROM movie WHERE ".$dbName_searchRefID."='".$_POST['DForm_search']."'";;
			if ($pQuery = $DB_connect->prepare($query)){
				$pQuery->execute();
			
				// my_sqli result Object
				$result = $pQuery->get_result();
				// array of records
				$table = $result->fetch_all();
			
				if (!empty($table)){
					/* Query : Delete Query String */
					$prefix = '';
					$queryString = "DELETE FROM movie WHERE ";
					$queryString .= $dbName_searchRefID."='".$_POST['DForm_search']."'";

					// Execute Update Query
					if ($DB_connect->query($queryString) === TRUE){
						/*
						 * Store Log
						 * Format : operation type, formValid & formInvalid, fieldsValid & fieldsInvalid
						 */

						// Set Log Values
						$log_time = date("Y-m-d");
						$log_operationType = "delete";
						$log_formValid = "true";
						
						// Store Log
						$prefix = '';
						$logString = $log_time .','. $log_operationType .','. $log_formValid .',';
						foreach ($dbInfoArray as $dbInfo){ 
							$logString .= empty($prefix)?'':$prefix;
							$logString .= 'true';
							$prefix = ',';
						}
						
						// add in a new log for SUCCESSFUL form submission into log file
						is_dir($dir_root.'/'.$dir_log_folder) || @mkdir($dir_root.'/'.$dir_log_folder) || die("Can't Create folder");
						file_put_contents($dir_root.'/'.$dir_log_folder.'/'.$logFileName, stripslashes($logString)."\r\n", FILE_APPEND);
						
						$globalMsg = "<span class='success'>Delete Form Submission Completed</span>";
					} else {
						$globalMsg = "<span class='fail'>Delete Form Submission Incomplete - Database Delete Error (".$DB_connect->error.")</span>";
						$DForm_search_Val = $_POST['DForm_search'];
					}
				} // No Record to Delete
				else {
					$globalMsg = "<span class='fail'>Delete Form Incomplete - Target Record to Delete Does Not Exist</span>";
					$DForm_search_Val = $_POST['DForm_search'];
				}
			}
		} // Target Record not Specified
		else {
			$globalMsg = "<span class='fail'>Delete Form Incomplete - Missing Detail(RefID) for Target Record to Delete</span>";
			$DForm_search_Val = $_POST['DForm_search'];
		}

		// Close database connection
		$DB_connect->close();
	}
?>

<!-- read from log file to get summary of log contents -->
<?php

	// initial form summary values
	$logCount = $formValid = $formInvalid = 0;
	// initial operation type summary values
	$insertCount = $updateCount = $deleteCount = 0;
	// valid & invalid field summary values
	foreach ($dbInfoArray as $dbInfo){
		${$dbInfo[4].'_validCount'} = 0;
		${$dbInfo[4].'_invalidCount'} = 0;
	}
	
	// update form and field summary values if log exists (no log if no previous form submission)
	if (file_exists($dir_log_folder.'/'.$logFileName))
	{	// get log file details
		$logFile = file($dir_log_folder.'/'.$logFileName, FILE_IGNORE_NEW_LINES);
		
		// get log summary details
		foreach ($logFile as $logFileLine){
			if (!empty($logFileLine)){
				$logFileLineArray = explode(',', $logFileLine);
				
				if ( (count($logFileLineArray)-3) == count($dbInfoArray)){
					if (count(explode('-', $logFileLineArray[0])) == 3){
						if ( (checkdate( explode('-', $logFileLineArray[0])[1], 
										 explode('-', $logFileLineArray[0])[2], 
										 explode('-', $logFileLineArray[0])[0] )) )
						{
							// look into each logFileLineArray Element
							// constraint: <= 7 days
							$daysDiff = (new DateTime($logFileLineArray[0]))->diff(new DateTime());
							if ($daysDiff->days <= 7){
							
								if ($logFileLineArray[1] == 'insert')		$insertCount++;
								else if ($logFileLineArray[1] == 'update')	$updateCount++;
								else if ($logFileLineArray[1] == 'delete')	$deleteCount++;
								($logFileLineArray[2] === 'true')?			$formValid++ 				: $formInvalid++;
								// update valid & invalid counts
								for ($lfl_i = 3; $lfl_i < count($logFileLineArray); $lfl_i++){
									($logFileLineArray[$lfl_i] === 'true')? 
									${$dbInfoArray[$lfl_i-3][4].'_validCount'}++ : ${$dbInfoArray[$lfl_i-3][4].'_invalidCount'}++;
								}
								
								// update form count
								$logCount++;
							}
						}
					}
				}
			} // end of summary details
		} // log exists but empty content
	} //else { echo $dir_log_folder.'/'.$logFileName; }

?>

<!-- ------------------------------Top Content Section------------------------------ -->
<div class="top_content"><div id="top_content_msg"><?= empty($globalMsg)?"":$globalMsg?></div></div>

<!-- ------------------------------Content Section------------------------------ -->
<div class="content">

	<!-- ------------------------------Left Content Section------------------------------ -->
	<div class="left_content">
		<div>
			<!-- Navigation Tabs -->
			<nav class="nav_devTabs">
				<button onclick="tabSelect('right_content_summary', 'devTabs')">Summary</button>
				<button onclick="tabSelect('right_content_log', 'devTabs')">Log</button>
				<button onclick="tabSelect('right_content_form', 'devTabs')">CUD Form</button>
				<button onclick="tabSelect('right_content_api', 'devTabs')">API Form</button>
			</nav>
		</div>
	</div>
	<!-- End of Left Content Section -->
	
	<!-- ------------------------------Right Content Section------------------------------ -->
	<div class="right_content">
		
		<div class="devTabs" id="right_content_summary">
			<!-- Summary Title -->
			<div class="right_content_title"><h1>Log Summary</h1></div>
			
			<!-- Tables for Form Summary, Operation Types, Field Valid Summary, and Field Invalid Summary -->
			<div id="right_content_summary_content">
				<table>	<tr>	<th colspan="2">Forms Processed</th>								</tr>
						<tr>	<td>Total Forms</td>		<td><?=$logCount?></td>					</tr>
						<tr>	<td>Valid Forms</td>		<td><?=$formValid?></td>				</tr>
						<tr>	<td>Invalid Forms</td>		<td><?=$formInvalid?></td>				</tr>	</table>
						
				<table>	<tr>	<th colspan="2">Operation Types</th>								</tr>
						<tr>	<td>Creation</td>			<td><?=$insertCount?></td>				</tr>
						<tr>	<td>Update</td>				<td><?=$updateCount?></td>				</tr>
						<tr>	<td>Delete</td>				<td><?=$deleteCount?></td>				</tr>	</table>
				
				<table>	<tr>	<th colspan="2">Valid Fields</th>									</tr><?php
				foreach ($dbInfoArray as $dbInfo)
				{ echo "<tr>	<td>$dbInfo[4]</td>		<td>".${$dbInfo[4].'_validCount'}."</td>	</tr>"; }
				?></table>

				<table>	<tr>	<th colspan="2">Invalid Fields</th>									</tr><?php
				foreach ($dbInfoArray as $dbInfo)
				{ echo "<tr>	<td>$dbInfo[4]</td>		<td>".${$dbInfo[4].'_invalidCount'}."</td>	</tr>"; }
				?></table>
			</div>
		</div>
	
		<div class="devTabs" id="right_content_log">
			<!-- Log Title -->
			<div class="right_content_title">
				<h1>Log Details</h1>
				
				<!-- Download Log Form -->
				<div id="right_content_download_log">
					<form action="dev_downloadLog.php" method="POST">
						<select name="fileToDownload">
							<option value="<?php echo $logFileName ?>"> <?php echo $logFileName ?> </option>
						</select>
						
						<input type="submit" value="Download"/>
					</form>
				</div>
			</div>
			
			<!-- Table for Log File Entries -->
			<div class="shortenDiv" id="right_content_log_FileDetails" onclick="toggleDisplayHorizontal('right_content_log_FileDetails')">
			
				<!-- display log file details -->
				<?php
				echo "<table>";
					echo "<tr>	
							<th>Log No</th>			<th>Log Date</th>		<th>Operation Type</th>	<th>Form Valid</th>
							<th>Title</th>			<th>imdbRefID</th>		<th>Released Date</th>	
							<th>Type</th>			<th>Rating</th>			<th>Genre</th>
							<th>Total Season</th>	<th>Duration</th>		<th>Language</th>		<th>Country</th>
							<th>MetaScore</th>		<th>imdb Rating</th>	<th>imdb Votes</th>
						  </tr>";

					if (file_exists($dir_log_folder.'/'.$logFileName))
					{	// get log file details
						$logFile = file($dir_log_folder.'/'.$logFileName, FILE_IGNORE_NEW_LINES);
						
						// read each line in log file
						$logFileLineCount = 0;
						foreach ($logFile as $logFileLine){
							if (!empty($logFileLine)){
								$logFileLineArray = explode(',', $logFileLine);
								
								if ( count($logFileLineArray) == 16){
									if (count(explode('-', $logFileLineArray[0])) == 3){
										if ( (checkdate( explode('-', $logFileLineArray[0])[1], 
														 explode('-', $logFileLineArray[0])[2], 
														 explode('-', $logFileLineArray[0])[0] )) )
										{
											// look into each logFileLineArray Element
											// constraint: <= 7 days
											$daysDiff = (new DateTime($logFileLineArray[0]))->diff(new DateTime());
											if ($daysDiff->days <= 7){
												$logFileLineCount++;
												echo "<tr>";
													echo "<td>$logFileLineCount</td>";
													foreach ($logFileLineArray as $logFileLineElement){
														if ($logFileLineElement == 'false') { 
															echo "<td style='background-color: pink;'>$logFileLineElement</td>";
														} else { echo "<td>$logFileLineElement</td>"; }
													}
												echo "</tr>";
											}
										}
									}
								}
							} // end of summary details
						} // log exists but empty content
						if ($logFileLineCount === 0){ echo "<tr><td colspan='17'>No Log Content</td></tr>"; }
					} else { echo "<tr><td colspan='17'>No Log</td></tr>"; }
				echo "</table>";
				?>
				
			</div>
		</div>
		
		<!-- Form -->
		<div class="devTabs" id="right_content_form">
		
			<!-- Form Title -->
			<div class="right_content_title"><h1>Creation/Update/Delete Form</h1></div>
			
			<!-- Form Tabs -->
			<div class="nav_formTabs">
				<button class="formTabs_btn formTabs_btn_active" onclick="tabSelect('right_content_creation_form', 'formTabs', this, 'formTabs_btn', true, 'formTabs_btn_active')">Creation Form<hr/></button>
				<button class="formTabs_btn" onclick="tabSelect('right_content_update_form', 'formTabs', this, 'formTabs_btn', true, 'formTabs_btn_active')">Update Form<hr/></button>
				<button class="formTabs_btn" onclick="tabSelect('right_content_delete_form', 'formTabs', this, 'formTabs_btn', true, 'formTabs_btn_active')">Delete Form<hr/></button>
			</div>

			<!-- Form Content (Creation/Update/Delete Forms) -->
			<div id="right_content_forms">
			
				<!-- Creation Form Div -->
				<div class="formTabs" id="right_content_creation_form">
					<!-- Creation Form Content -->
					<div id="creation_form_content">
					
						<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
							<fieldset>
								<legend id="creation_form_legend">Creation Form</legend>
								
								<div id="creation_form_inputs_container">
								<?php
								foreach ($dbInfoArray as $dbInfo)
								{
									$text_label		= $dbInfo[4];
									$text_inputName	= 'CForm_'.$dbInfo[4];
									$text_inputID	= $text_inputName;
									$text_spanID	= 'CForm_'.$dbInfo[4].'_Error';
									$val_input		= inputNotEmpty(${'CForm_'.$dbInfo[4].'_Val'})?${'CForm_'.$dbInfo[4].'_Val'}:'';
									$val_span		= (!empty(${'CForm_'.$dbInfo[4].'_Err'}))?${'CForm_'.$dbInfo[4].'_Err'}:'';
									
									echo "<div class='creation_form_inputs_section'>";
									
										echo "<label for='$text_inputID'>$text_label</label><br/>";
										echo "<input type='text' name='$text_inputName' id='$text_inputID' value='".$val_input."' />";
										echo "<span id='$text_spanID'>",$val_span,"</span>";
									
									echo "</div>";
								}
								?>									
								</div>

								<!-- Reset & Submit -->
								<div class="forms_btns_container">
									<div><a class="forms_btns resetLink" href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">Reset</a></div>
									<div><input type="submit" class="forms_btns formSubmitBtn" id="" value="Create Record"/></div>
									<input type="hidden" name="creation_form"  value="creation_form"/>
								</div>
							</fieldset>
						</form>
						
					</div>
				</div>
			
				<!-- Update Form Div -->
				<div class="formTabs" id="right_content_update_form">
					<!-- Update Form Content -->
					<div id="update_form_content">
					
						<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" 
							  onkeypress="return event.keyCode != 13;">
							<fieldset>
								<legend id="update_form_legend">Update Form</legend>
							
								<div id="update_form_search">
									<label for="UForm_search">Target Record</label><br/>
									<input type="text" id="UForm_search" name="UForm_search" 
										   placeholder="Search by IMDbRefID" value="<?=$UForm_search_Val?>" 
										   onkeyup="getData_FormAutoComplete('UForm_search','autocomplete_u')" 
										   onchange="getData_FormData('u')"><br/>
									<button type="button" onclick="getData_FormData('u')">Search</button>
									<div class="autocomplete_popup" id="autocomplete_u" 
										 onmouseleave="popup_disappear('autocomplete_u')" onclick="popup_disappear('autocomplete_u')"></div>
								</div>
							
								<div id="update_form_inputs_container">
								<?php
								foreach ($dbInfoArray as $dbInfo)
								{
									$text_label		= $dbInfo[4];
									$text_inputName	= 'UForm_'.$dbInfo[4];
									$text_inputID	= $text_inputName;
									$text_spanID	= 'UForm_'.$dbInfo[4].'_Error';
									$val_input		= inputNotEmpty(${'UForm_'.$dbInfo[4].'_Val'})?${'UForm_'.$dbInfo[4].'_Val'}:'';
									$val_span		= (!empty(${'UForm_'.$dbInfo[4].'_Err'}))?${'UForm_'.$dbInfo[4].'_Err'}:'';
									
									echo "<div class='update_form_inputs_section'>";
									
										echo "<label for='$text_inputID'>$text_label</label><br/>";
										echo "<input type='text' name='$text_inputName' id='$text_inputID' value='".$val_input."' />";
										echo "<span id='$text_spanID'>",$val_span,"</span>";
									
									echo "</div>";
								}
								?>
								</div>
								
								<!-- Reset & Submit -->
								<div class="forms_btns_container">
									<div><a class="forms_btns resetLink" href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">Reset</a></div>
									<div><input class="forms_btns formSubmitBtn" type="submit" value="Update Record"/></div>
									<input type="hidden" name="update_form"  value="update_form"/>
								</div>
							</fieldset>
						</form>
						
					</div>
				</div>
			
				<!-- Delete Form Div -->
				<div class="formTabs" id="right_content_delete_form">
					<!-- Delete Form Content -->
					<div id="delete_form_content">
					
						<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" 
							  onkeypress="return event.keyCode != 13;">
							<fieldset>
								<legend id="delete_form_legend">Delete Form</legend>
								
								<div id="delete_form_search">
									<label for="DForm_search">Target Record</label><br/>
									<input type="text" id="DForm_search" name="DForm_search" 
										   placeholder="Search by IMDbRefID" value="<?=$DForm_search_Val?>" 
										   onkeyup="getData_FormAutoComplete('DForm_search','autocomplete_d')" 
										   onchange="getData_FormData('d')"><br/>
									<button type="button" onclick="getData_FormData('d')">Search</button>
									<div class="autocomplete_popup" id="autocomplete_d" 
										 onmouseleave="popup_disappear('autocomplete_d')" onclick="popup_disappear('autocomplete_d')"></div>
								</div>
								
								<div id="delete_form_inputs_container">
								<?php
								foreach ($dbInfoArray as $dbInfo)
								{	$text_inputID	= 'DForm_'.$dbInfo[4];
									
									echo "<div class='delete_form_inputs_section'>";
										echo "<div id='$text_inputID'></div>";
									echo "</div>";
								}
								?>
								</div>
								
								<!-- Reset & Submit -->
								<div class="forms_btns_container">
									<div><a class="forms_btns resetLink" href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">Reset</a></div>
									<div><input class="forms_btns formSubmitBtn" type="submit" value="Delete Record"/></div>
									<input type="hidden" name="delete_form" value="delete_form"/>
								</div>
							</fieldset>
						</form>
						
					</div>
				</div>
				
			</div> <!-- End of Form Content (Creation/Update/Delete Forms) -->
		</div> <!-- End of Right Content Form -->
		
		<!-- API Form (OMDb API Form) -->
		<div class="devTabs" id="right_content_api">
			<!-- API Form Title -->
			<div class="right_content_title"><h1>API Form</h1></div>
			
			<!-- Form to get record information from OMDB API -->
			<div id="right_content_api_form">
			
				<form action="http://www.omdbapi.com/" method="GET" onsubmit="return false;">
					<fieldset>
						<legend id="api_form_legend">Search Using IMDb ID</legend>
						
						<div id="api_form_inputs_container">
							<label for="i">ID:</label>
							<input type="text" id="i" name="i" placeholder="IMDb ID">
							&nbsp;&nbsp;
							
							<label>Plot:</label>
							<select name="plot" style="width: 100px;">
								<option value="" selected="">Short</option>
								<option value="full">Full</option>
							</select>
							&nbsp;&nbsp;
							
							<label>Response:</label>
							<select name="r" style="width: 100px;">
								<option value="">JSON</option>
								<option value="xml">XML</option>
							</select>
							&nbsp;&nbsp;
							
							<button type="button" onclick="getData_OMDbAPI()">Search</button>
							<button type="reset">Reset</button>
						</div>
					</fieldset>
				</form>
				
			</div>
		</div>
	
	</div>
	<!-- End of Right Content Section -->
	
</div>
<!-- End of Content Section -->
