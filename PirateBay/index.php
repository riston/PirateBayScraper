<?php
require_once './library/simple_html_dom.php';
require_once './library/imdb.class.php';
require_once './parse.class.php';

define('CACHE_DIR', './cache');
define('URL', 'https://thepiratebay.org/top/201');

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

$html	= new simple_html_dom();
$imdb	= new Imdb();
$parser	= new Parse(URL, $imdb, $html);

$html->load_file($parser->getCachedPirateBayPage());
$movieArray = $parser->removeDuplicate();

require_once 'page.inc.php';
?>