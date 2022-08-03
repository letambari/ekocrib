<?php include 'header.php'; ?>



<main id="content">
<section class="py-13">
<div class="container">
<div class="row login-register">
<div class="col-lg-5">
<div class="card border-0 shadow-xxs-2 mb-6">
<div class="card-body px-8 py-6">
<h4 class="card-title fs-30 font-weight-600 text-dark lh-16 mb-2">You're On the RIGHT PATH</h4>
<p class="mb-4">find amazing apartments</p>

<img src="media/register_image.jpg">


</div>
</div>
<div class="media rounded-lg bg-gray-01 p-6 mb-6 mb-lg-0">
<div class="mr-6 fs-60 lh-1 text-primary">
</div>

</div>
</div>
<div class="col-lg-7">
<div class="card border-0">
<div class="card-body px-6 pr-lg-0 pl-xl-13 py-6">
<h2 class="card-title fs-30 font-weight-600 text-dark lh-16 mb-2">Sign Up</h2>
<p class="mb-4">Already have an account? <a href="login" class="text-heading hover-primary"><u>login</u></a></p>
<form class="form">
<div class="form-row mx-n2">
<div class="col-sm-6 px-2">
<div class="form-group">
<label for="firstName" class="text-heading">First Name</label>
<input type="text" name="first-name" class="form-control form-control-lg border-0" id="firstName" placeholder="Destiny Innocent">
</div>
</div>
<div class="col-sm-6 px-2">
<div class="form-group">
<label for="lastName" class="text-heading">Phone Number</label>
<input type="text" name="phone" class="form-control form-control-lg border-0" id="phone" placeholder="08104456093">
</div>
</div>
</div>
<div class="form-row mx-n2">
<div class="col-sm-6 px-2">
<div class="form-group">
<label for="email" class="text-heading">Email</label>
<input type="text" class="form-control form-control-lg border-0" id="email" placeholder="destiny@example.com" name="email">
</div>
</div>
<div class="col-sm-6 px-2">
<div class="form-group">
<label for="user-role" class="text-heading">User Role
</label>
<select class="form-control border-0 shadow-none form-control-lg selectpicker" title="Select" data-style="btn-lg h-52" id="user-role" name="user-role">
<option value="2">Landlord</option>
<option value="3">Agent</option>
<option value="4">Occupant</option>
<option value="5">Applicant</option>
</select>
</div>
</div>
</div>
<div class="form-row mx-n2">
<div class="col-sm-6 px-2">
<div class="form-group">
<label for="password-1" class="text-heading">Password</label>
<div class="input-group input-group-lg">
<input type="text" class="form-control border-0 shadow-none" id="password-1" name="password" placeholder="Password">
<div class="input-group-append">
<span class="input-group-text bg-gray-01 border-0 text-body fs-18">
<i class="far fa-eye-slash"></i>
</span>
</div>
</div>
</div>
</div>
<div class="col-sm-6 px-2">
<div class="form-group">
<label for="re-password">Re-Enter Password</label>
<div class="input-group input-group-lg">
<input type="text" class="form-control border-0 shadow-none" id="re-password" name="re-password" placeholder="Password">
<div class="input-group-append">
<span class="input-group-text bg-gray-01 border-0 text-body fs-18">
<i class="far fa-eye-slash"></i>
</span>
</div>
</div>
</div>
</div>
</div>
 <button type="submit" class="btn btn-primary btn-lg btn-block rounded">Register</button>
</form>

<div id="result"></div>
<div class="divider text-center my-2">
<span class="px-4 bg-white lh-17 text text-heading">
or Sign Up with
</span>
</div>
<div class="row no-gutters mx-n2">
<div class="col-sm-6 px-2 mb-4">
<a href="#" class="btn btn-lg btn-block text-heading border px-0 rounded bg-hover-accent">
<img src="images/facebook.png" alt="Google" class="mr-2">
Facebook
</a>
</div>
<div class="col-sm-6 px-2 mb-4">
<a href="#" class="btn btn-lg btn-block text-heading border px-0 rounded bg-hover-accent">
<img src="images/google.png" alt="Google" class="mr-2">
Google
</a>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</section>
</main>

<!-- link to js processing -->

<script>
$(document).ready(function(){
    $("form").submit(function(event){
        event.preventDefault();
        var formValues = $(this).serialize();
        $thiss=$(this).find("[type=submit]");
	    $thiss.text("Wait...");
	    $thiss.addClass("disabled");
        
        $.post("code/register.php", formValues, function(data){
             if(data == "success"){
                  $thiss.text("Redirecting...");
                window.location.replace("login.php");
            } else{
                $thiss.text("Try Again");
                $thiss.removeClass("disabled");
                $("#result").html(data);
            }
        });
    });
});
</script>
<?php include 'footer.php'; ?>