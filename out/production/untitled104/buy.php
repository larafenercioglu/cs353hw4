<?php
session_start();
require_once "config.php";
$msg = '';

if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === FALSE){
    header("location: index.php");
} else if(!isset($_SESSION['loggedin'])){
    header("location: index.php");
}
$user = $_SESSION['login_user'];
$id = $_SESSION['user_id'];
$pid = $_SESSION['product_id'];
$pcount = $_SESSION['product_count'];

if (isset($_POST['return'])){
    header('location: welcome.php');
}
if (isset($_POST['profile'])){
    header('location: profile.php');
}

$query1 = "SELECT price FROM product WHERE pid = '$pid'";
$price = $mysqli->query($query1) or die('Error in query: ' . $mysqli->error);

$query2 = "SELECT wallet FROM customer WHERE cid = '$id'";
$wallet = $mysqli->query($query2) or die('Error in query: ' . $mysqli->error);

while($rowp = $price->fetch_assoc() and $roww = $wallet->fetch_assoc() ){
    $pr = $rowp['price'] ;
    $wa = $roww['wallet'];
    if($pr > $wa){
        $msg = "NOT ENOUGH MONEY";
        echo sprintf("<h3 style='color:red;'>%s</h3>", $msg);
    }
    else{
        $query_check = "SELECT stock FROM product WHERE pid = '$pid'";
        $query_checkk = $mysqli->query($query_check) or die('Error in query: ' . $mysqli->error);

        if($query_checkk->num_rows > 0){
            while($row = $query_checkk->fetch_assoc()){
                if($pcount > $row['stock']){
                    $msg = "NOT ENOUGH QUANTITY";
                    echo sprintf("<h3 style='color:red;'>%s</h3>", $msg);
                }else{//there is enough quantity
                    $query_check_presence = "SELECT pid FROM buy WHERE cid = '$id' and pid = '$pid'";
                    $query_check_presence_ = $mysqli->query($query_check_presence) or die('Error in query: ' . $mysqli->error);

                    //if the product is not bought for the first time
                    if($query_check_presence_->num_rows > 0){
                        $query = "UPDATE buy SET quantity = quantity + '$pcount' WHERE cid = '$id' and pid = '$pid'";
                        if ($mysqli->query($query) === TRUE) {
                            echo sprintf("<h3 style='color:green;'>%s</h3>","New product bought successfully\n");
                            $decrease = $pr * intval($pcount);
                            $update_wallet = "UPDATE customer SET wallet = wallet - '$decrease' WHERE cid = '$id'";
                            if ($mysqli->query($update_wallet) === TRUE) {
                                $updated_wallet = $mysqli->query($query2) or die('Error in query: ' . $mysqli->error);
                                while($row_wallet = $updated_wallet->fetch_assoc()){
                                    echo sprintf("%s%s", " remaining wallet amount: ", $row_wallet['wallet']);
                                }
                            }
                        }
                    }else{
                        //else
                        $query = "INSERT INTO buy VALUES ('$id','$pid','$pcount')";
                        if ($mysqli->query($query) === TRUE) {
                            echo sprintf("<h3 style='color:green;'>%s</h3>","New product bought successfully\n");
                            $decrease = $pr * intval($pcount);
                            $update_wallet = "UPDATE customer SET wallet = wallet - '$decrease' WHERE cid = '$id'";
                            if ($mysqli->query($update_wallet) === TRUE) {
                                $updated_wallet = $mysqli->query($query2) or die('Error in query: ' . $mysqli->error);
                                while($row_wallet = $updated_wallet->fetch_assoc()){
                                    echo sprintf("%s%s", " remaining wallet amount: ", $row_wallet['wallet']);
                                }
                            }
                        }
                    }
                    //decrease from the stock
                    $update_stock = "UPDATE product SET stock = stock - '$pcount' WHERE pid = '$pid'";
                    $mysqli->query($update_stock);
                }

            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        body {
            background-color: #fc8c54;
        }
        #profile_btn {
            margin: 5px;
        }

        #logout {
            margin: 5px;
        }

        #return_btn {
            margin: 5px;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <form action='logout.php' method='post'>
        <button id='logout' type='submit' name='logout'>Logout</button>
    </form>
    <form action='welcome.php' method='post'>
        <button id='return_btn' type='submit' name='return'>Return to welcome page</button>
    </form>
    <form action='profile.php' method='post'>
        <button id='profile_btn' type='submit' name='profile'>Go to profile page</button>
    </form>
</div>
</body>

