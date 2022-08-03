<?php
include 'connect.php';
session_start();
$firstname = htmlspecialchars($_POST['first-name']);
$phone = htmlspecialchars($_POST['phone']);
$email = htmlspecialchars($_POST['email']);
$password = htmlspecialchars($_POST['password']);
$confirmpassword = htmlspecialchars($_POST['re-password']);
$role = htmlspecialchars($_POST['user-role']);

$error = '';

//Validate User Input

if(empty($firstname) ||empty($phone) ||empty($email) || empty($password)|| empty($confirmpassword)|| empty($role))
{
    // error
    $error= '<div class="alert alert-danger mt-2">
                            <strong>All fields are required.</strong>
                                 </div>';
}



if(strlen($confirmpassword) < 6 || strlen($password) < 6)
{
    $error = '<div class="alert alert-danger mt-2">
                            <strong>Password is too short.</strong>
                                 </div>';
}

if($confirmpassword != $password)
{
    $error = '<div class="alert alert-danger mt-2">
                            <strong>Passowrds, does not match</strong>
                                 </div>';
}

if(empty($error))
{
//Check if user exists
    $stmt = $con->prepare("SELECT * FROM user_table WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows < 1)
    {
        //hash password
        // $time = time();
        // $password = password_hash($password, PASSWORD_DEFAULT);
        
        //Random email token
            $length = 5;    
            $token = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'),1,$length);
            
        // prepare and bind
        $stmt1 = $con->prepare("INSERT INTO user_table (fullname, email, phone, passwords, confirm_password, roles) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt1->bind_param("ssssss", $firstname, $email, $phone, $password,  $confirmpassword, $role);

        if ($stmt1->execute())
        { require_once 'welcome_mail.php';
                                        // $emails = 'Foreigncoin Trade <support@foreigncointrade.net>'; 
                                               
                                        //          $subject = 'Welcome';
                     
                                                //  $to = 'innocentdestiny228@gmail.com';  
                                                //  $from = 'support@foreigncointrade.net';
                                                //  $subject = 'Welcome';
                                                //  $headers = 'Welcome';
                                                //  $message = 'HHHHHHH';
                                                 
                                                //          $headers = "From: $from\n";
                                                //          $headers .= "MIME-Version: 1.0\n";
                                                //          $headers .= "Content-type: text/html; charset=iso-8859-1\n";
                                                //  mail($to, $subject, $message, $headers);
       
                                                echo 'success';

        }
        else
        {

            //echo $con->error; 
            '<div class="alert alert-danger mt-2">
                                                   <strong>Something went wrong, please contact support.</strong>
                                                </div>';
        }

    }
    else
    {
        echo '<div class="alert alert-danger mt-2">
               <strong>User info exists.</strong>
                     </div>';
    }
}else{
    echo "$error";
}
?>

