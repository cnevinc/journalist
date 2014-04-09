
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf8">

</head>
<body>
<?php
include_once('simple_html_dom.php');

$html = new simple_html_dom();
$url = "http://localhost:81/jl/list.htm";
$html->load_file($url);
$sample_count =100 ;
foreach($html->find("ul.postspermonth li a") as $link){
	if($i>$sample_count) break;
	
	echo "$i- parsing $link->href <BR>";
	parse($link->href);
	$i++;
		
		
}



function parse($url){
	global $html;
	global	$links_in_content;
	unset($links_in_content);
	
	global $a_result ;	
	$a_article["site"] = "Inside";
	$a_article["link"] = $url;

	$html->load_file($url);
	
	foreach($html->find("h2.entry-title") as $link){
		$a_article["title"] = $link->plaintext;
		// echo "Article Title:".$link->plaintext."<BR>";
		// echo "Article Title(count):".strlen($link->plaintext)."<BR>";
	}
	
	$ip = gethostbyname(parse_url($url, PHP_URL_HOST));
	
	foreach($html->find("a.author-link") as $link){
		$a_article["author"] = $link->plaintext;
		// echo "Auther:".$link->plaintext."<BR>";
	}

	$mainHtml = $a_article["link"];
		preg_match("/\d{4}\/\d{2}\/\d{2}/", $mainHtml, $matches);
		$time = $matches[0];
		$a_article["time"] = str_replace("/","-",$time);
	
	foreach($html->find("ol li a") as $link){
		if (strpos($link->href,'http') !== false) {
			$links_in_content[] = $link->href;
			// echo "Source Title:".$link->plaintext."<BR>";
			// echo "Source URL :".$link->href."<BR>";
		}
	}
	$a_article["links_in_content"] =$links_in_content ;

	//meta data # links 	
	foreach($html->find("section#content a") as $link){
		if (strpos($link->href,'http') !== false 	) { 
			if(domain($link->href)!=domain($url)){
				$links_in_content[] = $link->href;
				// echo "link_in_content:".$link->href."<BR>";
			}
		}
	}
	$a_article["links_in_content"] =$links_in_content ;
/*
	// meta data # images
	foreach($html->find("section#content img") as $link){
		// echo "content img:".$link->src."<BR>";
	}
*/
	// meta data # words in post
	foreach($html->find("section#content") as $link){
		$content  = $link->plaintext;
		// echo "Word count post:".$link->plaintext."<BR>";
		
		foreach($link->find("div.footnotes") as $link2){
			str_replace($link2->plaintext,"",$content);
			// echo "Word count post div2: ".$link2->plaintext."<BR>";
		}
		foreach($link->find("div.nav-next") as $link3){
			str_replace($link3->plaintext,"",$content);		
			// echo "Word count post div3: ".$link3->plaintext."<BR>";
		}
		foreach($link->find("div.nav-previous") as $link4){
			str_replace($link4->plaintext,"",$content);		
			// echo "Word count post div4: ".$link4->plaintext."<BR>";
			// echo $content;
		}
		// echo "Word count post:".(strlen($link->plaintext)-strlen($link2->plaintext)-strlen($link3->plaintext)-strlen($link4->plaintext));
		
		//$a_article["text_in_content"] = $content;
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

function domain($url) {
	$host = parse_url($url,PHP_URL_HOST);
	preg_match("/[^\.\/]+\.[^\.\/]+$/", $host, $matches);
    return @$matches[0];
}
function toDate($date){
	$date = str_replace("一月","01",$date);
	$date = str_replace("二月","02",$date);
	$date = str_replace("三月","03",$date);
	$date = str_replace("四月","04",$date);
	$date = str_replace("五月","05",$date);
	$date = str_replace("六月","06",$date);
	$date = str_replace("七月","07",$date);
	$date = str_replace("八月","08",$date);
	$date = str_replace("九月","09",$date);
	$date = str_replace("十月","10",$date);
	$date = str_replace("十一月","11",$date);
	$date = str_replace("十二月","12",$date);
	return $date;
}

$j_result= json_encode($a_result);
//echo "<pre>".urldecode($j_result)."</pre>";
$p=iconv("ASCII","UTF-8","\xEF\xBB\xBF".urldecode($j_result));
$file = dirname(__FILE__) . '/data/inside.txt';
file_put_contents($file,"\xEF\xBB\xBF".urldecode($j_result));
echo "Parsed " .$i ." files completed<BR>";

?>
</body>

