<?php
session_start();
require_once "config.php";
$msg = '';

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: welcome.php");
    exit;
}

function logIn($mysqli)
{
    $user = $_POST['username'];
    $pass = $_POST['password'];
    $query = "SELECT * FROM customer WHERE cname='$user' AND cid='$pass'";
    if($result = $mysqli->query($query)){
        if($result->num_rows==1) {
            $_SESSION['login_user'] = $user;
            $_SESSION['user_id'] = $pass;
            $_SESSION['loggedin'] = true;
            header("location: welcome.php");
        }
    }
    header('location: index.php?result=error');
}

if (isset($_POST['login'])){
    logIn($mysqli);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        body {
            background-color: #fc8c54;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <h2>Login</h2>
    <?php if(isset($_GET['result'])){
        $msg = "Invalid credentials";
        echo sprintf("<h3 style='color:red;'>%s</h3>", $msg);
    } else { echo "<p>Fill in to login.</p>";} ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="ID" required>
        <button type="submit" name="login">Login</button>
    </form>
</div>
</body>