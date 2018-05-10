<html>
	<?php
	
		function newWord() {
			return array(	'id' => null,
							'word'	=> '',
							'cst'	=> null,
							'varInf'=> null,
							'varSup'=> null);
		}
	
		/* Affiche la grille */
		function displayGrid() {
			global $grid;
			/*   _ _ _ _ _ _ _ _ 
				|_|_|_|0|_|_|_|_|	$grid
				|_|_|_|1|_|_|_|_|	
				|_|_|_|1|1|0|0|_|	letter [a-z] indique la lettre comprise dans la case
				|_|_|_|1|_|_|_|_|	state (0 ou 1) indique si la case interdit le passage de nouveaux mots
				|0|0|1|1|1|0|_|_|
				|_|_|_|1|_|_|_|_|
				|_|_|_|0|_|_|_|_|
				|_|_|_|_|_|_|_|_|
			*/
			for ($i=0; $i<count($grid); $i++) {
				for ($j=0; $j<count($grid[0]); $j++) {
					$tile = $grid[$i][$j];
					if ($tile['letter'] == "")
						echo "|_";
					else
						echo "|".$tile['letter'];
				}
				echo "|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				for ($j=0; $j<count($grid[0]); $j++) {
					$tile = $grid[$i][$j];
					if ($tile['letter'] == "") {
						if ($tile['blocked'])
							echo "|-";
						else
							echo "|_";
					} else {
						if ($tile['blocked'])
							echo "|1";
						else
							echo "|0";
					}
				}
				echo "|<br>";
			}
		}
		
		/* Trouve la case à partir de ses coordonnées */
		function getTile($y, $x, $attr) {
			global $grid;
			global $Dy, $Dx;
			$y -= $Dy;	$x -= $Dx;
			//echo "(".$y.",".$x.")<br>";
			if (isset($grid[$y][$x])) {
				if ($attr == '')
					return $grid[$y][$x];
				else
					return $grid[$y][$x][$attr];
			}
			return null;
		}
		
		/* Modifie la case à partir de ses coordonnées */
		function setTile($y, $x, $attr, $val) {
			global $grid;
			global $tile;
			global $h_Words, $v_Words;
			global $Dy, $Dx;
			$y -= $Dy;	$x -= $Dx;
			$dy = 0;	$y0 = 0;
			$dx = 0;	$x0 = 0;
			//* echo */ echo "<br>setTile(".$y.",".$x.",".$attr.",".$val.")";
			if (isset($grid[$y][$x]))
				$grid[$y][$x][$attr] = $val;
			else {
				//* echo */ echo " <-";
				if ($y < 0) {
					$y0 = $y;
					$dy = -$y;
				} else if ($y >= count($grid))
					$dy = $y-count($grid)+1;
				if ($x < 0) {
					$x0 = $x;
					$dx = -$x;
				} else if ($x >= count($grid[0]))
					$dx = $x-count($grid[0])+1;
				$grid2 = array_pad(array(), count($grid)+$dy, array_pad(array(), count($grid[0])+$dx, $tile));
				for ($j=0; $j<count($grid); $j++) {
					for ($i=0; $i<count($grid[0]); $i++)
						$grid2[$j-$y0][$i-$x0] = $grid[$j][$i];
				}
				$grid2[$y-$y0][$x-$x0][$attr] = $val;
				$grid = $grid2;
				foreach ($h_Words as $w) {
					$w['cst'] -= $y0;
					$w['varInf'] -= $x0;
					$w['varSup'] -= $x0;
				}
				foreach ($v_Words as $w) {
					$w['cst'] -= $x0;
					$w['varInf'] -= $y0;
					$w['varSup'] -= $y0;
				}
			}
			$Dy += $y0;
			$Dx += $x0;
			//* echo */ displayGrid();
		}
		
		/* Ajoute l'id du mot à chacune de ses cases */
		function addId($y, $x, $id) {
			global $grid;
			global $Dy, $Dx;
			$y -= $Dy;	$x -= $Dx;
			array_push($grid[$y][$x]['idWord'], $id);
		}
		
		/* Trouver l'abscisse d'un mot depuis l'index */
		function getX($word, $index, $round) {
			if ($round % 2)
				return $word['cst'];
			return $word['varInf'] + $index;
		}
		
		/* Trouver l'ordonnée d'un mot depuis l'index */
		function getY($word, $index, $round) {
			if ($round % 2)
				return $word['varInf'] + $index;
			return $word['cst'];
		}
		
		/* Renvoie la liste des id des mots placés */
		function idWords() {
			global $h_Words;
			global $v_Words;
			$listId = "";
			$everyWords = array_merge($h_Words, $v_Words);
			for ($i=0; $i<count($everyWords); $i++) {
				if ($i != 0)
					$listId .= ',';
				$listId .= $everyWords[$i]['id'];
			}
			return $listId;
		}
		
		/* Indique si un mot a toutes ses lettres bloquées */
		function blockedWord($word, $round) {
			global $grid;
			for ($index=0; $index<strlen($word['word']); $index++) {
				$x = getX($word, $index, $round);
				$y = getY($word, $index, $round);
				if (!getTile($y,$x,'blocked'))
					return false;
			}
			return true;
		}
		
	?>
</html>