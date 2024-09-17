<?php 
    included('partials.head', ["title" => "My Profile"]);
    included('partials.nav') ;
    use App\Http\Controllers\Auth\Auth;
?>


<div class="container-fluid mt-3 row card p-3 bg-light">
    <h3 class="text-center text-danger">My Profile</h3>
    <?php if(session('message')) { ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Success!</strong> <?= session('message') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>  
  <?php } ?>
    <div align="left">
        <a href="<?= route("home") ?>" class="btn btn-secondary btn-sm" >Back to Home</a>
    </div>   
    <form id="ajaxForm" action="<?= route("update-profile") ?>" method="POST" class="col-md-7 offset-md-3" enctype="multipart/form-data">
    <input type="hidden" name="_method" value="PUT">
    <div class="mb-3">
        <label for="image" class="form-label">Profile Photo:</label>
        <input type="file" class="form-control" id="image" name="image">
        <p class="text-danger"><?= $_SESSION['errors']['image'] ?? '' ?></p>
        <div class="preview-img mt-2">
              <img src="<?= getFilePath('users', Auth::image())?>" class="h-50 w-50"/>
        </div>
    </div>
    <div class="mb-3 mt-3">
        <strong class="text-danger"><?= $error ?? '' ?></strong><br>
        <label for="name" class="form-label">Your Name:</label>
        <input type="text" class="form-control" id="name" placeholder="Enter your name" name="name" value="<?= Auth::name() ?>">    
        <p class="text-danger"><?= $_SESSION['errors']['name'] ?? '' ?></p>
    </div>    
    <div class="mb-3 mt-3">
        <strong class="text-danger"><?= $error ?? '' ?></strong><br>
        <label for="email" class="form-label">Your Email Address : (You can't change)</label>
        <input type="email" class="form-control" id="email" placeholder="Enter your email" name="email" value="<?= Auth::email() ?>" disabled>    
    </div> 
    <div class="mb-3">
        <label for="old_password" class="form-label">Old Password:</label>
        <input type="password" class="form-control" id="old_password" placeholder="Enter old password" name="old_password" value="<?= old('old_password') ?>">
        <p class="text-danger"><?= $_SESSION['errors']['old_password'] ?? '' ?></p>
    </div>     
    <div class="mb-3">
        <label for="new_password" class="form-label">New Password:</label>
        <input type="password" class="form-control" id="new_password" placeholder="Enter new password" name="new_password" value="<?= old('new_password') ?>">
        <p class="text-danger"><?= $_SESSION['errors']['new_password'] ?? '' ?></p>
    </div> 
    <div align="center">
        <button type="submit" class="btn btn-success px-md-4" title="Update">Update</button>
    </div>
    </form> 
</div>


<?php included('partials.foot') ?>