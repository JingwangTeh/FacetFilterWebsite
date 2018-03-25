<?php
	/*
	 * Associative array of filters classification for multiple facets
	 */
	$filterClassification = array(
		$facetName_releasedDate	 => array(
			'interval' 			=> array(
				'unknown'		=> array(
					array(
						array('0 - 1970', -1)
					), -1
				),
				'long ago'		=> array(
					array(
						array('1970+ - 1980', 1970),
						array('1980+ - 1990', 1980),
						array('1990+ - 2000', 1990)
					), 1970
				),
				'some time ago'=> array(
					array(
						array('2000+ - 2005', 2000),
						array('2005+ - 2010', 2005)
					), 2000
				),
				'recent'		=> array(
					array(
						array('2010+ - 2015', 2010),
						array('2015+', 2015)
					), 2010
				)
			)
		),
		$facetName_totalSeason	=> array(
			'interval' 			=> array(
				'short series'	=> array(
					array(
						array('0 - 1', -1),
						array('1+ - 3', 1)
					), -1
				),
				'average series'=> array(
					array(
						array('3+ - 5', 3),
						array('5+ - 10', 5)
					), 3
				),
				'long series'	=> array(
					array(
						array('10+ - 25', 10),
						array('25+', 25)
					), 10
				)
			)
		),
		$facetName_movieDuration => array(
			'interval'			=> array(
				'short' 		=> array(
					array(
						array('0 - 2', -1),
						array('2+ - 5', 2),
						array('5+ - 7', 5),
						array('7+ - 10', 7)
					), -1
				),
				'medium' 		=> array(
					array(
						array('10+ - 20', 10),
						array('20+ - 30', 20)
					), 10
				),
				'long' 		=> array(
					array(
						array('30+ - 60', 30),
						array('60+ - 120', 60)
					), 30
				),
				'very long' 	=> array(
					array(
						array('120+', 60)
					), 120
				)
			)
		),
		$facetName_metaScore	=> array(
			'interval' 			=> array(
				'terrible'		=> array(
					array(
						array('0 - 10', -1),
						array('10+ - 25', 10)
					), -1
				),
				'bad'		 	=> array(
					array(
						array('25+ - 35', 25),
						array('35+ - 50', 35)
					), 25
				),
				'average'		=> array(
					array(
						array('50+ - 65', 50)
					), 50
				),
				'good'			=> array(
					array(
						array('65+ - 75', 65),
						array('75+ - 85', 75)
					), 65
				),
				'excellent'	=> array(
					array(
						array('85+ - 95', 85),
						array('95+ - 100', 95)
					), 85
				)
			)
		),
		$facetName_imdbRating	=> array(
			'interval' 			=> array(
				'very low' 	=> array(
					array(
						array('0.0 - 1.0', -0.1),
						array('1.0+ - 2.5', 1.0)
					), -0.1
				),
				'bad' 			=> array(
					array(
						array('2.5+ - 3.5', 2.5),
						array('3.5+ - 5.0', 3.5)
					), 2.5
				),
				'average' 		=> array(
					array(
						array('5.0+ - 6.5', 5.0)
					), 5.0
				),
				'good' 		=> array(
					array(
						array('6.5+ - 7.5', 6.5),
						array('7.5+ - 8.5', 7.5)
					), 6.5
				),
				'excellent' 	=> array(
					array(
						array('8.5+ - 9.5', 8.5),
						array('9.5+ - 10.0', 9.5)
					), 8.5
				)
			)
		),
		$facetName_imdbVotes => array(
			'interval' 			=> array(
				'very low vote'=> array(
					array(
						array('0 - 50', -1),
						array('50+ - 250', 50)
					), -1
				),
				'low vote'		=> array(
					array(
						array('250+ - 500', 250),
						array('500+ - 750', 500),
						array('750+ - 1,000', 750)
					), 250
				),
				'average vote' => array(
					array(
						array('1,000+ - 2,500', 1000),
						array('2,500+ - 5,000', 2500),
						array('5,000+ - 10,000', 5000)
					), 1000
				),
				'high vote'	=> array(
					array(
						array('10,000+ - 25,000', 10000),
						array('25,000+ - 50,000', 25000),
						array('50,000+ - 100,000', 50000)
					), 10000
				),
				'very high vote'=> array(
					array(
						array('100,000+ - 500,000', 100000),
						array('500,000+ - 1,000,000', 500000),
						array('1,000,000+', 1000000)
					), 100000
				)
			)
		)
	);
?>