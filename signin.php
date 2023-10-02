<?php
// include("inc/connect.inc.php");
$con = mysqli_connect("localhost","root","", "e-commerce") or die("Couldn't connect to SQL server");
session_start();

if (isset($_SESSION['user_login'])) {
    header("location: index.php");
    exit();
}

if (isset($_POST['signup'])) {
    // Retrieve user input and trim whitespace
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $mobile = trim($_POST['mobile']);
    $address = trim($_POST['signupaddress']);
    $password = trim($_POST['password']);

    try {
        // Validate user input
        if (empty($first_name) || empty($last_name) || empty($email) || empty($mobile) || empty($address) || empty($password)) {
            throw new Exception('All fields are required');
        }
        if (!ctype_alpha($first_name[0]) || !ctype_alpha($last_name[0])) {
            throw new Exception('First and last name must start with a letter');
        }
        if (strlen($first_name) < 2 || strlen($first_name) > 20 || strlen($last_name) < 2 || strlen($last_name) > 20) {
            throw new Exception('First and last name must be 2-20 characters long');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format');
        }
        if (!is_numeric($mobile)) {
            throw new Exception('Mobile must be a number');
        }
        if (strlen($password) < 2) {
            throw new Exception('Password is too weak');
        }

        // Check if email already exists
        $email_check_query = "SELECT email FROM `users` WHERE email=?";
        $stmt = $con->prepare($email_check_query);
        if (!$stmt) {
            throw new Exception('Database error: ' . $con->error);
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            throw new Exception('Email already taken');
        }

        // Hash the password using bcrypt
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Insert user into the database using prepared statement
        $insert_query = "INSERT INTO users (first_name, last_name, email, mobile, address, password) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $con->prepare($insert_query);
        if (!$stmt) {
            throw new Exception('Database error: ' . $con->error);
        }
        $stmt->bind_param("ssssss", $first_name, $last_name, $email, $mobile, $address, $hashed_password);
        
        if ($stmt->execute()) {
            $success_message = '<div class="signupform_content"><h2><font face="bookman">Registration successful!</font></h2>
                <div class="signupform_text" style="font-size: 18px; text-align: center;">
                <font face="bookman">Email: ' . $email . '<br>Registration successful!</font></div></div>';
        } else {
            throw new Exception('Error inserting user: ' . $stmt->error);
        }
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Welcome to ebuybd online shop</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body class="home-welcome-text" style="background-image: url(image/homebackgrndimg2.png);">
    <div class="homepageheader" style="position: inherit;">
	<div class="signinButton loginButton">
				<div class="uiloginbutton signinButton loginButton" style="margin-right: 40px;">
					<a style="text-decoration: none;" href="signin.php">SIGN UP</a>
				</div>
				<div class="uiloginbutton signinButton loginButton" style="">
					<a style="text-decoration: none;" href="login.php">LOG IN</a>
				</div>
			</div>
			<div style="float: left; margin: 5px 0px 0px 23px;">
				<a href="index.php">
					<img style=" height: 75px; width: 130px;" src="image/cart.png">
				</a>
			</div>
			<div class="">
				<div id="srcheader">
					<form id="newsearch" method="get" action="http://www.google.com">
					        <input type="text" class="srctextinput" name="q" size="21" maxlength="120"  placeholder="Search Here..."><input type="submit" value="search" class="srcbutton" >
					</form>
				<div class="srcclear"></div>
				</div>
			</div>
    </div>
    <?php
    if (isset($success_message)) {
        echo $success_message;
    } else {
        echo '
        <div class="holecontainer" style="float: right; margin-right: 36%; padding-top: 26px;">
            <div class="container">
                <div>
                    <div>
                        <div class="signupform_content">
                            <h2>Sign Up Form!</h2>
                            <div class="signupform_text"></div>
                            <div>
                                <form action="" method="POST" class="registration" name="signup">
                                    <div class="signup_form">
									<div>
														<td >
															<input name="first_name" id="first_name" placeholder="First Name" required="required" class="first_name signupbox" type="text" size="30"  >
														</td>
													</div>
													<div>
														<td >
															<input name="last_name" id="last_name" placeholder="Last Name" required="required" class="last_name signupbox" type="text" size="30" >
														</td>
													</div>
													<div>
														<td>
															<input name="email" placeholder="Enter Your Email" required="required" class="email signupbox" type="email" size="30" >
														</td
			>										</div>
													<div>
														<td>
															<input name="mobile" placeholder="Enter Your Mobile" required="required" class="email signupbox" type="text" size="30" >
														</td>
													</div>
													<div>
														<td>
															<input name="signupaddress" placeholder="Write Your Full Address" required="required" class="email signupbox" type="text" size="30" >
														</td>
													</div>
													<div>
														<td>
															<input name="password" id="password-1" required="required"  placeholder="Enter New Password" class="password signupbox " type="password" size="30">
														</td>
													</div>
													<div>
														<input name="signup" class="uisignupbutton signupbutton" type="submit" value="Sign Me Up!">
													</div>
													<div class="signup_error_msg">';
														
															if (isset($error_message)) {echo $error_message;}
															
														
													echo'</div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    	';
			}

		 ?>
</body>
</html>
