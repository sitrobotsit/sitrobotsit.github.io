<?php
#$data = file_get_contents('http://mymovieapi.com/?title=Oblivion&type=json&plot=simple&episode=1&limit=1&yg=0&mt=none&lang=en-US&offset=&aka=simple&release=simple&business=0&tech=0');


# $data = file_get_contents('http://whats-on-netflix.com/whats-new/united-kingdom/feed/');

#$jsonString = '{ "this_is_json" : "hello!" }';
   echo '<ul>' ;
$data = simplexml_load_file('http://whats-on-netflix.com/whats-new/united-kingdom/feed/');
    foreach ($data->channel->item as $netflix_movie){
		$title=$netflix_movie->title;
		$description=$netflix_movie->description;
		/*$date=$netflix_movie['dateplayed'];*/
        echo '<li><ul><li>' . $title . '</li>';
		#echo '<li>' .$description  . '</li>';
		 echo '</li></ul>';
	}

echo '</ul>' ;
/*$imdb_data = json_decode($data, false);
foreach ($imdb_data as $d){
	echo $d->title;
}*/


$options = array(
	"indent"    => "    ",
	"linebreak" => "\n",
	"typeHints" => false,
	"addDecl"   => true,
	"encoding"  => "UTF-8",
	"rootName"   => "rdf:RDF",
	"defaultTagName" => "item"
);

/*$serializer = new XML_Serializer($options);

if ($serializer->serialize($imdb_data)) {
	header('Content-type: text/xml');
	echo $serializer->getSerializedData();
}*/

#$data = json_decode($data, true);
#$data = array('title' => $data->title, 'rating' => $data['rating']);
#$data = json_encode($data);
#print($data);

?>