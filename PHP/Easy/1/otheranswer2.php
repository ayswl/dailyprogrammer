<?php if(isset($_GET["fuckit"])) { // Remove the data file and redirect
	unlink("data");
	header("Location: ./1.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<title>Reddit DB</title>
	<style>form:before{ content:"Input"; font-size:1.5em; } form{ background: #DDD; } ul,form{  padding: 1em;  list-style-type:none; max-width:30%; margin:0 auto; } ul{ border-bottom: 1px solid #999; } ul:first-child,form:first-child{ margin-top:10em; } ul.errors{ background:#FDD; } ul.errors:before{ content:"Error!"; font-size: 1.5em; color:#F00; } ul.output{ background:#DFD; } ul.output:before{ content:"Output"; font-size: 1.5em; color:#090; } ul.latest{ background:#DDF; } ul.latest:before{ content:"Latest users"; font-size:1.5em; color:#00F; } li a { color: #666; text-decoration:none; font-family: sans-serif; font-size: 0.8em; float: right; } 	</style>
</head>

<body>

	<?php
	function validate($input) {
		$output = array();
		$errors = array();
		foreach($input as $key => $value) {
			if($key == "age") {
				$age = (int)$value;
				
				if($age == 0) 
					$errors[] = "Invalid age";
				else
					$output["age"] = $age;
			}
			else if(strlen($value) < 1)
				$errors[] = "Invalid $key";
			else
				$output[$key] = filter_var($value,FILTER_SANITIZE_SPECIAL_CHARS);
		}
		return array($output,$errors);
	}
	function printErrors($errors) {
		$out = "";
		foreach($errors as $e)
			$out .= "<li>$e</li>\n";
		if(strlen($out) > 0) {
			print("<ul class=errors>$out</ul>");
			return true;
		}
		return false;
	}
	function printBack($output) {
		$out = "";
		foreach($output as $k => $v) {
			if($k == "reddit")
				$k = "$k username ";
			$out .= "<li>Your $k is $v</li>\n";
		}
		print("<ul class=output>$out</ul>");
	}
	function store($output) {
		file_put_contents("data",serialize($output)."\n",FILE_APPEND);
	}
	function fetch() {
		$fname = "data";
		if($file = @file_get_contents($fname)) {
			$lines = explode("\n",$file);
			$users = array();
			foreach($lines as $l) {
				$users[] = unserialize($l);
			}
			return($users);
		}
		return false;
	}
	function latest($users) {
		$users = array_reverse($users);
		return array_slice($users,0,5);
	}
	if(!empty($_POST)) {
		$output = validate($_POST);
		$errors = $output[1];
		$output = $output[0];
		if(!printErrors($errors)) {
			printBack($output);
			store($output);
		}
	}
	if($users = fetch()) {
		print "<ul class=latest>";
		foreach(latest($users) as $u) {
			if($u)
				printf("<li>Name: %s -- reddit username: %s -- Age: %d</li>",
					$u["name"], $u["reddit"], $u["age"]);
		}
		print "<li><a href='?fuckit'>clear history</a></li>";
		print "</ul>";
	}
	?>
	<form action="" method="post"><br>
		<input type="text" class="text" value="" name="name" />
		<label for="name">Your name?</label><br>
		<input type="text" name="age" value="" />
		<label for="age">Your age?</label><br>
		<input type="text" class="text" value="" name="reddit" />
		<label for="reddit">Your reddit username?</label><br>
		<input type="submit" class="submit" value="Go" name="" />
	</form>
</body>
</html>