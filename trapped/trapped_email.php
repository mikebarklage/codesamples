<?php

header("Access-Control-Allow-Origin: *");
$json = file_get_contents("php://input");
$post = json_decode($json, true);

// server-side form validation
if ($post) {
	if (empty($post['yname']) && empty($post['email'])) {
		echo json_encode(["sent" => false, "message" => "Required fields not filled"]);
	}
	else {

		http_response_code(200);
		$subject = $post['yname']."'s brain is trapped in a computer!";
		$to = $post['email'];
		$from = $post['email'];
		
		$msg = "Attention: ".$post['yname']."'s brain is currently trapped in a computer.<br><br>";
		
		// process the causes array into readable list, including the Other wild card
		if(!empty($post['causes'])) {
			$causes_text = implode(", ", $post['causes']);
			if ((strpos($causes_text, ", Other") > 0) && (!empty($post['otext']))) {
				$causes_text = str_replace(", Other", ", ".htmlentities($post['otext']), $causes_text);
			}
			$msg .= "Cause(s): ".$causes_text."<br><br>";
		}

		if(!empty($post['message'])) {
			$msg .= "Message:<br><br>".htmlentities($post['message']);
		}
		else {
			$msg .= "No message attached";
		}
		
		$msg .= "<br><br>---<br>Email sent by HeyMyBrainIsTrappedInAComputer.com";

		$headers = "MIME-Version: 1.0\r\n";
		$headers.= "Content-type: text/html; charset=UTF-8\r\n";
		$headers.= "From: <" . $from . ">";
		$emailResult = mail($to, $subject, $msg, $headers);

		echo json_encode(array("sent" => $emailResult));
		
	}
}
else {
	// something wrong with the POST data
	echo json_encode(["sent" => false, "message" => "No POST data available"]);
}
