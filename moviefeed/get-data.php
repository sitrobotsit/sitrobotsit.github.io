<?php

// cron run by www.setcronjob.com

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


$netflixcountry= $_GET['c'];

if ($netflixcountry == 'usa')
{
	$url =  'http://netflixusacompletelist.blogspot.co.uk/';
}
else
{
	$url =  'http://netflixukcompletelist.blogspot.co.uk/';
}



#$url = 'test-raw-data.html';
$html = file_get_contents($url);

$result = array();

libxml_use_internal_errors(true);

$title_classname = "name";
$year_classname = "year";
$thumbnail_classname = "thumbnail";

$domdocument = new DOMDocument();

$domdocument->loadHTML($html);

$a = new DOMXPath($domdocument);
$title = $a->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $title_classname ')]");
$year = $a->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $year_classname ')]");


for ($i = $title->length - 1; $i > -1; $i--)
{
	$clean_title = '';
	$tv_clean_title = '';
	$send_year = '';
	$send_title = '';
	$send_tv_title  = '';

	$raw_title = $title->item($i)->firstChild->nodeValue;


	/*$htmln = new simple_html_dom();
	$htmln->load($html);
	$items = $htmln->find('div.thumbnail',0);
	print_r($items);*/

	/*$thumbnail_location = $thumbnail->find($i)->src;*/



	// Create a DOM object from a string
/*	$html = str_get_html('<nav><ul>
 <li id="idli1" class="cls">List 1</li><li>List 2</li><li class="cls">List 3</li>
 </ul></nav>');*/

	// Get the id of the first LI in UL, and change its content
	/*$idli = $html->find('li', 0)->id;
	if($idli) echo 'First LI id: '. $idli;
	$html->find('ul li', 0)->innertext = '<b>PHP Simple HTML DOM</b>';
	echo $html;*/

	$year_junk = array("(", ")");

	$clean_title =  $raw_title;

	if (preg_match("/, The/", $raw_title))
	{
		$clean_title = "The " . preg_replace("~, The~",'', $raw_title);
	}

	if (preg_match("/, A/", $raw_title))
	{
		$clean_title = "A " . preg_replace("~, A~",'', $raw_title);
	}

	$tv_clean_title  = $clean_title;

	$clean_year = str_replace($year_junk, "", $year->item($i)->firstChild->nodeValue);

	// if TV
	if  (preg_match("/season/i", $raw_title) OR preg_match("/series/i", $raw_title))
	{

		$title_junk = array("-", ", The");
		$clean_title = str_replace($title_junk, "", $raw_title);
		$clean_title = preg_replace("~Season\s[0-9]+~",'', $clean_title);
		$clean_title = preg_replace("~Series\s[0-9]+~",'', $clean_title);
		$send_tv_title = $tv_clean_title;
	}
	// movie
	else
	{
		$send_year = substr($clean_year, 0, 4);
		$send_tv_title = '';
	}

	$send_title = trim($clean_title);


	//imbd($send_title, $send_year, $send_tv_title, $netflixcountry);

}

#print_r($result);

function safe($value){
	return mysql_real_escape_string($value);
}


