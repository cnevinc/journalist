
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf8">

</head>
<?php
include_once('simple_html_dom.php');

//$mainHtml = file_get_contents ("http://www.bnext.com.tw/"	);
//preg_match_all("/article\/list\/cid\/\d{0,3}/", $mainHtml, $matches);
//print_r($matches);

unset($links);
$links[] = "http://www.bnext.com.tw/article/list/cid/144";

$cat_html = new simple_html_dom();

$base_url = "http://www.bnext.com.tw";
$url = "http://www.bnext.com.tw/article/list/cid/144";
$cat_html->load_file($url);
$sample_count =100;

foreach($cat_html->find("dd.PageBar a") as $link){
	$found = "http://www.bnext.com.tw".$link->href."<BR>";
	 if (!in_array($found ,$links)){
		$links[] = $found ; 
		echo "http://www.bnext.com.tw".$link->href."<BR>";
	}
}


$html = new simple_html_dom();	
foreach($links as $url){
	echo "Looking at page ... $url <BR>";
	$cat_html->load_file($url);
	foreach($cat_html->find("ul.ListRowBlock li a") as $link){
		if (strpos($link->href,'/id/') !== false) {
			if ($i>$sample_count) break;

			echo "$i-".$base_url.$link->href."<BR>";
			parse($base_url.$link->href);
			$i++;
		}
	}
}

//parse("http://www.bnext.com.tw/article/view/id/31595");
//parse("http://www.bnext.com.tw/article/view/id/31444");

function parse($url){
	global $html;
	global $a_result ;	
	global $links_in_content;
	unset($links_in_content);
	
	
	$a_article["site"] = "數位時代";
	$a_article["link"] = $url;

	$html->load_file($url);
	
	// Title
	foreach($html->find("div.ViewBox h1") as $link){
		$a_article["title"] = $link->plaintext;
	}	
	
	// Date
	foreach($html->find("span.Date") as $link){
		$a_article["time"] = str_replace("發表日期：","",$link->plaintext);
	}
	
	// Authors
	foreach($html->find("span.Author") as $link){
		$a_article["author"] = str_replace("數位時代網站｜撰文者：","",$link->plaintext);
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
$cat_html->clear(); 
unset($html);
unset($cat_html);

$j_result= json_encode($a_result);

//echo "<pre>".urldecode($j_result)."</pre>";
$p=iconv("ASCII","UTF-8","\xEF\xBB\xBF".urldecode($j_result));
$file = dirname(__FILE__) . '/data/bnext.txt';
file_put_contents($file,"\xEF\xBB\xBF".urldecode($j_result)."\n\r");
echo "Parsed " .$i ." files completed<BR>";
?>

<BR/>
<pre>
<?php
//var_dump($a_result);
?>
</pre>

