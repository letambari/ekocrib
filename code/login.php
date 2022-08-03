<?php
include 'connect.php';
	session_start();
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);

    if (empty($email) || empty($password)) {
    	// if entered details is empty
    	echo '<div class="alert alert-danger">
                            <strong>All fields are required.</strong>
                                 </div>';
    } else {
        //Verify User Login Details
	$stmt = $con->prepare("SELECT * FROM user_table WHERE email = ? OR phone = ? AND passwords = ?");
    $stmt->bind_param("sss", $email, $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();
        
    		if ($result->num_rows < 1) {
                echo '<div class="alert alert-danger" style="margin-top:-20px;">
                            <strong>User does not exists.</strong>
                                 </div>';
            }else{
                	while ($user = $result->fetch_object()) {
                	    if($user->passwords == $password) {
                	        //Login User
                	    $_SESSION['user_id'] = $user->id;
                        echo 'success';	        
                	        
                	    }else{
                	        //decline access
                	        // echo '<div class="alert alert-danger" style="margin-top:-20px;">
                            // <strong>Incorrect Login.</strong>
                            //      </div>';
                            echo $con->error; 
                	    } 
                    	                        }
                    	}
                    	
                    	
    	
    }

?>