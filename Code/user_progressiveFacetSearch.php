<?php
	include 'php-commons/database.php';
	include 'php-commons/constants.php';
	include 'php-commons/filters.php';
	include 'php-commons/functions.php';
	
	/************************* Facet Filter (AJAX): Respond to AJAX call *************************/

	// get json_encoded userSelections from POST, and Decode it into an array
	$userSelections_array = json_decode($_POST['userSelections'], true);

	// default sort order = sort by unique value/interval, if false, sort by count
	$toSortUnique = (array_key_exists('sortOrder', $_POST))? $_POST['sortOrder']:'true';
	if ($toSortUnique === 'false') $toSortUnique = false;
	else if ($toSortUnique === 'true') $toSortUnique = true;
	else $toSortUnique = true;

	$indexed_filtered_facetArray_unique = array();
	if (!empty($userSelections_array))
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
		
		/* Get Records based on user selections and aggregate for filters */
		$queryMatch = "";
		$collection = db_matchResults($userSelections);
		
		$filtered_facetArray_unique = array();
		if ($toSortUnique){		// sort by key
			foreach ($facetInfoArray as $facetInfo)
			{	$filtered_facetArray_unique[$facetInfo[0]] = db_matchResults_unique($facetInfo, $queryMatch, true);	}	
		}
		else { 					// sort by count
			foreach ($facetInfoArray as $facetInfo)
			{	$filtered_facetArray_unique[$facetInfo[0]] = db_matchResults_unique($facetInfo, $queryMatch, false);	}
		}
		
	}
	else if (empty($userSelections_array))
	{	/* aggregate facets and sort the unique arrays */
		$filtered_facetArray_unique = array();
		if ($toSortUnique){		// sort by key
			foreach ($facetInfoArray as $facetInfo)
			{	$filtered_facetArray_unique[$facetInfo[0]] = db_uniqueFacet_all($facetInfo, true);	}	
		}
		else { 					// sort by count
			foreach ($facetInfoArray as $facetInfo)
			{	$filtered_facetArray_unique[$facetInfo[0]] = db_uniqueFacet_all($facetInfo, false);	}
		}
		
		include 'php-commons/collection.php';
	}

	/*
	 * Convert Associative array into single dimension Indexed array, delimited by sep array
	 * note: JSON automatically sort associative array by alphabetical order on keys,
	 *		 which prevents using sorting with associative array before passing back to AJAX
	 */
	$sep = array('--','|||','~','||','|');
	foreach($filtered_facetArray_unique as $facetName => $facetUnique_array)
	{
		$facet_joinedString = $facetName . $sep[0];
		
		foreach($facetUnique_array as $categoryOrValue => $intervalOrCount)
		{
			$facet_joinedString .= empty($prefix_sep_1)? '':$prefix_sep_1;
			
			// if $intervalOrCount is an array of intervals-count pairs
			// section : category~ interval|count||interval|count  |||  category~ interval|count
			if (is_array($intervalOrCount))
			{
				$facet_joinedString .= $categoryOrValue . $sep[2];
				
				foreach($intervalOrCount as $interval => $count)
				{
					$facet_joinedString .= empty($prefix_sep_2)? '':$prefix_sep_2;
					$facet_joinedString .= $interval . $sep[4] . $count;
					
					$prefix_sep_2 = $sep[3];
				}
				$prefix_sep_2 = "";
			}
			// if $intervalOrCount is only count
			// section : value|count   |||   value|count
			else
			{
				$facet_joinedString .= $categoryOrValue . $sep[4];
				$facet_joinedString .= $intervalOrCount;
			}
			
			$prefix_sep_1 = $sep[1];
		}
	
		$indexed_filtered_facetArray_unique[] = $facet_joinedString;
		$prefix_sep_1 = "";
	}

	// pass the "sep-delimited" array back to AJAX
	echo json_encode(array($indexed_filtered_facetArray_unique,$collection));
?>
