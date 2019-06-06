<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
	<head>
		<title>Yeep::ACCES REDACTEUR</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="styles.css" type="text/css" charset="utf-8" />
	</head>
	<body>
<center>
	<div id="header"><h1>SUMMIZ REDACTOR<br></h1>
	<a href="index.php">SUMMIZ Home page</a> <a href="imgUpload/">Image Uploader</a>
</div>
<div id="content">	

   <?php
	if (isset($_POST['mot_de_passe']) AND $_POST['mot_de_passe'] ==  "720") // Si le mot de passe est bon
	{
	// On affiche les codes
	?>
		
			
		<p><em>Bienvenue sur Summiz Redactor! Vous pouvez des maintenant modifier les articles et news avec vos collaborateur sur ce board.</em></p>
		<p>_________________</p>
		<?php
$liste_news = unserialize(file_get_contents('news.txt'));
if(!empty($liste_news)) {
	foreach($liste_news as $id => $news) {
		$news = array_map('htmlspecialchars', $news);
		?>
		<h2><?php echo $news['titre'] ?></h2>
			<i>Ajout&eacute;e par <?php echo $news['auteur'] ?></i> <br />
			<p><?php echo $news['contenu'] ?></p>
			<i><a href="supprimer_news.php?id=<?php echo $id ?>" onclick="return confirm('Etes-vous s&ucirc;r de vouloir supprimer cette news ?');">Supprimer</a>
			&nbsp;
			<a href="editer_news.php?id=<?php echo $id ?>">Editer</a></i>
			<br /><br />
		<?php
	}
}
else {
	echo 'Il n\'y a aucune news pour le moment<br />';
}
echo '<a href="ajouter_news.php">Ajouter une news</a>';
?>

        <?php
	}
	else // Sinon, on affiche un message d'erreur
	{
		echo '<p>Deconnexion soudaine ou/et votre mot de passe est errone.</p>
		<p> Le site peut egalement être en cours de maintenance. Veulliez réessayer plus tard. </p>
		<p> POWERED BY SUMMIZ</p>';
	}
	?>
	
		<p>Initialement cree par William CLEMENT.</p>
		
		<p>(L) Summiz</p>
</div>
</center>		
	</body>
</html>