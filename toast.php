<?php
ini_set("display_errors",0);error_reporting(0);

 
class ContentExtractor {
     
    var $container_tags = array(
            'div', 'table', 'td', 'th', 'tr', 'tbody', 'thead', 'tfoot', 'col', 
            'colgroup', 'ul', 'ol', 'html', 'center', 'span'
        );
    var $removed_tags = array(
            'script', 'noscript', 'style', 'form', 'meta', 'input', 'iframe', 'embed', 'hr', 'img',
            '#comment', 'link', 'label'
        );
    var $ignore_len_tags = array(
            'span'
        );  
         
    var $link_text_ratio = 0.04;
    var $min_text_len = 20;
    var $min_words = 0; 
     
    var $total_links = 0;
    var $total_unlinked_words = 0;
    var $total_unlinked_text='';
    var $text_blocks = 0;
     
    var $tree = null;
    var $unremoved=array();
     
    function sanitize_text($text){
        $text = str_ireplace('&nbsp;', ' ', $text);
        $text = html_entity_decode($text, ENT_QUOTES);
         
        $utf_spaces = array("\xC2\xA0", "\xE1\x9A\x80", "\xE2\x80\x83", 
            "\xE2\x80\x82", "\xE2\x80\x84", "\xE2\x80\xAF", "\xA0");
        $text = str_replace($utf_spaces, ' ', $text);
         
        return trim($text);
    }
     
    function extract($text, $ratio = null, $min_len = null){
        $this->tree = new DOMDocument();
         
        $start = microtime(true);
        if (!@$this->tree->loadHTML($text)) return false;
         
        $root = $this->tree->documentElement;
        $start = microtime(true);
        $this->HeuristicRemove($root, ( ($ratio == null) || ($min_len == null) ));
         
        if ($ratio == null) {
            $this->total_unlinked_text = $this->sanitize_text($this->total_unlinked_text);
             
            $words = preg_split('/[\s\r\n\t\|?!.,]+/', $this->total_unlinked_text);
            $words = array_filter($words);
            $this->total_unlinked_words = count($words);
            unset($words);
            if ($this->total_unlinked_words>0) {
                $this->link_text_ratio = $this->total_links / $this->total_unlinked_words;// + 0.01;
                $this->link_text_ratio *= 1.3;
            }
             
        } else {
            $this->link_text_ratio = $ratio;
        };
         
        if ($min_len == null) {
            $this->min_text_len = strlen($this->total_unlinked_text)/$this->text_blocks;
        } else {
            $this->min_text_len = $min_len;
        }
         
        $start = microtime(true);
        $this->ContainerRemove($root);
         
        return $this->tree->saveHTML();
    }
     
    function HeuristicRemove($node, $do_stats = false){
        if (in_array($node->nodeName, $this->removed_tags)){
            return true;
        };
         
        if ($do_stats) {
            if ($node->nodeName == 'a') {
                $this->total_links++;
            }
            $found_text = false;
        };
         
        $nodes_to_remove = array();
         
        if ($node->hasChildNodes()){
            foreach($node->childNodes as $child){
                if ($this->HeuristicRemove($child, $do_stats)) {
                    $nodes_to_remove[] = $child;
                } else if ( $do_stats && ($node->nodeName != 'a') && ($child->nodeName == '#text') ) {
                    $this->total_unlinked_text .= $child->wholeText;
                    if (!$found_text){
                        $this->text_blocks++;
                        $found_text=true;
                    }
                };
            }
            foreach ($nodes_to_remove as $child){
                $node->removeChild($child);
            }
        }
         
        return false;
    }
     
    function ContainerRemove($node){
        if (is_null($node)) return 0;
        $link_cnt = 0;
        $word_cnt = 0;
        $text_len = 0;
        $delete = false;
        $my_text = '';
         
        $ratio = 1;
         
        $nodes_to_remove = array();
        if ($node->hasChildNodes()){
            foreach($node->childNodes as $child){
                $data = $this->ContainerRemove($child);
                 
                if ($data['delete']) {
                    $nodes_to_remove[]=$child;
                } else {
                    $text_len += $data[2];
                }
                 
                $link_cnt += $data[0];
                 
                if ($child->nodeName == 'a') {
                    $link_cnt++;
                } else {
                    if ($child->nodeName == '#text') $my_text .= $child->wholeText;
                    $word_cnt += $data[1];
                }
            }
             
            foreach ($nodes_to_remove as $child){
                $node->removeChild($child);
            }
             
            $my_text = $this->sanitize_text($my_text);
             
            $words = preg_split('/[\s\r\n\t\|?!.,\[\]]+/', $my_text);
            $words = array_filter($words);
         
            $word_cnt += count($words);
            $text_len += strlen($my_text);
             
        };
 
        if (in_array($node->nodeName, $this->container_tags)){
            if ($word_cnt>0) $ratio = $link_cnt/$word_cnt;
             
            if ($ratio > $this->link_text_ratio){
                    $delete = true;
            }
             
            if ( !in_array($node->nodeName, $this->ignore_len_tags) ) {
                if ( ($text_len < $this->min_text_len) || ($word_cnt<$this->min_words) ) {
                    $delete = true;
                }
            }
             
        }   
         
        return array($link_cnt, $word_cnt, $text_len, 'delete' => $delete);
    }
     
}

