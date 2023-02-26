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
$return_pid = $_SESSION['return_product_id'];
$return_count = $_SESSION['return_count'];

if (isset($_POST['return'])){
    header('location: welcome.php');
}
if (isset($_POST['profile'])){
    header('location: profile.php');
}

//check if there is enough quantity
$query1 = "SELECT quantity FROM buy WHERE pid = '$return_pid' and cid = '$id'";
$check_quantity = $mysqli->query($query1) or die('Error in query: ' . $mysqli->error);

while($row = $check_quantity->fetch_assoc()){
    if($row['quantity'] < $return_count){
        $msg = "NOT ENOUGH QUANTITY AS YOUR INPUT QUANTITY";
        echo sprintf("<h3 style='color:red;'>%s</h3>", $msg);
        break;
    }
    else if($row['quantity'] == $return_count){
        $query_delete = "DELETE FROM buy WHERE pid = '$return_pid' and cid = '$id'";
        $mysqli->query($query_delete);
    }else{
        $query_update = "UPDATE buy SET quantity = quantity - '$return_count' WHERE pid = '$return_pid' and cid = '$id'";
        $mysqli->query($query_update) or die('Error in query: ' . $mysqli->error);
    }
    //update the wallet
    $query_price = "SELECT price FROM product WHERE pid = '$return_pid'";
    $price = $mysqli->query($query_price) or die('Error in query: ' . $mysqli->error);
    while($rowp = $price->fetch_assoc()){
        $increase = $rowp['price'] * intval($return_count);
        $query_update_wallet = "UPDATE customer SET wallet = wallet + '$increase' WHERE cid = '$id'";
        if ($mysqli->query($query_update_wallet) === TRUE) {
            $msg = "PRODUCT(S) RETURNED SUCCESSFULLY";
            echo sprintf("<h3 style='color:green;'>%s</h3>", $msg);
            $query2 = "SELECT wallet FROM customer WHERE cid = '$id'";
            $updated_wallet = $mysqli->query($query2) or die('Error in query: ' . $mysqli->error);
            while($row_wallet = $updated_wallet->fetch_assoc()){
                echo sprintf("%s%s", " current wallet amount: ", $row_wallet['wallet']);
            }
        }
    }
    //update products, add returned items
    $query_check_product = "SELECT * FROM product WHERE pid = '$return_pid'";
    $product = $mysqli->query($query_check_product) or die('Error in query: ' . $mysqli->error);
    while($rowp = $product->fetch_assoc()){
        $query_update_stock = "UPDATE product SET stock = stock + '$return_count' WHERE pid = '$return_pid'";
        $mysqli->query($query_update_stock) or die('Error in query: ' . $mysqli->error);
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

