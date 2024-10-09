<?php

// configuration settings
$logdata = true;
$logfile = "/var/www/html/idme/userinfo.csv";

// collect data
$remote_address = $_SERVER['REMOTE_ADDR'] ?: "na";
$x_fwd_for = $_SERVER['HTTP_X_FORWARDED_FOR'] ?: "na";
$http_client_ip = $_SERVER['HTTP_CLIENT_IP'] ?: "na";

// default to false
$output_headers = false;
$log_headers = false;

//	header_control values:
//		o, output only
//		l, log only
//		b, both: output & log
if(isset($_GET['h'])) {
	$header_control = $_GET['h'];
	if($header_control == 'o') {
		$output_headers = true;
	} else if($header_control == 'l') {
		$log_headers = true;
	} else if($header_control == 'b') {
		$output_headers = true;
		$log_headers = true;
	}
	$request_headers = getallheaders();
}

if($logdata) {
	if($log_headers) {
		// serialize headers into a string
		$serialized_headers = serialize($request_headers);
		logme($logfile, $remote_address . "," . $x_fwd_for . "," . $http_client_ip . "," . $serialized_headers);
	} else {
		logme($logfile, $remote_address . "," . $x_fwd_for . "," . $http_client_ip);
	}
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
image/svg+xml
application/xml
*********************/

if(isset($encoding)) {
	if($encoding == "html") {
		header('Content-Type: text/html');
		echo "<html><body>";
		echo "<span name='ra'>" . $remote_address;
		echo "</span><span name='xff'>" . $x_fwd_for;
		echo "</span><span name='hxi'>" . $http_client_ip;
		if($output_headers) {
			foreach($request_headers as $header_field=>$header_value) {
				echo "<span class='hdr'>" . $header_field . "=" . $header_value . "</span>";
			}
		}
		echo "</body></html>";
    } else if($encoding == "css") {
  		header('Content-Type: text/css');
  		echo "body .ra { background-color: " . $remote_address . "; } ";
  		echo "div .xff { background-color: " . $x_fwd_for . "; } ";
  		echo "span .hci { background-color: " . $http_client_ip . "; } ";
		if($output_headers) {
			echo "p .hdr { font-family: ";
			foreach($request_headers as $header_field=>$header_value) {
				 echo $header_field . "=" . $header_value . ", ";
			}
			echo "; }";
		}
    } else if($encoding == "csv") {
		header('Content-Type: text/csv');
  		echo "ra," . $remote_address . PHP_EOL;
  		echo "xff," . $x_fwd_for . PHP_EOL;
  		echo "hci," . $http_client_ip . PHP_EOL;
		if($output_headers) {
			foreach($request_headers as $header_field=>$header_value) {
				echo  "hdr," . $header_field . "=" . $header_value . PHP_EOL;
			}
		}
    } else if($encoding == "js") {
  		header('Content-Type: text/javascript');
  		echo "document.ready(function() {";
		echo "let ra=" . $remote_address . ";";
		echo "let xff=" . $x_fwd_for . ";";
		echo "let hci=" . $http_client_ip . ";";
		if($output_headers) {
			foreach($request_headers as $header_field=>$header_value) {
				echo "let" . $header_field . "=" . $header_value . ";";
			}
		}
		echo "});";
    } else if($encoding == "txt") {
  		header('Content-Type: text/plain');
		echo "ra:" . $remote_address . ";";
		echo "xff:" . $x_fwd_for . ";";
		echo "hci:" . $http_client_ip . ";";
		if($output_headers) {
			foreach($request_headers as $header_field=>$header_value) {
				echo "hdr:" . $header_field . "=" . $header_value . ";";
			}
		}
	} else {
  		header('Content-Type: application/json');
		  if($output_headers) {
			$userinfo = array("ra"=>$remote_address,"xff"=>$x_fwd_for,"hci"=>$http_client_ip,"hdr"=>$request_headers);
		} else {
			$userinfo = array("ra"=>$remote_address,"xff"=>$x_fwd_for,"hci"=>$http_client_ip);
		}
		echo json_encode($userinfo);
    }
} else {
  	header('Content-Type: application/json');
  	
	if($output_headers) {
		$userinfo = array("ra"=>$remote_address,"xff"=>$x_fwd_for,"hci"=>$http_client_ip,"hdr"=>$request_headers);
	} else {
		$userinfo = array("ra"=>$remote_address,"xff"=>$x_fwd_for,"hci"=>$http_client_ip);
	}
	echo json_encode($userinfo);
}

function logme($logfile,$message) {
	// format the current time
	$t = time();
	$ts = date("Y-m-d,H:i:s",$t);

	// open the log file
	$logger = fopen($logfile, "a") or die("Unable to open logfile!");
	// record entry, one per line
	fwrite($logger, $ts . ',' . $message . PHP_EOL);
	fclose($logger);
}

?>
