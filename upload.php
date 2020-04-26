<?php

$sizeLimit = 1024000;
$nbFilesInForm = 5;
$errorsTrack = [];
$authExtentions = ["image/jpg" ,"image/png" , "image/gif" ];


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

                // controle de la taille
                if ($size > $sizeLimit) {
                    $errorsTrack[] = "Le fichier [" . $name . "] depasse la taille limite et a été refusé par le controle de validation. Taille [ " . $size . "].<br>";
                    break;
                }

                // generation part name aleatoire , conservation  nom  initial volontaire + genkey
                $allFileNames = $_FILES["pictures"]["name"];
                $name = basename($allFileNames[$key]);
                $fileInfo = new SplFileInfo($name);
                $ext = pathinfo($name, PATHINFO_EXTENSION);
                $aloneNaqme = pathinfo($name, PATHINFO_FILENAME);
                $uniqIdName = uniqid($aloneNaqme, true) . "." . $ext;

                // controle du  typê de fichier
                $allTypes=  $_FILES["pictures"]["type"];
                $ceType= $allTypes[$key];
                if(!in_array($key,$authExtentions)){
                    $errorsTrack[] = "Le fichier [" . $name . "] est pas d'un type MIME authaurisé.<br>";
                    break; // ajouter suite correctiun  odyssey GD
                }

                move_uploaded_file($tmp_name, "uploads/$uniqIdName");
                break;

            case UPLOAD_ERR_INI_SIZE :
                $errorsTrack[] = "Le fichier  [" . $name . "] depasse la taille limite et a été refusé par le serveur.<br>";
                break;
        }

    }
}

// affichage des erreurs
if (!empty($errorsTrack)) {
    for ($i = 0; $i < count($errorsTrack); $i++) {
        if (!empty($errorsTrack[$i])) echo "Erreur [" . $i . "] : " . (string)$errorsTrack[$i];
    }
}

// detection du  delete
if(isset($_POST)){
    if(isset($_POST["delete"])){
        delete("uploads/" . $_POST["delete"]);
    }
}


// fonctions php
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


<form action="?multiple=true" method="post" enctype="multipart/form-data">
    <p>Images:<br>
        <?php for ($i = 0; $i < $nbFilesInForm; $i++) { ?>
            <input type="file" name="pictures[]"/><br>
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
                     alt="<?= $file->getFilename() ?>" height="auto" width="100">
                <figcaption><?= $file->getFilename() ?></figcaption>
            </figure>
            <input type="hidden" id="delete" name="delete" value="<?= $file->getFilename() ?>">
            <input name="but" type="submit" value="delete"/>
        </p>
    </form>
<?php } ?>
