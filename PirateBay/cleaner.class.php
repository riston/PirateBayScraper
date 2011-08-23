<?php

/**
 * 
 */
class PirateBay_Cleaner	{
	
	/**
	 * Hold all the keywords of the movie.
	 */
	private $keywords = array();
	
	public function filterRunner($title) 
	{
		$title = $this->filterDetailsFor($title);
		$title = $this->filterOutKeywords($title);
		$title = $this->filterDotsAndDoubleSpaces($title);
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
