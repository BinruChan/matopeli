<?php

header("Content-Type: application/json; charset: utf-8");


// Database configuration
$host = "localhost";
$user = "score";
$password = "3a23d998208fcc0f5f108caea7ebb846dbc2cce11e6eef07616cc062fd58f07c";
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
		$result = mysqli_query($dblink, "SELECT name, score FROM ".$table." ORDER by score DESC LIMIT 4");
		
		// Process result into an array
		$i = 1;
		$top5[0] = array("name"=>"Ossi Portaankorva");
		while ($row = mysqli_fetch_assoc($result)) {
			$top5[$i] = $row;
			$i = $i + 1;
		}
		
		// Add "Ossi Portaankorva"
		if ($top5[1]["score"] < 50) {
			$top5[0] = array(
				"name" => "Ossi Portaankorva",
				"score" => "54"
			);
			
		} else {
			$top5[0] = array(
				"name" => "Ossi Portaankorva",
				"score" => strval(ceil($top5[1]["score"]/9 + 1)*9)
			);
		}
		
		// Encode the top5 list in JSON
		echo json_encode($top5, JSON_UNESCAPED_UNICODE);
		
		break;
	
	case "POST":
		// Sanitize the inputs and insert them to the database
		$name = "'". mysqli_real_escape_string($dblink, $_POST["name"]) ."'";
		$score = "'". mysqli_real_escape_string($dblink, $_POST["score"]) ."'";
		mysqli_query($dblink, "INSERT INTO `snake` (`name`, `score`) VALUES (".$name.",".$score.")");
		
		break;
	
}
	
mysqli_close($dblink);

?>
