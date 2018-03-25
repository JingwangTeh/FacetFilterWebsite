<?php
	/* Connect to database */
	$DB_connect = @new mysqli ($db_connect_var['host'], $db_connect_var['username'], $db_connect_var['password'], $db_connect_var['database']);
	/* Database Connection Errors */
	if ($DB_connect->connect_errno > 0)
	{ die(); }
	
	/* Query : Get All Records */
	$query = "SELECT * FROM movie";
	
	if ($pQuery = $DB_connect->prepare($query)){
		$pQuery->execute();
		
		// my_sqli result Object
		$result = $pQuery->get_result();
		// array of records
		$table = $result->fetch_all();
		
		// convert array of records into an associative array
		$collection = array();
		foreach ($table as $record)
		{	
			$collectionRecord = array();
			$collectionRecord['movieIndex'] = $record[0];
			$collectionRecord['movieTitle'] = $record[1];
			$collectionRecord['imdbRefID'] = $record[2];
			$collectionRecord['releasedDate'] = $record[3];
			$collectionRecord['movieType'] = $record[5];
			$collectionRecord['contentRating'] = $record[6];
			$collectionRecord['movieGenre'] = array_map('trim',explode(',', $record[7]));
			$collectionRecord['totalSeason'] = $record[8];
			$collectionRecord['movieDuration'] = $record[10];
			$collectionRecord['movieLanguage'] = array_map('trim',explode(',', $record[12]));
			$collectionRecord['movieCountry'] = array_map('trim',explode(',', $record[13]));
			$collectionRecord['metaScore'] = $record[14];
			$collectionRecord['imdbRating'] = $record[16];
			$collectionRecord['imdbVotes'] = $record[18];
			
			$collection[] = $collectionRecord;
		}
		
		// Close Connection
		$pQuery->free_result();
		$pQuery->close();
	}
	
	// Close database connection
	$DB_connect->close();
?>