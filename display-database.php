<style>
table,td,th{
    border: 1px solid black;   
}
td{
    padding: 10px;
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
    print "
    <table>            
        <tr>
            <th>Field</th>
            <th>Type</th>
            <th>Null</th>
            <th>Key</th>
            <th>Default</th>
            <th>Extra</th>
        </tr>
    ";

    $q = mysqli_query($connection, "DESCRIBE $all_tables[$i]");
    while ($row = mysqli_fetch_array($q)) {
        // var_dump($row);
        print "               
            <tr>
                <td>{$row['Field']}</td>
                <td>{$row['Type']}</td>
                <td>{$row['Null']}</td>
                <td>{$row['Key']}</td>
                <td>{$row['Default']}</td>
                <td>{$row['Extra']}</td>
            </tr>   
        ";
    }
    print "</table>";


}

// var_dump($all_tables);
mysqli_close($connection);
?>
