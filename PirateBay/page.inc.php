<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
	<title>PirateBay Movie top 100</title>
	<link rel="stylesheet" href="css/reset.css" type="text/css" media="screen" charset="utf-8"/>
	<link rel="stylesheet" href="css/site.css" type="text/css" media="screen" charset="utf-8"/>
	<link rel="stylesheet" href="css/sprite.css" type="text/css" media="screen" charset="utf-8"/>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
	<script src="js/jcarousellite_1.0.1.min.js"></script>
	<script type="text/javascript" charset="utf-8">
		$(document).ready(function() {
			var torrentBtn = $('.torrent-button');
			$(torrentBtn).click(function() {
				$(this).prev('.torrent').fadeToggle("slow", "swing", function() {
				});
				
			});
		});
	</script>
</head>
<body>
<div id="container">
	<!--
	<div id="sidebar">
		<?php for ($i = 0; $i < 25; $i++): ?>
			<?php echo $i; ?><br />
		<?php endfor; ?>
	</div>
	-->
<?php  foreach ($movieArray as $movie): ?>
	<div class="movie">
		<!-- IMDB data view here -->
		<?php if (!isset($movie['error'])): ?>
			<a href="<?php echo $movie['imdb_url']; ?>" class="movie-title"><?php echo $movie['title']; ?></a>
			<p>
				<?php echo imgDisplay($movie); ?>
				<?php echo $movie['storyline']; ?>
			</p>
			<p>
				Year: <span><?php echo $movie['year']; ?></span>
			</p>
			<p>
				Rating: <span><?php echo $movie['rating']; ?></span>, 
				Votes: <span><?php echo $movie['votes']; ?></span>
			</p>
			<ul class="genres">
				<?php foreach ($movie['genres'] as $genre): ?>
					<li><a href="http://www.imdb.com/genre/<?php echo $genre; ?>">
						<?php echo $genre; ?></a></li>
				<?php endforeach; ?>
			</ul>

			<hr />
		<?php endif; ?>
		
		<!-- Piratebay information -->
		<div class="torrent">
			<table>
				<thead>
					<tr>
						<th>Torrent Name</th>
						<th>Action</th>
						<th>Keywords</th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ($movie['torrent'] as $torrent): ?>
					<tr>
						<td><?php echo $torrent['title']; ?></td>
						<td>
							<a href="<?php echo $torrent['link'] ?>" class="ss_sprite ss_arrow_down" title="Torrent download"></a>
							<a href="<?php echo $torrent['magnet'] ?>" class="ss_sprite ss_transmit" title="Download with magnet"></a>
						</td>
						<td>
						<?php foreach ($torrent['keywords'] as $keyword): ?>
							<a href="#"><?php echo $keyword; ?></a>
						<?php endforeach; ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<div class="torrent-button"><a href="#<?php echo $movie['title']; ?>">Show torrent data</a></div>
	</div>
	
<?php endforeach; ?>
</div>
</body>
</html>