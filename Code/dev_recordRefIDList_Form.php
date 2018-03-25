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
	// get json_encoded userSelections from POST, and Decode it into an array
	if (array_key_exists('userSelections', $_POST)){ $userSelections_array = json_decode($_POST['userSelections'], true); }
	
	if (!empty($refID)){
		if (empty($userSelections_array)){
			/* Query : Get Details for Record specified by RefID */
			$query = "SELECT ";
			foreach ($dbInfoArray as $dbInfo){
				$query .= (empty($prefix))? '':$prefix;
				$query .= $dbInfo[1];
				$prefix = ', ';
			} $prefix = '';
			$query .= " FROM movie WHERE ".$dbName_searchRefID." LIKE '%".$refID."%'"." LIMIT 10";
			
			if ($pQuery = $DB_connect->prepare($query)){
				$pQuery->execute();
				
				// my_sqli result Object
				$result = $pQuery->get_result();
				// array of records
				$table = $result->fetch_all();
				
				if(count($table) > 0){
					$listOfID = array();
					foreach($table as $record)
					{ $listOfID[] = $record[1]; }
					
					echo json_encode($listOfID);
				} else { echo ""; }
				
				// Close Connection
				$pQuery->free_result();
				$pQuery->close();
			}
		}
		else if (!empty($userSelections_array))
		{	/*
			 * standardize super and sub arrays into a single array (sub)
			 */
			foreach ($userSelections_array as $section => $array_of_super_or_sub_or_select_group)
			{	// in each section, find which section it is so that the correct facetName can be provided
				$currentFacetName = section_facetName($section);
				// count whether there is only select/super/sub, or there are both super and sub
				$super_or_sub_or_select_groupKeys = array_keys($array_of_super_or_sub_or_select_group);
				
				// get corresponding intervals for each category and add it to the sub name's array
				if (count($super_or_sub_or_select_groupKeys) == 1) {
					if (strpos($super_or_sub_or_select_groupKeys[0], "super")) {
						// get corresponding sub name of super
						$string_sub = str_replace("super","sub",$super_or_sub_or_select_groupKeys[0]);
						
						// get all categories selected by user
						foreach($userSelections_array[$section][$super_or_sub_or_select_groupKeys[0]] as $category)
						{
							// get all intervals of each category
							$selectedIntervals = filter_classificationToInterval($currentFacetName, $category);
							// add intervals to sub
							foreach($selectedIntervals as $interval){ $userSelections_array[$section][$string_sub][] = $interval; }
						}
						// delete current category array
						unset($userSelections_array[$section][$super_or_sub_or_select_groupKeys[0]]);
					}
				}
				else if (count($super_or_sub_or_select_groupKeys) == 2) {
					if (strpos($super_or_sub_or_select_groupKeys[0], "super")) {
						// get corresponding sub name of super
						$string_sub = str_replace("super","sub",$super_or_sub_or_select_groupKeys[0]);
						
						// get all categories selected by user
						foreach($userSelections_array[$section][$super_or_sub_or_select_groupKeys[0]] as $category)
						{
							// get all intervals of each category
							$selectedIntervals = filter_classificationToInterval($currentFacetName, $category);
							// add intervals to sub
							foreach($selectedIntervals as $interval){ $userSelections_array[$section][$string_sub][] = $interval; }
						}
						// delete current category array
						unset($userSelections_array[$section][$super_or_sub_or_select_groupKeys[0]]);
					}
					else if (strpos($super_or_sub_or_select_groupKeys[1], "super")) {
						// get corresponding sub name of super
						$string_sub = str_replace("super","sub",$super_or_sub_or_select_groupKeys[1]);

						// get all categories selected by user
						foreach($userSelections_array[$section][$super_or_sub_or_select_groupKeys[1]] as $category)
						{
							// get all intervals of each category
							$selectedIntervals = filter_classificationToInterval($currentFacetName, $category);
							// add intervals to sub
							foreach($selectedIntervals as $interval){ $userSelections_array[$section][$string_sub][] = $interval; }
						}
						// delete current category array
						unset($userSelections_array[$section][$super_or_sub_or_select_groupKeys[1]]);
					}
				}
			}

			// Format userSelections_array to a simpler array with dbColumnNames as key
			$userSelections = array();
			foreach($userSelections_array as $section => $array_of_sub_or_select_group){
				// get dbcolumn name based on section name
				$dbColumnName = section_facetName($section, true);
			
				// loop through each intervalGroup (only 1 key-value pair)
				foreach($array_of_sub_or_select_group as $sub_or_select => $intervalGroup)
				{ $userSelections[$dbColumnName] = $intervalGroup; }
			}
			
			/* Query : Get Details for Record specified by RefID AND userSelections */
			$query = "SELECT ";
			foreach ($dbInfoArray as $dbInfo){
				$query .= (empty($prefix))? '':$prefix;
				$query .= $dbInfo[1];
				$prefix = ', ';
			} $prefix = '';
			$query .= " FROM movie WHERE (".$dbName_searchRefID." LIKE '%".$refID."%') AND ";
			
			foreach($userSelections as $section => $intervalGroup){
				$query .= empty($prefixAND)? "":$prefixAND;
				$query .= "(";
				
				// get each interval
				foreach ($intervalGroup as $interval)
				{	// check if column is comma delimited
					if (!(isDBColumn_commaDelimited($section)))
					{
						$query .= empty($prefixOR)? "":$prefixOR;
						$query .= $section ." = '". $interval ."'";
						
						// OR
						$prefixOR = ' OR ';
					}
					else if (isDBColumn_commaDelimited($section))
					{
						$query .= empty($prefixOR)? "":$prefixOR;
						$query .= $section ." LIKE '%". $interval ."%'";
						
						// OR (changed to AND for dbColumns with multiple values (comma-delimited)
						$prefixOR = ' AND ';
					}
				}
				$query .= ")";
				
				// AND
				$prefixAND = " AND ";
				$prefixOR = "";
			}
		
			if ($pQuery = $DB_connect->prepare($query)){
				$pQuery->execute();
				
				// my_sqli result Object
				$result = $pQuery->get_result();
				// array of records
				$table = $result->fetch_all();
				
				if(count($table) > 0){
					$listOfID = array();
					foreach($table as $record)
					{ $listOfID[] = $record[1]; }
					
					echo json_encode($listOfID);
				} else { echo ""; }
				
				// Close Connection
				$pQuery->free_result();
				$pQuery->close();
			}
		}
	} else { echo ""; }
		
	// Close database connection
	$DB_connect->close();
?>