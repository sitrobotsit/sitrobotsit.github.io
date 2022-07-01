<?php

// cron run by www.setcronjob.com
mb_internal_encoding("UTF-8");
require_once("dbConstants.php");


$netflixcountry= $_GET['c'];

if ($netflixcountry == 'usa')
{
	$url =  'http://netflixusacompletelist.blogspot.co.uk/';
}
else
{
	$url =  'http://netflixukcompletelist.blogspot.co.uk/';
}

include('php_html_dom/simple_html_dom.php');

#$url = 'test-raw-data.html';
$html = file_get_html($url);

// Find all listings
foreach($html->find('div.listing') as $movie_container)
{

	$year_junk = array("(", ")");

	$listing['title'] = trim($movie_container->find('div.name', 0)->plaintext);

	#$listing['title'] = htmlspecialchars_decode($listing['title']);


	#$test = 'This is a &#269;&#x5d0; test&#39;';

	#echo $test . "<br />\n";
	#echo preg_replace_callback('/&#([0-9a-fx]+);/mi', 'replace_num_entity', $test);

	$listing['title'] =   preg_replace_callback('/&#([0-9a-fx]+);/mi', 'replace_num_entity', $listing['title']);

	$listing['year'] = trim($movie_container->find('span.year', 0)->plaintext);
	$listing['image'] = trim($movie_container->find('div.thumbnail img', 0)->src);

	if (preg_match("/, The/", $listing['title']))
	{
		$listing['title'] = "The " . preg_replace("~, The~",'', $listing['title']);
	}

	if (preg_match("/, A/", $listing['title']))
	{
		$listing['title'] = "A " . preg_replace("~, A~",'', $listing['title']);
	}

	$listing['year'] = str_replace($year_junk, "", $listing['year']);

	// if TV
	if  (preg_match("/season/i", $listing['title'])
		OR preg_match("/series/i", $listing['title'])
		OR preg_match("/episodes/i", $listing['title']))
	{
		$listing['tv_title'] = $listing['title'];
		$title_junk = array("-", ", The");
		$listing['title'] = str_replace($title_junk, "", $listing['title']);
		$listing['title'] = preg_replace("~Season\s[0-9]+~",'', $listing['title']);
		$listing['title'] = preg_replace("~Series\s[0-9]+~",'', $listing['title']);
		$listing['title'] = preg_replace("~Episodes\s[0-9]+~",'', $listing['title']);
		$listing['title'] = trim($listing['title']);


		/*$listing['tv_title'] = $listing['title'];*/
	}
	// movie
	else
	{
		$listing['year'] = substr($listing['year'], 0, 4);
		$listing['tv_title'] = '';
	}




	imbd($listing['title'], $listing['year'], $listing['tv_title'],$listing['image'], $netflixcountry);

	unset($listing);

}



function safe($value){
	return mysql_real_escape_string($value);
}


