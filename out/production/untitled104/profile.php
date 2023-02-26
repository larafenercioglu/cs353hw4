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

$user = $_SESSION['login_user'];
$id = $_SESSION['user_id'];
$query = "SELECT p.pid as pid, p.pname as pname, b.quantity as quantity FROM product as p, buy as b WHERE p.pid = b.pid and b.cid='$id' and b.quantity > 0";
$boughts = $mysqli->query($query) or die('Error in query: ' . $mysqli->error);

if(isset($_POST['return'])){
    if($_POST['return_amount'] > 0){
        $_SESSION['return_product_id'] = $_POST['return'];
        $_SESSION['return_count'] = $_POST['return_amount'];
        header("location:return.php");
    }else{
        header("location:profile.php");
    }

}

if(isset($_POST['add'])){
//update wallet
    $input_amount = $_POST['add_amount'];
    if($input_amount > 0){
        $query_update_wallet = "UPDATE customer SET wallet = wallet + '$input_amount' WHERE cid = '$id'";
        $mysqli->query($query_update_wallet);
    }
    header("location:profile.php");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        body {
            background-color: #fc8c54;
        }

        #boughts {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        #boughts td, #boughts th{
            border: 1px solid #ddd;
            padding: 8px;
        }

        #boughts tr:nth-child(even){background-color: #f2f2f2;}
        #boughts tr:nth-child(odd){background-color: #f2f2f2;}


        #boughts tr:hover {background-color: #ddd;}

        #boughts th {
            padding-top: 10px;
            padding-bottom: 10px;
            text-align: left;
            background-color: #f45414;
            color: white;
        }

        .return_input {
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

        .wallet{
            font-family: Arial, Helvetica, sans-serif;
            font-size: 18px;
            padding-top: 12px;
            padding-bottom: 12px;
        }

        .amount_btn{
            margin: 5px;
        }

        .wallet_input{
            width: 250px;
        }

        .return_btn {
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
    <h1>Bought Products</h1>
    <table id="boughts">
        <tr>
            <th>Product ID</th>
            <th>Product Name</th>
            <th>Quantity</th>
            <th>Click Button to Return</th>
        </tr>
        <form method="post">
            <?php
            echo sprintf(" <form method='post'>
                                            <input type = 'input' name='add_amount' placeholder='add money' required style='margin: 5px'/>                            
                                            <button class='btn' type='submit' name='add' value=''> Deposit </button>
                                     </form>");
            $query2 = "SELECT wallet FROM customer WHERE cid = '$id'";
            $updated_wallet = $mysqli->query($query2) or die('Error in query: ' . $mysqli->error);
            while($row_wallet = $updated_wallet->fetch_assoc()){
                echo sprintf("<p class = 'wallet'>Current wallet amount: %s</p>", $row_wallet['wallet']);
            }
            if($boughts->num_rows > 0)
            {
                while($row = $boughts->fetch_assoc()){
                    $product_id = $row['pid'];
                    echo sprintf("<tr> <td>%s</td> <td>%s</td> <td>%s</td>
                                <form method='post'>
                                     <td> 
                                        <input type = 'input' name='return_amount' placeholder='please enter the amount' required/>                            
                                        <button class='btn' type='submit' name='return' value='$product_id'> Return </button>
                                    </td>
                                </form>
                                </tr>", $row['pid'], $row['pname'], $row['quantity']);
                }
            }
            ?>
        </form>
    </table>
</div>
</body>