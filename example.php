<?php 
	include("src/apod.php");
    $apod_data = apc_fetch("apod_data");

    if(isset($apod_data)){ ?>
    	<h2><?php echo $apod_data["title"]; ?></h2>
    	<img src="<?php echo $apod_data["thumb"] ?>" />

    	<p>Here is the full hosted image from NASA</p>
    	<img src="<?php echo $apod_data["hosted_image_path"] ?>" />
    <?php } ?>