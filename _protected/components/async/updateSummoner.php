<?php
//Proc_Close (Proc_Open ("COMMAND", Array (), $foo));
// php updateSummoner.php -- 'summoner_names'
//$argv[2]



//GET URL FOR CURL
$url = "google.com";

if(iset($argv[2]) && is_int($argv[2])){
	$url = "update group/".$argv[2];
}



//CURL
// create curl resource 
$ch = curl_init(); 

// set url 
curl_setopt($ch, CURLOPT_URL, $url); 

//return the transfer as a string 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 

// $output contains the output string 
$output = curl_exec($ch); 

// close curl resource to free up system resources 
curl_close($ch);  
?>