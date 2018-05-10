<?php include 'top.php'; ?>


<html>
	<head>
		<title> CrossWords </title>
		<link href="static/css/index.css" type="text/css" rel ="stylesheet">
	</head>

	<body>
		<div class="centercont">
		<div class="container">
			<h1>CrossWords</h1>
		</div>

		<div class="container2">
			<form action='game.php' method='post'>
				<input type='submit' value='Play the game !'><br>
				<div class="slidecontainer">
				<input class="slider" type="range" id="myRange" name='difficulty' min="1" max="10" step="1" value="1" onchange="sliderChange(this.value)">
				</div>
				<br>
				<span class="difficulty">Difficulty</span>
				<br>
				<span class="difficulty2" id="sliderStatus">1</span>
			</form>
		</div>
	</div>
		<div class="container3">
			<input type='button' class="dbb" value='Improve the database !' onclick='window.location = "db.php"'>
		</div>
	</body>
</html>

<script>
var slider = document.getElementById("myRange");
var output = document.getElementById("sliderStatus");
output.innerHTML = slider.value;

slider.oninput = function() {
  output.innerHTML = this.value;
}
</script>
