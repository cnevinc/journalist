﻿
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf8">

</head>
<?php
include_once('simple_html_dom.php');

unset($links);

$cat_html = new simple_html_dom();
$html = new simple_html_dom();	
$base_url = "http://techorange.com/all/page/";
$sample_count = 100;
for($i = 2; $i<20;$i++){
	
	$url = $base_url.$i;
	$cat_html->load_file($url);

	foreach($cat_html->find("div#loop h2 a") as $link){
		if($sample_index>$sample_count) break;
		echo "$sample_index-".$base_url.$link->href."<BR>";
		parse($link->href);
		$sample_index++;
	}
	
}



function parse($url){
	global $html;
	global $a_result ;	
	global $links_in_content;
	unset($links_in_content);
	
	
	$a_article["site"] = "Tech Orange";
	$a_article["link"] = $url;

	$html->load_file($url);
	
	// Title
	foreach($html->find("div.to_single_title") as $link){
		$a_article["title"] = $link->plaintext;
	}	
	
	// Date
	foreach($html->find("div.to_single_date") as $link){
		$mainHtml = $link->plaintext;
		preg_match("/\d{4}\-\d{2}\-\d{2}/", $mainHtml, $matches);
		$a_article["time"] = ($matches[0]);
	}
	
	// Authors
	foreach($html->find("div[class='a-row author clearfix'] a") as $link){
		$a_article["author"] = $link->plaintext;
	}
	
	// links_in_content
	foreach($html->find("div[class='to_single_content_article clear'] a") as $link){
		if (strpos($link->href,'http') !== false) {
			$links_in_content[] = $link->href;
		}
	}
	$a_article["links_in_content"] =$links_in_content ;
	
	// text_in_content
	foreach($html->find("div[class='to_single_content_article clear']") as $link){
		//$a_article["text_in_content "] = $link->plaintext;
	}
	
	
	foreach($a_article as $key => $value){
		if (!is_array($value)){
			$a_article[$key]= urlencode($value);
		}
	}
	$a_result[] = array ("article" => $a_article);
}
	
$html->clear(); 
unset($html);



$cat_html->clear(); 
unset($cat_html);

$j_result= json_encode($a_result);

//echo "<pre>".urldecode($j_result)."</pre>";
$p=iconv("ASCII","UTF-8","\xEF\xBB\xBF".urldecode($j_result));
$file = dirname(__FILE__) . '/data/orange.txt';
file_put_contents($file,"\xEF\xBB\xBF".urldecode($j_result)."\n\r");
echo "Parsed " .$sample_index ." files completed<BR>";
?>

<BR/>
Parse Completed!
<pre>
<?php
//var_dump($a_result);
?>
</pre>

