<?php 
    included('partials.head', ["title" => "Register"]);
    included('partials.nav') 
?>


<div class="container-fluid mt-3 row card p-3 bg-light">
<h3 class="text-center text-danger"><b>User Register</b></h3>
    <form id="ajaxForm" action="<?= route("register") ?>" method="POST" class="col-md-4 offset-md-4" enctype="multipart/form-data">
    <div class="mb-3 mt-3">
        <strong class="text-danger"><?= $error ?? '' ?></strong><br>
        <label for="name" class="form-label">Name:</label>
        <input type="name" class="form-control" id="name" placeholder="Enter name" name="name" value="<?= old('name') ?>">
            <p class="text-danger"><?= $errors['name'] ?? '' ?></p>
    </div>    
    <div class="mb-3 mt-3">
        <label for="email" class="form-label">Email:</label>
        <input type="email" class="form-control" id="email" placeholder="Enter email" name="email" value="<?= old('email') ?>">
            <p class="text-danger"><?= $errors['email'] ?? '' ?></p>
    </div>
    <div class="mb-3 mt-3">
        <label for="phone" class="form-label">Phone:</label>
        <input type="text" class="form-control" id="phone" placeholder="Enter phone" name="phone" value="<?= old('phone') ?>">
            <p class="text-danger"><?= $errors['phone'] ?? '' ?></p>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password:</label>
        <input type="password" class="form-control" id="password" placeholder="Enter password" name="password" value="<?= old('password') ?>">
        <p class="text-danger"><?= $errors['password'] ?? '' ?></p>
    </div>
    <div class="mb-3">
        <label for="cpassword" class="form-label">Confirm Password:</label>
        <input type="password" class="form-control" id="cpassword" placeholder="Re-type password" name="password_confirmation" value="<?= old('password_confirmation') ?>">
        <p class="text-danger"><?= $errors['password_confirmation'] ?? '' ?></p>
    </div>
    <div class="mb-3">
        <label for="avatar" class="form-label">Profile Photo:</label>
        <input type="file" class="form-control" id="avatar" name="avatar">
        <p class="text-danger"><?= $_SESSION['errors']['avatar'] ?? '' ?></p>
        <div class="preview-img mt-2">
              <img src="<?= getFilePath('users', '')?>" class="h-50 w-50"/>
        </div>
    </div>
    
    <div class="form-check mb-3">
        Already i have an account?<a href="<?= route("login.view") ?>"> Login</a> 
    </div>
    <button type="submit" class="btn btn-success" title="Register">Register</button>
    </form> 
</div>



<?php included('partials.foot') ?>