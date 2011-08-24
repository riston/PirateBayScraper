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
		<!-- IMDB data view here -->
		<?php $movieArray = cacheIMDBMovie($imdb, $cleaner->getMovieName()); ?>
		<?php if (!isset($movieArray['error'])): ?>
			<a href="<?php echo $movieArray['imdb_url']; ?>" class="movie-title"><?php echo $movieArray['title']; ?></a>	
			<p>
				<?php echo imgDisplay($movieArray); ?>
				<?php echo $movieArray['storyline']; ?>
			</p>
			<p>
				Year: <span><?php echo $movieArray['year']; ?></span>
			</p>
			<p>
				Rating: <span><?php echo $movieArray['rating']; ?></span>, Votes: <span><?php echo $movieArray['votes']; ?></span>
			</p>
			<ul class="genres">
				<?php foreach ($movieArray['genres'] as $genre): ?>
					<li><a href="http://www.imdb.com/genre/<?php echo $genre; ?>">
						<?php echo $genre; ?></a></li>
				<?php endforeach; ?>
			</ul>
			<hr />
		<?php endif; ?>
		
		<!-- Piratebay information -->
		<h3><?php echo $cleaner->getMovieName(); ?></h3>
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