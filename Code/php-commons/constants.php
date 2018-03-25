<?php
	// default text string
	$lockText_default = "unlock";
	
	/*
	 * Strings for column names of the collection
	 */
	// facet name, db name, isCategory, isCommaDelimited, isDate
	$facetInfoArray = array(
		array('releasedDate'	, 'released_date_interval'	, true	, false	, true),
		array('movieType'		, 'movie_type'				, false	, false	, false),
		array('contentRating'	, 'content_rating'			, false	, false	, false),
		array('movieGenre'		, 'movie_genre'				, false	, true	, false),
		array('totalSeason'		, 'total_season_interval'	, true	, false	, false),
		array('movieDuration'	, 'movie_duration_interval'	, true	, false	, false),
		array('movieLanguage'	, 'movie_language'			, false	, true	, false),
		array('movieCountry'	, 'movie_country'			, false	, true	, false),
		array('metaScore'		, 'meta_score_interval'		, true	, false	, false),
		array('imdbRating'		, 'imdb_rating_interval'	, true	, false	, false),
		array('imdbVotes'		, 'imdb_votes_interval'		, true	, false	, false)
	);

	$facetName_releasedDate = $facetInfoArray[0][0];
	$facetName_totalSeason = $facetInfoArray[4][0];
	$facetName_movieDuration = $facetInfoArray[5][0];
	$facetName_metaScore = $facetInfoArray[8][0];
	$facetName_imdbRating = $facetInfoArray[9][0];
	$facetName_imdbVotes = $facetInfoArray[10][0];
	
	// imdbRefID db column name for querying in update form
	$dbName_searchTitle = "movie_title";
	$dbName_searchRefID = "imdb_Ref_ID";
	
	// db name, interval db name, facet name
	$valueAndIntervalArray = array(
		array('released_date'	, 'released_date_interval'	, 'releasedDate'),
		array('total_season'	, 'total_season_interval'	, 'totalSeason'),
		array('movie_duration'	, 'movie_duration_interval'	, 'movieDuration'),
		array('meta_score'		, 'meta_score_interval'		, 'metaScore'),
		array('imdb_rating'		, 'imdb_rating_interval'	, 'imdbRating'),
		array('imdb_votes'		, 'imdb_votes_interval'		, 'imdbVotes')
	);

	/*
	 * Array for Form validation
	 */
	// (0-4) 	api name, db name, isCategory/hasInterval, interval db name, facet name, ...
	// (5-9)	...booleans : isCommaDelimited, isDate, isNum, isNumSpaceSep, isNumCommaSep, ...
	// (10-14)	..............isInt, isFloat, hasRange, min, max
	$dbInfoArray = array(
		array('Title'			, 'movie_title'		, false	, ''						, 'movieTitle'						
			, false	, false	, false	, false	, false	
			, false	, false	, false	, 0		, 0
		),
		array('imdbID'			, 'imdb_Ref_ID'		, false	, ''						, 'imdbRefID'						
			, false	, false	, false	, false	, false	
			, false	, false	, false	, 0		, 0
		),
		array('Released'		, 'released_date'	, true	, 'released_date_interval'	, $facetInfoArray[0][0]		
			, false	, true	, false	, false	, false	
			, false	, false	, false	, 0		, 0
		),
		array('Type'			, 'movie_type'		, false	, ''						, $facetInfoArray[1][0]		
			, false	, false	, false	, false	, false	
			, false	, false	, false	, 0		, 0
		),
		array('Rated'			, 'content_rating'	, false	, ''						, $facetInfoArray[2][0]		
			, false	, false	, false	, false	, false	
			, false	, false	, false	, 0		, 0
		),
		array('Genre'			, 'movie_genre'		, false	, ''						, $facetInfoArray[3][0]		
			, true	, false	, false	, false	, false	
			, false	, false	, false	, 0		, 0
		),
		array('totalSeasons'	, 'total_season'	, true	, 'total_season_interval'	, $facetInfoArray[4][0]		
			, false	, false	, true	, false	, false	
			, true	, false	, false	, 0		, 0
		),
		array('Runtime'			, 'movie_duration'	, true	, 'movie_duration_interval'	, $facetInfoArray[5][0]		
			, false	, false	, true	, true	, false	
			, true	, false	, false	, 0		, 0
		),
		array('Language'		, 'movie_language'	, false	, ''						, $facetInfoArray[6][0]		
			, true	, false	, false	, false	, false	
			, false	, false	, false	, 0		, 0
		),
		array('Country'			, 'movie_country'	, false	, ''						, $facetInfoArray[7][0]		
			, true	, false	, false	, false	, false	
			, false	, false	, false	, 0		, 0
		),
		array('Metascore'		, 'meta_score'		, true	, 'meta_score_interval'		, $facetInfoArray[8][0]		
			, false	, false	, true	, false	, false	
			, true	, false	, true	, 0		, 100
		),
		array('imdbRating'		, 'imdb_rating'		, true	, 'imdb_rating_interval'	, $facetInfoArray[9][0]		
			, false	, false	, true	, false	, false	
			, false	, true	, true	, 0		, 10
		),
		array('imdbVotes'		, 'imdb_votes'		, true	, 'imdb_votes_interval'		, $facetInfoArray[10][0]	
			, false	, false	, true	, false	, true	
			, true	, false	, false	, 0		, 0
		)
	);
?>