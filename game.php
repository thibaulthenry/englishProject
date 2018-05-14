<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script>

	function myShowFunction(definition, difficulty, positionx, positiony, width) {
		var popup = document.getElementById("myPopup");
		popup.innerHTML = definition;
		var offsetHeight = document.getElementById('myPopup').offsetHeight;

		if (definition == "") {
			popup.innerHTML = "No definition available, please update the database !";
		}

		if (difficulty == 1) {
			popup.style["background-color"] = "rgba(76, 172, 35, 0.9)";
		} else if (difficulty == 2) {
			popup.style["background-color"] = "rgba(44, 220, 116, 0.9)";
		} else if (difficulty == 3) {
			popup.style["background-color"] = "rgba(211, 218, 58, 0.9)";
		} else if (difficulty == 4) {
			popup.style["background-color"] = "rgba(222, 142, 24, 0.9)";
		} else {
			popup.style["background-color"] = "rgba(220, 40, 40, 0.9)";
		}

		if (definition != null) {
			popup.style.position = 'absolute';
			popup.style.left = positionx + width/2;
			if (offsetHeight > 65) {
				popup.style.top = positiony - width - 20;
			} else {
				popup.style.top = positiony - width;
			}
			popup.classList.add("show");
		}
	}

	function myHideFunction() {
		var popup = document.getElementById("myPopup");
		var popupDiv = document.getElementsByClassName("popup");
		popup.classList.remove("show");
	}

	$(document).ready(function(){

		var ambiance = new Audio('static/sounds/ambiance.mp3');
		ambiance.play();

		var horizontal = true;

		$("#valid").show();
		$("#success").hide();
		$("input.unfound").css("background-color", "#ffffff");

		// Passe à la case suivante après entrée d'une lettre
		$("input.unfound").on('keypress', function(){
			var y = parseInt($(this).attr('y'));
			var x = parseInt($(this).attr('x'));
			var rightTile = $("input[y='"+y+"'][x='"+(x+1)+"']");
			var downTile = $("input[y='"+(y+1)+"'][x='"+x+"']");
			if (rightTile.attr('letter') != "" && horizontal) {
				horizontal = true;
				rightTile.focus();
			} else if (downTile.attr('letter') != "") {
				horizontal = false;
				downTile.focus();
			}
		});

		// Permet l'horizontal par défaut
		$("input.unfound").click(function(){
			horizontal = true;
		});

		// Efface contenu de la case selectionnee
		$("input.unfound").on('focus', function(){
			$(this).val("");
		});

		// Met le mot survolé en surbrillance
		$("input.unfound").hover(
			function(){
				var idWord = parseInt($(this).attr('idWord1'));
				$("input.unfound[idWord1='"+idWord+"'], input.unfound[idWord2='"+idWord+"']").css("background-color", "#CFCFCF");
				$("input.unfound[idWord1='"+idWord+"'], input.unfound[idWord2='"+idWord+"']").css("color", "#800000");
				var pos = $(this).offset();
				myShowFunction($(this).attr('definition'), $(this).attr('level'), pos.left, pos.top, $(this).width() + 6);
			},
			function(){
				var idWord = parseInt($(this).attr('idWord1'));
				$("input.unfound[idWord1='"+idWord+"'], input.unfound[idWord2='"+idWord+"']").css("background-color", "#ffffff");
				$("input.unfound[idWord1='"+idWord+"'], input.unfound[idWord2='"+idWord+"']").css("color", "black");
				myHideFunction();
			}
		);

		// Clique sur Validate
		$("#valid").click(function(){
			// Valide les bonnes cases
			$("input.unfound").each(function(index){
				if ($(this).val().toUpperCase() == $(this).attr('letter')) {
					$(this).removeClass('unfound').addClass('found');
					$(this).css("background-color", "#AAFFAA");
					$(this).css("color", "#009700");
					//$(this).attr('readonly', 'readonly');
				}
			});
			// Teste si partie gagnee
			if ($("input[letter]:not(.blank)").length == $("input.found").length) {
				$("#valid").hide();
				$("#success").show();
				$('.grid').css("border-color", "005000");
				var audio = new Audio('static/sounds/tada.mp3');
				audio.play();
				ambiance.pause();
			} else {
				var audio = new Audio('static/sounds/wrong.wav');
				audio.play();
			}

		});

	});
