<?php
	/*
	 * Function : sanitize_input
	 * Usage    : clear whitespaces, slashes, and any special characters
	 * Input	: string to be sanitized
	 * Return   : sanitized value
	 */
	function sanitize_input($user_input){
		$user_input = trim($user_input);
		$user_input = htmlspecialchars($user_input);
		$user_input = stripslashes($user_input);
		return $user_input;
	}
	
	/*
	 * Function : inputNotEmpty
	 * Usage    : check if input is empty 
	 *			  (to handle input with value of 0, as 0 is considered as empty)
	 * Input	: input string to check
	 * Return   : boolean true/false, true if not empty, false if empty and not 0
	 */
	function inputNotEmpty($input){
		return ($input === "0" || $input);
	}
	
	/*
	 * Function : section_facetName
	 * Usage    : find facet name (or dbcolumn name) based on section name 
	 *			  that has facet name as the substring
	 * Input	: section name
	 * Return   : facet name (or dbcolumn name)
	 */
	function section_facetName($section, $facetName_dbColumn = false)
	{	global $facetInfoArray;
		foreach ($facetInfoArray as $facetInfo)
		{	// return facetName or dbColumn name
			if (strpos($section, $facetInfo[0]) != false){
				if (!$facetName_dbColumn){ return $facetInfo[0]; }
				else if ($facetName_dbColumn){ return $facetInfo[1]; }
			}
		}
	}

	/*
	 * Function : isDBColumn_commaDelimited
	 * Usage    : check whether the current column is comma delimited (have multiples, e.g. genre)
	 * Input	: DBColumn
	 * Return   : facet name (or dbcolumn name)
	 */
	function isDBColumn_commaDelimited($dbColumn)
	{	global $facetInfoArray;
		foreach ($facetInfoArray as $facetInfo)
		{	if ($dbColumn == $facetInfo[1])
			{	// commaDelimited bool = true, for the dbColumn
				if ($facetInfo[3])
				{ return true; }
			}
		}
		return false;
	}	
	
	/*
	 * Function : filter_classificationToInterval
	 * Usage    : get array of intervals for the classification/category from filterClassification array
	 * Input	: facet name and category
	 * Return   : array of intervals
	 */
	function filter_classificationToInterval($targetFacet, $targetCategory)
	{	global $filterClassification;
		
		$selectedIntervals = array();
		foreach($filterClassification[$targetFacet]['interval'][$targetCategory][0] as $interval_limit)
		{ $selectedIntervals[] = $interval_limit[0]; }
		
		return $selectedIntervals;
	}
	
	/*
	 * Function : filter_intervalToClassification
	 * Usage    : get name of facet, and interval to traverse filterClassification array to find classification
	 * Input	: facet name and interval
	 * Return   : classification
	 */
	function filter_intervalToClassification($targetFacet, $targetInterval)
	{	global $filterClassification;
		
		foreach (array_reverse($filterClassification[$targetFacet]['interval']) as $classification => $intervalGroup){
			foreach (array_reverse($intervalGroup[0]) as $interval){
				if ($interval[0] == $targetInterval)
				{ return $classification; }
			}
		}
		return "";
	}
	
	/*
	 * Function : filter_valueToInterval
	 * Usage    : get name of facet, and value to find interval in filterClassification array
	 * Input	: facet name and interval
	 * Return   : classification
	 */
	function filter_valueToInterval($targetFacet, $targetValue)
	{	global $filterClassification;
		
		foreach (array_reverse($filterClassification[$targetFacet]['interval']) as $classification => $intervalGroup){
			if ($targetValue > $intervalGroup[1]){
				foreach (array_reverse($intervalGroup[0]) as $interval){
					if ($targetValue > $interval[1])
					{ return $interval[0]; }
		}	}	}
		return "";
	}
		
	/*
	 * Function : db_updateIntervals
	 * Usage    : get the refID to update all interval values of a record
	 * Input	: imdbRefID
	 * Return   : none
	 */
	function db_updateIntervals($RefID = "")
	{	global $db_connect_var;
		global $dbName_searchRefID;
		global $valueAndIntervalArray;
		
		// Connect to database
		$DB_connect = @new mysqli ($db_connect_var['host'], $db_connect_var['username'], $db_connect_var['password'], $db_connect_var['database']);
		// Database Connection Errors
		if ($DB_connect->connect_errno > 0){	die();	}
		
		if (inputNotEmpty($RefID)){
			$prefix = '';
			$query = "SELECT ";
			foreach ($valueAndIntervalArray as $valueIntervalPair){
				$query .= (empty($prefix))? '':$prefix;
				$query .= $valueIntervalPair[0];
				$prefix = ', ';
			}
			$query .= " FROM movie WHERE ".$dbName_searchRefID."='".$RefID."'";
			if ($pQuery = $DB_connect->prepare($query)){
				$pQuery->execute();
			
				// my_sqli result Object
				$result = $pQuery->get_result();
				// array of records
				$table = $result->fetch_all();
			
				// get intervals for the selected record
				$intervalsArray = array();
				$intervalsArray[] = filter_valueToInterval($valueAndIntervalArray[0][2], explode('/', $table[0][0])[2]);
				for ($vip_i = 1; $vip_i < count($valueAndIntervalArray); $vip_i++)
				{	// pass facet name, record #1's value to function
					$intervalsArray[] = filter_valueToInterval($valueAndIntervalArray[$vip_i][2],$table[0][$vip_i]);
				}
				
				// update the record's intervals
				$prefix = '';
				$queryString = "UPDATE movie SET ";
				for ($vip_j = 0; $vip_j < count($valueAndIntervalArray); $vip_j++){
					$queryString .= (empty($prefix))? '':$prefix;
					$queryString .= $valueAndIntervalArray[$vip_j][1]."='".$intervalsArray[$vip_j]."'";
					$prefix = ', ';
				}
				$queryString .= " WHERE ".$dbName_searchRefID."='".$RefID."'";
				
				// Execute Update Query
				if ($DB_connect->query($queryString) === TRUE){ }
			}
		} // Default to update all records if no specific ID provided
		else {
			$prefix = '';
			$query = "SELECT ".$dbName_searchRefID.", ";
			foreach ($valueAndIntervalArray as $valueIntervalPair){
				$query .= (empty($prefix))? '':$prefix;
				$query .= $valueIntervalPair[0];
				$prefix = ', ';
			}
			$query .= " FROM movie";
			if ($pQuery = $DB_connect->prepare($query)){
				$pQuery->execute();
			
				// my_sqli result Object
				$result = $pQuery->get_result();
				// array of records
				$table = $result->fetch_all();
			
				foreach ($table as $record)
				{	// RefID
					$recordRefID = $record[0];
					
					// get intervals for the selected record
					$intervalsArray = array();
					$intervalsArray[] = filter_valueToInterval($valueAndIntervalArray[0][2], explode('/', $record[1])[2]);
					for ($vip_i = 1; $vip_i < count($valueAndIntervalArray); $vip_i++)
					{	// pass facet name, record #1's value to function
						$intervalsArray[] = filter_valueToInterval($valueAndIntervalArray[$vip_i][2],$record[$vip_i + 1]);
					}
					
					// update the record's intervals
					$prefix = '';
					$queryString = "UPDATE movie SET ";
					for ($vip_j = 0; $vip_j < count($valueAndIntervalArray); $vip_j++){
						$queryString .= (empty($prefix))? '':$prefix;
						$queryString .= $valueAndIntervalArray[$vip_j][1]."='".$intervalsArray[$vip_j]."'";
						$prefix = ', ';
					}
					$queryString .= " WHERE ".$dbName_searchRefID."='".$recordRefID."'";
				
					// Execute Update Query
					if ($DB_connect->query($queryString) === TRUE){ }
				}
			}
		}
		
		// Close database connection
		$DB_connect->close();		
	}

	/*
	 * Function : db_uniqueFacet_all
	 * Usage    : aggregate a facet to display
	 * Input	: facetInfo containing info of the facet, and sort order
	 * Return   : aggregated and sorted array of unique values of the facet
	 */
	function db_uniqueFacet_all($facetInfo, $sortOrderDefault = true)
	{	global $db_connect_var;
		
		/* Connect to database */
		$DB_connect = @new mysqli ($db_connect_var['host'], $db_connect_var['username'], $db_connect_var['password'], $db_connect_var['database']);
		/* Database Connection Errors */
		if ($DB_connect->connect_errno > 0){	die();	}

		/* Query : Get Unique Facets */
		// if not comma delimited
		if (!$facetInfo[3]){
			$query = "SELECT ".$facetInfo[1].", COUNT(".$facetInfo[1].") total".
					 " FROM movie".
					 " GROUP BY ".$facetInfo[1];
			if ($sortOrderDefault){ $query .= " ORDER BY ".$facetInfo[1]. " ASC"; }
			else if (!$sortOrderDefault){ $query .= " ORDER BY total DESC"; }
		} // if it is comma delimited
		else if ($facetInfo[3]){
			$query = "SELECT cd, COUNT(cd) total".
					 " FROM (".
					 " SELECT SUBSTRING_INDEX(SUBSTRING_INDEX(".$facetInfo[1].", ',', movies.n), ',', -1) cd".
					 " FROM (SELECT 1 n union all SELECT 2 union all SELECT 3 union all SELECT 4 union all SELECT 5) movies".
					 " INNER JOIN movie ON CHAR_LENGTH(".$facetInfo[1].")-CHAR_LENGTH(REPLACE(".$facetInfo[1].", ',', ''))>=movies.n-1)".
					 " AS x".
					 " GROUP BY cd";
			if ($sortOrderDefault){ $query .= " ORDER BY cd ASC"; }
			else if (!$sortOrderDefault){ $query .= " ORDER BY total DESC"; }
		}

		// Execute Query
		$uniqueArray = array();
		if ($pQuery = $DB_connect->prepare($query)){
			$pQuery->execute();
			
			// my_sqli result Object
			$result = $pQuery->get_result();
			// array of records
			$table = $result->fetch_all();
			
			// no categories
			if (!$facetInfo[2]){
				foreach($table as $record)
				{	$uniqueArray[$record[0]] = $record[1];	}
			}
			// has categories
			else if ($facetInfo[2]){
				foreach($table as $record)
				{	$uniqueArray[ filter_intervalToClassification($facetInfo[0], $record[0]) ][$record[0]] = $record[1];	}
				
				// sort categories
				if ($sortOrderDefault){ ksort($uniqueArray); }
				else if (!$sortOrderDefault){ 
					$array_of_categoryCount = array();
					foreach ($uniqueArray as $category => $intervalGroup)
					{	// get total count for each category
						$totalCategoryCount = 0;
						foreach($intervalGroup as $interval => $count)
						{	$totalCategoryCount += $count;	}
						$array_of_categoryCount[$category] = $totalCategoryCount;
					}
					// sort category-totalcount array based on total count
					arsort($array_of_categoryCount);
					
					// pass the sorted intervalGroup array to array_of_categoryCount of the same key
					foreach($array_of_categoryCount as $category => $totalCategoryCount)
					{ $array_of_categoryCount[$category] = $uniqueArray[$category]; }
					// overwrite uniqueArray with sorted array of category-intervals
					$uniqueArray = $array_of_categoryCount;
				}
			}
			
			// Free Results
			$pQuery->free_result();
			$pQuery->close();
		} else {	echo $DB_connect->error;	}
	
		// Close database connection
		$DB_connect->close();
		
		return $uniqueArray;
	}
	
	/*
	 * Function : db_matchResults
	 * Usage    : match results from database based on user selections and search term
	 * Input	: user selections and search term
	 * Return   : array of matching records
	 */
	function db_matchResults($userSelections, $searchVal_RefID="")
	{	global $db_connect_var;
		global $queryMatch;
		global $dbName_searchTitle;
		global $dbName_searchRefID;
		$table_resultDisplay = array();
		
		// connect to database
		$DB_connect = @new mysqli ($db_connect_var['host'], $db_connect_var['username'], $db_connect_var['password'], $db_connect_var['database']);
		if ($DB_connect->connect_errno > 0){ die(); }
		
		// Query : Get Records based on user selections
		$resultQuery = "SELECT * FROM movie WHERE ";
		if (!empty($userSelections)){
			foreach($userSelections as $section => $intervalGroup){
				$resultQuery .= empty($prefixAND)? "":$prefixAND;
				$resultQuery .= "(";
				
				// get each interval
				foreach ($intervalGroup as $interval)
				{	// check if column is comma delimited
					if (!(isDBColumn_commaDelimited($section)))
					{
						$resultQuery .= empty($prefixOR)? "":$prefixOR;
						$resultQuery .= $section ." = '". $interval ."'";
						
						// OR
						$prefixOR = ' OR ';
					} else if (isDBColumn_commaDelimited($section))
					{
						$resultQuery .= empty($prefixOR)? "":$prefixOR;
						$resultQuery .= $section ." LIKE '%". $interval ."%'";
						
						// OR (changed to AND for dbColumns with multiple values (comma-delimited)
						$prefixOR = ' AND ';
					}
				}
				$resultQuery .= ")";
				
				// AND
				$prefixAND = " AND ";
				$prefixOR = "";
			}
			if (!empty($searchVal_RefID))
			{ $resultQuery .= " AND (".$dbName_searchTitle." LIKE '%".$searchVal_RefID."%')"; }
		}
		else if (empty($userSelections)){
			if (!empty($searchVal_RefID)){
				$resultQuery .= "(".$dbName_searchTitle." LIKE '%".$searchVal_RefID."%')";
			}
		}

		if (!empty($userSelections) || !empty($searchVal_RefID)){
			// Execute Query
			if ($pResultQuery = $DB_connect->prepare($resultQuery)){
				$pResultQuery->execute();
				
				// my_sqli result object
				$result_pResultQuery = $pResultQuery->get_result();
				// array of records
				$table_resultQuery = $result_pResultQuery->fetch_all();
				
				foreach ($table_resultQuery as $record)
				{
					$resultQueryRecord = array();
					$resultQueryRecord['movieIndex'] = $record[0];
					$resultQueryRecord['movieTitle'] = $record[1];
					$resultQueryRecord['imdbRefID'] = $record[2];
					$resultQueryRecord['releasedDate'] = $record[3];
					$resultQueryRecord['movieType'] = $record[5];
					$resultQueryRecord['contentRating'] = $record[6];
					$resultQueryRecord['movieGenre'] = array_map('trim',explode(',', $record[7]));
					$resultQueryRecord['totalSeason'] = $record[8];
					$resultQueryRecord['movieDuration'] = $record[10];
					$resultQueryRecord['movieLanguage'] = array_map('trim',explode(',', $record[12]));
					$resultQueryRecord['movieCountry'] = array_map('trim',explode(',', $record[13]));
					$resultQueryRecord['metaScore'] = $record[14];
					$resultQueryRecord['imdbRating'] = $record[16];
					$resultQueryRecord['imdbVotes'] = $record[18];
					
					$table_resultDisplay[] = $resultQueryRecord;
				}
				$queryMatch = $resultQuery;
				
				// Free Results
				$pResultQuery->free_result();
				$pResultQuery->close();
			} else {	echo $DB_connect->error;	}
		}
		
		// Close Connection
		$DB_connect->close();
		
		return $table_resultDisplay;
	}
	
	/*
	 * Function : db_matchResults_unique
	 * Usage    : aggregate a facet based on a filtered collection (query string) to display
	 * Input	: facet info for the facet, an sql query string, sort order
	 * Return   : aggregated and sorted array of unique values of the facet
	 */
	function db_matchResults_unique($facetInfo, $collectionQuery, $sortOrderDefault = true)
	{	global $db_connect_var;
		
		// connect to database
		$DB_connect = @new mysqli ($db_connect_var['host'], $db_connect_var['username'], $db_connect_var['password'], $db_connect_var['database']);
		if ($DB_connect->connect_errno > 0){ die(); }
		// ...database connection success

		/* Query : Get Unique Facets from collectionQuery */
		// if not comma delimited
		if (!$facetInfo[3]){
			$query = "SELECT ".$facetInfo[1].", COUNT(".$facetInfo[1].") total".
					 " FROM (".$collectionQuery.") AS fc".
					 " GROUP BY ".$facetInfo[1];
			if ($sortOrderDefault){ $query .= " ORDER BY ".$facetInfo[1]. " ASC"; }
			else if (!$sortOrderDefault){ $query .= " ORDER BY total DESC"; }
		}
		// if it is comma delimited
		else if ($facetInfo[3]){
			$query = "SELECT cd, COUNT(cd) total".
					 " FROM (".
					 " SELECT SUBSTRING_INDEX(SUBSTRING_INDEX(".$facetInfo[1].", ',', movies.n), ',', -1) cd".
					 " FROM (SELECT 1 n union all SELECT 2 union all SELECT 3 union all SELECT 4 union all SELECT 5) movies".
					 " INNER JOIN (".$collectionQuery.") AS fc ON CHAR_LENGTH(".$facetInfo[1].")-CHAR_LENGTH(REPLACE(".$facetInfo[1].", ',', ''))>=movies.n-1)".
					 " AS x".
					 " GROUP BY cd";
			if ($sortOrderDefault){ $query .= " ORDER BY cd ASC"; }
			else if (!$sortOrderDefault){ $query .= " ORDER BY total DESC"; }
		}
		
		// Execute Query
		$uniqueArray = array();
		if ($pQuery = $DB_connect->prepare($query)){
			$pQuery->execute();
			
			// my_sqli result Object
			$result = $pQuery->get_result();
			// array of records
			$table = $result->fetch_all();
			
			// no categories
			if (!$facetInfo[2]){
				foreach($table as $record)
				{	$uniqueArray[$record[0]] = $record[1];	}
			}
			// has categories
			else if ($facetInfo[2]){
				foreach($table as $record)
				{	$uniqueArray[ filter_intervalToClassification($facetInfo[0], $record[0]) ][$record[0]] = $record[1];	}
				
				// sort categories
				if ($sortOrderDefault){ ksort($uniqueArray); }
				else if (!$sortOrderDefault){ 
					
					$array_of_categoryCount = array();
					foreach ($uniqueArray as $category => $intervalGroup)
					{	// get total count for each category
						$totalCategoryCount = 0;
						foreach($intervalGroup as $interval => $count)
						{	$totalCategoryCount += $count;	}
						$array_of_categoryCount[$category] = $totalCategoryCount;
					}
					// sort category-totalcount array based on total count
					arsort($array_of_categoryCount);
					
					// pass the sorted intervalGroup array to array_of_categoryCount of the same key
					foreach($array_of_categoryCount as $category => $totalCategoryCount)
					{ $array_of_categoryCount[$category] = $uniqueArray[$category]; }
					// overwrite uniqueArray with sorted array of category-intervals
					$uniqueArray = $array_of_categoryCount;
				}
			}
			
			// Free Results
			$pQuery->free_result();
			$pQuery->close();
		} else {	echo $DB_connect->error;	}
		
		// Close Connection
		$DB_connect->close();
		
		return $uniqueArray;
	}
?>