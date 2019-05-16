<?php
include("db.php");

// create your zip file
$zipname = 'file.zip';
$zip = new ZipArchive;
$zip->open($zipname, ZipArchive::CREATE);
$all_tables = [];
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
                // print "$col_name => $col_value<hr>";
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
?>