//gogogo

require_once('simplehtmldom_1_5/simple_html_dom.php');



$keyword = "";
if(isset($_POST['submit'])) {
    $suffix = "";
    $keyword = str_replace(" ", "+", $_POST['keyword'] . " " . $suffix);
}

if(!empty($keyword)) {
    $url  = 'http://www.google.com/search?hl=en&safe=active&tbo=d&site=&source=hp&q='.$keyword.'&oq=' .$keyword;
    $html = file_get_html($url);
	

    $linkObjs = $html->find('h3.r a');
} else {
    $linkObjs = [];
}

echo "
<!DOCTYPE html>
<html>
<head>
<meta content='text/html;charset=ISO-8859-1' http-equiv='Content-Type'><title>Summiz</title>
<link rel='icon' type='image/png' href='k.png'>
<link rel='STYLESHEET' href='style.css' type='text/css'>
<meta content='William A. Clément' name='author'>
<link rel='stylesheet' href='css/style.css' />


</head>
<body>";

echo '
 ';
echo "


<div id='rogue'>
";


if(count($linkObjs) >0) {
    echo '

<br> <br> <br> <br> <br> <br> <br>
<center><img src="img/logo.png" style="width: 200px; height: auto;" alt="Stop Wars"><div id="lol">Summurized !<div class="content"><div style="color:white"><br><br><font face="Lucida sans">
<br><img src="img/mini.png" name="photo" style="width: 25%; height: auto;" /><b>+
Software + design</b>
<br>
<br>
Created by William Clément<br>
</font></div></div></div>
			
<br>
<form method="post" autocomplete="off" action="toast.php"><input placeholder="what do you want to read on ?" style="border: medium none ; background: transparent url() repeat scroll 0% 50%; -moz-background-clip: initial; -moz-background-origin: initial; -moz-background-inline-policy: initial; cursor: pointer; width: 370px; height: 50px; font-size: 14pt;"  name="keyword" value="" type="text"><br>
<br><center><button class="bouton" type="submit" onkeypress="if (event.keyCode == 13) mafonction()" name="submit" value="Search" type="submit" border="none" ;="">Generate article</button></center>
</form>
	<br>'.count($linkObjs).' Resultats trouvés pour le mot '.$keyword.'</br>

';
} else {
    echo ' 
<br> <br> <br> <br> <br> <br> <br>
<center><img src="img/logo.png" style="width: 200px; height: auto;" alt="Stop Wars"><div id="lol">Hi. Your request seems to be wrong. Please retry !<div class="content"><div style="color:white"><br><br><font face="Lucida sans">
<br><img src="img/mini.png" name="photo" style="width: 25%; height: auto;" /><b>+
Software + design</b>
<br>
<br>
Created by William Clément<br>
</font></div></div></div>
			
<br>
<form method="post" autocomplete="off" action="toast.php"><input placeholder="what do you want to read on ?" style="border: medium none ; background: transparent url() repeat scroll 0% 50%; -moz-background-clip: initial; -moz-background-origin: initial; -moz-background-inline-policy: initial; cursor: pointer; width: 370px; height: 50px; font-size: 14pt;"  name="keyword" value="" type="text"><br>
<br><center><button class="bouton" type="submit" onkeypress="if (event.keyCode == 13) mafonction()" name="submit" value="Search" type="submit" border="none" ;="">Generate article</button></center>
</form>
	
';
}
function vire($texte){
	//Tableau de mots interdits
	$mots_interdits = array('cal','calcule','calculatrice','calculette','calculer','mod');
	//On passe le texte en minuscule
	$texte = strtolower($texte);
	//Compteur
	$i = 1;
	//On bloucle
	foreach($mots_interdits as $result){
		//strpos — Cherche la position de la première occurrence dans une chaîne
		$trouve = strpos($texte , $result);
		if($trouve !== false && $i == 1){
			echo '
			<div class="features"><iframe width="590" height="390" src="http://web2.0calc.fr/widgets/horizontal/?options=%7B%22angular%22%3A%22deg%22%2C%22options%22%3A%22show%22%2C%22menu%22%3A%22show%22%7D" scrolling="no" style="border: 1px solid silver; "> 
</iframe></div>
			';
			$i++;
		} 
	}
}
vire($keyword);

