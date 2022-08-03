<?php
include_once 'connect.php';

if($_SESSION['user_id'] != TRUE){
    header('Location: logout');
}else {


    $user_login = $_SESSION['user_id'];
    

    
    $get_user = "SELECT * FROM user_table WHERE id = '$user_login'";
    $query = mysqli_query($con, $get_user);
    while($row = mysqli_fetch_array($query)){
        $id = $row['id'];
        $fullname = $row['fullname'];
        $email = $row['email'];
        $roles = $row['roles'];
    }


    if($roles == 0){

    }elseif($roles == 1) {

    }elseif($roles == 2) {

    }elseif($roles == 3) {

    }elseif($roles == 4) {

    }elseif($roles == 5) {

    }






}
?>