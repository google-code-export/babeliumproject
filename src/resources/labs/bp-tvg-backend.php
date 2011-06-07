<?php

session_start();

//cambridgeDictionaryQuery('climate');
//makeImageFromText('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus euismod libero vel lacus tristique quis ullamcorper diam ultrices. Ut sit amet tellus dui.'); //26

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'default';

if ( !in_array($action, array('querydictionary', 'downloadimages', 'makeimagefromtext','default'), true) )
	$action = 'default';
	
$http_post = ('POST' == $_SERVER['REQUEST_METHOD']);
switch ($action) {
	case 'querydictionary':
		$query = $_GET['query'];
		echo cambridgeDictionaryQuery($query);
		break;
	default:
		echo 'No action requested';
		break;
}

function cambridgeDictionaryQuery($query){
	$base_url = 'http://dictionary.cambridge.org';
	$ch = curl_init();
	$query_sanitized = str_replace(" ","-",$query);
	$query_sanitized = htmlentities($query_sanitized, ENT_QUOTES, 'UTF-8');
	curl_setopt($ch, CURLOPT_URL, $base_url.'/search/british/direct/?q='.$query_sanitized);

	//curl_setopt($ch, CURLOPT_POST, 1);

	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.12) Gecko/2009070611 Firefox/3.0.12");

	$result = curl_exec($ch);

	if(preg_match('/id="cdo-spellcheck-container"/m',$result)){
		//The term you searched for didn't gave any results decomponse it (if it's possible) and search for individual words
		$results = "No results";

	} else {
		foreach(preg_split("/(\r?\n)/", $result) as $line){
			//WARNING: I'm not 100% sure this pattern is constant, maybe the <a> tag's attributes exchange order in certain cases.
			preg_match('/href="([^"]+)" class="cdo-topic cdo-link"/ism', $line, $matches);
			if (count($matches) > 0){
				//echo $matches[1]."\n";
				curl_setopt($ch, CURLOPT_URL, $base_url.$matches[1]);
				$result_topic = curl_exec($ch);
				break;
			}
		}
		if(isset($result_topic)){
			$relatedWords = array();
			foreach(preg_split("/(\r?\n)/", $result_topic) as $line){
				preg_match('/<span class="hwd">([^<]+)</ism', $line, $matches);
				if (count($matches) > 0){
					if(!array_search($matches[1],$relatedWords))
						array_push($relatedWords,$matches[1]);
				}
			}
			if(count($relatedWords) > 0)
				$results = implode(', ',$relatedWords);
			else
			$results = "No related thesaurus found";
		} else {
			$results = "No related thesaurus found";
		}
	}
	curl_close($ch);
	return json_encode($results);
	//return $results;

}

function makeImageFromText($text){
	define('SLIDE_WIDTH', 720);   // width of image
	define('SLIDE_HEIGHT', 576);   // height of image
	define('SLIDE_FONTSIZE', 28);
	define('SLIDE_TEXTANGLE',0);
	define('MAX_CHARS_PER_LINE', 30); //calculated for a font size equal to 28 using arial font

	define('FONT','./arial.ttf');

	$words = explode(" ",trim($text));
	$lines = array();
	if(count($words) <= 1){
		array_push($lines,trim($text));
	} else {
		$line = '';
		for($j=0; $j<count($words); $j++){
			if(strlen($line)+1+strlen($words[$j]) <= MAX_CHARS_PER_LINE){
				$line .= $words[$j].' ';
			} else {
				array_push($lines,$line);
				$line = $words[$j].' ';
			}
			if($j == count($words) -1){
				array_push($lines,$line);
			}
			//echo $line . "\n";
		}
	}

	// Create the image
	$img = imagecreatetruecolor(SLIDE_WIDTH, SLIDE_HEIGHT);

	// Set a white background with black text and gray graphics
	$bg_color = imagecolorallocate($img, 255, 255, 255);     // white
	$text_color = imagecolorallocate($img, 0, 0, 0);         // black
	$graphic_color = imagecolorallocate($img, 64, 64, 64);   // dark gray

	// Fill the background
	imagefilledrectangle($img, 0, 0, SLIDE_WIDTH, SLIDE_HEIGHT, $bg_color);

	$textbox = imagettfbbox(SLIDE_FONTSIZE, SLIDE_TEXTANGLE, FONT, $lines[0]);
	$textbox_height = abs($textbox[3]-$textbox[5]);
	$image_center_x = imagesx($img)/2;
	$image_center_y = imagesy($img)/2;

	// Draw the word/phrase string
	for($i=0;$i<count($lines);$i++){
			
		//Return an 8 item array with the coords of the box as follows when succeeded: lower-left x,y lower-right x,y upper-right x,y upper-left x,y
		$bbox = imagettfbbox(SLIDE_FONTSIZE, SLIDE_TEXTANGLE, FONT, $lines[$i]);

		// This is our cordinates for X and Y
		$x = $image_center_x - abs($bbox[0]-$bbox[2])/2;
		$y = $image_center_y - (($textbox_height/2)*(count($lines)-2-(2*$i)));
		imagettftext($img, SLIDE_FONTSIZE, SLIDE_TEXTANGLE, $x, $y, $text_color, FONT, $lines[$i]);
			
	}
	//Save the image we've just generated
	$folder_hash = md5(session_id()+'date or sth stored in the session');
	$folder_abs = dirname(__FILE__).'/images/'.$folder_hash;
	$_SESSION['temp_folder'] = $folder_abs;
	if(mkdir($folder_abs))
		imagepng($img, $folder_abs.'/text'.sprintf("%02d",$i).'.png');

	// Clean up
	imagedestroy($img);

}

function buildVideo(){
	//Command-line call to melt or ffmpeg. Video format: (Image |5s| Word/Phrase/Phrasal verb |5s|)xN times + White image |2min| explain what you saw in the images
	//$ melt ABSOLUTE_PATH/*png out=125 -filter luma:%luma01.pgm luma.softness=0.2 -repeat 2 -consumer avformat:OUTPUTFILENAME.EXTENSION b=1500k

}

function retrieveSelectedImageFiles($imgUrls){
	for($i=0; $i<count($imgUrls);$i++){
		$urlPieces = explode($imgUrls[$i],'/');
		if(!(count($urlPieces) > 0))
		return;
			
		$filename = $urlPieces[count($urlPieces)-1]; //the filename
		//$filedir = '/md5 hash of the session_id + randomly generated identifier, for example/';

		$imgFile = file_get_contents($imgUrls[$i]);

		if(isset($_SESSION['temp_folder'])){
			$file_loc=$_SESSION['temp_folder'].'/img'.sprintf("%02d",$i);

			$file_handler=fopen($file_loc,'w');

			if(fwrite($file_handler,$imgFile)==false){
				echo "Error saving file: ".$imgUrls[$i]."\n";
			}
		}
	}
}


?>