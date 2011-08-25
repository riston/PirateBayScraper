<?php
require_once './library/simple_html_dom.php';
require_once './library/imdb.class.php';
require_once './cleaner.class.php';

define('CACHE_DIR', './cache');
$url = 'https://thepiratebay.org/top/201';
$cachePirateBayPage = CACHE_DIR .'/pirate.cache';
$cacheLife = 360; // In seconds



function writeToFile($filename, $content) {
	$fp = fopen($filename, 'w');
	fwrite($fp, $content);
	fclose($fp);
}

function cachePageFromUrl($url, $cacheFile, $cacheLife) {
	$modifiedTime = @filemtime($cacheFile);
	if (!$modifiedTime && ((time() - $modifiedTime) <= $cacheLife)) {
		// Make cache file first then start handling
		printf("Downloading the file\n");
		writeToFile($cacheFile, file_get_contents($url));
	}
}

function cacheIMDBMovie($imdb, $movieName) {
	$file = CACHE_DIR. '/'. $movieName;
	if (file_exists($file)) {
		// The data exists for movie.
		$movieArray = unserialize(file_get_contents($file));
	} else {
		// If the movie has not loaded yet
		$movieArray = $imdb->getMovieInfo($movieName);
		writeToFile($file, serialize($movieArray));
	}
	return $movieArray;
}

function imgDisplay($movieArray) {
	$src = '';
	if (strlen($movieArray['poster_small'] >= 1) ||
			key_exists('poster_small', $movieArray)) {
		$src = './library/imdbImage.php?url='. $movieArray['poster_small'];
	} else {
		$src = './img/404-logo.jpg';
	}
	return sprintf('<img src="%s" width="101" height="150" />', $src);
}

function getPirateBayInfo($html) {
	$titles = array();
	foreach ($html->find('.detName .detLink') as $element) {
		$titles[] = $element->title;
	}
	
	$links = array();
	foreach ($html->find('a[title=Download this torrent]') as $link) {
		$links[] = $link->href ." \n";
	}
	
	$magnets = array();
	foreach ($html->find('a[title$=using magnet]') as $link) {
		$magnets[] = $link->href ." \n";
	}

	$movieArray = array();
	for ($i = 0; $i < count($titles); $i++) {
		$movieArray[] = array('title' => $titles[$i], 'link' => $links[$i], 'magnet' => $magnets[$i]);
	}	
	return $movieArray;
}

cachePageFromUrl($url, $cachePirateBayPage, $cacheLife);
$html = new simple_html_dom();
$html->load_file($cachePirateBayPage);
$imdb = new Imdb();

$pirateMovieArray = getPirateBayInfo($html);
$movieArray = array();
foreach ($pirateMovieArray as $movie) {
	$cleaner = new PirateBay_Cleaner();
	$cleaner->filterRunner($movie['title']);
	
	$movieRow = array();
	$movieRow = cacheIMDBMovie($imdb, $cleaner->getMovieName());
	$movieRow['torrent_title'] = $movie['title'];
	$movieRow['torrent_link'] = $movie['link'];
	$movieRow['torrent_magnet'] = $movie['magnet'];
	$movieRow['torrent_keywords'] = $cleaner->getKeywords();
	$movieArray[] = $movieRow;
}
array_unique($movieArray);
require_once 'page.inc.php';
?>
