<?php

/**
 * 
 */
class PirateBay_Cleaner	{
	
	/**
	 * Hold all the keywords of the movie.
	 */
	private $keywords = array();
	private $movieName;
	
	public function filterRunner($title) 
	{
		$title = $this->filterDetailsFor($title);
		$title = $this->filterOutKeywords($title);
		$title = $this->filterDotsAndDoubleSpaces($title);
		$title = $this->filterMovieName($title);
		return $title;
	}
	
	/**
	 * Get all the keywords related to movie.
	 * @return array string keywords
	 */
	public function getKeywords()
	{
		return $this->keywords;
	}
	
	public function getMovieName() 
	{
		return $this->movieName;
	}

	protected function filterDetailsFor($str)
	{
		return preg_replace('/Details for /', '', $str);
	}
	
	protected function filterDotsAndDoubleSpaces($str) 
	{
		$str = str_replace('.', ' ', $str);
		$str = preg_replace('/\s{2,}/', '', $str);
		return $str;
	}
	
	protected function filterMovieName($str)
	{
		// The movie name is usually in form like the "name and year [2000]|(2000)"
		$matches = array();
		if (preg_match('/([A-Za-z0-9 \(\)])+(\[\d{4}\]|\(\d{4}\)|\d{4})+/', $str, $matches)) {
			$this->movieName = $matches[0];
			return $matches[0];
		}
		$str = trim(str_replace(preg_split('//', '[{(-)}]'), ' ', $str));
		$this->movieName = $str;
		return $str;
	}
	
	protected function filterOutKeywords($str) 
	{
		$keywords = array(
			'DVDRip', 'PPVRip', 'Cam', 'XviD', 'TS', 'R5', 'AC3', 'BRrip'. 'Line',
			'x264', 'dvdscr', 'READNFO', 'BDRip', 'BRRip', 'HDRipHQ', 'HDTV', 'READ NFO',
			'LiNE', 'LIMITED', '480p', '720p', '1080p', 'TELESYNC', 'HDRipHQ', 'UNRATED');
			
		foreach ($keywords as $keyword) {
			if (preg_match('/'.$keyword.'/i', $str)) {
				$str = preg_replace('/'.$keyword.'/i', '', $str);
				$this->keywords[] = $keyword;
			}
		}
		return $str;
	}
}