function imbd($title, $year, $tv_title = '', $netflixcountry)
{
	require_once("dbConstants.php");

	$title = urlencode($title);

	$url = "http://www.omdbapi.com/?t=" . $title . "&y=". $year . "&tomatoes=true";

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


	if($imdb['Response'] == 'True')
	{
		$con = mysql_connect(DBHOST ,DBUSER, DBPASS);
		mysql_select_db(DBNAME, $con);

		mysql_query("set names utf8;");

		$is_already_there = "SELECT imdbID from sit_mv  WHERE imdbID = '" . $imdb['imdbID'] . "'";

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


			$Title 		= safe($imdb['Title']);
			$TitleTV 	= safe($imdb['TitleTV']);
			$Year 		= safe($imdb['Year']);
			$Rated      = safe($imdb['Rated']);
			$Released   = safe($imdb['Released']);
			$Runtime    = safe($imdb['Runtime']);
			$Genre      = safe($imdb['Genre']);
			$Director   = safe($imdb['Director']);
			$Writer     = safe($imdb['Writer']);
			$Actors     = safe($imdb['Actors']);
			$Plot       = safe($imdb['Plot']);
			$Language   = safe($imdb['Language']);
			$Country    = safe($imdb['Country']);
			$Awards     = safe($imdb['Awards']);
			$AwardsCopy = safe($imdb['AwardsCopy']);
			$Poster     = safe($imdb['Poster']);
			$Metascore  = safe($imdb['Metascore']);
			$imdbRating = safe($imdb['imdbRating']);
			$imdbVotes  = safe($imdb['imdbVotes']);
			$imdbID     = safe($imdb['imdbID']);
			$Type       = safe($imdb['Type']);
			$tomatoMeter = safe($imdb['tomatoMeter']);
			$tomatoImage  = safe($imdb['tomatoImage']);
			$tomatoRating = safe($imdb['tomatoRating']);
			$tomatoReviews  = safe($imdb['tomatoReviews']);
			$tomatoFresh    = safe($imdb['tomatoFresh']);
			$tomatoRotten   = safe($imdb['tomatoRotten']);
			$tomatoConsensus = safe($imdb['tomatoConsensus']);
			$tomatoUserMeter  = safe($imdb['tomatoUserMeter']);
			$tomatoUserRating  = safe($imdb['tomatoUserRating']);
			$tomatoUserReviews  = safe($imdb['tomatoUserReviews']);
			$DVD                = safe($imdb['DVD']);
			$BoxOffice         = safe($imdb['BoxOffice']);
			$Production        = safe($imdb['Production']);
			$Website           = safe($imdb['Website']);

			$sql = mysql_query("INSERT INTO sit_mv
			 (id,Title,TitleTV,Year,Rated,Released,Runtime,Genre,Director,Writer,Actors,Plot,Language,Country,Awards,AwardsCopy,Poster,Metascore,
			 imdbRating,imdbVotes,imdbID,Type,tomatoMeter,tomatoImage,tomatoRating,tomatoReviews,tomatoFresh,tomatoRotten,tomatoConsensus,
			 tomatoUserMeter,tomatoUserRating,tomatoUserReviews,DVD,BoxOffice,Production,Website,NetflixCountry,created)
			 VALUES('', '$Title', '$TitleTV', '$Year', '$Rated', '$Released', '$Runtime', '$Genre',
			 '$Director', '$Writer', '$Actors', '$Plot', '$Language', '$Country', '$Awards',
			 '$AwardsCopy', '$Poster', '$Metascore', '$imdbRating', '$imdbVotes', '$imdbID',
			 '$Type', '$tomatoMeter', '$tomatoImage', '$tomatoRating', '$tomatoReviews', '$tomatoFresh',
			 '$tomatoRotten', '$tomatoConsensus', '$tomatoUserMeter', '$tomatoUserRating', '$tomatoUserReviews',
			 '$DVD', '$BoxOffice', '$Production', '$Website', '$netflixcountry', now())") or die(mysql_error());


		}
	}
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


/*echo '<ul>';
if($tv_title != '')
{
	echo '<li>TV Title: ' .  $tv_title . '</li>';
}
else
{

}
echo '<li>Title: ' .  $imdb['Title'] . '</li>';

echo '<li>Released: ' .  $imdb['Released'] . '</li>';
echo '<li>Genre: ' .  $imdb['Genre'] . '</li>';
echo '<li>IMDB Rating: ' .  $imdb['imdbRating'] . '</li>';
if(isset($imdb['tomatoRating']))
{
	echo '<li>Roten Tomato Rating: ' .  $imdb['tomatoRating'] . '</li>';
}
echo '<li>Director: ' .  $imdb['Director'] . '</li>';
echo '<li>Actors: ' .  $imdb['Actors'] . '</li>';
echo '<li>Awards: ' .  $imdb['Awards'] . '</li></ul>';
#echo '<li><img src="' .  $imdb['Poster'] . '"></li></ul>';

print_r($imdb);*/


?>