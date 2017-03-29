<?php
require_once 'template.php';

?>

<div class="well">
<form class="form-horizontal" _lpchecked="1" action="upload.php" method="post" enctype="multipart/form-data">
  <fieldset>
    <legend>Submit song info</legend>
    <div class="form-group">
      <div class="col-lg-10">
        <label class="col-lg-2 control-label">Title</label>
        <input type="text" class="form-control" name="inputTitle" placeholder=<php echo style="cursor: auto;">
      </div>
      <div class="col-lg-10">
        <label class="col-lg-2 control-label">Artist</label>
        <input type="text" class="form-control" name="inputArtist" placeholder="Artist" style="cursor: auto;">
      </div>
      <div class="col-lg-10">
        <label class="col-lg-2 control-label">Added on</label>
        <input type="text" class="form-control" name="inputDate" placeholder="e.g. '1705' for May 2017" style="cursor: auto;">
      </div>
    </div>
    <div class="form-group">
      <div class="col-lg-10">
        <label class="col-lg-2 control-label">Select language</label>
        <select class="form-control" name="inputLanguage">
        <option value="CZECH">CZECH</option>
        <option value="ENGLISH">ENGLISH</option>
        <option value="SPANISH">SPANISH</option>
        <option value="SLOVAK">SLOVAK</option>
        <option value="OTHER">OTHER</option>
        </select>
      </div>
    </div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Select best quality file:</label>
        <input type="file" name="best" id="best">

        <label class="col-lg-2 control-label">Select compressed quality file:</label>
        <input type="file" name="compressed" id="compressed">
        
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