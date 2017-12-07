<?php session_start(); ?>
<?php
    function login()
    {
    include ("connect.php");
    $mysqli = connect();
    $hash = hash('sha256', $_POST['password']);
    $query = "SELECT * FROM manager where (managerEmail ='$_POST[username]' AND
                    managerPW ='$hash')";
    if(!$mysqli->query($query, $con))
    {
        header("Location: index.php");
        exit();
        //echo "1";
    }
    else
    {
        $result = $mysqli->query($query, $con);
        $value = mysqli_fetch_object($result);
        if(!mysqli_num_rows($result)==0)
        {
            $_SESSION['userid'] = $value->managerID;
            header("Location: employees.php");
            exit();
        }
        else
        {
            $_SESSION['error'] = "Invalid username or password";
        }
    }
    }

    if (isset($_POST['username']) && isset($_POST['password']))
    {
        login();
    }
?>
<script>
    function show()
    {
        document.getElementById("error").style.visibility = "visible";
    }
</script>


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
	<div class="row" data-equalizer>
		<div class="medium-6  large-6 small-centered columns" data-equalizer-watch>
			<form action="" method="post" class="loginpad">
					<h3 class="text-center">Log in</h3>
                        <?php
                        if ($_SESSION['error'])
                        {
                             //header("Location: wtf.html");
                             echo "{$_SESSION['error']}";
                             unset($_SESSION['error']);
                        }
                        ?>
                    <label>Email
					<input type="text" name = "username">
					</label>
	
					<label>Password
					<input type="password" name = "password">
					</label>
					<button type="Submit" class="button expanded">Login</button>
                    <p class="text-center"><a href="register.php">New? Make an account.</a></p>   
				</div>
			</form>
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
