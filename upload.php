<?php
    require_once "template.php";
    require_once "makeTextNiceAgain.php";

    $db = new SQLite3('FinalDB.db');

    $artist = $_POST["inputArtist"];
    $title = $_POST["inputTitle"];

    $nice_artist = makeTextNiceAgain($artist);
    $nice_title = makeTextNiceAgain($title);

    $target_dir = "PDFs/";
    $target_file_original = $target_dir . $nice_artist . "_" . $nice_title . "-sken.pdf";
    $target_file_compressed = $target_dir . $nice_artist . "_" . $nice_title . "-comp.pdf";
    $target_file_gen = $target_dir . $nice_artist . "_" . $nice_title . "-gen.pdf";

    $uploading_sken = is_uploaded_file($_FILES["best"]["tmp_name"]);
    $uploading_comp = is_uploaded_file($_FILES["compressed"]["tmp_name"]);
    $uploading_gen = is_uploaded_file($_FILES["gen"]["tmp_name"]);

    $updating = $_POST["updating"];
    $updating_title = !empty($_POST['inputTitle']);
    $updating_artist = !empty($_POST["inputArtist"]);
    $updating_date = !empty($_POST['inputDate']);

    echo "Artist: " . $artist . ", updating: ". $updating_artist . "<br>";
    echo "Title: ". $title . ", updating: ". $updating_title . "<br>";
    echo "Updating date: " . $updating_date . "<br>";

    $uploadOk = 1;
    $alert_type = "alert-danger";
    $message="<strong> Error </strong> <br>";

    //Beginning of code
        $uploadOk = checkUpload($uploadOk, $artist, $title, $db);
    if ($uploadOk == 0) {
        $GLOBALS['message'] .= "Sorry, your file was not uploaded. </br>";
        // If everything is ok, try to upload the file
    } else {
        uploadFiles($target_file_original, $target_file_compressed, $target_file_gen, $db);
    }

    //Check if it's OK to upload the file and write to DB
    function checkUpload($uploadOk, $artist, $title, $db)
    {
        //We only care if its a new record
        if($GLOBALS['updating']==false){
            $uploadOk = checkInputExistence($artist, $title);
            echo "not updating";
        }

        //We only care if were changing the title
        if ($uploadOk == 1 && $GLOBALS['updating_title']) {
            echo "checking title";
            $uploadOk = checkTitle($db, $title);
            echo $uploadOk;
        }
        if ($uploadOk == 1) {
            echo "checking files";
            $uploadOk = checkFiles($db, $title);
        }
        return $uploadOk;
    }

    //Check if an artist and a title are provided
    function checkInputExistence($artist, $title)
    {
        if ($artist == null || $title == null) {
            $GLOBALS['message'].= "You need to provide an artist and a title! </br>";
            return 0;
        }

        //Check if comp and orig are set
        if ($_FILES["best"] == null || $_FILES["compressed"] == null) {
            $GLOBALS['message'] .= "Sorry, you need to upload at least \"Best\" and \"Compressed\" files... </br>";
            return 0;
        }
        return 1;
    }

    //Check if the file already exists
    function checkTitle($db, $title){
        //Title is always unique
        $stmt = $db->prepare('SELECT * FROM Songs WHERE Title = :title');
        $stmt->bindValue('title', $title, SQLITE3_TEXT);
        $result=$stmt->execute();
        if ($result != null) {
            $row = $result->fetchArray(SQLITE3_ASSOC);
            if($row !== FALSE && $row['_id']!=$_POST['id']){
                $GLOBALS['message'] .= "Sorry, title already exists. </br>";
                return 0;
            }
            return 1;
        } else {
            echo "db error when checking the title";
        }
    }

    function checkFiles(){
        // Check file sizes
        if ($_FILES["best"]["size"] > 50000000) {
            $GLOBALS['message'] .= "Sorry, maximum \"Best\" size is 50MB. </br>";
            return 0;
        }
        if ($_FILES["compressed"]["size"] > 5000000) {
            $GLOBALS['message'] .= "Sorry, maximum \"Compressed\" size is 5MB. </br>";
            return 0;
        }
        if ($_FILES["gen"]["size"] > 500000) {
            $GLOBALS['message'] .= "Sorry, maximum \"Gen\" size is 5MB. </br>";
            return 0;
        }

        // Allow only PDFs, check only if it was submitted
        if ((pathinfo(basename($_FILES["best"]["name"]), PATHINFO_EXTENSION) != "pdf") && $GLOBALS['uploading_sken']) {
            $GLOBALS['message'] .= "Sorry, \"Best\" is not a PDF file. </br>";
            return 0;
        }
        if ((pathinfo(basename($_FILES["compressed"]["name"]), PATHINFO_EXTENSION) != "pdf") && $GLOBALS['uploading_comp']) {
            $GLOBALS['message'] .= "Sorry, \"Compressed\" is not a PDF file. </br>";
            return 0;
        }
        if ((pathinfo(basename($_FILES["gen"]["name"]), PATHINFO_EXTENSION) != "pdf") && $GLOBALS['uploading_gen']) {
            $GLOBALS['message'] .= "Sorry, \"Gen\" is not a PDF file. </br>";
            return 0;
        }
        return 1;
    }

    //TODO: backup old files
    function uploadFiles($target_file_original, $target_file_compressed, $target_file_gen, $db)
    {
        //Try to upload files only if they are provided
        if($GLOBALS['uploading_sken']){
            if(!move_uploaded_file($_FILES["best"]["tmp_name"], $target_file_original)){
                $GLOBALS['message'] .= "Sorry, there was an error uploading your sken file. <br>";
                return;
            }
        }
        if($GLOBALS['uploading_comp']){
            if(!move_uploaded_file($_FILES["comp"]["tmp_name"], $target_file_compressed)){
                $GLOBALS['message'] .= "Sorry, there was an error uploading your sken file. <br>";
                return;
            }
        }
        if ($GLOBALS['uploading_gen']) {
            if(!move_uploaded_file($_FILES["gen"]["tmp_name"], $target_file_gen)){
                $GLOBALS['message'] .= "Sorry, there was an error uploading your gen file. <br>";
                return;
            }
        }
        writeIntoDB($db);
        $GLOBALS['message'] .= "The file " . basename($_FILES["best"]["name"]) . " has been uploaded. <br>";
    }

    function writeIntoDB($db)
    {
        $GLOBALS['alert_type'] = "alert-success";
        $GLOBALS['message'] = "<strong>Success!</strong><br>";
        $addedOn = $_POST["inputDate"];
        if (strlen($addedOn) != 4 && $GLOBALS['updating_date']) {
            $addedOn = date("y") . date("m");
            $GLOBALS['message'] .= "Incorrect date, set to " . $addedOn . " <br>";
        }
        $lang = $_POST["inputLanguage"];

        if($GLOBALS['updating']){
            $sql="UPDATE Songs SET Lang=:lang,";
            if ($GLOBALS['uploading_gen']) {
                $sql .= " hasGen=1,";
            }
            
            //Only add when changed
            if($GLOBALS['updating_artist']){
                $sql .= " Artist=:artist,";
            }
            if($GLOBALS['updating_title']){
                $sql .= " Title=:title,";
            }
            if($GLOBALS['updating_date']){
                $sql .= " AddedOn=:addedon,";
            }
            
            //Remove last comma
            $sql = substr($sql, 0, -1);

            //Add condition
            $sql .= " WHERE _id=:id";
            echo $sql;
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':lang', $lang, SQLITE3_TEXT);
            
            //Only bind when changed
            if($GLOBALS['updating_artist']){
                $stmt->bindValue(':artist', $GLOBALS['artist'], SQLITE3_TEXT);
            }
            if($GLOBALS['updating_title']){
                $stmt->bindValue(':title', $GLOBALS['title'], SQLITE3_TEXT);
            }
            if($GLOBALS['updating_date']){
                $stmt->bindValue(':addedon', $addedOn, SQLITE3_INTEGER);
            }

            $stmt->bindValue(':id', $_POST['id'], SQLITE3_TEXT);
            $stmt->execute();

        } else{
            $stmt = $db->prepare('INSERT INTO Songs (Title, Artist, Lang, HasGen, AddedOn) VALUES (:title, :artist, :lang, :hasgen, :addedon)');
            $stmt->bindValue(':artist', $GLOBALS['artist'], SQLITE3_TEXT);
            $stmt->bindValue(':title', $GLOBALS['title'], SQLITE3_TEXT);
            $stmt->bindValue(':lang', $lang, SQLITE3_TEXT);
            $stmt->bindValue(':addedon', $addedOn, SQLITE3_INTEGER);

            if ($GLOBALS['uploading_gen']) {
                $hasGen = 1;
            } else {
                $hasGen = 0;
            }
            $stmt->bindValue(':hasgen', $hasGen);

            $stmt->execute();
        }
    }
?>

<div class="alert alert-dismissible <?php echo $alert_type; ?>">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
  <?php echo $message; ?>
</div>
</body>
</html>