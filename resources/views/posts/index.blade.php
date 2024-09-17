<?php 
    included('partials.head', ["title" => "Home"]);
    included('partials.nav') ;
?>



<div class="container-fluid mt-3">
  <h3 class="text-center text-danger">All Post List</h3><hr>
  <div class="mb-3" align="center">
    <a href="<?= route("post.create") ?>" class="btn btn-success btn-sm" >Publish New Post</a>
</div>
   <div class="row">
    <?php foreach($posts as $key => $post) { ?>
    <div class="card col-md-3 mb-2">
        <img src="<?= getFilePath('posts', $post->image) ?>" class="card-img-top" alt="" height="200">
        <div class="card-body">
            <h5 class="card-title"><?= $post->title ?></h5>
            <p class="card-text"><?= substr($post->body, 0, 250) ?></p><hr>
            <p class="card-text"><?= $post->view ?> views <br>
                Published at : <?= date('d F, Y', strtotime($post->published_at)) ?><br>
                Published By : <?= $post->name ?>
            </p>
            <a href="<?= route("post.show", $post->id) ?>" class="btn btn-success">Read More...</a>
        </div>`
    </div>  
    <?php } ?>
    </div>
</div>


<?php included('partials.foot') ?>