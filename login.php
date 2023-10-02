<?php
// include("inc/connect.inc.php");
$con = mysqli_connect("localhost", "root", "", "e-commerce") or die("Couldn't connect to SQL server");
session_start();

if (isset($_SESSION['user_login'])) {
    header("location: index.php");
    exit();
}
if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    try {
        // Validate user input
        if (empty($email) || empty($password)) {
            throw new Exception('Both email and password are required');
        }

        // Check if email exists in the database
        $email_check_query = "SELECT * FROM users WHERE email=?";
        $stmt = $con->prepare($email_check_query);
        if (!$stmt) {
            throw new Exception('Database error: ' . $con->error);
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            throw new Exception('Email not found');
        }

        $user = $result->fetch_assoc();
        $hashed_password = $user['password'];

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_login'] = $user['id'];
            header("location: index.php");
            exit();
        } else {
            throw new Exception('Invalid password');
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
                <input type="text" class="srctextinput" name="q" size="21" maxlength="120" placeholder="Search Here...">
                <input type="submit" value="search" class="srcbutton">
            </form>
            <div class="srcclear"></div>
        </div>
    </div>
</div>
<?php
if (isset($error_message)) {
    echo '<div class="error_message">' . $error_message . '</div>';
}
?>
<div class="holecontainer" style="float: right; margin-right: 36%; padding-top: 26px;">
    <div class="container">
        <div>
            <div>
                <div class="signupform_content">
                    <h2>Login</h2>
                    <div class="signupform_text"></div>
                    <div>
                        <form action="" method="POST" class="registration" name="login">
                            <div class="signup_form">
                                <div>
                                    <input name="email" placeholder="Enter Your Email" required="required" class="email signupbox" type="email" size="30">
                                </div>
                                <div>
                                    <input name="password" id="password-1" required="required" placeholder="Enter Password" class="password signupbox" type="password" size="30">
                                </div>
                                <div>
                                    <input name="login" class="uisignupbutton signupbutton" type="submit" value="Log In">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
