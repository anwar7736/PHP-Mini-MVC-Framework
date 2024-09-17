<?php 
    included('partials.head', ["title" => "Edit Post"]);
    included('partials.nav') ;
?>


<div class="container-fluid mt-3 row card p-3 bg-light">
    <h3 class="text-center text-danger">Edit Post</h3>
    <div align="left">
        <a href="<?= route("home") ?>" class="btn btn-secondary btn-sm" >Back to Home</a>
    </div>   
    <form id="ajaxForm" action="<?= route("post.update", $post->id) ?>" method="POST" class="col-md-7 offset-md-3" enctype="multipart/form-data">
    <div class="mb-3 mt-3">
        <input type="hidden" name="_method" value="PUT">
        <strong class="text-danger"><?= $error ?? '' ?></strong><br>
        <label for="title" class="form-label">Post Title:</label>
        <input type="title" class="form-control" id="title" placeholder="Enter post title" name="title" value="<?= $post->title ?>">    
            <p class="text-danger"><?= $_SESSION['errors']['title'] ?? '' ?></p>
    </div>
    <div class="mb-3">
        <label for="body" class="form-label">Post Description:</label>
        <textarea class="summernote" id="body" placeholder="Enter post body" name="body">
        <?= $post->body ?>
        </textarea>
        <p class="text-danger"><?= $_SESSION['errors']['body'] ?? '' ?></p>
    </div>    
    <div class="mb-3">
        <label for="image" class="form-label">Post Cover Photo:</label>
        <input type="file" class="form-control" id="image" name="image">
        <p class="text-danger"><?= $_SESSION['errors']['image'] ?? '' ?></p>
        <div class="preview-img mt-2 form-control">
              <img src="<?= getFilePath('posts', $post->image)?>" class="h-100 w-100"/>
        </div>
        
    </div>
    <div align="center">
        <button type="submit" class="btn btn-success px-md-4" title="Publish Now">Publish Now</button>
    </div>
    </form> 
</div>



<?php included('partials.foot') ?>