// JavaScript : Function to disallow checkbox checking both super categories and sub categories
function toggleFacetCheck(facet, super_or_sub, classification)
{	// class names of super and sub checkboxes
	var toggleTrigger = facet + "_" + super_or_sub + "_" + classification;
	var toggleTarget;
	
	// if checkbox is super AND is checked
	if (super_or_sub == "super"){
		if (document.getElementsByClassName(toggleTrigger)[0].checked == true)
		{	// then uncheck the sub checkbox
			toggleTarget = facet + "_sub_" + classification;
			var toggleTargetElement = document.getElementsByClassName(toggleTarget)
			
			for (var i = 0; i < toggleTargetElement.length; i++)
			{ toggleTargetElement[i].checked = false; }
		}
	} // if checkbox is sub AND is checked
	else if (super_or_sub == "sub"){	
		// as long as there's one sub checkbox that is checked, uncheck the super checkbox
		var toggleTriggerElement = document.getElementsByClassName(toggleTrigger);
		for (var i = 0; i < toggleTriggerElement.length; i++)
		{	if (toggleTriggerElement[i].checked == true)
			{	toggleTarget = facet + "_super_" + classification;
				var toggleTargetElement = document.getElementsByClassName(toggleTarget)
				toggleTargetElement[0].checked = false
				break;
			}
		}
	}
}

// JavaScript (UserPage) : Function to push/pull Div Sections for display
function setPushPull(elementID)
{	// Get Element
	var targetElement = document.getElementsByClassName(elementID)[0];
	// Push Div
	if(targetElement.classList.contains('pullDiv')){
		targetElement.classList.remove('pullDiv');
		targetElement.classList.add('pushDiv');
	}
}

// JavaScript (UserPage) : Function to extend/shorten Div Sections for display
function clickDisplayID(elementID)
{	// Get Element
	var targetElement = document.getElementById(elementID);
	
	// Extend Div
	if(targetElement.classList.contains('shortenDiv')){
		targetElement.classList.remove('shortenDiv');
		targetElement.classList.add('extendDiv');
	} // Shorten Div
	else if(targetElement.classList.contains('extendDiv')){
		targetElement.classList.remove('extendDiv');
		targetElement.classList.add('shortenDiv');
	}
}

// Javascript (DevPage) : Function to select tabs by displaying/hiding tab sections
function tabSelect(tabID, tabClass, 
				   tabButton = '', tabButtonClass = '', 
				   hasTabLine = false, tabLineClass = ''){
	var tabs = document.getElementsByClassName(tabClass);
	for (var i = 0; i < tabs.length; i++)
	{ tabs[i].style.display = "none"; }
	
	// if <hr/> line underneath is active
	if (hasTabLine) {
		var tab_btns = document.getElementsByClassName(tabButtonClass);
		for (var j = 0; j < tab_btns.length; j++)
		{ tab_btns[j].classList.remove(tabLineClass); }
	}

	document.getElementById(tabID).style.display = "block";
	if (hasTabLine) tabButton.classList.add(tabLineClass);
}

// JavaScript (DevPage) : Function to extend/shorten Div Sections for display
function toggleDisplayHorizontal(elementID)
{	// Get Element
	var targetElement = document.getElementById(elementID);

	// Extend Div
	if(targetElement.classList.contains('shortenDiv')){
		targetElement.classList.remove('shortenDiv');
		targetElement.classList.add('extendDiv');
	} // Shorten Div
	else if(targetElement.classList.contains('extendDiv')){
		targetElement.classList.remove('extendDiv');
		targetElement.classList.add('shortenDiv');
	}
}

// Javascript : Function to send AJAX call to OMDB Api to retrieve record information
function getData_OMDbAPI(){
	var ID = document.getElementById('i').value;
	
	var xhttpAPI = new XMLHttpRequest();
	xhttpAPI.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			var api_response_decoded = JSON.parse(this.responseText);
			
			// if response is valid (valid record)
			if (api_response_decoded['Response'] == "True"){
				//console.log(api_response_decoded);
				updateDatabase_OMDbAPI(api_response_decoded);							
			} // Debug
			else {
				console.log('invalid response from api');
				console.log(api_response_decoded);
			}
		}
	};
	xhttpAPI.open("GET", "http://www.omdbapi.com/?i="+ID, true);
	xhttpAPI.send();
}

// Javascript : Function to update database with record information obtained from OMDB Api
function updateDatabase_OMDbAPI(api_response_decoded){
	var xhttpUpdate = new XMLHttpRequest();
	xhttpUpdate.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			var result = this.responseText;
			
			// if record successfully created
			if (result == "true"){
				console.log("record creation success");
			} else { console.log("record creation failed"); }
		}
	};
	
	// url to respond to AJAX call
	var url = "dev_recordCreate_OMDbAPI.php";
	
	// open POST request, and set header for POST request
	xhttpUpdate.open("POST", url, true);
	xhttpUpdate.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

	// convert array to JSON string
	var api_response_decoded_jsonString = JSON.stringify(api_response_decoded);
	var var_send = "api_response="+encodeURIComponent(api_response_decoded_jsonString);
	// send AJAX POST request
	xhttpUpdate.send(var_send);
}

