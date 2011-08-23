<?php
require_once './simple_html_dom.php';
require_once './cleaner.class.php';

$url = 'https://thepiratebay.org/top/201';

$html = file_get_html($url);
/*
foreach ($html->find('.product_descr') as $product) {

}
*/
foreach ($html->find('.detName a') as $element) {
	$cleaner = new PirateBay_Cleaner();
	printf("%s\n", $cleaner->filterRunner($element->title));
	print_r($cleaner->getKeywords());
	//printf("https://www.thepiratebay.org%s\n", $element->href);
	
}


function writeToFile($filename, $content) {
	if (is_writeable($filename)) {
		$fp = fopen($filename, 'w');
		fwrite($fp, $content);
		fclose($fp);
	}
}
?>
