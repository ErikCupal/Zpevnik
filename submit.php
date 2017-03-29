<?php
require_once 'template.php';

//default for a new submission
$title="Title";
$artist="Artist";
$added_on="e.g. '1705' for May 2017";
$language="CZECH";
$languages = array("CZECH", "ENGLISH", "SPANISH", "SLOVAK", "OTHER");
$updating = "false";

//Runs if we are editing a record
if(isset($_GET['id'])){
  $GLOBALS['updating']="true"; 
  $id=$_GET['id'];
  $db = new SQLite3('FinalDB.db');
  $sql = 'SELECT * FROM Songs WHERE _id=:id LIMIT 1';
  $stmt = $db->prepare($sql);
  $stmt->bindValue(':id', $id);
  $result=$stmt->execute();
  if ($result != null) {
      $row = $result->fetchArray(SQLITE3_ASSOC);
      $GLOBALS['title'] = $row['Title'];
      $GLOBALS['artist'] = $row['Artist'];
      $GLOBALS['hasGen'] = $row['hasGen'];
      $GLOBALS['added_on'] = $row['AddedOn'];
      $GLOBALS['language'] = $row['Lang'];
  }
}

//Generate the language selector with the correct option pre-selected
function generateSelect($options, $optionToSelect) {
    foreach ($options as $option => $value) {
        if($value == $optionToSelect)
            $html .= '<option value="'.$value.'" selected="selected">'.$value.'</option>';
        else
            $html .= '<option value="'.$value.'">'.$value.'</option>';
    }
    $html .= '</select>';
    return $html;
}
?>

<div class="well">
<form class="form-horizontal" _lpchecked="1" action="upload.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="updating" value="<?php echo $updating;?>">
<input type="hidden" name="id" value="<?php echo $id;?>">
  <fieldset>
    <legend>Submit song info</legend>
    <div class="form-group">
      <div class="col-lg-10">
        <label class="col-lg-2 control-label">Title</label>
        <input type="text" class="form-control" name="inputTitle" placeholder="<?php echo $title;?>" style="cursor: auto;">
      </div>
      <div class="col-lg-10">
        <label class="col-lg-2 control-label">Artist</label>
        <input type="text" class="form-control" name="inputArtist" placeholder="<?php echo $artist;?>" style="cursor: auto;">
      </div>
      <div class="col-lg-10">
        <label class="col-lg-2 control-label">Added on</label>
        <input type="text" class="form-control" name="inputDate" placeholder="<?php echo $added_on;?>" style="cursor: auto;">
      </div>
    </div>

    <div class="form-group">
      <div class="col-lg-10">
        <label class="col-lg-2 control-label">Select language</label>
        <select class="form-control" name="inputLanguage">
          <?php echo generateSelect($languages, $language);?>
        </select>
      </div>
    </div>

    <div class="form-group">
        <label class="col-lg-2 control-label">Select best quality file:</label>
        <input type="file" name="best" id="best">
        <br>
        <label class="col-lg-2 control-label">Select compressed quality file:</label>
        <input type="file" name="compressed" id="compressed">
        <br>
        <label class="col-lg-2 control-label">Select generated file:</label>
        <input type="file" name="gen" id="gen">
    </div>

    <div class="form-group">
      <div class="col-lg-10 col-lg-offset-2">
         <button type="submit" class="btn btn-primary">Submit</button>
         <button type="reset" class="btn btn-default">Cancel</button>
      </div>
    </div>
  </fieldset>
</form>
</div>

</body>
</html>