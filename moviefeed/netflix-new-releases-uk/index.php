<?php
date_default_timezone_set('Europe/London');
header('Content-type: application/xml; charset=ISO-8859-1');

require_once '../rss_feed_class.php'; // configure appropriately

// set more namespaces if you need them
$xmlns = 'xmlns:content="http://purl.org/rss/1.0/modules/content/"
    xmlns:wfw="http://wellformedweb.org/CommentAPI/"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"';

// configure appropriately - pontikis.net is used as an example
$a_channel = array(
	"title" => "Netflix UK New Streaming Releases",
	"link" => "http://www.sitrobotsit.com",
	"description" => "Sit Robot Sit Netflix UK Streaming",
	"language" => "en",
	"image_title" => "sitrobotsit.com",
	"image_link" => "http://www.sitrobotsit.com",
	"image_url" => "http://www.sitrobotsit.com/moviefeed/rss.jpg",
);
$site_url = 'http://www.imdb.com'; // configure appropriately
$site_name = 'Netflix UK Streaming'; // configure appropriately

// database connection settings
$a_db = array(
	"db_server" => "213.171.200.46",
	"db_name" => "monarchydb",
	"db_user" => "monarchyadmin",
	"db_passwd" => "n3wv0yag3",
);

$netflix_country = 'uk';


$rss = new rss_feed($a_db, $xmlns, $a_channel, $site_url, $site_name, $full_feed = true, $netflix_country);
echo $rss->create_feed();
?>