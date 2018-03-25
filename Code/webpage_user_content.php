<!-- Facet Filter : Step 1 - aggregate unique -->
<?php
	/*
	 * aggregate each facet and get unique value-count associative array for display
	 */
	$facetUnique = array();
	foreach ($facetInfoArray as $facetInfo)
	{	$facetUnique[] = db_uniqueFacet_all($facetInfo, true);	}
	
	$sortOrder = '';
	$ac_search_Val = '';
?>

<!-- Facet Filter : Step 2 - User Input Criteria -->
<?php
	/*
	 * Get user-submitted faceted filter selections
	 * to obtain an array of matching indexes for each facet
	 */
	if ($_SERVER['REQUEST_METHOD'] === 'POST')
	{
		// prepare array for making sql query to get results based on user selections
		// & get corresponding intervals for selected categories
		$userSelections = array();
		foreach ($facetInfoArray as $facetInfo)
		{	// has no category (select)
			if (!$facetInfo[2]){
				if (array_key_exists($facetInfo[0].'Select', $_POST))
				{ $userSelections[$facetInfo[1]] = $_POST[$facetInfo[0].'Select']; }
			}
			// has category (super & sub)
			else if ($facetInfo[2]){
				if (array_key_exists($facetInfo[0].'Select_super', $_POST) || array_key_exists($facetInfo[0].'Select_sub', $_POST))
				{
					if (array_key_exists($facetInfo[0].'Select_sub', $_POST))
					{ $userSelections[$facetInfo[1]] = $_POST[$facetInfo[0].'Select_sub']; }
					if (array_key_exists($facetInfo[0].'Select_super', $_POST))
					{	// convert category(super) to its corresponding intervals(sub)
						$super = $_POST[$facetInfo[0].'Select_super'];
						foreach ($super as $category)
						{
							$super = filter_classificationToInterval($facetInfo[0], $category);
							foreach ($super as $interval){ $userSelections[$facetInfo[1]][] = $interval; }
						}
					}
				}
			}
		}
		
		$ac_search_Val = (inputNotEmpty($_POST['ac_search']))? sanitize_input($_POST['ac_search']):null;
		
		// get user selected results from database
		$queryMatch = "";
		$table_resultDisplay = db_matchResults($userSelections, $ac_search_Val);
		
		$sortOrder = (!empty($_POST['sortOrder']))? sanitize_input($_POST['sortOrder']):null;
		if (!empty($userSelections))
		{	$facetUnique = array();
			/* unique facet on full collection *
			if (empty($sortOrder) || $sortOrder != 'count'){
				foreach ($facetInfoArray as $facetInfo)
				{	$facetUnique[] = db_uniqueFacet_all($facetInfo, true);	}
			} else {
				foreach ($facetInfoArray as $facetInfo)
				{	$facetUnique[] = db_uniqueFacet_all($facetInfo, false);	}
			}
			/* unique facet on filtered collection */
			if (empty($sortOrder) || $sortOrder != 'count'){
				foreach ($facetInfoArray as $facetInfo)
				{	$facetUnique[] = db_matchResults_unique($facetInfo, $queryMatch, true);	}
			} else {
				foreach ($facetInfoArray as $facetInfo)
				{	$facetUnique[] = db_matchResults_unique($facetInfo, $queryMatch, false);	}
			}
		}
	}
?>

<!-- ------------------------------Top Content Section------------------------------ -->
<div class="top_content"><div id="lockedSequenceDisplay"></div></div>

