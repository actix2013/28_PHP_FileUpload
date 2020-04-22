<?php

$sizeLimit = 1024000;
$nbFilesInForm = 5;
$errorsTrack = [];


// pour le formulaire plusieurs champs
if (!empty($_FILES['pictures'])) {
    $container = $_FILES['pictures'];
    $errors = $container["error"];
    $nbFiles = count($container);
    foreach ($errors as $key => $error) {
        switch ($error) {
            case UPLOAD_ERR_OK :
                $allFileTmpNames = $_FILES["pictures"]["tmp_name"];
                $tmp_name = $allFileTmpNames[$key];
                $allFileSize = $_FILES["pictures"]["size"];
                $size = basename($allFileSize[$key]);
                if ($size > $sizeLimit) {
                    $errorsTrack[] = "Le fichier " . $name . " depasse la taille limite et a été refusé par le controle de validation. Taille [ " . $size . "].<br>";
                    break;
                }
                $allFileNames = $_FILES["pictures"]["name"];
                $name = basename($allFileNames[$key]);
                $fileInfo = new SplFileInfo($name);
                $ext = pathinfo($name, PATHINFO_EXTENSION);
                $aloneNaqme = pathinfo($name, PATHINFO_FILENAME);
                $uniqIdName = uniqid($aloneNaqme, true) . "." . $ext;
                move_uploaded_file($tmp_name, "uploads/$uniqIdName");
                break;

            case UPLOAD_ERR_INI_SIZE :
                $errorsTrack[] = "Le fichier " . $name . " depasse la taille limite et a été refusé par le serveur.<br>";
                break;
        }

    }
}

if (!empty($errorsTrack)) {
    for ($i = 0; $i < count($errorsTrack); $i++) {
        if (!empty($errorsTrack[$i])) echo "Erreur [" . $i . "] : " . (string)$errorsTrack[$i];
    }
}

if(isset($_POST)){

    if(isset($_POST["delete"])){

        delete("uploads/" . $_POST["delete"]);
    }
}

?>


<form action="?multiple=true" method="post" enctype="multipart/form-data">
    <p>Images:<br>
        <?php for ($i = 0; $i < $nbFilesInForm; $i++) { ?>
            <input type="file" accept="image/png, image/jpeg, image/gif" name="pictures[]"/><br>
        <?php } ?>
        <input name="userfile" type="submit" value="Send"/>
    </p>
</form>


<?php
$listFiles = getListFiles();
foreach ($listFiles as $file) { ?>
    <form action="" method="post" >
        <p>Images:<br>
            <figure>
                <img src="<?= "uploads/" . $file->getFilename() ?>"
                     alt="<?= $file->getFilename() ?>">
                <figcaption><?= $file->getFilename() ?></figcaption>
            </figure>
            <input type="hidden" id="delete" name="delete" value="<?= $file->getFilename() ?>">
            <input name="but" type="submit" value="delete"/>
        </p>
    </form>
<?php } ?>


<?php
function getListFiles()
{
    $it = new FilesystemIterator("uploads");
    return $it;
}
function delete(string $filePath) {
    if(file_exists($filePath)){
        unlink($filePath);
    }
}
?>
