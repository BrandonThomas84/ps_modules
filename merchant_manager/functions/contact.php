<?php 
require ('functions.php');
//check for secure session 
sec_session_start(); 
if(login_check($mysqli) == true) {

	function functionSet($function){
		if(isset($function)){
			return $function;
		} else {
			return "<strong style=\"color: red;\">Server function not set</strong>";
		}
	}

	function bugSubmit($message){
		$to      = "bugs@perspektivedesigns.com";
		$subject = "Bug Submission: ";
		$message = "Message Start:\r\n" . $message;
		$headers = "From: " . $_SESSION['email'] . "\r\n" . "Reply-To: " . $_SESSION['email'] . "\r\n" . "X-Mailer: PHP/" . phpversion();
		$error = print_r(error_get_last());
		$phpInfo = "<br/><h1>PHP INFO:</h1> \r\n\n 
			 <b>PHP_SELF:</b> " . @functionSet($_SERVER["PHP_SELF"]) . "<br/>\r\n" . 
			"<b>GATEWAY_INTERFACE:</b> " . @functionSet($_SERVER["GATEWAY_INTERFACE"]) . "<br/>\r\n" . 
			"<b>SERVER_ADDR:</b> " . @functionSet($_SERVER["SERVER_ADDR"]) . "<br/>\r\n" . 
			"<b>SERVER_NAME:</b> " . @functionSet($_SERVER["SERVER_NAME"]) . "<br/>\r\n" . 
			"<b>SERVER_SOFTWARE:</b> " . @functionSet($_SERVER["SERVER_SOFTWARE"]) . "<br/>\r\n" . 
			"<b>SERVER_PROTOCOL:</b> " . @functionSet($_SERVER["SERVER_PROTOCOL"]) . "<br/>\r\n" . 
			"<b>REQUEST_METHOD:</b> " . @functionSet($_SERVER["REQUEST_METHOD"]) . "<br/>\r\n" . 
			"<b>REQUEST_TIME:</b> " . @functionSet($_SERVER["REQUEST_TIME"]) . "<br/>\r\n" . 
			"<b>REQUEST_TIME_FLOAT:</b> " . @functionSet($_SERVER["REQUEST_TIME_FLOAT"]) . "<br/>\r\n" . 
			"<b>QUERY_STRING:</b> " . @functionSet($_SERVER["QUERY_STRING"]) . "<br/>\r\n" . 
			"<b>DOCUMENT_ROOT:</b> " . @functionSet($_SERVER["DOCUMENT_ROOT"]) . "<br/>\r\n" . 
			"<b>HTTP_ACCEPT:</b> " . @functionSet($_SERVER["HTTP_ACCEPT"]) . "<br/>\r\n" . 
			"<b>HTTP_ACCEPT_CHARSET:</b> " . @functionSet($_SERVER["HTTP_ACCEPT_CHARSET"]) . "<br/>\r\n" . 
			"<b>HTTP_ACCEPT_ENCODING:</b> " . @functionSet($_SERVER["HTTP_ACCEPT_ENCODING"]) . "<br/>\r\n" . 
			"<b>HTTP_ACCEPT_LANGUAGE:</b> " . @functionSet($_SERVER["HTTP_ACCEPT_LANGUAGE"]) . "<br/>\r\n" . 
			"<b>HTTP_CONNECTION:</b> " . @functionSet($_SERVER["HTTP_CONNECTION"]) . "<br/>\r\n" . 
			"<b>HTTP_HOST:</b> " . @functionSet($_SERVER["HTTP_HOST"]) . "<br/>\r\n" . 
			"<b>HTTP_REFERER:</b> " . @functionSet($_SERVER["HTTP_REFERER"]) . "<br/>\r\n" . 
			"<b>HTTP_USER_AGENT:</b> " . @functionSet($_SERVER["HTTP_USER_AGENT"]) . "<br/>\r\n" . 
			"<b>HTTPS:</b> " . @functionSet($_SERVER["HTTPS"]) . "<br/>\r\n" . 
			"<b>REMOTE_ADDR:</b> " . @functionSet($_SERVER["REMOTE_ADDR"]) . "<br/>\r\n" . 
			"<b>REMOTE_HOST:</b> " . @functionSet($_SERVER["REMOTE_HOST"]) . "<br/>\r\n" . 
			"<b>REMOTE_PORT:</b> " . @functionSet($_SERVER["REMOTE_PORT"]) . "<br/>\r\n" . 
			"<b>REMOTE_USER:</b> " . @functionSet($_SERVER["REMOTE_USER"]) . "<br/>\r\n" . 
			"<b>REDIRECT_REMOTE_USER:</b> " . @functionSet($_SERVER["REDIRECT_REMOTE_USER"]) . "<br/>\r\n" . 
			"<b>SCRIPT_FILENAME:</b> " . @functionSet($_SERVER["SCRIPT_FILENAME"]) . "<br/>\r\n" . 
			"<b>SERVER_ADMIN:</b> " . @functionSet($_SERVER["SERVER_ADMIN"]) . "<br/>\r\n" . 
			"<b>SERVER_PORT:</b> " . @functionSet($_SERVER["SERVER_PORT"]) . "<br/>\r\n" . 
			"<b>SERVER_SIGNATURE:</b> " . @functionSet($_SERVER["SERVER_SIGNATURE"]) . "<br/>\r\n" . 
			"<b>PATH_TRANSLATED:</b> " . @functionSet($_SERVER["PATH_TRANSLATED"]) . "<br/>\r\n" . 
			"<b>SCRIPT_NAME:</b> " . @functionSet($_SERVER["SCRIPT_NAME"]) . "<br/>\r\n" . 
			"<b>REQUEST_URI:</b> " . @functionSet($_SERVER["REQUEST_URI"]) . "<br/>\r\n" . 
			"<b>PHP_AUTH_DIGEST:</b> " . @functionSet($_SERVER["PHP_AUTH_DIGEST"]) . "<br/>\r\n" . 
			"<b>PHP_AUTH_USER:</b> " . @functionSet($_SERVER["PHP_AUTH_USER"]) . "<br/>\r\n" . 
			"<b>PHP_AUTH_PW:</b> " . @functionSet($_SERVER["PHP_AUTH_PW"]) . "<br/>\r\n" . 
			"<b>AUTH_TYPE:</b> " . @functionSet($_SERVER["AUTH_TYPE"]) . "<br/>\r\n" . 
			"<b>PATH_INFO:</b> " . @functionSet($_SERVER["PATH_INFO"]) . "<br/>\r\n" . 
			"<b>ORIG_PATH_INFO:</b> " . @functionSet($_SERVER["ORIG_PATH_INFO"]) . "<br/>\r\n";
		
		//server sends email containing bug report information
		mail($to, $subject, $message . $error . $phpInfo, $headers);
		
		//used for testing puposes - uncommont to set function to echo the values
		//echo $to . $subject . $message . $error . $phpInfo . $headers;
	}

	//send bug submission email
	if(isset($_POST["bugSubmit"])){ 
		bugSubmit("");
		header("Location: ../index.php?msg=sc0005");
	}
	
	//testing purposes - uncomment to view what function output is
	//bugSubmit("");

} else {
	//redirects if not logged in
	header("Location: ../index.php");
}
?>