<?php
session_start();
require_once "config.php";
if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === FALSE){
    header("location: index.php");
} else if(!isset($_SESSION['loggedin'])){
    header("location: index.php");
}
if (isset($_POST['return'])){
    header('location: welcome.php');
}
if (isset($_POST['profile'])){
    header('location: profile.php');
}

if(isset($_POST['buy'])){
    if($_POST['buy_amount'] > 0){
        $_SESSION['product_id'] = $_POST['buy'];
        $_SESSION['product_count'] = $_POST['buy_amount'];
        header("location:buy.php");
    }
    else{
        header("location:welcome.php");
    }
}

$user = $_SESSION['login_user'];
$id = $_SESSION['user_id'];
$query = "SELECT pid, pname, price, stock FROM product WHERE stock > 0";
$products = $mysqli->query($query) or die('Error in query: ' . $mysqli->error);;


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        #products {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        #products td, #products th{
            border: 1px solid #ddd;
            padding: 8px;
        }

        #products tr:nth-child(even){background-color: #f2f2f2;}
        #products tr:nth-child(odd){background-color: #f2f2f2;}


        #products tr:hover {background-color: #ddd;}

        #products th {
            padding-top: 10px;
            padding-bottom: 10px;
            text-align: left;
            background-color: #f45414;
            color: white;
        }

        .buy_input {
            margin-left: 500px;
            margin-top: 700px;
            padding: 5px;
            margin:0 auto;
            display: block;
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

        .buy_btn {
            margin: 5px;
        }

        .wallet{
            font-family: Arial, Helvetica, sans-serif;
            font-size: 18px;
            padding-top: 12px;
            padding-bottom: 12px;
        }
        body {
            background-color: #fc8c54;
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
    <h1>Available Products</h1>
    <table id="products">
        <tr>
            <th>Product ID</th>
            <th>Product Name</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Click The Button to BUY</th>
        </tr>
        <form method="post">
            <?php
            $query2 = "SELECT wallet FROM customer WHERE cid = '$id'";
            $updated_wallet = $mysqli->query($query2) or die('Error in query: ' . $mysqli->error);
            while($row_wallet = $updated_wallet->fetch_assoc()){
                echo sprintf("<p class = 'wallet'>Current wallet amount: %s</p>", $row_wallet['wallet']);
            }
            if($products->num_rows > 0){
                while($row = $products->fetch_assoc()){
                    $product_id = $row['pid'];
                    echo sprintf("<tr> <td>%s</td> <td>%s</td> <td>%s</td> <td>%s</td>
                                <form method='post'>
                                     <td> 
                                        <input type = 'input' name='buy_amount' placeholder='please enter the amount' required/>                            
                                        <button class='btn' type='submit' name='buy' value='$product_id'> Buy </button>
                                    </td>
                                </form>
                                </tr>", $row['pid'], $row['pname'], $row['price'], $row['stock']);
                }
            }
            ?>
        </form>
    </table>
</div>
</body>