<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
	<head>
		<title>Yeepr Add Post</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="styles.css" type="text/css" charset="utf-8" />
	</head>
	<body>
<center>	
    <div id="header">
<h1>SUMMIZ REDACTOR</h1><br>
<center><a href="home.php">REDACTOR HOME</a></center> <center><a href="index.php">Accès rédacteur</a></center>
</div>
<div id="content">
<?php
if(isset($_POST['titre']) && isset($_POST['contenu']) && isset($_POST['pseudo'])) {
     //On définit les variables
	$titre = $_POST['titre'];
     $contenu = $_POST['contenu'];
     $pseudo = $_POST['pseudo'];
	//On récupère les données déjà existantes
	$news = unserialize(file_get_contents('news.txt'));
	$news[] = array('titre' => $titre, 'auteur' => $pseudo, 'contenu' => $contenu);
	file_put_contents('news.txt', serialize($news));
	
	echo 'La news a bien &eacute;t&eacute; ajout&eacute;e !';
	echo '<br />';
	echo '<a href="home.php">Retour</a>';
}
else {
	?>
	<form action="" method="post">
		<label for="pseudo">Votre pseudo :</label> <input type="text" name="pseudo" id="pseudo" /><br />
		<label for="titre">Titre de la news :</label> <input type="text" name="titre" id="titre" /><br />
		<label for="contenu">Contenu de la news :</label> <br />
		<textarea name="contenu" id="contenu" rows="20" cols="60"></textarea><br />
		<input type="submit" value="Ajouter la news" />
	</form>
	<?php
}
?>        
	
</div>   
</center>     
	</body>
</html>