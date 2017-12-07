<?php
    function login($username, $password)
    {
    include ("connect.php");
    $con = connect();
    $hash = hash('sha256', $password);
    $query = "SELECT * FROM manager where (managerEmail ='$username' AND
                    managerPW ='$hash')";
    if(!mysql_query($query, $con))
    {
    //    header("Location: index.html");
    }
    else
    {
        $result = mysql_query($query, $con);
        if(!mysql_num_rows($result)==0)
        {
            //echo "2";
            header("Location: update.php");
            //show();
        }
        else
        {
            //echo "3";
            //header("Location: table.php");
            //header("Location: errorIndex.html");
            //echo '<script> show(); </script>';
        }
    }
    }
?>
