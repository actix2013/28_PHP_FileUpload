<?php

$sizeLimit = 1024000;
$nbFilesInForm = 1;
$errorsTrack = [];
$authExtentions = ["image/jpg", "image/jpeg", "image/png", "image/gif" ];

// detection ,erreur somme de fichier trop gros dans le file multiples.
if(!empty($_GET["multiple"])){
    if(empty($_FILES)){
        $errorsTrack[] = "Un probleme non pris en charge est survenue. <br> Généralement ce probleme est du a une taille de fichier additionnés trop  grosse. <br> Sur ce serveur la taille max pour la somme des fichiers est de 8 MB.";
    }
}

// traitement des files et detection erreur

if (!empty($_FILES['pictures'])) {
    $container = $_FILES['pictures'];
    $errors = $container["error"];
    $nbFiles = count($container);

    foreach ($errors as $key => $error) {

        $allFileSize = $_FILES["pictures"]["size"];
        $size = basename($allFileSize[$key]);
        $allFileTmpNames = $_FILES["pictures"]["tmp_name"];
        $tmp_name = $allFileTmpNames[$key];
        $allFileNames = $_FILES["pictures"]["name"];
        $name = basename($allFileNames[$key]);

        if($error === 0) {

                // controle de la taille
                if ($size >= $sizeLimit) {
                    $errorsTrack[] = "Le fichier [" . $name . "] depasse la taille limite interne au  programme [ " . $sizeLimit .  "] et a été refusé . Taille [ " . $size . "].<br>";
                }

                // generation part name aleatoire , conservation  nom  initial volontaire + genkey
                $fileInfo = new SplFileInfo($name);
                $ext = pathinfo($name, PATHINFO_EXTENSION);
                $aloneNaqme = pathinfo($name, PATHINFO_FILENAME);
                $uniqIdName = uniqid($aloneNaqme, true) . "." . $ext;

                // controle du  typê de fichier
                $allTypes=  $_FILES["pictures"]["type"];
                $ceType= $allTypes[$key];
                if(!in_array($ceType,$authExtentions)){
                    $errorsTrack[] = "Le fichier [" . $name . "] est de type MIME [" . $ceType . "] . Ce type n'est authaurisé. <br>";
                }else{
                    move_uploaded_file($tmp_name, "uploads/$uniqIdName");
                }


        }

        if($error === 1) {
            $errorsTrack[] = "Le fichier [" . $name . "] depasse la taille limite du serveur PHP et a été refusé par le controle de validation.<br>";
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
            <input multiple="multiple" type="file" name="pictures[]" accept="image/png, image/jpeg, image/gif"/><br>
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
