<?php
    require_once 'song.php';
    require_once 'template.php';
    function queryDB($query = null)
    {
        $db = new SQLite3('FinalDB.db');
        $sql = 'SELECT * FROM Songs ';

        if (!empty($_GET)) {
            $languages = "";
            if (isset($_GET['czech'])) {
                if ($languages != "") {
                    $languages .= " OR Lang=";
                }
                $languages .= "'CZECH'";
            }
            if (isset($_GET['english'])) {
                if ($languages != "") {
                    $languages .= " OR Lang=";
                }
                $languages .= "'ENGLISH'";
            }
            if (isset($_GET['slovak'])) {
                if ($languages != "") {
                    $languages .= " OR Lang=";
                }
                $languages .= "'SLOVAK'";
            }
            if (isset($_GET['spanish'])) {
                if ($languages != "") {
                    $languages .= " OR Lang=";
                }
                $languages .= "'SPANISH'";
            }
            if (isset($_GET['other'])) {
                if ($languages != "") {
                    $languages .= " OR Lang=";
                }
                $languages .= "'OTHER'";
            }
            if ($languages != "" && $languages != "'CZECH' OR Lang='SPANISH' OR Lang='ENGLISH' OR Lang='SLOVAK' OR Lang='OTHER'") {
                $sql .= "WHERE (Lang=" . $languages . ")";
            }
        }

        @$query = $_GET['query'];
        if ($query == null) {
            $sql .= ' ORDER BY Title asc';
            $result = $db->query($sql);
        } else {
            $query = htmlspecialchars($query);
            $sql .= " AND (Title LIKE :query OR Artist LIKE :query) ORDER BY Title asc";
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':query', '%' . $query . '%', SQLITE3_TEXT);
            $result = $stmt->execute();
        }
        if ($result != null) {
            $i = 0;
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $songs[$i] = new Song($row['_id'], $row['Title'], $row['Artist'], $row['hasGen'], $row['AddedOn'], $row['Lang']);
                $i++;
            }
        }
        return $songs;
    }

    $czech = @$_GET["czech"];
    $english = @$_GET["english"];
    $spanish = @$_GET["spanish"];
    $slovak = @$_GET["slovak"];
    $other = @$_GET["other"];

    if (empty($_GET)) {
        $czech = "checked";
        $spanish = "checked";
        $english = "checked";
        $slovak = "checked";
        $other = "checked";
    }

    $songs = queryDB();
    $page_title = "Domčíkův Zpěvník";
?>

<form class="form-horizontal" action="index.php" method="GET">
<fieldset>
<legend>Search settings</legend>
<div class="form-group">
    <input type="text" class="col-lg-10" placeholder="Search" name="query"/>
    <input class="btn btn-default" type="submit" value="Search"/>
    </div>
    <div class="well">
    
    <label for="czech" class="col-lg-2 control-label">Czech</label>
    <input type="checkbox" id= "czech" name="czech" value="checked" <?php echo $czech; ?>/>
    English
    <input type="checkbox" name="english" value="checked" <?php echo $english; ?>/>
    Spanish
    <input type="checkbox" name="spanish" value="checked" <?php echo $spanish; ?>/>
    Slovak
    <input type="checkbox" name="slovak" value="checked" <?php echo $slovak; ?>/>
    Other
    <input type="checkbox" name="other" value="checked" <?php echo $other; ?>/>
    </div>
        <br>
    <br>
    Sort By:
    <select name="sortBy">
        <option value="Title">Title</option>
        <option value="Artist">Artist</option>
        <option value="AddedOn">Date added</option>
    </select>
    <select name="ascDesc">
        <option value="asc">Ascending</option>
        <option value="desc">Descending</option>
    </select>
    </fieldset>
</form>

<table class="table table-striped table-hover" style="width:auto;">
    <thead>
    <tr>
        <th>Title</th>
        <th>Artist</th>
        <th>Language</th>
        <th>AddedOn</th>
        <th>GenPDF</th>
        <th>Original</th>
        <th>Compressed</th>
        <th>Edit</th>
    </tr>
    </thead>
    <?php
        foreach ($songs as $song) {
            ?>
            <tr>
                <td><?php echo $song->getTitle(); ?></td>
                <td><?php echo $song->getArtist(); ?></td>
                <td><?php echo $song->getLanguage(); ?></td>
                <td><?php echo $song->getDateAdded(); ?></td>
                <td><?php echo $song->getGenLink(); ?></td>
                <td><?php echo $song->getSkenLink(); ?></td>
                <td><?php echo $song->getCompLink(); ?></td>
                <td>
                    <a href="submit.php?id=<?php echo $song->getId()?>" class="btn btn-primary btn-xs">Edit</a>
                </td>
                </td>
            </tr>
            <?php
        }
    ?>
</table>
</body>
</html>