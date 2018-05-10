<?php	include 'top.php';

	echo "<h3 class='ml13'>Add Word</h3>";

	$bdd = connectBDD();

	$word = "";
	$definition = "";
	$level = 1;

	if (isset($_GET["id"])) {
		$id = $_GET["id"];
		$query = "SELECT * FROM Words WHERE id = ".$id;
		$result = $bdd->query($query);
		$elt = $result->fetch_assoc();
		$word = $elt['word'];
		$definition = $elt['definition'];
		$level = $elt['level'];
	}

	echo "
	<div class='container'>
	<form action='db.php' enctype='multipart/form-data' method='POST'>

	<div class='container2'>
	<input type='text' name='word' value='".$word."' maxlength='20' placeholder='Word name' autocomplete='off'>
	</div>
	<div class='container3'>
	<input type='textarea' name='definition' value='".$definition."' placeholder='Word definition' maxlength='100' autocomplete='off'>
	</div>

	<div class='container5'>
	<input type='submit' name='add' value='Add'>
	</div>

	<div class='slidecontainer'>
	<input class='slider' type='range' id='myRange' name='level' min='1' max='5' step='1' name='level' value='".$level."' onchange='sliderChange(this.value)'>
	</div>
	<br>
	<span class='difficulty'>Difficulty</span>
	<br>
	<span class='difficulty2' id='sliderStatus'>1</span>

	</form>
	</div>";
?>

<html>
	<head>
		<title> CrossWords </title>
		<link href="static/css/word.css" type="text/css" rel ="stylesheet">
	</head>

	<body>

	</body>
</html>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/2.0.2/anime.min.js"></script>
<script>
$('.ml13').each(function(){
  $(this).html($(this).text().replace(/([^\x00-\x80]|\w)/g, "<span class='letter'>$&</span>"));
});

anime.timeline({loop: true})
  .add({
    targets: '.ml13 .letter',
    translateY: [100,0],
    translateZ: 0,
    opacity: [0,1],
    easing: "easeOutExpo",
    duration: 5000,
    delay: function(el, i) {
      return 300 + 30 * i;
    }
  }).add({
    targets: '.ml13 .letter',
    translateY: [0,-100],
    opacity: [1,0],
    easing: "easeInExpo",
    duration: 10000,
    delay: function(el, i) {
      return 100 + 30 * i;
    }
  });
</script>

<script>
var slider = document.getElementById("myRange");
var output = document.getElementById("sliderStatus");
output.innerHTML = slider.value;

slider.oninput = function() {
  output.innerHTML = this.value;
}
</script>
