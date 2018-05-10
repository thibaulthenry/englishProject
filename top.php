<?php
	/* Connecte à la BDD */
	function connectBDD() {
		if (!isset($bdd)) {
			$servername = "localhost";
			$username = "root";
			$password = "";
			$dbname = "englishdb";

			$bdd = new mysqli($servername, $username, $password, $dbname);
			if ($bdd->connect_error) {
				die("Connection failed: " . $bdd->connect_error);
			}
		}
		return $bdd;
	}
?>

<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
		<link rel="stylesheet" type="text/css" href="static/css/top.css">
	</head>

	<body>
		<nav>
			<a href="index.php"><img class="logo" src="static/images/logo.png"></a>
			<ul id="myDIV">
				<li><a id="index" class="btn" href="index.php">Home</a></li>
				<li><a id="db" class="btn" href="db.php">Database</a></li>
				<li><a id="credits" class="btn" href="credits.php">Credits</a></li>
			</ul>
		</nav>
	</body>

	<script  type="text/javascript">
		window.onload = function() {
			var bool = true;
			var header = document.getElementById("myDIV");
			var btns = header.getElementsByClassName("btn");

			for (var i = 0; i < btns.length; i++) {
				if (document.URL.includes(btns[i].id)) {
					document.getElementById(btns[i].id).style["background"] = '#800000';
					bool = false;
				}
			}

			if (bool) {
				document.getElementById("index").style["background"] = '#800000';
			}
		}
	</script>
</html>
