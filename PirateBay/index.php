<?php
require_once './library/simple_html_dom.php';
require_once './library/imdb.class.php';
require_once './cleaner.class.php';

define('CACHE_DIR', './cache');
$url = 'https://thepiratebay.org/top/201';
$cachePirateBayPage = './cache/pirate.cache';
$cacheLife = 360; // In seconds

$modifiedTime = @filemtime($cachePirateBayPage);
if (!$modifiedTime && (time() - $modifiedTime >= $cacheLife)) {
	// Make cache file first then start handling
	printf("Downloading the file\n");
	writeToFile($cachePirateBayPage, file_get_contents($url));
}

$html = file_get_html($cachePirateBayPage);
$imdb = new Imdb();

function writeToFile($filename, $content) {
	$fp = fopen($filename, 'w');
	fwrite($fp, $content);
	fclose($fp);

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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
	<title>PirateBay Movie top 100</title>
	<link rel="stylesheet" href="css/reset.css" type="text/css" media="screen" charset="utf-8"/>
	<link rel="stylesheet" href="css/site.css" type="text/css" media="screen" charset="utf-8"/>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
	<script type="text/javascript" charset="utf-8">
		$(document).ready(function() {
			
		});
	</script>
</head>
<body>
	

<?php  foreach ($html->find('.detName a') as $element): ?>
	<?php 
	$cleaner = new PirateBay_Cleaner(); 
	$cleaner->filterRunner($element->title);
	?>
	<div class="movie">
		<?php $movieArray = cacheIMDBMovie($imdb, $cleaner->getMovieName()); ?>
		<?php if (!isset($movieArray['error'])): ?>
			<h3><?php echo $movieArray['title']; ?></h3>	
			<img src="./library/imdbImage.php?url=<?php echo $movieArray['poster_small']; ?>" width="101" height="150" />
		<?php endif; ?>
		
		<!-- Piratebay information -->
		<h2><?php echo $cleaner->getMovieName(); ?></h2>
		<p>The keywords with movie from Pirate Bay</p>
		<?php foreach ($cleaner->getKeywords() as $keyword): ?>
		<ul>
			<li><?php echo $keyword; ?></li>
		</ul>
		<?php endforeach; ?>
	</div>
<?php endforeach; ?>
</body>
</html>