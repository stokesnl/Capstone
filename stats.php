<?php session_start(); 

    if(isset($_POST['employees']))
    {
        header("Location: employees.php");
    }
    else if(isset($_POST['management']))
    {
        header("Location: management.php");
    }
    else if(isset($_POST['stats']))
    {
        header("Location: stats.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Payroll</title>
    <link rel="stylesheet" type="text/css" href="css/app.css">
    <link rel="stylesheet" type="text/css" href="table.css">
  </head>
  <div id="fullcontainter">
  <div class="top-bar">
    <h3><a href="index.php">Web Payroll</a></h3>
    <ul class="menu">
        <li><a href="#" onClick="logout()">Logout</a></li>
        <li><a href="demonstration.php">Demonstration</a></li>
        <li><a href="about.php">About Us</a></li>
    </ul>
  </div>

    <?php

     include("connect.php");
     $mysqli = connect();
    if(isset($_POST['jobcost']))
     {
         //No user input values here, no need for prepared statement
         //get jobcost information
         $result = $mysqli->query("select j.jobID, j.location, sum(ej.hrsWorked) AS hrsWorked, e.payRate
            from employee e
             join emp_job ej on e.ID = ej.ID
             join job j  on ej.jobID = j.jobID
             where e.managerID = '{$_SESSION['userid']}'
             group by j.jobID
             order by j.location");
         $printed = 3;

     }
    else if(isset($_POST['joblocs']))
     {
         //No user input values here, no need for prepared statement;
         //Get job location information
        $result = $mysqli->query("select j.location, j.description, j.status
              from job j  
              where j.ceoID IN (select ceoID from manager
              where managerID = '{$_SESSION['userid']}')
              order by j.location");
        $printed = 3;
     }

     else
     {
         //No user input values here, no need for prepared statement
         //Get employee hours worked information
         $result = $mysqli->query("select e.fName, e.lName, sum(ej.hrsWorked) AS hrsWorked, e.payRate
              from employee e
              join emp_job ej
              where e.managerID = '{$_SESSION['userid']}' AND
              e.ID = ej.ID AND
              ej.paid != 'Y'
              group by ej.ID
              order by e.lName");
         $printed = 4;
        $_POST['emphrs'] = 'x';
     }
    ?>
    <div class="tabx" align="center">
    <form class="tabs" action="" method="post">
    <input type="hidden" name="employees">
    <button class="page" formmethod="post">Employees</button>
    </form>
    <form class="tabs" action="" method="post">
    <input type="hidden" name="management">
    <button class="page" formmethod="post">Management</button>
    </form>
    <form class="tabs" action="" method="post">
    <input type="hidden" name="stats">
    <button class="page" formmethod="post">Stats</button>
    </form>
    </div>

    <div class="tabx" align="center">
    <form class="tabs" action="" method="post">
    <input type="hidden" name="jobcost">
    <button class="tablinks" formmethod="post">Job Payroll</button>
    </form>
    <form class="tabs" action="" method="post">
    <input type="hidden" name="emphrs">
    <button class="tablinks" formmethod="post">Payroll Owed</button>
    </form>
    <form class="tabs" action="" method="post">
    <input type="hidden" name="joblocs">
    <button class="tablinks" formmethod="post">Jobs</button>
    </form>
    </div>
    <div class="tablecontainer">
    <div class="row" data-equalizer>
        <div class="medium-12 large-12 columns" data-eqalizer-watch>

  <?php
        //display appropriate table headers
        echo "<table>";
        if(isset($_POST['joblocs']))
        {
            echo "<th>Job Location</th> <th>Job Description</th> <th>Status</th>";
        }
        else if(isset($_POST['jobcost']))
        {
            echo "<th>Job Number</th> <th>Job Location</th> <th>Man Hours On Job</th> <th>Job Payroll</th>";
        }
        else
        {
            echo "<th>First Name</th> <th>Last Name</th> <th>Hours Worked</th> <th>Pay Rate</th> <th>Owed</th>";
        }
    while($row = mysqli_fetch_array($result))
    {
        echo "<tr>";
        //display the data from the result
        for($x = 0; $x < $printed; $x++)
        {
                if(isset($_POST['employees']) and $x == 2)
                {
                    echo "<td>$" .$row[$x]."</td>";
                    echo "<td>" .$row[5]. "</td>";
                    $x++;
                }
                else if(isset($_POST['emphrs']) and $x == 3)
                {
                    echo "<td>$" .$row[$x]."</td>";
                }
                else
                {
                    echo "<td>" .$row[$x]."</td>";
                }
        }
        if(isset($_POST['emphrs']) or isset($_POST['jobcost']))
        {
            $owed = $row['hrsWorked'] * $row[3];
            echo "<td>$ $owed</td>";
        }
        echo "</tr>";
    }
    ?>
   </table>
   <?php
        if(isset($_POST['joblocs']))
        {
            echo "<p align='center'> U = upcoming I = In progress F = Finished</p>";
        }
   ?>

   </div></div>


    </div>
    <div class="footer">
        <h5 align="center"> Contact Us </h5>
        <p align="center"> 999-999-999 <br> RandomEmail@aol.com <br>
        &copy; Appalachian State Universtiy. All rights reserved</p>
    </div>
  </body>
</html>

<script>
    function logout()
    {
        document.location = 'logout.php';
    }
</script>
