<title>Display MySQL Database</title>
<h2>Check database to display</h2>
<h3>By default, System Database are Hidden(row 24-31)</h3>

<form method="post">
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
    <input type="submit" name="submit" value="Display"/>
</form>

<?php
if(isset($_POST['submit'])){//to run PHP script on submit
    $limit = $_POST['limit'];
    $all_tables = [];

    foreach ($_POST['database'] as $database) {
        print $database."</br>";

        mysqli_select_db($connection, $database) or die(mysqli_error($connection));

        $result = mysqli_query($connection, "show tables");
        while ($table = mysqli_fetch_array($result)) {
            array_push($all_tables, $table[0]);
        }

        for ($i=0; $i < count($all_tables); $i++) {

            $sql = "SELECT * FROM $all_tables[$i] LIMIT $limit";
            $result = $connection->query($sql);
            if ($result->num_rows > 0) {
                
                $increment = 0;

                $all_col_name = [];
                $all_col = [];
                $new_row = [];

                print "<table>";
                print "<tr>";
                while ($row = $result->fetch_assoc()) {
                    array_push($new_row, $row);
                    //var_dump($new_row);
                    foreach ($row as $col_name => $col_value) {
                        if ($increment >= 0 and $increment < count($row)) {
                            print "<th>$col_name</th>";
                            array_push($all_col_name, $col_name);
                        }
                        $increment++;
                    }
                    print "</tr><tr>";
                    foreach ($row as $cell) {
                        print "<td>$cell</td>";                        
                    }
                }         

                print "</tr>";
                print "<table>";
                print "<br><hr><br>";

            } else {
                print "Database: $all_tables[$i] => 0 rows";
            }
        }
       
        mysqli_close($connection);
    }
}
?>
<style>
table,td,th{
    border: 1px solid black;
}
td{
    padding: 5px;
}
</style>