<?php
//	if(isset($_REQUEST)){
		echo file_put_contents("test.txt", json_encode($_REQUEST), FILE_APPEND);
//	}ss
?>
