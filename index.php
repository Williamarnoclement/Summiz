<!DOCTYPE html>
<html lang="en" class="no-js">

<head>

<!-- title and meta -->
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1.0" />
<meta name="description" content="" />
<title>Summiz | Fresh news & summary </title>
    
<!-- css -->

<link rel="stylesheet" href="css/font-awesome/font-awesome.css">
<link rel="stylesheet" href="css/base.css" />
<link rel="stylesheet" href="css/style.css" />
<link rel="STYLESHEET" href="style.css" type="text/css">
<meta content="William A. Clément" name="author">
<link rel="stylesheet" href="css/style.css" />

</head>



<body>


<div id="wrapper">

   <br><br><br><br><br><br>
<center>  
    <header>
        <div class="branding">
				<div class="container clearfix">
				<center><img src="img/logo.png" style="width: 200px; height: auto;" alt="Stop Wars"><div id="lol">Summurize easy!<div class="content"><div style="color:white"><br><br><font face="Lucida sans">
				<br><img src="img/mini.png" name="photo" style="width: 25%; height: auto;" /><b>+
				Software + design</b>
				<br>
				<br>
				Created by William Clément<br>
				</font></div></div></div>
							
				<br>
				<form method="post" autocomplete="off" action="toast.php"><input placeholder="what do you want to read on ?" style="border: medium none ; background: transparent url() repeat scroll 0% 50%; -moz-background-clip: initial; -moz-background-origin: initial; -moz-background-inline-policy: initial; cursor: pointer; max-width: 370px; width:60%; height: 50px; font-size: 14pt;"  name="keyword" value="" type="text"><br>
				<br><center><button class="bouton" type="submit" onkeypress="if (event.keyCode == 13) mafonction()" name="submit" value="Search" type="submit" border="none" ;="">Generate article</button></center>
				</form>
				</center>                </div>
            </div>
        

    </header>
</center>
</div>

    <div id="main">
        <div style="max-width:1000px;">

            
                <h2>NEWS</h2>
<?php
$liste_news = unserialize(file_get_contents('news.txt'));
if(!empty($liste_news)) {
	foreach($liste_news as $id => $news) {
		$news = array_map('htmlspecialchars', $news);
		?>
		<h1><?php echo $news['titre'] ?></h1>
			<i>Ajout&eacute;e par <?php echo $news['auteur'] ?></i> <br />
			<p><?php echo $news['contenu'] ?></p>
			<i><a href="#" onclick="return confirm('Etes-vous s&ucirc;r de vouloir supprimer cette news ?');"></a>
			&nbsp;
			<a href="#"></a></i>
			<br /><br />
		<?php
	}
}
else {
	echo 'Il n\'y a aucune news pour le moment<br />';
}
echo '<a href="#"></a>';
?>		
            

        </div>
    </div><!-- #main -->


    <footer>
<center><a href="newsr.php">Accès rédacteur</a></center>
    </footer><!-- /footer -->

</div><!-- /#wrapper -->

<!-- js -->
<script src="js/classie.js"></script>
<script src="js/nav.js"></script>

<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-34160351-1']);
  _gaq.push(['_trackPageview']);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>

</body>
</html>