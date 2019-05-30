<?php

include_once("config.php"); //include the config

$mypagetype='nosb';

$boxstyles = array(
    'ad' => "box-style7",
    'home' => "box-style1",
    'list' => "box-style1",
    'nosb' => "box-style4",
    'rightsb' =>"box-style5",
    'leftsb'=>"box-style6");

include "top.php";

if($_GET["key"] && $_GET["userID"]){
    $key = $_GET["key"];
    $userID = $_GET["userID"];
    $q = mysql_query("SELECT * FROM $table WHERE userID='$userID' and code='$key'");
    $r = mysql_fetch_assoc($q);
    $n = mysql_num_rows($q);
    
    if($n){
        if($_POST["new_email"]){
            $validemail = eregi("^[a-z0-9_-]+(\.[a-z0-9_-]+)*@([a-z0-9_-]+\.)*[a-z0-9_-]+\.[a-z]{2,}$", $_POST["new_email"]);
            if($validemail){
                $alpha = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                $length = 11;
                    
                for($i=0; $i<$length; $i++){
                    $ran = rand(0, strlen($alpha)-1);
                    $new_key .= substr($alpha, $ran, 1);
                }
                
                $userID = $r["userID"];
                mysql_query("UPDATE $table SET email='".$_POST["new_email"]."', code='$new_key', activated='false' WHERE userID='$userID'");
                
                //SENDS EMAIL THAT TELLS THE USER TO ACTIVATE THE ACCOUNT
                        
                $activation = "activation.php?key=".$new_key;
                
                $your_email = 'you'; //CHANGE TO YOUR SETTINGS
                $domain = $_SERVER["HTTP_HOST"]; //YOUR DOMAIN AND EXTENSION
                $directory = dirname($_SERVER["PHP_SELF"]); //FOLDER WHERE THE FILES WILL BE LOCATED
                
                $to = $_POST["new_email"];
                $subject = "Activate Account";
                $message = "Welcome, ".$r["username"].". You must activate your account via this message to log in. Click the following link to do so: http://".$domain.$directory."/".$activation;
                $headers = "From: Your Site <".$your_email."@".$domain.">\r\n"; //MODIFY TO YOUR SETTINGS
                $headers .= "Content-type: text/html\r\n";
                mail($to, $subject, $message, $headers);
                
                echo 'Check <b>'.$_POST["new_email"].'</b> to activate your account.';
            } else {
                echo 'Invalid email address';
            }
        } else {
            if($r["activated"]=="true"){
                echo 'Account already activated. <a href="home.php">Go to the Home Page</a>';
            } else {
                echo 'Is this your email address: <b>'.$r["email"].'</b>? If so, <a href="home.php?email='.$r["email"].'&key='.$key.'">click here to login.</a>
                <form action="'.$_SERVER['PHP_SELF'].'?key='.$key.'" method="post">
                <table>
                <tr><td align="right">If not, enter it here: </td><td align="left"><input name="new_email"></td></tr>
                <tr><td align="right"></td><td align="left"><input type="submit" value="Submit"></td></tr>
                </table>
                </form>';
            }
        }
    } else {
        echo 'Invalid Key';
    }
} else {
    header("Location: /profile.php");
}

include "bottom.php";

?>

