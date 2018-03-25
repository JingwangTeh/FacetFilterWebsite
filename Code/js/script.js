var text_lock = "lock";
var text_toLock = "lock?";
var text_unlock = "unlock";
var text_toUnlock = "unlock?";

var userSelections_locked = {};
var sectionLocked_sequence = [];

// Javascript : Function to check whether section is currently locked or not
function isSectionLocked(facetSectionID)
{	
	for (var i = 0; i < sectionLocked_sequence.length; i++)
	{	// section is currently locked
		if (sectionLocked_sequence[i].includes(facetSectionID)){ return true; }
	} return false;
}

// JavaScript : Function to	handle progressive filter from locking a section
function ajaxFacetFilter(facetSectionID)
{	
	// facet section, label section, lock element, and the input section
	var facetSection = document.getElementById(facetSectionID);
	var facetSectionLabel = facetSection.children[0];
	var facetSectionLockElement = facetSection.children[0].children[1];
	var facetSectionInput = facetSection.children[1];
	
	// check if locked yet or not,
	// and find the current to-be-unlocked section's index in sectionLocked_sequence array
	var isLocked = false;
	var lockedIndex;
	for (var i = 0; i < sectionLocked_sequence.length; i++)
	{	// section is currently locked
		if (sectionLocked_sequence[i].includes(facetSectionID))
		{	isLocked = true;
			// locked index for the section recorded
			lockedIndex = i;
			break;
		}
	}
	
	var canAJAX = true;
	var isUserSelectionEmpty = true;
	// if current section is not in locked_sequence (section not yet locked)
	if (!isLocked)
	{
		// (LOCKS the section) add the section into the lock sequence 
		sectionLocked_sequence.push(facetSectionID);
		// create user selections OBJECT for the SPECIFIC facet
		userSelections_locked[facetSectionID] = {};
		
		// look through each input checkbox element, 
		// and add user selections for the section into userSelections_locked object
		for (var i = 0; i < facetSectionInput.children.length; i+=2)
		{	// get categories/intervals that are checked
			// note: children[i] was refering to the label, and then children[0] refers to the input
			if (facetSectionInput.children[i].children[0].checked == true)
			{	// check whether it is an empty selection
				isUserSelectionEmpty = false;
				
				// create a category array to store intervals
				if ( userSelections_locked[facetSectionID][facetSectionInput.children[i].children[0].name] == undefined )
				{ userSelections_locked[facetSectionID][facetSectionInput.children[i].children[0].name] = []; }
				
				// store intervals into category array
				userSelections_locked[facetSectionID][facetSectionInput.children[i].children[0].name].push(facetSectionInput.children[i].children[0].value);
			}
		}
		
		// if user selection is not empty, proceed to disabling the checkboxes, and then finally an AJAX call
		if (!isUserSelectionEmpty)
		{	// disable each checkboxes in the most recent locked section
			for (var i = 0; i < facetSectionInput.children.length; i+=2)
			{
				// disable checkbox
				facetSectionInput.children[i].children[0].disabled = true;
				// add a hidden input with same name and value to ensure checkbox value submission (checked checkboxes only)
				if (facetSectionInput.children[i].children[0].checked == true)
				{
					var hiddenCheckboxNode = document.createElement("input");
					
					hiddenCheckboxNode.type = "hidden";
					hiddenCheckboxNode.name = facetSectionInput.children[i].children[0].name;
					hiddenCheckboxNode.value = facetSectionInput.children[i].children[0].value;
					
					// append to inside label (under checkbox)
					facetSectionInput.children[i].appendChild(hiddenCheckboxNode);
				}
			}
			
			// set lock status to lock
			var lockElementOutput = "<span class='lockText' onclick='ajaxFacetFilter(\""+facetSectionID+"\")'>";
			lockElementOutput += text_lock;
			lockElementOutput += "</span>";
			facetSectionLockElement.innerHTML = lockElementOutput;
		}
		// however, if user selection is indeed empty
		else if (isUserSelectionEmpty)
		{
			// (rollback) remove the latest section from the locked_sequence
			if (sectionLocked_sequence.length != 0)
			{	// rollback the addition of the newest (latest) section
				sectionLocked_sequence.splice((sectionLocked_sequence.length - 1), 1);
			}

			// as well as removing it from the userSelections_locked object
			delete userSelections_locked[facetSectionID];
			
			// change text back to text_unlock ("unlock")
			// as user should not be able to lock an empty selection
			var lockElementOutput = "<span class='lockText' onclick='ajaxFacetFilter(\""+facetSectionID+"\")'>";
			lockElementOutput += text_unlock;
			lockElementOutput += "</span>";
			facetSectionLockElement.innerHTML = lockElementOutput;
			
			// do nothing (no AJAX call) as user selection is empty
			canAJAX = false;
			alert('cannot lock an empty selection');
		}
	}
	// if locked already, then unlock it
	else if (isLocked)
	{	// check if section being unlocked is the latest section that was locked
		if (lockedIndex == (sectionLocked_sequence.length - 1))
		{	// if it is the latest section that was locked,
			// go ahead and remove the section from the locked_sequence,
			if (sectionLocked_sequence.length != 0)
			{	// remove last element
				sectionLocked_sequence.splice((sectionLocked_sequence.length - 1), 1);
			}

			// as well as removing it from the userSelections_locked object
			delete userSelections_locked[facetSectionID];
			
			// set lock status to unlock
			var lockElementOutput = "<span class='lockText' onclick='ajaxFacetFilter(\""+facetSectionID+"\")'>";
			lockElementOutput += text_unlock;
			lockElementOutput += "</span>";
			facetSectionLockElement.innerHTML = lockElementOutput;
		} else {
			// maintain lock text in element as text_lock ("locked")
			var lockElementOutput = "<span class='lockText' onclick='ajaxFacetFilter(\""+facetSectionID+"\")'>";
			lockElementOutput += text_lock;
			lockElementOutput += "</span>";
			facetSectionLockElement.innerHTML = lockElementOutput;
			
			// do nothing (no AJAX call) if can't unlock
			canAJAX = false;
			alert('cannot unlock section');
		}
	}
	
	var lockedSequenceDiv = document.getElementById('lockedSequenceDisplay');
	var output = "";
	for (var i = 0; i < sectionLocked_sequence.length; i++)
	{
		// get facet name from section id (..._facetName_...)
		var start = sectionLocked_sequence[i].indexOf('_');
		var end = sectionLocked_sequence[i].lastIndexOf('_');
		var diff = end - start;
		
		// display locked sequence
		output += "<div ";
		if (i == sectionLocked_sequence.length - 1)
		{	output += "class='lockedSequence_lastSection'"; }
		else
		{	output += "class='lockedSequence_notLastSection'"; }
		output += "onclick='ajaxFacetFilter(\""+sectionLocked_sequence[i]+"\")'>";
			output += sectionLocked_sequence[i].substr(start+1, diff -1) + "<span class='hoverX'> X </span>";
		output += "</div>";
	}
	lockedSequenceDiv.innerHTML = output;
	
	// get elements for selecting sort order
	var element_sortUnique = document.getElementById('btn_sortUnique');
	var element_sortCount = document.getElementById('btn_sortCount');
	// set sort unique order based on element checked status
	if (element_sortUnique.checked == true) { var sortUnique = true; }
	else if (element_sortCount.checked == true) { var sortUnique = false; }
	else { var sortUnique = true; }
	
	/************************* AJAX Call *************************/
	if (canAJAX)
	{	// XMLHttp object for AJAX call
		var xmlhttp = new XMLHttpRequest();
		
		xmlhttp.onreadystatechange = function(){
			if (this.readyState == 4 && this.status == 200){
				if (this.responseText != "")
				{	response_decoded = JSON.parse(this.responseText)[0];
					response_resultDisplay = JSON.parse(this.responseText)[1];
					
					// -FORMAT for response_decoded indexed array-
					// "section -- category~ interval|count||interval|count  |||  category~ interval|count"
					// "section -- value|count   |||   value|count"
				
					for (var rd_i = 0; rd_i < response_decoded.length; rd_i++)
					{	// full string
						var string_facet = response_decoded[rd_i];
						// section
						var array_stringFacet = string_facet.split("--");
						var string_section = array_stringFacet[0];
					
						// check if section to display is locked or not
						var toDisplay = true;
						for (var l = 0; l < sectionLocked_sequence.length; l++)
						{	// don't display for the sections that have been locked
							// (toDisplay = false, if section is found inside sectionLocked)
							if (sectionLocked_sequence[l].includes(string_section)){
								toDisplay = false;
								break;
							}
						}
						
						// display new unique value-count for each section that's not locked
						if (toDisplay)
						{
							var targetSectionID = 'facetedFilterForm_'+string_section+'_Section';
							var targetSectionElement = document.getElementById(targetSectionID).children[1];
							var output = "";
							
							// if has values
							if (array_stringFacet[1]){
								// category or value
								var array_stringCategoryOrValueGroup = array_stringFacet[1].split("|||");
								// has category
								if (array_stringCategoryOrValueGroup[0].split("~").length == 2)
								{
									// Set text strings
									var facetSection_inputClass_super 	= 'facetedFilterFormSection_inputSuper';
									var facetSection_inputClass_sub 	= 'facetedFilterFormSection_inputSub';
									var facetSection_inputName_super 	= string_section+'Select_super[]';
									var facetSection_inputName_sub	 	= string_section+'Select_sub[]';

									// loop through each category-intervalGroup pair
									for (var i = 0; i < array_stringCategoryOrValueGroup.length; i++)
									{
										var categoryIntervalGroupPair = array_stringCategoryOrValueGroup[i].split("~");
										// unique category
										var uniqueCategory = categoryIntervalGroupPair[0];
										
										// Additional text strings for classification
										var facetSection_inputClass_super_classification = string_section+'_super_'+uniqueCategory;
										var facetSection_inputClass_sub_classification = string_section+'_sub_'+uniqueCategory;
										
										// Get total number of unique_count (category count) from all intervals in each interval group
										var categoryCount = 0;
										var array_stringIntervalGroup = categoryIntervalGroupPair[1].split("||");
										for (var j = 0; j < array_stringIntervalGroup.length; j++)
										{	var intervalCountGroupPair = array_stringIntervalGroup[j].split('|');
											// unique count
											var uniqueCount = intervalCountGroupPair[1];
											categoryCount += +uniqueCount;
										}

										// Display current category/classification
										output += "<label>";
											output += "<input type='checkbox' ";
											output +=		"class='"+facetSection_inputClass_super+" "+facetSection_inputClass_super_classification+"' ";
											output +=		"name='"+facetSection_inputName_super+"' ";
											output +=		"value='"+uniqueCategory+"' "
											output +=		"onclick='toggleFacetCheck(\""+string_section+"\", \"super\", \""+uniqueCategory+"\")' ";
											// if previously checked
											for (var c_i=0; c_i < targetSectionElement.children.length; c_i+=2)
											{	if (targetSectionElement.children[c_i].children[0].value == uniqueCategory){
													if (targetSectionElement.children[c_i].children[0].checked == true)
													{ output += "checked='checked' "; }
												}
											}
											output += "/>";
											output += uniqueCategory+" ("+categoryCount+") ";
										output += "</label><br/>";
										
										// Display all intervals for current category/classification
										for (var k = 0; k < array_stringIntervalGroup.length; k++)
										{	var intervalCountGroupPair = array_stringIntervalGroup[k].split('|');
											// unique interval
											var uniqueInterval = intervalCountGroupPair[0];
											// unique count
											var uniqueCount = intervalCountGroupPair[1];
											
											output += "<label>";
												output += "<input type='checkbox' ";
												output +=		"class='"+facetSection_inputClass_sub+" "+facetSection_inputClass_sub_classification+"' ";
												output +=		"name='"+facetSection_inputName_sub+"' ";
												output +=		"value='"+uniqueInterval+"' "
												output +=		"onclick='toggleFacetCheck(\""+string_section+"\", \"sub\", \""+uniqueCategory+"\")' ";
												// if previously checked
												for (var c_i=0; c_i < targetSectionElement.children.length; c_i+=2)
												{	if (targetSectionElement.children[c_i].children[0].value == uniqueInterval){
														if (targetSectionElement.children[c_i].children[0].checked == true)
														{ output += "checked='checked' "; }
													}
												}
												output += "/>";
												output += uniqueInterval+" ("+uniqueCount+") ";
											output += "</label><br/>";
										}
									}
								}
								// not category
								else if(array_stringCategoryOrValueGroup[0].split("~").length == 1)
								{	// set text strings
									var facetSection_inputClass_select	= 'facetedFilterFormSection_inputSuper';
									var facetSection_inputName_select	= string_section+'Select[]';
										
									// loop through each uniqueValue-count pair
									for (var i = 0; i < array_stringCategoryOrValueGroup.length; i++)
									{	var valueCountPair = array_stringCategoryOrValueGroup[i].split("|");
										// unique value
										var uniqueValue = valueCountPair[0];
										// unique count
										var uniqueCount = valueCountPair[1];
										
										output += "<label>";
											output += "<input type='checkbox' ";
											output += 		 "class='"+facetSection_inputClass_select+"' ";
											output +=		 "name='"+facetSection_inputName_select+"' ";
											output +=		 "value='"+uniqueValue+"' ";
											// if previously checked
											for (var c_i=0; c_i < targetSectionElement.children.length; c_i+=2)
											{	if (targetSectionElement.children[c_i].children[0].value == uniqueValue){
													if (targetSectionElement.children[c_i].children[0].checked == true)
													{ output += "checked='checked' "; }
												}
											}
											output += "/>";
											output += uniqueValue+" ("+uniqueCount+") ";
										output += "</label><br/>";
									}
								}
							} // no values
							else { var output = "No Matching Filters"; }
							
							// add filtered facet input to the element
							targetSectionElement.innerHTML = output;
						}
					}
				
					// Display results
					results_section_element = document.getElementById('results_section');
					if (typeof response_resultDisplay !== 'undefined' && response_resultDisplay.length > 0)
					{	var results_output = "";
						for (var rc_i = 0; rc_i < response_resultDisplay.length; rc_i++){
							results_output += "<div>";
								results_output += "<div>";
									results_output += "<div><span>"
														+ response_resultDisplay[rc_i]['movieType']
														+ "<br/>Rating: "+response_resultDisplay[rc_i]['contentRating']
													+ "</span></div>";
									results_output += "<div><span>"+response_resultDisplay[rc_i]['movieTitle']+"</span></div>";
									results_output += "<div>meta score<br/>"+response_resultDisplay[rc_i]['metaScore']+"/100</div>";
									results_output += "<div>rating<br/>"+response_resultDisplay[rc_i]['imdbRating']+"/10.0</div>";
									results_output += "<div>votes<br/>"+response_resultDisplay[rc_i]['imdbVotes']+"</div>";
								results_output += "</div>";
								
								results_output += "<div>";
									results_output += "<br/><div><span>Genre: </span>";
									var prefix = "";
									for (var i = 0; i < response_resultDisplay[rc_i]['movieGenre'].length; i++)
									{ 
										results_output += !(prefix)?"":prefix;
										results_output += response_resultDisplay[rc_i]['movieGenre'][i];
										prefix = ", ";
									}
									results_output += "</div>";
									
									results_output += "<br/><div><span>Language: </span>";
									prefix = "";
									for (var i = 0; i < response_resultDisplay[rc_i]['movieLanguage'].length; i++)
									{
										results_output += !(prefix)?"":prefix;
										results_output += response_resultDisplay[rc_i]['movieLanguage'][i];
										prefix = ", ";
									}
									results_output += "</div>";
									
									results_output += "<br/><div><span>Country: </span>";
									prefix = "";
									for (var i = 0; i < response_resultDisplay[rc_i]['movieCountry'].length; i++)
									{
										results_output += !(prefix)?"":prefix;
										results_output += response_resultDisplay[rc_i]['movieCountry'][i];
										prefix = ", ";
									}
									results_output += "</div><br/>";
								
									results_output += "<a target='_blank' href='http://www.imdb.com/title/"
													+ response_resultDisplay[rc_i]['imdbRefID']
													+ "'><div class='ext_link_imdb'>";
										results_output += "To IMDb Page";
									results_output += "</div></a>";
								results_output += "</div>";
								
								results_output += "<div>";
									results_output += "<div>Seasons: ";
									results_output += (response_resultDisplay[rc_i]['totalSeason'] == 0)?'No':response_resultDisplay[rc_i]['totalSeason']
									results_output += " season</div>";
									results_output += "<div>Duration: "+response_resultDisplay[rc_i]['movieDuration']+" mins</div>";
									results_output += "<div>Released Since: "+response_resultDisplay[rc_i]['releasedDate']+"</div>";
								results_output +=  "</div>";
								
							results_output += "</div>";
						}
						results_section_element.innerHTML = results_output;
					} else { results_section_element.innerHTML = "<div>No Matching Movie Content</div>"; }
				} //else {	console.log("empty response");	}
			}
		};
		
		// url to respond to AJAX call for progressive facet search
		var url = "user_progressiveFacetSearch.php";
		
		// open POST request, and set header for POST request
		xmlhttp.open("POST", url, true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		
		// convert array to JSON string
		var userSelections_locked_jsonString = JSON.stringify(userSelections_locked);
		var var_send = "userSelections="+encodeURIComponent(userSelections_locked_jsonString);
		var_send += "&sortOrder="+sortUnique;
		// send AJAX POST request
		xmlhttp.send(var_send);
	} // finish AJAX call

}

// JavaScript : Function to sort by unique values alphabetically, or by counts highest-first
function sortUniqueOrCount(sortUnique = true)
{	//var userSelectionChecked = {};
	
	/* Get All checked values *
	var filterForm_sections = document.getElementById('facetedFilterForm_Content').children;
	for (var ff_i = 0; ff_i < filterForm_sections.length; ff_i++)
	{
		var filterForm_sections_Element = filterForm_sections[ff_i];
		var filterForm_sections_ElementID = filterForm_sections_Element.id;
		
		// look into each input element for the each current section
		for (var ffc_i = 0; ffc_i < filterForm_sections_Element.children[1].children.length; ffc_i += 2 )
		{	var current_input = filterForm_sections_Element.children[1].children[ffc_i].children[0];
			
			// if section is checked, add into user selections array
			if(current_input.checked == true){
				if (userSelectionChecked[filterForm_sections_ElementID] == undefined)
				{ userSelectionChecked[filterForm_sections_ElementID] = {}; }
				
				// create a category array to store intervals
				if ( userSelectionChecked[filterForm_sections_ElementID][current_input.name] == undefined )
				{ userSelectionChecked[filterForm_sections_ElementID][current_input.name] = []; }
				
				// store intervals into category array
				userSelectionChecked[filterForm_sections_ElementID][current_input.name].push(current_input.value);
			}
		}
	}*/
	
	// XMLHttp object for AJAX call
	var xmlhttp = new XMLHttpRequest();

	/*
	 * Function to execute after receiving response
	 */
	xmlhttp.onreadystatechange = function(){
		if (this.readyState == 4 && this.status == 200){
			if (this.responseText != "")
			{	response_decoded = JSON.parse(this.responseText)[0];
				
				// -FORMAT for response_decoded indexed array-
				// "section : category~ interval|count||interval|count  |||  category~ interval|count"
				// "section : value|count   |||   value|count"
			
				for (var rd_i = 0; rd_i < response_decoded.length; rd_i++)
				{	// full string
					var string_facet = response_decoded[rd_i];
					// section
					var array_stringFacet = string_facet.split("--");
					var string_section = array_stringFacet[0];
					
					// check if section to display is locked or not
					var toDisplay = true;
					for (var l = 0; l < sectionLocked_sequence.length; l++)
					{	// don't display for the sections that have been locked
						// (toDisplay = false, if section is found inside sectionLocked)
						if (sectionLocked_sequence[l].includes(string_section)){
							toDisplay = false;
							break;
						}
					}
					
					// display new unique value-count for each section that's not locked
					if (toDisplay)
					{	var targetSectionID = 'facetedFilterForm_'+string_section+'_Section';
						var targetSectionElement = document.getElementById(targetSectionID).children[1];
						var output = "";
					
						// if has values
						if (array_stringFacet[1]){
							// category or value
							var array_stringCategoryOrValueGroup = array_stringFacet[1].split("|||");
							// has category
							if (array_stringCategoryOrValueGroup[0].split("~").length == 2)
							{	// Set text strings
								var facetSection_inputClass_super 	= 'facetedFilterFormSection_inputSuper';
								var facetSection_inputClass_sub 	= 'facetedFilterFormSection_inputSub';
								var facetSection_inputName_super 	= string_section+'Select_super[]';
								var facetSection_inputName_sub	 	= string_section+'Select_sub[]';
	
								// loop through each category-intervalGroup pair
								for (var i = 0; i < array_stringCategoryOrValueGroup.length; i++)
								{	var categoryIntervalGroupPair = array_stringCategoryOrValueGroup[i].split("~");
									// unique category
									var uniqueCategory = categoryIntervalGroupPair[0];
									
									// Additional text strings for classification
									var facetSection_inputClass_super_classification = string_section+'_super_'+uniqueCategory;
									var facetSection_inputClass_sub_classification = string_section+'_sub_'+uniqueCategory;
									
									// Get total number of unique_count (category count) from all intervals in each interval group
									var categoryCount = 0;
									var array_stringIntervalGroup = categoryIntervalGroupPair[1].split("||");
									for (var j = 0; j < array_stringIntervalGroup.length; j++)
									{ 
										var intervalCountGroupPair = array_stringIntervalGroup[j].split('|');
										// unique count
										var uniqueCount = intervalCountGroupPair[1];
										categoryCount += +uniqueCount;
									}
									
									// Display current category/classification
									output += "<label>";
										output += "<input type='checkbox' ";
										output +=		"class='"+facetSection_inputClass_super+" "+facetSection_inputClass_super_classification+"' ";
										output +=		"name='"+facetSection_inputName_super+"' ";
										output +=		"value='"+uniqueCategory+"' "
										output +=		"onclick='toggleFacetCheck(\""+string_section+"\", \"super\", \""+uniqueCategory+"\")' ";
										// if previously checked
										for (var c_i=0; c_i < targetSectionElement.children.length; c_i+=2)
										{	if (targetSectionElement.children[c_i].children[0].value == uniqueCategory){
												if (targetSectionElement.children[c_i].children[0].checked == true)
												{ output += "checked='checked' "; }
											}
										}
										output += "/>";
										output += uniqueCategory+" ("+categoryCount+") ";
									output += "</label><br/>";
									
									// Display all intervals for current category/classification
									for (var k = 0; k < array_stringIntervalGroup.length; k++)
									{	var intervalCountGroupPair = array_stringIntervalGroup[k].split('|');
										// unique interval
										var uniqueInterval = intervalCountGroupPair[0];
										// unique count
										var uniqueCount = intervalCountGroupPair[1];
										
										output += "<label>";
											output += "<input type='checkbox' ";
											output +=		"class='"+facetSection_inputClass_sub+" "+facetSection_inputClass_sub_classification+"' ";
											output +=		"name='"+facetSection_inputName_sub+"' ";
											output +=		"value='"+uniqueInterval+"' "
											output +=		"onclick='toggleFacetCheck(\""+string_section+"\", \"sub\", \""+uniqueCategory+"\")' ";
											// if previously checked
											for (var c_i=0; c_i < targetSectionElement.children.length; c_i+=2)
											{	if (targetSectionElement.children[c_i].children[0].value == uniqueInterval){
													if (targetSectionElement.children[c_i].children[0].checked == true)
													{ output += "checked='checked' "; }
												}
											}
											output += "/>";
											output += uniqueInterval+" ("+uniqueCount+") ";
										output += "</label><br/>";
									}
								}
							}
							// not category
							else if(array_stringCategoryOrValueGroup[0].split("~").length == 1)
							{	// set text strings
								var facetSection_inputClass_select	= 'facetedFilterFormSection_inputSuper';
								var facetSection_inputName_select	= string_section+'Select[]';

								// loop through each uniqueValue-count pair
								for (var i = 0; i < array_stringCategoryOrValueGroup.length; i++)
								{	var valueCountPair = array_stringCategoryOrValueGroup[i].split("|");
									// unique value
									var uniqueValue = valueCountPair[0];
									// unique count
									var uniqueCount = valueCountPair[1];
									
									output += "<label>";
										output += "<input type='checkbox' ";
										output += 		 "class='"+facetSection_inputClass_select+"' ";
										output +=		 "name='"+facetSection_inputName_select+"' ";
										output +=		 "value='"+uniqueValue+"' ";
										// if previously checked
										for (var c_i=0; c_i < targetSectionElement.children.length; c_i+=2)
										{	if (targetSectionElement.children[c_i].children[0].value == uniqueValue){
												if (targetSectionElement.children[c_i].children[0].checked == true)
												{ output += "checked='checked'"; }
											}
										}
										output += "/>";
										output += uniqueValue+" ("+uniqueCount+") ";
									output += "</label><br/>";
								}
							}
						} else { var output = "No Matching Filters"; }
						
						// add filtered facet input to the element
						targetSectionElement.innerHTML = output;
					}
				}
			} //else {	console.log("empty response");	}
		}
	}
	
	// url to respond to AJAX call for progressive facet search
	var url = "user_progressiveFacetSearch.php";
	
	// open POST request, and set header for POST request
	xmlhttp.open("POST", url, true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	
	// convert array to JSON string
	var userSelections_locked_jsonString = JSON.stringify(userSelections_locked);
	var var_send = "userSelections="+encodeURIComponent(userSelections_locked_jsonString);
	/*var userSelectionChecked_jsonString = JSON.stringify(userSelectionChecked);
	var var_send = "userSelections="+encodeURIComponent(userSelectionChecked_jsonString);*/
	var_send += "&sortOrder="+sortUnique;
	// send AJAX POST request
	xmlhttp.send(var_send);
	
	// finish AJAX call
}
