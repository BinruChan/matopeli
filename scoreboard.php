<?php

// Database configuration
$host = "localhost";
$user = "score";
$password = "";
$database = "score";
$table = "snake";


// Connect to the database
$dblink = mysqli_connect($host, $user, $password, $database);


// Check if connection failed
if (mysqli_connect_errno($dblink)) {
	
	die();
}


switch ($_SERVER["REQUEST_METHOD"]) {
	
	case "GET":
		
		// Query the top 4 scores from the database
		$result = mysqli_query($dblink, "SELECT name, score FROM ".$table." ORDER by score DESC LIMIT 5");
	
		}
		
		header("Content-Type: application/json; charset: utf-8");
		
		// Encode the top5 list in JSON
		echo json_encode($top5, JSON_UNESCAPED_UNICODE);
		
		break;
	
	
	case "POST":
		
		// Get the user IP
		$ip = ip2long($_SERVER["REMOTE_ADDR"]);
		
		// Check if it is blacklisted
		$listed = mysqli_query($dblink, "SELECT * FROM `blacklist` WHERE IP='".$ip."' LIMIT 1");
		
		if (mysqli_fetch_row($listed)) {
			
			http_response_code(403);
			die("Stahp!");
		}
		
		// Sanitize the inputs
		$name = mysqli_real_escape_string($dblink, $_POST["name"]);
		$score = mysqli_real_escape_string($dblink, $_POST["score"]);
		
		
		// Check if the inputs are invalid
		if ($name == '' | $name == 'Ossi Portaankorva') {

			http_response_code(400);
			die();
		}
		
		// Blacklist the IP if the score is too high
		if ($score > 625) {
			
			mysqli_query($dblink, "INSERT INTO `blacklist` (`IP`) VALUES ('".$ip."') ");
			http_response_code(400);
			die("No Way!");
		}
		
		if ($score>=5) {
			
			// Get the best score by the player from the database
			$oldscore = mysqli_fetch_assoc(mysqli_query($dblink, "SELECT score FROM `".$table."` WHERE name='".$name."' ORDER by score DESC LIMIT 1"))["score"];
			
			
			// Insert the new score to the database only if it is better than the old one
			if ($score > $oldscore) {
				
				// Insert the new score to the database
				mysqli_query($dblink, "INSERT INTO `".$table."` (`name`, `score`) VALUES ('".$name."','".$score."')");
				
				// Delete the player's old score
				mysqli_query($dblink, "DELETE FROM `".$table."` WHERE name='".$name."' AND score<'".$score."'");
			}
		}
		
		break;

}

mysqli_close($dblink);

?>
