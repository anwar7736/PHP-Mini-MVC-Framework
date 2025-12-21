<?php 
    included('partials.head', ["title" => "Page Not Found"]);
    included('partials.nav') ;
?>



<div class="container-fluid mt-3 text-center text-danger">
<h4><?= $code ?> | Not Found</h4>
</div>



<?php included('partials.foot') ?>