
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf8">

</head>
<?php
include_once('simple_html_dom.php');
/*
//$mainHtml = file_get_contents ("http://www.bnext.com.tw/"	);
//preg_match_all("/article\/list\/cid\/\d{0,3}/", $mainHtml, $matches);
//print_r($matches);
unset($links);
$links[] = "http://www.bnext.com.tw/article/list/cid/144";

$cat_html = new simple_html_dom();

$base_url = "http://www.bnext.com.tw";
$url = "http://techorange.com/all/page/2";
$cat_html->load_file($url);
$sample_count =10;

foreach($cat_html->find("div#loop h2 a") as $link){
	 $links[] = "".$link->href."<BR>";
}
var_dump($links);

exit;


foreach($links as $url){
	echo "Looking at page ... $url <BR>";
	$cat_html->load_file($url);
	foreach($cat_html->find("ul.ListRowBlock li a") as $link){
		if (strpos($link->href,'/id/') !== false) {
			echo "$i-".$base_url.$link->href."<BR>";
			//parse($base_url.$link->href);
			$i++;
		}
	}
}
echo $i;*/
$html = new simple_html_dom();	

//parse("http://www.bnext.com.tw/article/view/id/31595");
parse("http://techorange.com/2014/04/02/how-thinking-like-a-hacker-will-grow-your-business/");
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
		$a_article["time"] = str_replace(" 發布","",$link->plaintext);
		$a_article["time"] = str_replace("於 ","",$a_article["time"]);
			echo $a_article["time"]."<BR>";
	}
	
	// Authors
	foreach($html->find("div#share-about a") as $link){
		$a_article["author"] = str_replace("","",$link->plaintext);
	}
	
	// links_in_content
	foreach($html->find("div.Article a") as $link){
		if (strpos($link->href,'http') !== false) {
			$links_in_content[] = $link->href;
		}
	}
	$a_article["links_in_content"] =$links_in_content ;
	
	// text_in_content
	foreach($html->find("div.Article") as $link){
		$a_article["text_in_content "] = $link->plaintext;
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
var_dump($a_result);
exit;

$cat_html->clear(); 
unset($cat_html);

$j_result= json_encode($a_result);

//echo "<pre>".urldecode($j_result)."</pre>";
$p=iconv("ASCII","UTF-8","\xEF\xBB\xBF".urldecode($j_result));
$file = dirname(__FILE__) . '/data/bnext.txt';
file_put_contents($file,"\xEF\xBB\xBF".urldecode($j_result));
?>

<BR/>
Parse Completed!
<pre>
<?php
//var_dump($a_result);
?>
</pre>

