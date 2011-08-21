<?php
var_dump($_FILES);
if (isset($_POST['upload'])) {
	foreach($_FILES['images']['error'] as $error) {
		if($error == UPLOAD_ERR_OK) {
			$name = $_FILES["images"]["name"];
			move_uploaded_file($_FILES["images"]["tmp_name"], "/tmp/" . $_FILES['images']['name']);
		}
	}
	
	echo "<h2>Successfully Uploaded Images</h2>";
} else {
	die('You are on wrong page');
}
