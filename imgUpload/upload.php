<!DOCTYPE html>
<html>
    <head><TITLE>Nanorift
    </TITLE>
	</head>
    <body> 
	<div style="text-align: center;"><br><br><br><br><br><br><a href="index.php"><img style="width: 20%; height: auto;" alt="" src="logo.png"></a><br><br>
        <?PHP
        
        // 
        $MAX_FILE_SIZE = 2000000;
        $MAX_FILE_SIZE_STR = '2MB';
        $ACCEPTED_FILE_TYPES = array(IMAGETYPE_JPEG, IMAGETYPE_PNG);
        $ACCEPTED_FILE_TYPES_STR = 'JPG, PNG';
        $IMG_FRAME_WIDTH = 400;
        $IMG_FRAME_HEIGHT = 300;
        $IMG_FRAME_ASPECT = $IMG_FRAME_WIDTH / $IMG_FRAME_HEIGHT;
        
        // 
        $tempFileLocation = $_FILES['uploadedFile']['tmp_name'];
        @$imageInfo = getimagesize($tempFileLocation);
        
        // 
        $fileSize = $_FILES['uploadedFile']['size'];
        if ($fileSize > $MAX_FILE_SIZE) {
            echo "<p>Fichier trop gros</p>";
            echo "<p>Maximum: {$MAX_FILE_SIZE_STR}.</p>";
            exit();
        }

        // 
        $imageFileType = $imageInfo[2];
        if (!in_array($imageFileType, $ACCEPTED_FILE_TYPES)) {
            echo "<p>Fichiers invalides</p>";
            echo "<p>les fichiers acceptes sont: {$ACCEPTED_FILE_TYPES_STR}</p>";
            exit();
        }

        // 
        $targetFileName = $_FILES['uploadedFile']['name'];
        $targetFileLocation = "uploads/{$targetFileName}";
        $moveSuccess = move_uploaded_file($tempFileLocation, $targetFileLocation);
        if (!$moveSuccess) {
            echo "<p>ERREUR INTERNE</p>";
            exit();
        }

        // 
        // 
        $imageWidth = $imageInfo[0];
        $imageHeight = $imageInfo[1];
        $imageAspectRatio = $imageWidth / $imageHeight;

        if ($imageAspectRatio >= $IMG_FRAME_ASPECT) {
            $imageSizeAttr = "width='$IMG_FRAME_WIDTH'";
        } elseif ($imageAspectRatio < $IMG_FRAME_ASPECT) {
            $imageSizeAttr = "height='$IMG_FRAME_HEIGHT'";
        }

        $imageFrame = <<<HTML
                <div  style='width:{$IMG_FRAME_WIDTH}px; max-width:{$IMG_FRAME_WIDTH}px; height:{$IMG_FRAME_HEIGHT}px; border-style:transparent; position:center;'>
                <img src='$targetFileLocation' alt='Uploaded Image' {$imageSizeAttr}>
                </div>
HTML;
        echo $imageFrame;
		//
  $files = glob("uploads/*.*");

  for ($i=1; $i<count($files); $i++)

{

$image = $files[$i];
$supported_file = array(
    'gif',
    'jpg',
    'jpeg',
    'png'
);

$ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
if (in_array($ext, $supported_file)) {
    print $image ."<br />";
    echo '<img style="height:100px;weidth:auto;" src="'.$image .'" alt="Random image" />'."<br /><br />";
} else {
    continue;
 }

}
        ?>
		</div>
    </body>
</html>
