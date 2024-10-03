<?php

// configuration settings
$logdata = false;
$logfile = "/var/www/html/idme/userinfo.csv";

// collect data
$remote_address = $_SERVER['REMOTE_ADDR'] ?: "na";
$x_fwd_for = $_SERVER['HTTP_X_FORWARDED_FOR'] ?: "na";
$http_client_ip = $_SERVER['HTTP_CLIENT_IP'] ?: "na";

if($logdata) {
	// format the current time
	$t=time();
	$ts = date("Y-m-d,H:i:s",$t);

	// open the log file
	$logfile = fopen($logfile, "a") or die("Unable to open logfile!");
	// record entry, one per line
	fwrite($logfile, $ts . ',' . $remote_address . "," . $x_fwd_for . "," . $http_client_ip . PHP_EOL);
	fclose($logfile);
}

// encode and output
$encoding = $_GET['e'];

/************ interesting mime types 
text/css
text/csv
text/html
text/javascript
text/plain
image/gif
image/jpeg
*********************/

if(isset($encoding)) {
	if($encoding == "html") {
		header('Content-Type: text/html');
		echo "<html><body><span name='ra'>" . $remote_address . "</span><span name='xff'>" . $x_fwd_for . "</span><span name='hxi'>" . $http_client_ip . "</body></html>";
    } else if($encoding == "css") {
  		header('Content-Type: text/css');
  		echo "body .ra { background-color: " . $remote_address . "; } ";
  		echo "div .xff { background-color: " . $x_fwd_for . "; }";
  		echo "span .hci { background-color: " . $http_client_ip . "; }";
    } else if($encoding == "csv") {
		header('Content-Type: text/csv');
  		echo "ra," . $remote_address . PHP_EOL;
  		echo "xff," . $x_fwd_for . PHP_EOL;
  		echo "hci," . $http_client_ip . PHP_EOL;
    } else if($encoding == "js") {
  		header('Content-Type: text/javascript');
  		echo "document.ready(function() {";
		echo "let ra=" . $remote_address . ";";
		echo "let xff= " . $x_fwd_for . ";";
		echo "let hci=" . $http_client_ip . ";";
		echo "});";
    } else if($encoding == "txt") {
  		header('Content-Type: text/plain');
		echo "ra:" . $remote_address . ";";
		echo "xff:" . $x_fwd_for . ";";
		echo "hci:" . $http_client_ip . ";";
	} else {
  		header('Content-Type: application/json');
  		$userinfo = array("ra"=>$remote_address,"xff"=>$x_fwd_for,"hci"=>$http_client_ip);
  		echo json_encode($userinfo);
    }
} else {
  	header('Content-Type: application/json');
  	$userinfo = array("ra"=>$remote_address,"xff"=>$x_fwd_for,"hci"=>$http_client_ip);
	echo json_encode($userinfo);
}

?>