</script>

<?php 	include 'top.php';
		include 'crosswords.php';

	echo "

	<div class='popup'>
  <div class='popuptext' id='myPopup'></div>
	</div>

	<h3 class='ml13'>CrossWords</h3>";

	$difficulty = 5;
	if (isset($_POST['difficulty']))
		$difficulty = $_POST['difficulty'];
	$grid = generateGrid($difficulty);
	$bdd = connectBDD();

	$listDef = array();
	$listLevel = array();
	foreach ($grid as $line) {
		foreach ($line as $tile) {
			foreach ($tile['idWord'] as $id)
				$listDef[$id] = "";
		}
	}

	foreach ($grid as $line) {
		foreach ($line as $tile) {
			foreach ($tile['idWord'] as $id)
				$listLevel[$id] = "";
		}
	}

	foreach ($listDef as $id => $definition) {
		if ($id != null) {
			$query = 'SELECT definition FROM Words WHERE id = '.$id;
			$query2 = 'SELECT level FROM Words WHERE id = '.$id;

			$result = $bdd->query($query);
			$result2 = $bdd->query($query2);

			if ($result->num_rows > 0) {
				$word = $result->fetch_assoc();
				$listDef[$id] = $word['definition'];
			}

			if ($result2->num_rows > 0) {
				$word2 = $result2->fetch_assoc();
				$listLevel[$id] = $word2['level'];
			}
		}
	}

	$size = 40;
	$ysize = count($grid) * $size;
	$xsize = count($grid[0]) * $size;

	while (520 < $ysize || 720 < $xsize) {
		$size = $size - 4;
		$ysize = count($grid) * $size;
		$xsize = count($grid[0]) * $size;
	}

	$finalSizeX = $xsize + 80;
	$finalSizeY = $ysize + 80;
	$fontsize = $size/2;

	echo "<div class='gameDiv' style='width:".$finalSizeX."; height:".$finalSizeY."'><form class='form' action='/game.php' method='post' id='grid'><div class='grid' style='width:$xsize'>";
		for ($j=0; $j<count($grid); $j++) {
			for ($i=0; $i<count($grid[0]); $i++) {

				echo "<input type='text' y='".$j."' x='".$i."' letter='".$grid[$j][$i]['letter']."' size='1' maxlength='1' autocomplete='off' style='text-transform:uppercase; font-size:".$fontsize."; width:".$size."; height:".$size."'";
				if ($grid[$j][$i]['letter'] == "")
					echo "class='blank' disabled ";
				else {
					echo "class='unfound' idWord1='".$grid[$j][$i]['idWord'][0]."' ";
					if (isset($grid[$j][$i]['idWord'][1]))
						echo "idWord2='".$grid[$j][$i]['idWord'][1]."' ";
					else
						echo "definition='".$listDef[$grid[$j][$i]['idWord'][0]]."' ";
						echo "level='".$listLevel[$grid[$j][$i]['idWord'][0]]."' ";
				}
				echo ">";
				//* echo */ echo '<pre> '; print_r($grid[$j][$i]['idWord']); echo '</pre>';
			}
			echo "<br>";
		}
		echo "</div><br>
		<input class='validate' type='button' id='valid' value='Validate'>
		<div class='congratz' id='success'>Congratulations !</div>
	</form></div>";
?>

<html>
	<head>
		<title> CrossWords </title>
		<link href="static/css/game.css" type="text/css" rel ="stylesheet">
	</head>

	<body>

	</body>
</html>

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
