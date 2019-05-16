<style>
table,td,th{
    border: 1px solid black;
}
td{
    padding: 5px;
}
</style>
<?php
include("db.php");

$all_tables = [];

$result = mysqli_query($connection, "show tables");
while ($table = mysqli_fetch_array($result)) {
    array_push($all_tables, $table[0]);
}

for ($i=0; $i < count($all_tables); $i++) { 
    print "<h1>$all_tables[$i]</h1>";

    $sql = "SELECT * FROM $all_tables[$i] LIMIT 10";
    $result = $connection->query($sql);

    if ($result->num_rows > 0) {

        print "<table>";
        $increment = 0;
        while ($row = $result->fetch_assoc()) {

        print "<tr>";
                
                foreach ($row as $col_name => $col_value) {
                    // print "$col_name => $col_value<hr>";
                    if ($increment >= 0 and $increment < count($row)) {
                        print "<th>$col_name</th>";
                    }else{
                        print "<td>$col_value</td>";
                    }                    

                    if (count($row) == $increment+1) {
                        print "</tr><tr>";
                    }
                    $increment++;
                }                

        }
        print "</tr>";
        print "<table>";

    } else {
        echo "0 results";
    }
    exit;
}

mysqli_close($connection);
?>