// Javascript : Function to set value to an element
function setToInput(word, targetElement){
	ID = document.getElementById(targetElement);
	ID.value = word.innerHTML;
}

// Javascript : Function to hide Popup
function popup_disappear(targetElement){
	document.getElementById(targetElement).style.display = "none";
}

// Javascript : Function to send AJAX call to get list of matching RefID
function getData_FormAutoComplete(wordsElement, suggestionElement){
	var ID = document.getElementById(wordsElement).value;
	
	var xhttpUpdate = new XMLHttpRequest();
	xhttpUpdate.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			//console.log(this.responseText);
			if(this.responseText){ 
				var target = document.getElementById(suggestionElement);
				target.style.display = "block";
				
				var record_details = JSON.parse(this.responseText);
				
				target.innerHTML = "<ul>";
				for (var i=0; i<record_details.length; i++)
				{ target.innerHTML += "<li onclick='setToInput(this, \""+wordsElement+"\")'>"+ record_details[i] +"</li>"; }
				target.innerHTML += "</ul>";
				
				//console.log(record_details);
			} else { document.getElementById(suggestionElement).style.display = "none"; }
		}
	}
	
	// url to respond to AJAX call
	var url = "dev_recordRefIDList_Form.php";
	
	// open POST request, and set header for POST request
	xhttpUpdate.open("POST", url, true);
	xhttpUpdate.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

	// convert array to JSON string
	var var_send = "RefID="+encodeURIComponent(ID);
	// send AJAX POST request
	xhttpUpdate.send(var_send);
}

// Javascript : Function to send AJAX call to get list of matching RefID based on user selections
function getData_FormAutoComplete_Filtered(wordsElement, suggestionElement){
	var ID = document.getElementById(wordsElement).value;
	var userSelectionChecked = {};
	
	/* Get All checked values */
	var filterForm_sections = document.getElementById('facetedFilterForm_Content').children;
	for (var ff_i = 0; ff_i < filterForm_sections.length; ff_i++)
	{	var filterForm_sections_Element = filterForm_sections[ff_i];
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
	}
	
	var xhttpUpdate = new XMLHttpRequest();
	xhttpUpdate.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			if(this.responseText){ 
				var target = document.getElementById(suggestionElement);
				target.style.display = "block";
				
				var record_details = JSON.parse(this.responseText);
				
				target.innerHTML = "<ul>";
				for (var i=0; i<record_details.length; i++)
				{ target.innerHTML += "<li onclick='setToInput(this, \""+wordsElement+"\")'>"+ record_details[i] +"</li>"; }
				target.innerHTML += "</ul>";
			} else { document.getElementById(suggestionElement).style.display = "none"; }
		}
	}
	
	// url to respond to AJAX call
	var url = "dev_recordTitleList_Form.php";
	
	// open POST request, and set header for POST request
	xhttpUpdate.open("POST", url, true);
	xhttpUpdate.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

	// convert array to JSON string
	var var_send = "RefID="+encodeURIComponent(ID);
	var userSelectionChecked_jsonString = JSON.stringify(userSelectionChecked);
	var_send += "&userSelections="+encodeURIComponent(userSelectionChecked_jsonString);
	// send AJAX POST request
	xhttpUpdate.send(var_send);
}

