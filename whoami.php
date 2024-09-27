<?php

// collect data
$ipaddress = $_SERVER['REMOTE_ADDR'] ?: "na";
$x_fwd_for = $_SERVER['HTTP_X_FORWARDED_FOR'] ?: "na";
$http_client_ip = $_SERVER['HTTP_CLIENT_IP'] ?: "na";

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
		  echo "<html><body><span name='ra'>" . $ipaddress . "</span><span name='xff'>" . $x_fwd_for . "</span><span name='hxi'>" . $http_client_ip . "</body></html>";
    } else if($encoding == "css") {
  		header('Content-Type: text/css');
  		echo "body .ra { background-color: " . $ipaddress . "; } ";
  		echo "div .xff { background-color: " . $x_fwd_for . "; }";
  		echo "span .hci { background-color: " . $http_client_ip . "; }";
    } else if($encoding == "csv") {
		header('Content-Type: text/csv');
  		echo "ip," . $ipaddress . PHP_EOL;
  		echo "xff," . $x_fwd_for . PHP_EOL;
  		echo "hci," . $http_client_ip . PHP_EOL;
    } else if($encoding == "js") {
  		header('Content-Type: text/javascript');
  		echo "document.ready(function() {" . $ipaddress . "});";
    } else if($encoding == "js") {
  		header('Content-Type: text/plain');
  		echo "ip_address: " . $ipaddress . "});";
	} else {
  		header('Content-Type: application/json');
  		$userinfo = array("ip"=>$ipaddress,"xff"=>$x_fwd_for,"hci"=>$http_client_ip);
  		echo json_encode($userinfo);
    }
} else {
  header('Content-Type: application/json');
  $userinfo = array("ip"=>$ipaddress,"xff"=>$x_fwd_for,"hci"=>$http_client_ip);
  echo json_encode($userinfo);
}

?>
