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
    alert("Please ensure all values are entered and appropriate\n Data not entered");
}
function showForm()
{
    document.getElementById("EmpForm").style.display = "block";
}
function hideForm()
{
    document.getElementById("EmpForm").style.display = "none";
}
function showJobForm()
{
    document.getElementById("JobForm").style.display = "block";
}
function hideJobForm()
{
    document.getElementById("JobForm").style.display = "none";
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
    date_default_timezone_set("America/New_York");

    //verify that the data is of the correct format
    function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
     include("connect.php");
     $mysqli = connect();
    
    //Remove an employee from the database
    if(isset($_POST['remove']))
    {
        //mysql_query("delete from employee where ID = '$_POST[ID]'");
        $empstmt = $mysqli->prepare("delete from employee where ID = ?");
        $empstmt->bind_param("i", $_POST['ID']);
        $empstmt->execute();
        $_POST['memployees'] = 'x';
    }

     //Update employees information
     else if(isset($_POST['fname']) and isset($_POST['lname']) and isset($_POST['payrate']) and isset($_POST['role']))
     {
         if($_POST['fname'] == "" || $_POST['lname'] == "" || $_POST['payrate'] == "" || $_POST['payrate'] < 0 || $_POST['role'] == "")
         {
             echo "<script> badval(); </script>";
         }
         else
         {
         $mempstmt = $mysqli->prepare("update employee set fName = ?, lName = ?, payRate = ?, role = ? where ID = ?");
         $mempstmt->bind_param("ssdsi", $_POST['fname'], $_POST['lname'], $_POST['payrate'], $_POST['role'], $_POST['ID']);
         $mempstmt->execute();
         /*mysql_query("update employee
                    set fName = '$_POST[fname]',
                    lName = '$_POST[lname]',
                    payRate = '$_POST[payrate]',
                    role = '$_POST[role]'
                    where ID = '$_POST[ID]'");*/
         }
         $_POST['memployees'] = 'x';

     }

     //update an emp_job (hours) entry
     else if(isset($_POST['hrsworked']) and isset($_POST['Job']))
     {
         if($_POST['hrsworked'] < 0 || !validateDate($_POST['date']))
         {
             echo "<script> badval(); </script>";
         }
        else
        {
         /*mysql_query("update emp_job
                    set hrsWorked = '$_POST[hrsworked]',
                    workDate = '$_POST[date]',
                    jobID = '$_POST[Job]'
                    where ID = '$_POST[ID]'
                    and workDate = '$_POST[Prevdate]'");*/
            $hrsstmt = $mysqli->prepare("update emp_job
                    set hrsWorked = ?,
                    workDate = ?,
                    jobID = ?
                    where ID = ?
                    and workDate = ?");
            $hrsstmt->bind_param("isiis", $_POST['hrsworked'], $_POST['date'], $_POST['Job'], $_POST['ID'], $_POST['Prevdate']);
            $hrsstmt->execute();
        }
         $_POST['mhrs'] = 'x';
     }


     //update a job entry
     else if(isset($_POST['jlocation']) and isset($_POST['jdescription']) and isset($_POST['jstatus'])
                and isset($_POST['jscale']))
     {
         if($_POST['jlocation'] == "" || $_POST['jdescription'] == "" || $_POST['jstatus'] == "" || $_POST['jscale'] == "" || $_POST['jscale'] < 0 || $_POST['jscale'] > 9)
         {
             echo "<script> badval(); </script>";
         }
         else
         {
         /*mysql_query("update job
                    set location = '$_POST[jlocation]',
                    description = '$_POST[jdescription]',
                    status = '$_POST[jstatus]',
                    scale = '$_POST[jscale]'
                    where jobID = '$_POST[ID]'");*/
            $jobstmt = $mysqli->prepare("update job set location = ?,
                        description = ?,
                        status = ?,
                        scale = ?
                        where jobID = ?");
            $jobstmt->bind_param("sssii", $_POST['jlocation'], $_POST['jdescription'], $_POST['jstatus'], $_POST['jscale'], $_POST['ID']);
            $jobstmt->execute();
         }
         $_POST['mjobs'] = 'x';
     }

     //create a new employee
     else if(isset($_POST['firstName']) and isset($_POST['lastName']) and isset($_POST['PayRate']) and isset($_POST['Role']))
     {
         $id = mt_rand(1, 1000000000);
         if($_POST['firstName'] == "" || $_POST['lastName'] == "" || $_POST['PayRate'] < 0 || $_POST['Role'] == "")
         {
             echo "<script> badval(); </script>";
         }
         else
         {
         /*mysql_query("insert into employee
                    values('$_POST[firstName]', '$_POST[lastName]', '$_POST[PayRate]', '$id', '{$_SESSION['userid']}', '$_POST[Role]')");*/
            $addempstmt = $mysqli->prepare("insert into employee values (?, ?, ?, ?, ?, ?)");
            $addempstmt->bind_param("ssdiis", $_POST['firstName'], $_POST['lastName'], $_POST['PayRate'], $id, $_SESSION['userid'], $_POST['Role']);
            $addempstmt->execute();
         }
         //$_POST['addemp'] = 'x';
     }

     //create a new job
     else if(isset($_POST['location']) AND isset($_POST['description']) AND isset($_POST['scale']) AND isset($_POST['status']) AND isset($_POST['ceo']) AND isset($_POST['jobid']))
     {
         if($_POST['location'] == "" || $_POST['description'] == "" || $_POST['scale'] == "" || $_POST['scale'] < 0 || $_POST['scale'] > 9)
         {
             echo "<script> badval(); </script>";
         }
         else
         {
         //mysql_query("insert into job values('$_POST[location]', '$_POST[description]', '$_POST[status]', '$_POST[jobid]', '$_POST[scale]', '$_POST[ceo]')");
         $addjobstmt = $mysqli->prepare("insert into job values(?, ?, ?, ?, ?, ?)");
         $addjobstmt->bind_param("sssiii", $_POST['location'], $_POST['description'], $_POST['status'], $_POST['jobid'], $_POST['scale'], $_POST['ceo']);
         $addjobstmt->execute();
         }
         $_POST['mjobs'] = 'x';
     }

     //Edit job information table
     if(isset($_POST['mjobs']))
     {
         /*$result = mysql_query("select j.location, j.description, j.status, j.jobID, j.scale
            from job j
            join manager m on m.ceoID = '{$_SESSION['userid']}'
            and m.ceoID = j.ceoID
            group by j.jobID
            order by j.location");*/
         $jobsstmt = $mysqli->prepare("select j.location, j.description, j.status, j.jobID, j.scale
            from job j
            join manager m on m.ceoID = ?
            and m.ceoID = j.ceoID
            group by j.jobID
            order by j.location");
         $jobsstmt->bind_param("i", $_SESSION['userid']);
         $jobsstmt->execute();
         $result = $jobsstmt->get_result();
         $printed = 5;


     }
     //edit hours information table
     else if(isset($_POST['mhrs']))
     {
         /*$result = $mysqli->query("select e.fName, e.lName, e.payRate, ej.hrsWorked, j.jobID, ej.workDate, e.ID, j.location, j.description
                    from employee e
                    join emp_job ej on e.ID = ej.ID
                    join job j on ej.jobID = j.jobID
                    where e.managerID = '{$_SESSION['userid']}'
                    AND ej.paid != 'Y'
                    ORDER BY e.lName");*/
         $hrsstmt = $mysqli->prepare("select e.fName, e.lName, e.payRate, ej.hrsWorked, j.jobID, ej.workDate, e.ID, j.location, j.description
                                         from employee e
                                         join emp_job ej on e.ID = ej.ID
                                         join job j on ej.jobID = j.jobID
                                         where e.managerID = ?
                                         AND ej.paid != 'Y'
                                         ORDER BY e.lName");
         $hrsstmt->bind_param("i", $_SESSION['userid']);
         $hrsstmt->execute();
         $result = $hrsstmt->get_result();
         $printed = 5;
     }

     //edit employees information
     else
     {
         $empstmt = $mysqli->prepare("select * from employee where managerID = ? order by lName");
         $empstmt->bind_param("i", $_SESSION['userid']);
         $empstmt->execute();
         $result = $empstmt->get_result();
         /*$result = mysql_query("select *
             from employee
             where managerID = '{$_SESSION['userid']}'
             order by lName");*/
         $printed = 4;
         $_POST['memployees'] = 'x';

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
    <input type="hidden" name="memployees">
    <button class="tablinks" formmethod="post">Edit Employees</button>
    </form>
    <form class="tabs" action="" method="post">
    <input type="hidden" name="mjobs">
    <button class="tablinks" formmethod="post">Edit Jobs</button>
    </form>
    <form class="tabs" action="" method="post">
    <input type="hidden" name="mhrs">
    <button class="tablinks" formmethod="post">Edit Hours</button>
    </form>
    </div>
    <div class="tablecontainer">
    <div class="row" data-equalizer>
        <div class="medium-12 large-12 columns" data-eqalizer-watch>

  <?php
        //form to add an employee
        echo "<table>";
        if(isset($_POST['memployees']) AND !isset($_POST['addemp']) AND !isset($_POST['addjobs']) AND !isset($_POST['addhours']))
        {
            ?>
            <button class="centeredbut" onclick="showForm()">Add Employee</button>
            <div id="EmpForm">
            <form class = "" action = "" method="post">
                    <label>First Name
                    <input type="text" name="firstName" placeholder="John">
                    </label>

                    <label>Last Name
                    <input type="text" name="lastName" placeholder="Doe">
                    </label>

                    <label>Pay Rate
                    <input type="text" name="PayRate" placeholder="10.00">
                    </label>

                    <label>Role
                    <input type="text" name="Role" placeholder="Crewman"
                    </label>
                    <div>
                    <button class="submitbut" type="submit">Submit</button>
                    <button class="submitbut" onclick="hideForm()" type="button">Cancel</button>
                    </div>
            </form>
            </div>
    
            <?php
            //Display the appropriate table headers for tyhe displays
            echo "<th>First Name</th> <th>Last Name</th> <th>Pay Rate</th> <th>Role</th> <th>Submit Changes</th> <th>Remove Employee</th>";
        }
        else if(isset($_POST['mhrs']))
        {
            echo"<th>First Name</th> <th>Last Name</th> <th>Pay rate</th> <th style='width:100px'>Hours</th></style> <th>Job Number</th> <th>Date</th> <th>Submit</th>";
        }
        else if(isset($_POST['mjobs']))
        {
            //form to add a job
            ?>

            <button class="centeredbut" onclick="showJobForm()">Add Job</button>
            <div id="JobForm">
                <form class="" action="" method="post">
                    <h3 class=text-center">Job</h3>
                    <label>Location
                    <input type="text" name="location" placeholder="Some street, Raleigh NC">
                    </label>


                    <label>Description
                    <input type="text" name="description" placeholder="Home Roofing">
                    </label>


                    <label>Scale
                    <input type="text" name="scale" placeholder="0-9">
                    </label>
                    <?php
                    $ceo = $mysqli->query("select ceoID from manager where managerID = '{$_SESSION['userid']}'");
                    $ceoID = mysqli_fetch_array($ceo);
                    $max = $mysqli->query("select max(jobID) from job j where 1=1");
                    $maxR = mysqli_fetch_array($max);
                    $maxID = $maxR[0] + 1;
                    echo "<input type='hidden' name='status' value='U'>";
                    echo "<input type='hidden' name='ceo' value='$ceoID[0]'>";
                    echo "<input type='hidden' name='jobid' value='$maxID'>";
                    ?>
                    <div>
                    <button class="submitbut" type="submit">Submit</button>
                    <button class="submitbut" type="button" onclick="hideJobForm()">Cancel</button>
                    </div>
                </form>
            </div>
            <?php
            echo"<th>Location</th> <th>Description</th> <th style='width:100px'>Status</th> <th style='width:100px'>Job ID</th></style> <th style='width:100px'>Scale</th> <th>Submit</th>";
        }
    

    if(isset($_POST['memployees']) AND !isset($_POST['addemp']) AND !isset($_POST['addjobs']) AND !isset($_POST['addhours']))
    {
        while($row = mysqli_fetch_array($result))
        {
            //display employee information
           ?>
           <form class="" action="" method ="post">
           <tr>
           <?php
                echo "<td><input type='text' name='fname' value=" .$row[0]."></td>";
                echo "<td><input type='text' name='lname' value=" .$row[1]."></td>";
                echo "<td><input type='text' name='payrate' value=" .$row[2]."></td>";
                echo "<td><input type='text' name='role' value=" .$row[5]."></td>";
                echo "<td><input type='submit' name='update' value ='update'/></td>";
                echo "<td><input id='remove' type='submit' name='remove' value = 'Remove'/></td>";
                echo "<td style='display:none'><input type='hidden' name='ID' value=" .$row[3]."></td>";
                echo "</form>";

        }
    }

    else if(isset($_POST['mjobs']))
    {
        while($row = mysqli_fetch_array($result))
        {
            //display job information
           ?>
           <form class="" action="" method ="post">
           <tr>
           <?php
                echo "<td><input type='text' name='jlocation' value='$row[0]'></td>";
                echo "<td><input type='text' name='jdescription' value='$row[1]'></td>";
                echo "<td><input type='text' name='jstatus' value='$row[2]'></td>";
                echo "<td>$row[3]</td>";
                echo "</select></td>";
                echo "<td><input type='text' name='jscale' value='$row[4]'></td>";
                echo "<td><input type='submit' name='update'/></td>";
                echo "<td style='display:none'><input type='hidden' name='ID' value=" .$row[3]."></td>";
                echo "</form>";

        }
    }

    else if(isset($_POST['mhrs']))
    {
        while($row = mysqli_fetch_array($result))
        {
            //display hours information
           ?>
           <form class="" action="" method ="post">
           <tr>
           <?php
                echo "<td>$row[0]</td>";
                echo "<td>$row[1]</td>";
                echo "<td>$row[2]</td>";
                echo "<td><input type='text' name='hrsworked' value=".$row[3]." style='width:100px'></td>";
                $jobs = $mysqli->query("select jobID, location, description from job where ceoID IN
                    (Select ceoID from manager where managerID = '{$_SESSION['userid']}')");
                echo "<td><select name ='Job'>";
                echo "<option value='$row[4]'>$row[7]::$row[8]</option>";
                while($x = mysql_fetch_array($jobs))
                {
                    if($x[0] != $row[4])
                    {
                        echo "<option value= '$x[0]'>$x[1]::$x[2]</option>";
                    }
                }
                echo "</select></td>";
                echo "<td><input type='text' name='date' value='$row[5]'></td>";
                echo "<td><input type='submit' name='update'/></td>";
                echo "<td style='display:none'><input type='hidden' name='ID' value='$row[6]'></td>";
                echo "<td style='display:none'><input type='hidden' name='Prevdate' value='$row[5]'></td>";
                echo "</form>";
        }
    }
    ?>
    </table>
    </div></div></div>

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

