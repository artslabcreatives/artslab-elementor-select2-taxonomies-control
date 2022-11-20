<?php
	if(isset($_REQUEST)){
		$myfile = fopen("newfile.txt", "a+") or die("Unable to open file!");
		$txt = json_encode($_REQUEST);
		fwrite($myfile, $txt);
		fclose($myfile);
	}
?>