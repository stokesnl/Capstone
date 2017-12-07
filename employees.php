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
<script>
function badval()
{
    alert("Please ensure all values are entered and appropriately\n Data not entered");
}
</script>

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


     if(isset($_POST['paytheman']))
     {
         //Dont see any way someone can improperly use this
         //No need for prepared statement
         //Set paid stats fro employees to yes
         $mysqli->query("update emp_job
                    set paid = 'Y'
                    where ID = '$_POST[paytheman]'");
         $_POST['mpay'] = 'x';
     }
     //Add an entry to emp_job for hours worked
     else if(isset($_POST['Name']) AND isset($_POST['Job']) AND isset($_POST['hrsWorked']) AND isset($_POST['date']))
     {
         if($_POST['hrsWorked'] <= 0)
         {
             echo "<script> badval(); </script>";
         }
         else
         {
         //mysql_query("insert into emp_job
         //       values('$_POST[date]', '$_POST[Name]', '{$_SESSION['userid']}', '$_POST[Job]', '$_POST[hrsWorked]', 'N')");
            $hrsstmt = $mysqli->prepare("insert into emp_job values(?, ?, ?, ?, ?, 'N')");
            $hrsstmt->bind_param("ssisi", $_POST['date'], $_POST['Name'], $_SESSION['userid'], $_POST['Job'], 
                                    $_POST['hrsWorked']);
            $hrsstmt->execute();
         }
         $_POST['addhours'] = 'x';
     }
     //get the necessary information for paying an employee
     if(isset($_POST['mpay']))
     {
         $paystmt = $mysqli->prepare("select e.fName, e.lName, e.payRate, sum(ej.hrsWorked) AS hrsWorked, e.ID
                from employee e
                join emp_job ej
                where e.managerID = ? AND
                e.ID = ej.ID AND
                ej.paid = 'N'
                group by e.ID
                order by e.lName");
         $paystmt->bind_param("i", $_SESSION['userid']);
         $paystmt->execute();
         $result = $paystmt->get_result();
         $_POST['mpay'] = "mpay";
         $printed = 4;
         /*$result = mysql_query("select e.fName, e.lName, e.payRate, sum(ej.hrsWorked) AS hrsWorked, e.ID
         from employee e
         join emp_job ej
         where e.managerID = '{$_SESSION['userid']}' AND
         e.ID = ej.ID AND
         ej.paid = 'N'
         group by e.ID
         order by e.lName");*/

     }

     //otherwise just display employee information
     else
     {
         $empstmt = $mysqli->prepare("select * from employee where managerID = ? order by lName");
         $empstmt->bind_param("i", $_SESSION['userid']);
         $empstmt->execute();
         $result = $empstmt->get_result();
         $_POST['employees'] = "employees";
         $printed = 4;
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
    <input type="hidden" name="addhours">
    <button formmethod="post">Add hours</button>
    </form>
    <form class="tabs" action="" method="post">
    <input type="hidden" name="mpay">
    <button formmethod="post">Pay Employees</button>
    </form>
    <form class="tabs" action="" method="post">
    <input type="hidden" name="employees">
    <button formmethod="post">Employees</button>
    </form>
    </div>
    <div class="tablecontainer">
    <div class="row" data-equalizer>
        <div class="medium-12 large-12 columns" data-eqalizer-watch>
  <?php
        //display appropriate table headers
        echo "<table>";
        if(isset($_POST['employees']) AND !isset($_POST['addemp']) AND !isset($_POST['addhours']) AND !isset($_POST['addjobs']))
        {
         echo "<th>First Name</th>
               <th>Last Name</th>
               <th>Pay Rate</th>
               <th>Role</th>";
        }
        else if(isset($_POST['emphrs']))
        {
            echo "<th>First Name</th> <th>Last Name</th> <th>Hours Worked</th> <th>Pay Rate</th> <th>Owed</th>";
        }
        else if(isset($_POST['mpay']))
        {
            echo"<th>First Name</th> <th>Last Name</th> <th>Pay rate</th> <th style='width:100px'>Hours</th></style> <th>Owed</th> <th>Submit</th>";
        }


    if(isset($_POST['mpay']))
    {
        while($row = mysqli_fetch_array($result))
        {
            //display to show what an employee is owed and pay them
           ?>
           <form class="" action="" method ="post">
           <tr>
           <?php
                echo "<td>$row[0]</td>";
                echo "<td>$row[1]</td>";
                echo "<td>$$row[2]</td>";
                echo "<td style='width:100px'>$row[3]</td>";
                $monowed = $row[3] * $row[2];
                echo "<td>$$monowed</td>";
                echo "<td><input type='submit' name='update'></td>";
                echo "<td style='display:none'><input type='hidden' name='paytheman' value='$row[4]'></td>";
                echo "</form>";
        }
    }
    else if(isset($_POST['addhours']))
    {
        //form to add hours to an employee.
        ?>
        <div class="row" data-equalizer>

            <div class="medium-8 large-12 columns" data-equalizer-watch>
                <form class="" action="" method="post">
                    <h3 class=text-center">Employee</h3>
                    <?php
                    if(isset($_POST['ERROR']))
                    {
                    echo "<p align='center' style='color:red;'> $_POST[ERROR] </p>";
                    }
                        $name = $mysqli->query("select fName, lName, ID from employee where managerID = '{$_SESSION['userid']}'");
                        echo "<label> Employee Name";
                        echo "<select name='Name'>";
                        while($row = mysqli_fetch_array($name))
                        {
                            echo "<option value= '$row[2]'>$row[0] $row[1]</option>";
                        }
                        echo "</select></label>";
                        $jobs = $mysqli->query("select jobID, location, description from job where ceoID IN
                                (Select ceoID from manager where managerID = '{$_SESSION['userid']}')");
                        echo "<label> Job";
                        echo "<select name='Job'>";
                        while($row = mysqli_fetch_array($jobs))
                        {
                            echo "<option value= '$row[0]'>$row[1] $row[2]</option>";
                        }
                        echo "</select></label>";
                        ?>

                    <label>Hours Worked
                    <input type="text" name="hrsWorked" value="8">
                    </label>
                    <?php
                    date_default_timezone_set("America/New_York");
                    $date = date("Y/m/d");
                    echo "<label> Date";
                    echo "<input type='text' name='date' placeholder='YYYY/MM/DD' value='$date'></td>";
                    echo "</label>";
                    ?>
                    <input type="submit" onlick="return alert();">
                </form>
            </div>
        </div>
        <?php
    }
    else
    {
    while($row = mysqli_fetch_array($result))
    {
        //display any other result sets
        echo "<tr>";
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
    }
    ?>
    </table>
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

