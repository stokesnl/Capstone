<?php session_start(); ?>
<?php
    include ("connect.php");
    $hash = hash('sha256', $_POST['password1']);
    $mysqli = connect();
    $id = mt_rand(1, 100000);
    if(isset($_POST['password1']) && isset($_POST['password2']) && isset($_POST['FirstName']) && isset($_POST['LastName']) && isset($_POST['email']))
    {
        if($_POST['password1'] == $_POST['password2'])
        {
            $query = "insert into manager values ('$_POST[FirstName]', '$_POST[LastName]', '$_POST[email]',
                                                 '$id', '$hash', 9, '$id', '$id')";
            $mysqli->query($query);
            header("Location: index.php");
        }
    }
?>


<html class="no-js" lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Payroll</title>
    <link rel="stylesheet" href="css/app.css">
    <link rel="stylesheet" href="table.css">
  </head>
  <body>
    <div class="top-bar">
            <div>
                <h4><a href="index.php">Web payroll</a></h4>
            </div>
            <ul class ="menu">
                <li><a class="myanchor" href="about.php">About Us</a></li>
                <li><a class="myanchor" href="demonstration.php">Demonstration</a></li>
            </ul>
    </div>
    
    <div class="row" data-eqalizer>
        <div class="medium-6 large-6 large-centered columns" data-eqalizer-watch>
            <form action="" method="post" class="registerpad">
                    <h3 class="text-center">Registration</h3>
                    <label> Email *            
                    <input type="text" placeholder="email@email.com" name="email">
                    </label>
                    <?php
                        if($_POST['password1'] != $_POST['password2'])
                        {
                            echo '<p style="color:red;"> Passwords do not match </p>';
                            //echo $_post['password2'];
                        }
                    ?>
                    <label> Password
                    <input type="password" name="password1">
                    </label>
                    <label> Confirm Password
                    <input type="password" name="password2">
                    </label>
                    <label> First Name
                    <input type="text" name="FirstName">
                    </label>
                    <label> Last Name
                    <input type="text" name="LastName">
                    </label>
                    <button type="Submit" class="button expanded">Register</button>
            </form>
            <p>* Your email will be used to contact you in case of any issues with your account, and will also server as your login. </p>
        </div>
    </div>










    <div class="footer">
    <h5 align="center"> Contact Us </h5>
    <p align="center"> 999-999-999 <br> RandomEmail@aol.com <br>
    &copy; Appalachian State University. All rights reserved</p>
    </div>

    <script src="bower_components/jquery/dist/jquery.js"></script>
    <script src="bower_components/what-input/dist/what-input.js"></script>
    <script src="bower_components/foundation-sites/dist/js/foundation.js"></script>
    <script src="js/app.js"></script>
  </body>
</html>

