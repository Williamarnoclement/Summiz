<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
	<head>
		<title>Yeepr crash news</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="styles.css" type="text/css" charset="utf-8" />
	</head>
	<body>
<center>	
    <div id="header">
<a href="http://native.chez.com/yeep/"><img
style="border: 0px solid ; width: 372px; height: 210px;" alt=""
src="http://native.chez.com/yeep/headr.png"></a>
</div>
<div id="content">
<?php
//Si l'id passé en paramètre dans l'url n'existe pas, c'est que le visiteur a été amenené ici par hasard
if(!isset($_GET['id'])) {
	//Donc on redirige vers index.php
	header('Location: index.php');
	//Puis on stoppe l'exécution du script
	exit();
}
//On récupère l'array des news
$news = unserialize(file_get_contents('news.txt'));
//Puis l'id passé en paramètre
$id = (int) $_GET['id'];

//Si la news existe
if(isset($news[$id])) {
	//On efface l'index correspondant à l'id de la news
	unset($news[$id]);
	
	//Puis on sauvegarde le tout
	file_put_contents('news.txt', serialize($news));

	echo 'La news a bien &eacute;t&eacute; supprim&eacute;e !';
}
else {
	echo 'La news n\'existe pas.';
}
echo '<br />';
echo '<a href="home.php">Retour</a>';
?>
</div>   
</center>     
</body>
</html>