<?php
function connect()
{
    include("config.php");
    $dbh = new mysqli($servername, $dbusername, $dbpass, $dbname);
    if (!$dbh)
    {
        printf("Unable to connect to database\n");
        exit(1);
    }
    //mysql_select_db("stokesnl", $dbh);
    return $dbh;
}
?>
