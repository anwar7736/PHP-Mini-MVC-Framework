<?php 
    included('partials.head', ["title" => "Unauthorized Action"]);
    included('partials.nav') ;
?>



<div class="container-fluid mt-3 text-center text-danger">
<h4><?= $code ?> | Unauthorized Action</h4>
</div>


<?php included('partials.foot') ?>