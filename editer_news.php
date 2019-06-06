<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
	<head>
		<title>Yeep (re)post</title>
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
if(!isset($_GET['id'])) {
	header('Location: home.php');
	exit();
}

$news = unserialize(file_get_contents('news.txt'));
$newsAmodifier = (int) $_GET['id'];
if(isset($_POST['titre']) && isset($_POST['contenu'])) {
	$news[$newsAmodifier]['titre'] = $_POST['titre'];
	$news[$newsAmodifier]['contenu'] = $_POST['contenu'];
	file_put_contents('news.txt', serialize($news));
	echo 'La news a bien &eacute;t&eacute; edit&eacute;e.';
	echo '<br />';
	echo '<a href="home.php">Retour</a>';
} else {
	?>
	<form action="" method="POST">
	Auteur : <strong><?php echo $news[$newsAmodifier]['auteur'] ?></strong><br />
	<label for="titre">Titre de la news :</label> <input type="text" name="titre" id="titre" value="<?php echo $news[$newsAmodifier]['titre'] ?>" /><br />
	<label for="contenu">Contenu de la news : </label><br />
	<textarea name="contenu" id="contenu" rows="20" cols="60"><?php echo $news[$newsAmodifier]['contenu'] ?></textarea><br />
		<input type="submit" value="Appliquer les modifications" />
	</form>
	<?php
}
?>

</div>   
</center>     
</body>
</html>