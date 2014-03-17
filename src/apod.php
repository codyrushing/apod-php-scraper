<?php 
	
	/* CONFIG */

	// if you do not wish to save thumbnails, set to false
	$save_thumbnail = true;
	$thumbnail_width = 200; // in px
	// path to where you wish to save thumbnails (eg. "/path/to/images/folder/")
	$apod_folder_relative = "/";
    
    /* END CONFIG */

    $date_string = date("mdy");
    $apod_page_url = "http://apod.nasa.gov/apod/astropix.html";

    $apod_data = apc_fetch("apod_data");

    if(!$apod_data || $apod_data["date"] !== $date_string){

	    $ch = curl_init(); 
		libxml_use_internal_errors(true);

    	try {
	    	include("lib/SimpleImage.php");
		    $image = new SimpleImage();

		    // set url 
		    curl_setopt($ch, CURLOPT_URL, $apod_page_url); 

		    //return the transfer as a string 
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 

		    // $output contains the output string 
		    $full_html = curl_exec($ch); 

		    $DOM = new DOMDocument;
		   	$DOM->loadHTML($full_html);

		   	

		   	/* BEGIN SCRAPING NASA's DOM */

		   	$center_tags = $DOM->getElementsByTagName("center");

		   	// get image source
		   	if(!empty($center_tags)){
		   		$second_link = $center_tags->item(0)->getElementsByTagName("a")->item(1);
		   		if(!empty($second_link)){
		   			$img_src = $second_link->getElementsByTagName("img")->item(0)->getAttribute("src");
		   		}
		   	}

		   	// get title
		   	$second_center = $center_tags->item(1);
		   	if(!empty($second_center)){
		   		$b_tag = $second_center->getElementsByTagName("b");
		   		if(!empty($b_tag)){
		   			$img_title = $b_tag->item(0)->nodeValue;
		   		}
		   	}

			if(empty($img_src) || empty($img_title)){
				throw new Exception("could not parse APOD page");
			}

			$ext = substr($img_src, -4);

			/* END SCRAPING NASA's DOM */




			$hosted_image_path = "http://apod.nasa.gov/apod/" . $img_src;
			$local_thumb_path = null;
			if($save_thumbnail){
		
			    $apod_root = $_SERVER["DOCUMENT_ROOT"] . $apod_folder_relative;

				$fullsize_path = $apod_root . "full" . $ext;
				$thumb_path = $apod_root . "thumb" . $ext;
				file_put_contents(
					$fullsize_path, 
					file_get_contents($hosted_image_path)
				);

				$image->load($fullsize_path);
				$image->resizeToWidth($thumbnail_width);
				$image->save($thumb_path);

				$local_thumb_path = $apod_folder_relative . "thumb" . $ext;
			}

			$new_apod_data = array(
				"date" => $date_string,
				"title" => $img_title,
				"hosted_image_path" => $hosted_image_path,
				"thumb" => $local_thumb_path
			);

			// it's possible that we have a new date, but APOD has not yet updated its page, so we don't want to restore yesterday's content as today's
			if($apod_data["title"] != $new_apod_data["title"]){
				apc_store("apod_data", $new_apod_data);
				$apod_data = apc_fetch("apod_data");
			}
    	} catch(Exception $e){
    		// if above fails, we will just use whatever is the cache, presumably yesterday's APOD data
    	}

	    // close curl resource to free up system resources 
	    curl_close($ch);      

    }

?>
