<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script>
	$(window).blur(function(e) {
		$(window).focus(function(e) {
			(location.reload());
		});
	});

	/* Recherche de mots dans toutes les colonnes */
	function recherche() {
		var input, filter, condition, table, row, col, casei, i, j;
		input = document.getElementById("search");
		filter = input.value.toUpperCase();
		filter = filter.split(" ");
		table = document.getElementById("tableau");
		row = table.getElementsByTagName("tr");
		col = table.getElementsByTagName("td");

		for (i = 1; i < row.length; i++) {
			casei = row[i].getElementsByTagName("td");
			row[i].style.display = "";
			for (k = 0; k < filter.length; k++) {
				condition = false;
				for (j = 0; j < casei.length; j++) {
					if (col[j].style.display != "none") {
						if (casei[j].innerHTML.toUpperCase().indexOf(filter[k]) > -1)
							condition = true;
					}
				}
				if (!condition)
					row[i].style.display = "none";
			}
		}
	}

	/* Redirection vers la page correspondant à la ligne sélectionnée */
	function link(id){
		window.location = "word.php?id="+id;
		window.focus();
	}
</script>

<?php include 'top.php';

	echo "<div class=\"dbtext\" id=messenger></div>";

	$bdd = connectBDD();

	if (isset($_POST['word']) || isset($_POST['definition'])) {
		if (!(isset($_POST['word'])) || !(isset($_POST['definition'])))
			header('Location: word.php');
		$query = "INSERT INTO Words (word) VALUES ('".strtoupper($_POST['word'])."')";
		$result = $bdd->query($query);
		$query = "UPDATE Words SET definition = '".ucfirst($_POST['definition'])."', level = ".$_POST['level']." WHERE word = '".$_POST['word']."'";
		$result = $bdd->query($query);
	}

	if (isset($_POST['id'])) {
		$query = "DELETE FROM Words WHERE id = ".$_POST['id'];
		$result = $bdd->query($query);
	}

	echo "<div class=\"searchplus\">
	<input type='text' id='search' name='recherche' onkeyup='recherche()' placeholder='Search..' method='POST' autocomplete='off'>

	<input type='button' id='new' value='+' onclick='window.location = \"./word.php\"'>
	</div>
	<div class='divtable'>
	<table class='table' border='0' id='tableau'>
		<tr class='header'>
			<th>Word</th>
			<th>Definition</th>
			<th>Difficulty</th>
			<th></th>
		<tr>";

	$query = "SELECT DISTINCT * FROM Words ORDER BY word";
	$result = $bdd->query($query);
	for($i=0; $i<$result->num_rows; $i++) {
		$elt = $result->fetch_assoc();
		echo "
		<tr onclick='link(".$elt['id'].")'>
			<td class='word'>".$elt['word']."</td>
			<td class='def'>".$elt['definition']."</td>
			<td>".$elt['level']."</td>";

			if ($i%2 == 0) {
				echo "<td class=\"deletecase\">
					<form class='deleteform' action='db.php' method='POST'>
						<input type='hidden' name='id' value='".$elt['id']."'>
						<input class='deleteimg' name='delete' value='' type='submit'>
					</form>
				</td>";
			} else {
				echo "<td class=\"deletecase2\">
					<form class='deleteform'  action='db.php' method='POST'>
						<input type='hidden' name='id' value='".$elt['id']."'>
						<input class='deleteimg' 'sname='delete' value='' type='submit'>
					</form>
				</td>";
			}

		echo "</tr>";
	}
	echo "</table></div>";
?>


<html>
	<head>
		<title> CrossWords </title>
		<link href="static/css/db.css" type="text/css" rel ="stylesheet">
	</head>

	<body>

	</body>
</html>

<script>

var Messenger = function(el){
  'use strict';
  var m = this;

  m.init = function(){
    m.codeletters = "&#*+%?£@§$abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWYZ";
    m.message = 0;
    m.current_length = 0;
    m.fadeBuffer = false;
    m.messages = [
      'Fill the database with words and definitions',
      'Cool database right ?',
      'Don\'t leave this empty !',
      'Thanks for coming.'
    ];

    setTimeout(m.animateIn, 100);
  };

  m.generateRandomString = function(length){
    var random_text = '';
    while(random_text.length < length){
      random_text += m.codeletters.charAt(Math.floor(Math.random()*m.codeletters.length));
    }

    return random_text;
  };

  m.animateIn = function(){
    if(m.current_length < m.messages[m.message].length){
      m.current_length = m.current_length + 2;
      if(m.current_length > m.messages[m.message].length) {
        m.current_length = m.messages[m.message].length;
      }

      var message = m.generateRandomString(m.current_length);
      $(el).html(message);

      setTimeout(m.animateIn, 20);
    } else {
      setTimeout(m.animateFadeBuffer, 20);
    }
  };

  m.animateFadeBuffer = function(){
    if(m.fadeBuffer === false){
      m.fadeBuffer = [];
      for(var i = 0; i < m.messages[m.message].length; i++){
        m.fadeBuffer.push({c: (Math.floor(Math.random()*12))+1, l: m.messages[m.message].charAt(i)});
      }
    }

    var do_cycles = false;
    var message = '';

    for(var i = 0; i < m.fadeBuffer.length; i++){
      var fader = m.fadeBuffer[i];
      if(fader.c > 0){
        do_cycles = true;
        fader.c--;
        message += m.codeletters.charAt(Math.floor(Math.random()*m.codeletters.length));
      } else {
        message += fader.l;
      }
    }

    $(el).html(message);

    if(do_cycles === true){
      setTimeout(m.animateFadeBuffer, 50);
    } else {
      setTimeout(m.cycleText, 5000);
    }
  };

  m.cycleText = function(){
    m.message = m.message + 1;
    if(m.message >= m.messages.length){
      m.message = 0;
    }

    m.current_length = 0;
    m.fadeBuffer = false;
    $(el).html('');

    setTimeout(m.animateIn, 3000);
  };

  m.init();
}

console.clear();
var messenger = new Messenger($('#messenger'));
</script>
