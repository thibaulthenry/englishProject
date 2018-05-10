<?php include 'top.php';

echo "<div class='container'><h1 class='ml11'>
  <span class='text-wrapper'>
		<div>
    <div class='letters'>Valentin REINBOLD</div> <span style='color:rgba(210,0,0,0.8)' class='letters'>at BackEnd</span></div>
		<div class='letters'>Thibault HENRY</div> <span style='color:rgba(210,0,0,0.8)' class='letters'>at FrontEnd</span></div>
		</div>
  </span>
</div>

<div class='container2'>
  <div style='text-align: right;'>Musiques et images libres de droit</div>
		<div style='text-align: right;'>English Project 2018</div>
</div>

<div class='telecom'>
</div>";


?>



<html>
	<head>
		<link rel="stylesheet" type="text/css" href="static/css/credits.css">
	</head>

	<body>

	</body>
</html>

<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/2.0.2/anime.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script>
// Wrap every letter in a span
$('.ml11 .letters').each(function(){
  $(this).html($(this).text().replace(/([^\x00-\x80]|\w)/g, "<span class='letter'>$&</span>"));
});

anime.timeline({loop: true})
  .add({
    targets: '.ml11 .line',
    scaleY: [0,1],
    opacity: [0.5,1],
    easing: "easeOutExpo",
    duration: 700
  })
  .add({
    targets: '.ml11 .line',
    translateX: [0,$(".ml11 .letters").width()],
    easing: "easeOutExpo",
    duration: 700,
    delay: 100
  }).add({
    targets: '.ml11 .letter',
    opacity: [0,1],
    easing: "easeOutExpo",
    duration: 600,
    offset: '-=775',
    delay: function(el, i) {
      return 34 * (i+1)
    }
  }).add({
    targets: '.ml11',
    opacity: 0,
    duration: 1000,
    easing: "easeOutExpo",
    delay: 1000
  });
</script>
