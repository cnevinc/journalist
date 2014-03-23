<?php
include_once('simple_html_dom.php');

$url = "http://localhost:81/jl/list.htm";
$html = new simple_html_dom();
$html->load_file($url);

foreach($html->find("ul.postspermonth li a") as $link){
	if(++$i>=30) break;
	echo "$i-".$link->href."<br>";
		
		
}
	
$html->clear(); 
unset($html);
?>