<!-- ------------------------------Content Section------------------------------ -->
<div class="content">
	<!-- ------------------------------Left Content Section------------------------------ -->
	<div class="left_content pullDiv" onmouseout="setPushPull('left_content')">

		<!-- Faceted Filters Form -->
		<div class="left_content_facetedFilter">
			<form id="facetedFilterForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
		
				<!-- Buttons -->
				<div class="btns_container">
					<div><a class="btns resetLink" href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">Reset</a></div>
					<div><input class="btns submitBtn" type="submit" value="Filter"/></div>
					<input type="hidden" name="filter_form" value="filter_form"/>
				</div>
				
				<!-- Auto Complete Search -->
				<div class="left_content_facetedSearch">
					<div>
						<input type="text" id="ac_search" name="ac_search" 
							   placeholder="Search Title" value="<?=$ac_search_Val?>" 
							   onkeyup="getData_FormAutoComplete_Filtered('ac_search','autocomplete_s')" />
						<div class="autocomplete_popup" id="autocomplete_s" 
							 onmouseleave="popup_disappear('autocomplete_s')" onclick="popup_disappear('autocomplete_s')"></div>
					</div>
				</div>
		
				<!-- Sort -->
				<div class="left_content_facetedSort">
					<div>
						<label><input type="radio" id="btn_sortUnique" name="sortOrder" value="name" 
									  <?= (empty($sortOrder) || $sortOrder != 'count')? "checked='checked'":''?> 
									  onclick="sortUniqueOrCount(true)"/>Sort By Name</label><br/>
						<label><input type="radio" id="btn_sortCount" name="sortOrder" value="count" 
									  <?= ($sortOrder == 'count')? "checked='checked'":''?> 
									  onclick="sortUniqueOrCount(false)"/>Sort By Count</label>
					</div>
				</div>	
			
				<!-- Filters -->
				<div id="facetedFilterForm_Content"><?php
					//  FacetUniqueInfo requires: unique values/intervals(with categories), facet name, interval name, and bool hasCategory
					$facetUniqueInfoArray = array();
					for ($i=0; $i<count($facetInfoArray); $i++)
					{ $facetUniqueInfoArray[] = array($facetUnique[$i], $facetInfoArray[$i][0], $facetInfoArray[$i][1], $facetInfoArray[$i][2]); }
					
					// output facets
					foreach ($facetUniqueInfoArray as $facet_info)
					{
						$facetSection_sectionID = 'facetedFilterForm_'.$facet_info[1].'_Section';
						$facetSection_labelID = 'assignment6Form_'.$facet_info[1].'_Label';
						$facetSection_labelText = $facet_info[1];
						
						// Section
						echo "<div class='facetedFilterFormSection extendDiv' 
									id='$facetSection_sectionID'
									onclick='clickDisplayID(\"$facetSection_sectionID\")'>";
							/* *************************Section Label************************* */
							echo "<div class='facetedFilterFormSection_Label' 
										id='$facetSection_labelID'>";
								// Label
								echo "<div><label>$facetSection_labelText</label></div>";
								// LockText
								echo "<div><span class='lockText' onclick='ajaxFacetFilter(\"$facetSection_sectionID\")'>
												$lockText_default
											</span></div>";
							echo "</div>";
							/* *************************Section Input************************* */
							echo "<div class='facetedFilterFormSection_Input'>";
								if(!empty($facet_info[0])){
									// if has categories
									if ($facet_info[3])
									{	// Set text strings
										$facetSection_inputClass_super 	= 'facetedFilterFormSection_inputSuper';
										$facetSection_inputClass_sub 	= 'facetedFilterFormSection_inputSub';
										$facetSection_inputName_super 	= $facet_info[1].'Select_super[]';
										$facetSection_inputName_sub 	= $facet_info[1].'Select_sub[]';
										foreach ($facet_info[0] as $classification => $intervalGroup)
										{
											// Additional text strings for classification
											$facetSection_inputClass_super_classification = $facet_info[1].'_super_'.$classification;
											$facetSection_inputClass_sub_classification = $facet_info[1].'_sub_'.$classification;
											
											// Get total number of unique_count (category count) from all intervals in each interval group
											$categoryCount = 0;
											foreach ($intervalGroup as $categoryCount_interval => $categoryCount_count)
											{ $categoryCount += $categoryCount_count; }
											
											// Display current category/classification
											echo "<label>";
												echo "<input type='checkbox' 
															 class='$facetSection_inputClass_super $facetSection_inputClass_super_classification' 
															 name='$facetSection_inputName_super' 
															 value='$classification' 
															 onclick='toggleFacetCheck(\"$facet_info[1]\", \"super\", \"$classification\")' />";
												echo $classification ." (". $categoryCount .") ";
											echo "</label><br/>";
											
											// Display all intervals for current category/classification
											foreach ($intervalGroup as $interval => $count)
											{
												echo "<label>";
													echo "<input type='checkbox' 
																class='$facetSection_inputClass_sub $facetSection_inputClass_sub_classification' 
																name='$facetSection_inputName_sub'
																value='$interval' 
																onclick='toggleFacetCheck(\"$facet_info[1]\", \"sub\", \"$classification\")' ";
																if (!empty($userSelections[ $facet_info[2] ])){
																	if (in_array($interval, $userSelections[ $facet_info[2] ]))
																		echo "checked='checked' ";
																}
													echo "/>";
													echo $interval ." (". $count .") ";
												echo "</label><br/>";
											}
										}
									}
									// if no categories
									else
									{	// set text strings
										$facetSection_inputClass_select = 'facetedFilterFormSection_inputSuper';
										$facetSection_inputName_select 	= $facet_info[1].'Select[]';
										// Display all intervals
										foreach ($facet_info[0] as $unique_value => $unique_count)
										{
											echo "<label>";
												echo "<input type='checkbox' 
															 class='$facetSection_inputClass_select' 
															 name='$facetSection_inputName_select' 
															 value='$unique_value' ";
															if (!empty($userSelections[ $facet_info[2] ])){
																if (in_array($unique_value, $userSelections[ $facet_info[2] ]))
																	echo "checked='checked' ";
															}
												echo "/>";
												echo $unique_value ." (". $unique_count .") ";
											echo "</label><br/>";
										}
									}
								} else { echo "<label><span>Nothing</span></label><br/>"; }
							echo "</div>";
						echo "</div>"; 
					} // End of Section
				?></div>
			</form>
			
			<script>
				// JavaScript : Function to add event listeners (mouse over, out, and click onto lockTextElements)
				var lockTextElements = document.getElementsByClassName("lockText");
				for (var i = 0; i < lockTextElements.length; i++)
				{	// mouse over
					lockTextElements[i].addEventListener("mouseover", function(event){
						var lockTextElement = event.target || event.srcElement;
						mouseOverLock(lockTextElement);
					});
					// mouse out
					lockTextElements[i].addEventListener("mouseout", function(event){
						var lockTextElement = event.target || event.srcElement;
						mouseOutLock(lockTextElement);
					});
				}
				// JavaScript : Function to change text when mouse over lockTextElement
				function mouseOverLock(lockTextElement)
				{
					if (lockTextElement.innerHTML == text_unlock)
					{ lockTextElement.innerHTML = text_toLock; }
					else if (lockTextElement.innerHTML == text_lock)
					{ lockTextElement.innerHTML = text_toUnlock; }
					// default to "lock?" if text changed by user
					else { lockTextElement.innerHTML = text_toLock; }
				}
				// JavaScript : Function to change text when mouse out lockTextElement
				function mouseOutLock(lockTextElement)
				{
					if (lockTextElement.innerHTML == text_toLock)
					{ lockTextElement.innerHTML = text_unlock; }
					else if (lockTextElement.innerHTML == text_toUnlock)
					{ lockTextElement.innerHTML = text_lock; }
					// default back to "unlock" text if text changed by user
					else if (lockTextElement.innerHTML != text_lock && lockTextElement.innerHTML != text_unlock)
					{ lockTextElement.innerHTML = text_unlock; }
				}
			</script>
		</div>
		
	</div>
	<!-- End of Left Content Section -->
	
	<!-- ------------------------------Right Content Section------------------------------ -->
	<div class="right_content">

		<!-- Movie Results according to Faceted Filters -->
		<div class="right_content_resultDisplay">
			<div class="right_content_title"><h1>Movie Details</h1></div>
			
			<div class='right_content_resultDisplay_content' id="results_section">
			<?php
				// if there is no records in movie collection
				if (empty($collection))
				{ echo "<div>No Movie Content</div>"; }
				// if user selection filter returns a result
				else if (!empty($table_resultDisplay))
				{ // display selected results
					foreach ($table_resultDisplay as $record)
					{	echo "<div>";
							echo "<div>";
								echo "<div><span>",$record['movieType'],"<br/>Rating: ",$record['contentRating'],"</span></div>";
								echo "<div><span>",$record['movieTitle'],"</span></div>";
								echo "<div>meta score<br/>",$record['metaScore'],"/100</div>";
								echo "<div>rating<br/>",$record['imdbRating'],"/10.0</div>";
								echo "<div>votes<br/>",$record['imdbVotes'],"</div>";
							echo "</div>";
							
							echo "<div>";
								echo "<br/><div><span>Genre: </span>";
								$prefix = "";
								foreach($record['movieGenre'] as $genre){ 
									echo (empty($prefix))?"":$prefix;
									echo $genre;
									$prefix = ", ";
								}
								echo "</div>";
							
								echo "<br/><div><span>Language: </span>";
								$prefix = "";
								foreach($record['movieLanguage'] as $language){ 
									echo (empty($prefix))?"":$prefix;
									echo $language;
									$prefix = ", ";
								}
								echo "</div>";
								
								echo "<br/><div><span>Country: </span>";
								$prefix = "";
								foreach($record['movieCountry'] as $country){ 
									echo (empty($prefix))?"":$prefix;
									echo $country;
									$prefix = ", ";
								}
								echo "</div><br/>";
								
								echo "<a target='_blank' href='http://www.imdb.com/title/",$record['imdbRefID'],"'><div class='ext_link_imdb'>";
									echo "To IMDb Page";
								echo "</div></a>";
							echo "</div>";
							
							echo "<div>";
								echo "<div>Seasons: ",($record['totalSeason'] == 0)?'No':$record['totalSeason']," season</div>";
								echo "<div>Duration: ",$record['movieDuration']," mins</div>";
								echo "<div>Released Since: ",$record['releasedDate'],"</div>";
							echo "</div>";
						echo "</div>";
					}
				}
				// display all movie records if all selection is at default
				else if (empty($userSelections) && empty($ac_search_Val))
				{ // display all records
					foreach ($collection as $record)
					{	echo "<div>";
							echo "<div>";
								echo "<div><span>",$record['movieType'],"<br/>Rating: ",$record['contentRating'],"</span></div>";
								echo "<div><span>",$record['movieTitle'],"</span></div>";
								echo "<div>meta score<br/>",$record['metaScore'],"/100</div>";
								echo "<div>rating<br/>",$record['imdbRating'],"/10.0</div>";
								echo "<div>votes<br/>",$record['imdbVotes'],"</div>";
							echo "</div>";
							
							echo "<div>";
								echo "<br/><div><span>Genre: </span>";
								$prefix = "";
								foreach($record['movieGenre'] as $genre){ 
									echo (empty($prefix))?"":$prefix;
									echo $genre;
									$prefix = ", ";
								}
								echo "</div>";
							
								echo "<br/><div><span>Language: </span>";
								$prefix = "";
								foreach($record['movieLanguage'] as $language){ 
									echo (empty($prefix))?"":$prefix;
									echo $language;
									$prefix = ", ";
								}
								echo "</div>";
								
								echo "<br/><div><span>Country: </span>";
								$prefix = "";
								foreach($record['movieCountry'] as $country){ 
									echo (empty($prefix))?"":$prefix;
									echo $country;
									$prefix = ", ";
								}
								echo "</div><br/>";
								
								echo "<a target='_blank' href='http://www.imdb.com/title/",$record['imdbRefID'],"'><div class='ext_link_imdb'>";
									echo "To IMDb Page";
								echo "</div></a>";
							echo "</div>";
							
							echo "<div>";
								echo "<div>Seasons: ",($record['totalSeason'] == 0)?'No':$record['totalSeason']," season</div>";
								echo "<div>Duration: ",$record['movieDuration']," mins</div>";
								echo "<div>Released Since: ",$record['releasedDate'],"</div>";
							echo "</div>";
						echo "</div>";
					}
				}
				// if user's selection does not match any record in collection
				else if (empty($table_resultDisplay))
				{ echo "<div>No Matching Movie Content</div>"; }
			?>
			</div>
		</div>
		<!-- End of Result Display -->

	</div>
	<!-- End of Right Content Section -->
</div>
<!-- End of Content Section -->