function imbd($title, $year, $tv_title = '', $netfliximage,  $netflixcountry)
{

	$raw_title = $title;
	$title = urlencode($title);

	if($tv_title != '')
	{
		$post_year =  '';
	}
	else
	{
		$post_year = $year;
	}

	$url = "http://www.omdbapi.com/?t=" . $title . "&y=". $post_year . "&tomatoes=true";

	$imdb_json=file_get_contents($url);


#	$imdb_json = '{"Title":"All Wifed Out","Year":"2012","Rated":"N/A","Released":"15 Nov 2013","Runtime":"N/A","Genre":"Comedy, Romance","Director":"Jason Stein","Writer":"Tyler Boehm, Jason Stein","Actors":"Dustin Diamond, Nicole LaLiberte, Eve, Cassandra Starr","Plot":"After his girlfriend asks him to move in with her, a guy\'s two best friends plan an epic night out in New York City to help him decide if he wants to stay in his relationship or not.","Language":"English","Country":"USA","Awards":"N/A","Poster":"http://ia.media-imdb.com/images/M/MV5BMTk4NzkwNDkzNV5BMl5BanBnXkFtZTgwNjgyMzY0MDE@._V1_SX300.jpg","Metascore":"N/A","imdbRating":"5.3","imdbVotes":"37","imdbID":"tt2088980","Type":"movie","tomatoMeter":"N/A","tomatoImage":"N/A","tomatoRating":"N/A","tomatoReviews":"N/A","tomatoFresh":"N/A","tomatoRotten":"N/A","tomatoConsensus":"N/A","tomatoUserMeter":"N/A","tomatoUserRating":"N/A","tomatoUserReviews":"N/A","DVD":"N/A","BoxOffice":"N/A","Production":"N/A","Website":"N/A","Response":"True"}';

	$imdb=json_decode($imdb_json, true);



	if($tv_title != '')
	{
		$imdb['TitleTV'] = $tv_title;
	}
	else
	{
		$imdb['TitleTV']  = 'N/A';
	}
	if (!isset($imdb['AwardsCopy']))
	{
		$imdb['AwardsCopy'] = "N/A";
	}

	if($imdb['Response'] != 'True')
	{
		$imdb['imdbID'] = $title;
		$imdb['Title'] = $raw_title;
		$imdb['Year'] = $year;
		$imdb['Response'] = 'True';
	}


	if($imdb['Response'] == 'True')
	{
		$con = mysql_connect(DBHOST ,DBUSER, DBPASS);
		mysql_select_db(DBNAME, $con);

		mysql_query("set names utf8;");

		$is_already_there =
			"SELECT imdbID from sit_mv_staged
			WHERE imdbID = '" . $imdb['imdbID'] . "'
			AND NetflixCountry = '" . $netflixcountry . "'";

		$record = mysql_query($is_already_there) or die($is_already_there ."<br/><br/>".mysql_error());
		if (mysql_num_rows($record) > 0)
		{
			#echo 'Duplicate record';
			#mysql_query("UPDATE tour SET date='$date',location='$location',venue='$venue',description='$description'$bandsintownlink='$bandsintownlink' WHERE id = '" . $id . "'") or die (mysql_error());

		}
		else
		{

			// write the image locally
			if(isset($imdb['Poster']) AND $imdb['Poster'] != 'N/A')
			{
				$local_image_url = parse_url($imdb['Poster']);
				$local_image_url = ltrim($local_image_url['path'], '/');
				$save_local_image = grab_image($imdb['Poster'],  $local_image_url);
				$imdb['Poster'] = 'http://www.sitrobotsit.com/moviefeed/' . $local_image_url;
			}


			$Title 		= isset($imdb['Title']) ? safe($imdb['Title']) : 'N/A';
			$TitleTV 	= isset($imdb['TitleTV']) ? safe($imdb['TitleTV']) : 'N/A';
			$Year 		= isset($imdb['Year']) ? safe($imdb['Year']) : 'N/A';
			$Rated      = isset($imdb['Rated']) ? safe($imdb['Rated']) : 'N/A';
			$Released   = isset($imdb['Released']) ? safe($imdb['Released']) : 'N/A';
			$Runtime    = isset($imdb['Runtime']) ? safe($imdb['Runtime']) : 'N/A';
			$Genre      = isset($imdb['Genre']) ? safe($imdb['Genre']) : 'N/A';
			$Director   = isset($imdb['Director']) ? safe($imdb['Director']) : 'N/A';
			$Writer     = isset($imdb['Writer']) ? safe($imdb['Writer']) : 'N/A';
			$Actors     = isset($imdb['Actors']) ? safe($imdb['Actors']) : 'N/A';
			$Plot       = isset($imdb['Plot']) ? safe($imdb['Plot']) : 'N/A';
			$Language   = isset($imdb['Language']) ? safe($imdb['Language']) : 'N/A';
			$Country    = isset($imdb['Country']) ? safe($imdb['Country']) : 'N/A';
			$Awards     = isset($imdb['Awards']) ? safe($imdb['Awards']) : 'N/A';
			$AwardsCopy = isset($imdb['AwardsCopy']) ? safe($imdb['AwardsCopy']) : 'N/A';
			$Poster     = isset($imdb['Poster']) ? safe($imdb['Poster']) : 'N/A';
			$Metascore  = isset($imdb['Metascore']) ? safe($imdb['Metascore']) : 'N/A';
			$imdbRating = isset($imdb['imdbRating']) ? safe($imdb['imdbRating']) : 'N/A';
			$imdbVotes  = isset($imdb['imdbVotes']) ? safe($imdb['imdbVotes']) : 'N/A';
			$imdbID     = isset($imdb['imdbID']) ? safe($imdb['imdbID']) : 'N/A';
			$Type       =  isset($imdb['Type']) ? safe($imdb['Type']) : 'N/A';
			$tomatoMeter = isset($imdb['tomatoMeter']) ? safe($imdb['tomatoMeter']) : 'N/A';
			$tomatoImage  = isset($imdb['tomatoImage']) ? safe($imdb['tomatoImage']) : 'N/A';
			$tomatoRating = isset($imdb['tomatoRating']) ? safe($imdb['tomatoRating']) : 'N/A';
			$tomatoReviews  = isset($imdb['tomatoReviews']) ? safe($imdb['tomatoReviews']) : 'N/A';
			$tomatoFresh    =  isset($imdb['tomatoFresh']) ? safe($imdb['tomatoFresh']) : 'N/A';
			$tomatoRotten   =  isset($imdb['tomatoRotten']) ? safe($imdb['tomatoRotten']) : 'N/A';
			$tomatoConsensus = isset($imdb['tomatoConsensus']) ? safe($imdb['tomatoConsensus']) : 'N/A';
			$tomatoUserMeter  =   isset($imdb['tomatoUserMeter']) ? safe($imdb['tomatoUserMeter']) : 'N/A';
			$tomatoUserRating  =  isset($imdb['tomatoUserRating']) ? safe($imdb['tomatoUserRating']) : 'N/A';
			$tomatoUserReviews  =  isset($imdb['tomatoUserReviews']) ? safe($imdb['tomatoUserReviews']) : 'N/A';
			$DVD                =  isset($imdb['DVD']) ? safe($imdb['DVD']) : 'N/A';
			$BoxOffice         = isset($imdb['BoxOffice']) ? safe($imdb['BoxOffice']) : 'N/A';
			$Production        = isset($imdb['Production']) ? safe($imdb['Production']) : 'N/A';
			$Website           = isset($imdb['Website']) ? safe($imdb['Website']) : 'N/A';

			$sql = mysql_query("INSERT INTO sit_mv_staged
			 (id,Title,TitleTV,Year,Rated,Released,Runtime,Genre,Director,Writer,Actors,Plot,Language,Country,Awards,AwardsCopy,Poster,Metascore,
			 imdbRating,imdbVotes,imdbID,Type,tomatoMeter,tomatoImage,tomatoRating,tomatoReviews,tomatoFresh,tomatoRotten,tomatoConsensus,
			 tomatoUserMeter,tomatoUserRating,tomatoUserReviews,DVD,BoxOffice,Production,Website,NetflixCountry,NetflixPoster,created)
			 VALUES('', '$Title', '$TitleTV', '$Year', '$Rated', '$Released', '$Runtime', '$Genre',
			 '$Director', '$Writer', '$Actors', '$Plot', '$Language', '$Country', '$Awards',
			 '$AwardsCopy', '$Poster', '$Metascore', '$imdbRating', '$imdbVotes', '$imdbID',
			 '$Type', '$tomatoMeter', '$tomatoImage', '$tomatoRating', '$tomatoReviews', '$tomatoFresh',
			 '$tomatoRotten', '$tomatoConsensus', '$tomatoUserMeter', '$tomatoUserRating', '$tomatoUserReviews',
			 '$DVD', '$BoxOffice', '$Production', '$Website', '$netflixcountry', '$netfliximage', now())") or die(mysql_error());


		}
	}
	unset($imdb);
}


/*CREATE TABLE `sit_mv` (
	`id` int(11) unsigned NOT NULL auto_increment,
  `Title` varchar(1000) default NULL,
  `TitleTV` varchar(1000) default NULL,
  `Year` varchar(20) default NULL,
  `Rated` varchar(20) default NULL,
  `Released` varchar(100) default NULL,
  `Runtime` varchar(100) default NULL,
  `Genre` varchar(255) default NULL,
  `Director` varchar(255) default NULL,
  `Writer` varchar(255) default NULL,
  `Actors` varchar(1000) default NULL,
  `Plot` varchar(2000) default NULL,
  `Language` varchar(100) default NULL,
  `Country` varchar(100) default NULL,
  `Awards` varchar(1000) default NULL,
  `AwardsCopy` varchar(1000) default NULL,
  `Poster` varchar(1000) default NULL,
  `Metascore` varchar(20) default NULL,
  `imdbRating` varchar(4) default NULL,
  `imdbVotes` varchar(11) default NULL,
  `imdbID` varchar(50) default NULL,
  `Type` varchar(100) default NULL,
  `tomatoMeter` int(4) default NULL,
  `tomatoImage` varchar(100) default NULL,
  `tomatoRating` int(4) default NULL,
  `tomatoReviews` int(11) default NULL,
  `tomatoFresh` int(11) default NULL,
  `tomatoRotten` int(11) default NULL,
  `tomatoConsensus` varchar(2000) default NULL,
  `tomatoUserMeter` int(4) default NULL,
  `tomatoUserRating` varchar(4) default NULL,
  `tomatoUserReviews` varchar(50) default NULL,
  `DVD` varchar(100) default NULL,
  `BoxOffice` varchar(100) default NULL,
  `Production` varchar(100) default NULL,
  `Website` varchar(100) default NULL,
  `created` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;*/



/*Array
	(
	[Title] =&gt; Neil Young: Heart of Gold
    [Year] =&gt; 2006
    [Rated] =&gt; PG
    [Released] =&gt; 11 May 2006
    [Runtime] =&gt; 103 min
    [Genre] =&gt; Documentary, Music
    [Director] =&gt; Jonathan Demme
    [Writer] =&gt; N/A
    [Actors] =&gt; Neil Young, Emmylou Harris, Ben Keith, Spooner Oldham
    [Plot] =&gt; A film shot over during a two-night performance by Neil Young at Nashville's Ryman Auditorium.
    [Language] =&gt; English
    [Country] =&gt; USA
    [Awards] =&gt; 1 win &amp; 1 nomination.
    [Poster] =&gt; http://ia.media-imdb.com/images/M/MV5BMTU4NTY0NjYxMV5BMl5BanBnXkFtZTcwODM4MDIzMQ@@._V1_SX300.jpg
    [Metascore] =&gt; 85
    [imdbRating] =&gt; 7.8
    [imdbVotes] =&gt; 2,200
    [imdbID] =&gt; tt0473692
    [Type] =&gt; movie
    [tomatoMeter] =&gt; 90
    [tomatoImage] =&gt; certified
    [tomatoRating] =&gt; 8
    [tomatoReviews] =&gt; 98
    [tomatoFresh] =&gt; 88
    [tomatoRotten] =&gt; 10
    [tomatoConsensus] =&gt; Proving that it's neither better to burn out nor fade away, Neil Young: Heart of Gold works both as a concert film and a meditation on mortality.
	[tomatoUserMeter] =&gt; 79
    [tomatoUserRating] =&gt; 2.9
    [tomatoUserReviews] =&gt; 92,228
    [DVD] =&gt; 13 Jun 2006
    [BoxOffice] =&gt; $1.7M
    [Production] =&gt; Paramount Classics
    [Website] =&gt; http://www.heartofgoldmovie.com
    [Response] =&gt; True
)*/





function grab_image($url,$saveto){
	$ch = curl_init ($url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
	curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0; chromeframe/11.0.696.57)');
	$raw=curl_exec($ch);
	curl_close ($ch);
	if(file_exists($saveto))
	{
		unlink($saveto);
	}
	$fp = fopen($saveto,'x');
	fwrite($fp, $raw);
	fclose($fp);
}







function replace_num_entity($ord)
{
	$ord = $ord[1];
	if (preg_match('/^x([0-9a-f]+)$/i', $ord, $match))
	{
		$ord = hexdec($match[1]);
	}
	else
	{
		$ord = intval($ord);
	}

	$no_bytes = 0;
	$byte = array();

	if ($ord < 128)
	{
		return chr($ord);
	}
	elseif ($ord < 2048)
	{
		$no_bytes = 2;
	}
	elseif ($ord < 65536)
	{
		$no_bytes = 3;
	}
	elseif ($ord < 1114112)
	{
		$no_bytes = 4;
	}
	else
	{
		return;
	}

	switch($no_bytes)
	{
		case 2:
		{
			$prefix = array(31, 192);
			break;
		}
		case 3:
		{
			$prefix = array(15, 224);
			break;
		}
		case 4:
		{
			$prefix = array(7, 240);
		}
	}

	for ($i = 0; $i < $no_bytes; $i++)
	{
		$byte[$no_bytes - $i - 1] = (($ord & (63 * pow(2, 6 * $i))) / pow(2, 6 * $i)) & 63 | 128;
	}

	$byte[0] = ($byte[0] & $prefix[0]) | $prefix[1];

	$ret = '';
	for ($i = 0; $i < $no_bytes; $i++)
	{
		$ret .= chr($byte[$i]);
	}

	return $ret;
}




?>