function vire2($texte2){
	//Tableau de mots interdits
	$mots_interdits2 = array('7','seven','sept');
	//On passe le texte en minuscule
	$texte2 = strtolower($texte2);
	//Compteur
	$i2 = 1;
	//On bloucle
	foreach($mots_interdits2 as $result2){
		//strpos — Cherche la position de la première occurrence dans une chaîne
		$trouve2 = strpos($texte2 , $result2);
		if($trouve2 !== false && $i2 == 1){
			echo '
			<div class="features">
		<canvas id="game" width="600" height="800">
			<p lang="fr">Votre navigateur ne supporte pas HTML 5.</p> <!-- Un petit message pour les dinosaures -->
		</canvas></div>
<script src="quintus-all.js"></script> 
<script src="partie-1.js"></script> 
<script src="partie-2.js"></script>
<script src="partie-3.js"></script>

			';
			$i2++;
		} 
	}
}
vire2($keyword);

function vire3($texte3){
	//Tableau de mots interdits
	$mots_interdits3 = array('asteroid','play','space','asteroids','mod');
	//On passe le texte en minuscule
	$texte3 = strtolower($texte3);
	//Compteur
	$i3 = 1;
	//On bloucle
	foreach($mots_interdits3 as $result3){
		//strpos — Cherche la position de la première occurrence dans une chaîne
		$trouve3 = strpos($texte3 , $result3);
		if($trouve3 !== false && $i3 == 1){
			echo '
			<div class="features"><iframe width="620" height="438" src="http://www.playmycode.com/play/embed/krakatomato/asteroids" marginheight="0" marginwidth="0" scrolling="no" frameborder="0" style="border: none; border-size: 0; overflow: hidden; overflow-x: hidden; overflow-y: hidden;"></iframe>
			</div>';
			$i3++;
		} 
	}
}
vire3($keyword);







function vire5($texte5){
	//Tableau de mots interdits
	$mots_interdits5 = array('heure','horaire','temps','mod');
	//On passe le texte en minuscule
	$texte5 = strtolower($texte5);
	//Compteur
	$i5 = 1;
	//On bloucle
	foreach($mots_interdits5 as $result5){
		//strpos — Cherche la position de la première occurrence dans une chaîne
		$trouve5 = strpos($texte5 , $result5);
		if($trouve5 !== false && $i5 == 1){
			echo '
					<div class="features">
					<table width="150" border="0" cellpadding="0" cellspacing="0" STYLE="border-color: #cccccc ; border-width: 1px;border-style: solid;">
						<tr>
						<td width="150" height="19"><div align="center"><IFRAME src="http://www.Heure.com/heure-fr.php?timezone=1&size=36" WIDTH="140px" HEIGHT="35px" BORDER="0" MARGINWIDTH="0" MARGINHEIGHT="10" HSPACE="0" VSPACE="0" FRAMEBORDER="0" SCROLLING="no"> </IFRAME>
                   		</div></td>
					  </tr>
					  <tr>
						<td height="12" valign="top"><div align="center"><font face="Arial, Helvetica, sans-serif" size="1">Powered by <a href="http://www.Heure.com" style="color:#000000;text-decoration:none; ">Heure.com</a></font></div></td>
					  </tr>
				  </table>
					</div>
			';
			$i5++; 
		} 
	}
}
vire5($keyword);

foreach ($linkObjs as $linkObj) {
    $title = trim($linkObj->plaintext);
    $link  = trim($linkObj->href);
	
	
    
    // if it is not a direct link but url reference found inside it, then extract
    if (!preg_match('/^https?/', $link) && preg_match('/q=(.+)&amp;sa=/U', $link, $matches) && preg_match('/^https?/', $matches[1])) {
        $link = $matches[1];
    } else if (!preg_match('/^https?/', $link)) { // skip if it is not a valid link
        continue;    
    }
    
    //echo '<p>Title: ' . $title . '<br />';
    //echo 'Link: <a href="'.$link.'">' . $link . '</a><br></p>';
    echo '
    
    <div id="feature">
	<a href="'.$link.'" target="_blank">
	
     <p><img src="http://www.google.com/s2/favicons?domain='.$link.'" width="20" height="20" alt="Mon Image"/>'.$title.'</p></a>
	
	 
	</div>

    
    </a>    
    ';
	
	$as = file_get_contents(''.$link.'');
 
$extractor = new ContentExtractor();
$content = $extractor->extract($as); 



//error_reporting(E_ALL);

require_once 'includes/summarizer.php';
require_once 'includes/html_functions.php';

$summarizer = new Summarizer();


	//echo '<pre>';
	$text = strip_tags($content);

	
	//replace some Unicode characters with ASCII
	$text2 = normalizeHtml($text);
	//generate the summary with default parameters
	$rez = $summarizer->summary($text);
	//print_r($rez);
	
	//$rez is an array of sentences. Turn it into contiguous text by using implode().
	$summary = implode(' ',$rez);
	//echo '</pre>';



//echo strip_tags($content);

echo $text2;
echo $summary;
}



echo "
</ul>
</div>
</div>
</div>
</div>
<br>
<br>

</html>

";








?>