// Javascript : Function to send AJAX call to retrieve database record by RefID
function getData_FormData(formTypeChar){
	if (formTypeChar == 'u') var targetID = 'UForm_search';
	else if (formTypeChar == 'd') var targetID = 'DForm_search';
	var ID = document.getElementById(targetID).value;
	
	var xhttpUpdate = new XMLHttpRequest();
	xhttpUpdate.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			if (formTypeChar == 'u'){
				if(this.responseText){ 
					var record_details = JSON.parse(this.responseText);
					document.getElementById('UForm_movieTitle').value 		= record_details['movieTitle'];
					document.getElementById('UForm_imdbRefID').value 		= record_details['imdbRefID'];
					document.getElementById('UForm_releasedDate').value 	= record_details['releasedDate'];
					document.getElementById('UForm_movieType').value 		= record_details['movieType'];
					document.getElementById('UForm_contentRating').value 	= record_details['contentRating'];
					document.getElementById('UForm_movieGenre').value 		= record_details['movieGenre'];
					document.getElementById('UForm_totalSeason').value 		= record_details['totalSeason'];
					document.getElementById('UForm_movieDuration').value 	= record_details['movieDuration'];
					document.getElementById('UForm_movieLanguage').value 	= record_details['movieLanguage'];
					document.getElementById('UForm_movieCountry').value 	= record_details['movieCountry'];
					document.getElementById('UForm_metaScore').value 		= record_details['metaScore'];
					document.getElementById('UForm_imdbRating').value 		= record_details['imdbRating'];
					document.getElementById('UForm_imdbVotes').value 		= record_details['imdbVotes'];
				} else { 
					document.getElementById('UForm_movieTitle').value 		= "";
					document.getElementById('UForm_imdbRefID').value 		= "";
					document.getElementById('UForm_releasedDate').value 	= "";
					document.getElementById('UForm_movieType').value 		= "";
					document.getElementById('UForm_contentRating').value 	= "";
					document.getElementById('UForm_movieGenre').value 		= "";
					document.getElementById('UForm_totalSeason').value 		= "";
					document.getElementById('UForm_movieDuration').value 	= "";
					document.getElementById('UForm_movieLanguage').value 	= "";
					document.getElementById('UForm_movieCountry').value 	= "";
					document.getElementById('UForm_metaScore').value 		= "";
					document.getElementById('UForm_imdbRating').value 		= "";
					document.getElementById('UForm_imdbVotes').value 		= "";
				}
			} else if (formTypeChar == 'd'){
				if(this.responseText){ 
					var record_details = JSON.parse(this.responseText);
					document.getElementById('DForm_movieTitle').innerHTML 		= 
						"<div><div>movieTitle : </div><div>"	+record_details['movieTitle']		+"</div></div>";
					document.getElementById('DForm_imdbRefID').innerHTML 		= 
						"<div><div>imdbRefID : </div><div>"		+record_details['imdbRefID']		+"</div></div>";
					document.getElementById('DForm_releasedDate').innerHTML 	= 
						"<div><div>releasedDate : </div><div>"	+record_details['releasedDate']		+"</div></div>";
					document.getElementById('DForm_movieType').innerHTML 		= 
						"<div><div>movieType : </div><div>"		+record_details['movieType']		+"</div></div>";
					document.getElementById('DForm_contentRating').innerHTML 	= 
						"<div><div>contentRating : </div><div>"	+record_details['contentRating']	+"</div></div>";
					document.getElementById('DForm_movieGenre').innerHTML 		= 
						"<div><div>movieGenre : </div><div>"	+record_details['movieGenre']		+"</div></div>";
					document.getElementById('DForm_totalSeason').innerHTML 		= 
						"<div><div>totalSeason : </div><div>"	+record_details['totalSeason']		+"</div></div>";
					document.getElementById('DForm_movieDuration').innerHTML	= 
						"<div><div>movieDuration : </div><div>"	+record_details['movieDuration']	+"</div></div>";
					document.getElementById('DForm_movieLanguage').innerHTML	= 
						"<div><div>movieLanguage : </div><div>"	+record_details['movieLanguage']	+"</div></div>";
					document.getElementById('DForm_movieCountry').innerHTML		= 
						"<div><div>movieCountry : </div><div>"	+record_details['movieCountry']		+"</div></div>";
					document.getElementById('DForm_metaScore').innerHTML 		= 
						"<div><div>metaScore : </div><div>"		+record_details['metaScore']		+"</div></div>";
					document.getElementById('DForm_imdbRating').innerHTML 		= 
						"<div><div>imdbRating : </div><div>"	+record_details['imdbRating']		+"</div></div>";
					document.getElementById('DForm_imdbVotes').innerHTML		= 
						"<div><div>imdbVotes : </div><div>"		+record_details['imdbVotes']		+"</div></div>";
				} else {
					document.getElementById('DForm_movieTitle').innerHTML 		= "";
					document.getElementById('DForm_imdbRefID').innerHTML 		= "";
					document.getElementById('DForm_releasedDate').innerHTML 	= "";
					document.getElementById('DForm_movieType').innerHTML 		= "";
					document.getElementById('DForm_contentRating').innerHTML 	= "";
					document.getElementById('DForm_movieGenre').innerHTML 		= "";
					document.getElementById('DForm_totalSeason').innerHTML 		= "";
					document.getElementById('DForm_movieDuration').innerHTML	= "";
					document.getElementById('DForm_movieLanguage').innerHTML	= "";
					document.getElementById('DForm_movieCountry').innerHTML		= "";
					document.getElementById('DForm_metaScore').innerHTML 		= "";
					document.getElementById('DForm_imdbRating').innerHTML 		= "";
					document.getElementById('DForm_imdbVotes').innerHTML		= "";
				}
			}
		}
	};
	
	// url to respond to AJAX call
	var url = "dev_recordInfo_Form.php";
	
	// open POST request, and set header for POST request
	xhttpUpdate.open("POST", url, true);
	xhttpUpdate.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

	// convert array to JSON string
	var var_send = "RefID="+encodeURIComponent(ID);
	// send AJAX POST request
	xhttpUpdate.send(var_send);
}
