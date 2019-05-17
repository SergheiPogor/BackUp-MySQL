<title>BackUp MySQL Database</title>
<h2>Check database to backup</h2>
<h3>By default, System Database are Hidden(row 24-31)</h3>
<form>

    <?php
    // database credits
    $db_host   = "localhost";
    $db_user   = "";
    $db_pass   = "";

    $connection = mysqli_connect($db_host, $db_user, $db_pass);

    if (!$connection) {
        echo "Error: Unable to connect to MySQL => ".mysqli_connect_errno();
        exit;
    }

    $system_db = ['information_schema','mysql','performance_schema','phpmyadmin','test']; // system default database
    $connection = mysqli_connect('localhost', 'root', '19diEz11');
    $result = mysqli_query($connection,"SHOW DATABASES");

    while ($row = mysqli_fetch_assoc($result)) {
        if (!in_array($row['Database'], $system_db)) { // hide System Database
            ?>
                <label>
                    <input type='checkbox' name='database[]' value=<?php print $row['Database'] ?> /><?php print $row['Database'] ?>
                </label>                
                <br>
            <?php
        }
    }

    ?>
    <label>ROWS LIMIT
        <select name="limit">
            <option value="1">1</option>
            <option value="10">10</option>
            <option value="100">100</option>
            <option value="1000">1000</option>
        </select>
    </label><br>
    <input type="submit" name="submit" value="BackUp"/>
</form>

<?php

if(isset($_GET['submit'])){//to run PHP script on submit

    // create your zip file
    $zipname = 'file.zip';
    $zip = new ZipArchive;
    $zip->open($zipname, ZipArchive::CREATE);

    $all_tables = [];

    foreach ($_GET['database'] as $database) {
        print $database."</br>";

        mysqli_select_db($connection, $database) or die(mysqli_error($connection));

        $result = mysqli_query($connection, "show tables");
        while ($table = mysqli_fetch_array($result)) {
            array_push($all_tables, $table[0]);
        }

        for ($i=0; $i < count($all_tables); $i++) {

            $sql = "SELECT * FROM $all_tables[$i]";
            $result = $connection->query($sql);
            if ($result->num_rows > 0) {
                $increment = 0;
                $all_col_name = [];
                $all_col = [];
                $new_row = [];
                while ($row = $result->fetch_assoc()) {
                    array_push($new_row, $row);
                    foreach ($row as $col_name => $col_value) {
                        if ($increment >= 0 and $increment < count($row)) {
                            array_push($all_col_name, $col_name);
                        }
                        $increment++;
                    }
                }
        
                // create a temporary file
                $fd = fopen('php://temp/maxmemory:104857600', 'w');
                if (false === $fd) {
                    die('Failed to create temporary file');
                }
                // write the data to csv
                fputcsv($fd, $all_col_name);
                foreach ($new_row as $record) {
                    fputcsv($fd, $record);
                }
                var_dump($fd);
                // return to the start of the stream
                rewind($fd);
                // add the in-memory file to the archive, giving a name
                $zip->addFromString("$all_tables[$i].csv", stream_get_contents($fd));
                //close the file
                fclose($fd);
            } else {
                echo "0 results<br>";
            }
        }
        // close the archive
        $zip->close();
        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename='.$zipname);
        // header('Content-Length: ' . filesize($zipname));

        readfile($zipname);
        // remove the zip archive
        // you could also use the temp file method above for this.
        unlink($zipname);
        mysqli_close($connection);
    }
}
?>
