<?php
require_once './cleaner.class.php';

/**
 * Class to parse the elements from piratebay and imdb.
 * 
 */
class Parse {
	
	/**
	 * Location of the cache directory.
	 * @access private
	 * @var string
	 */
	private $cacheDir = './cache';
	
	private $url;
	
	private $pageCache;
	
	private $cacheLife;
	
	private $imdb;
	
	private $html;


	public function __construct($url, $imdb, $html, $pageCache ='pirate.cache', $cacheLife = 360) {
		$this->url				= $url;
		$this->pageCache		= $this->cacheDir . DIRECTORY_SEPARATOR . $pageCache;
		$this->cacheLife		= $cacheLife;
		$this->imdb			= $imdb;
		$this->html			= $html;
	}
	
	private function cacheIMDBMovie($movieName) {
		$file = $this->cacheDir . DIRECTORY_SEPARATOR. $movieName;
		if (file_exists($file)) {
			// The data exists for movie.
			$movieArray = unserialize(file_get_contents($file));
		} else {
			// If the movie has not loaded yet
			$movieArray = $this->imdb->getMovieInfo($movieName);
			$this->writeToFile($file, serialize($movieArray));
		}
		return $movieArray;
	}
	
	/**
	 * Cache the given page.
	 * @return boolean true if the file has cached false, if already exists.
	 */
	private function cachePageFromUrl() {
		$modifiedTime = @filemtime($this->pageCache);
		if (!$modifiedTime || ((time() - $modifiedTime) >= $this->cacheLife)) {
			// Make cache file first then start handling
			printf("Downloading the file\n");
//			print "Get the file";
			$this->writeToFile($this->pageCache, file_get_contents($this->url));
			return true;
		}
		return false;
	}
	
	/**
	 * Parse the html info from piratebay.org with the library simple_html_doms.
	 * @return array of html
	 */
	private function parseFromPirateBay() {
		$titles = array();
		foreach ($this->html->find('.detName .detLink') as $element) {
			$titles[] = $element->title;
		}

		$links = array();
		foreach ($this->html->find('a[title=Download this torrent]') as $link) {
			$links[] = $link->href ." \n";
		}

		$magnets = array();
		foreach ($this->html->find('a[title$=using magnet]') as $link) {
			$magnets[] = $link->href ." \n";
		}

		$movieArray = array();
		for ($i = 0; $i < count($titles); $i++) {
			$movieArray[] = array('title' => $titles[$i], 'link' => $links[$i], 'magnet' => $magnets[$i]);
		}	
		return $movieArray;
	}
	
	public function removeDuplicate() {
		$movieArray = $this->joinPirateBayImdbData();
		
		$titles = array();
		foreach ($movieArray  as $movie) {
			if (isset($movie['title']))
				$titles[] = $movie['title'];
		}
		print_r($titles);
		print_r(array_unique($titles));
		
		// Find the duplicates and join into one movie array the torrent info.
		for ($i = 0; $i < count($movieArray); $i++) {
			for ($j = 0; $j < count($movieArray); $j++) {
				
				if ($i != $j && isset($movieArray[$i]) && isset($movieArray[$j]) && 
						(array_key_exists('title', $movieArray[$i]) && array_key_exists('title', $movieArray[$j]))) {
					
					if (trim($movieArray[$i]['title']) == trim($movieArray[$j]['title'])) {
						echo "Duplicate {$i} {$movieArray[$i]['title']} {$j} {$movieArray[$j]['title']} <br />";
						$torrentData = array(
							'title'			=> $movieArray[$j]['torrent'][0]['title'], 
							'link'			=> $movieArray[$j]['torrent'][0]['link'], 
							'magnet'		=> $movieArray[$j]['torrent'][0]['magnet'],
							'keywords' 	=> $movieArray[$j]['torrent'][0]['keywords']
						);
						$movieArray[$i]['torrent'][] = $torrentData;
						$movieArray[$j] = null;
						//unset($movieArray[$j]);
					}
				}
			}
		}
		return $movieArray;
	}


	public function joinPirateBayImdbData() {
		$this->cachePageFromUrl();
		
		$pirateMovieArray = $this->parseFromPirateBay();
		$movieArray = array();
		foreach ($pirateMovieArray as $movie) {
			$cleaner = new PirateBay_Cleaner();
			$cleaner->filterRunner($movie['title']);

			$movieRow = array();
			$movieRow = $this->cacheIMDBMovie( $cleaner->getMovieName());

			$torrentInfo = array(
				'title' 	=> $movie['title'], 
				'link' 		=> $movie['link'], 
				'magnet' 	=> $movie['magnet'],
				'keywords' 	=> $cleaner->getKeywords(),		
			);
			$movieRow['torrent'] = array();
			$movieRow['torrent'][] = $torrentInfo;
			$movieArray[] = $movieRow;
		}
		return $movieArray;
	}
	
	public function getCachedPirateBayPage() {
		return $this->pageCache;
	}

	/**
	 * Simple write to file, if file does not exist creates new.
	 * @param string $filename filename with location
	 * @param string $content
	 */
	private function writeToFile($filename, $content) {
		$fp = fopen($filename, 'w');
		fwrite($fp, $content);
		fclose($fp);
	}

}