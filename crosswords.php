<?php 

	$tile = array(	'letter'  => '',
					'blocked' => false, 
					'idWord' => array() );
	$grid = array_pad(array(), 1, array_pad(array(), 1, $tile));
											
	$v_Words = array();		// liste des mots verticaux
	$h_Words = array();		// liste des mots horizontaux

	include 'functions.php';
	
	function generateGrid($nbWords) {
		$bdd = connectBDD();
		global $v_Words, $h_Words;
		global $grid;
		
		$maxWordLength = 20;
					
		$word = newWord();
		$wordRef = newWord();
		$fitWord = newWord();
						
				/* Mot 0 */
				
		// Tirage au sort du mot
		$query = 'SELECT id, word FROM Words ORDER BY RAND() LIMIT 1';
		$result = $bdd->query($query);
		if ($result->num_rows > 0) {
			$foundWord = $result->fetch_assoc();
			$word['id'] = $foundWord['id'];
			$word['word'] = $foundWord['word'];
			$word['cst'] = 0;
			$word['varInf'] = 0;
			$word['varSup'] = strlen($word['word']);
			// Remplissage de la grille */
			for ($i=0; $i<strlen($word['word']); $i++) {
				setTile($word['cst'], $word['varInf']+$i, 'letter', $word['word'][$i]);
				addId($word['cst'], $word['varInf']+$i, $word['id']);
			}
			// Stockage des mots en mémoire 
			array_push($h_Words, $word);
		}
		//* affichage */ displayGrid();	
			
				/* Mot round */
				
		for ($round=1; $round<$nbWords; $round++) {	// Nouveau mot
			$Dy = 0;								// Déplacement y de la grille
			$Dx = 0;								// Déplacement x de la grille
			$refWords = array();
			$triedWords = array();
			if ($round % 2)
				$refWords = $h_Words;
			else
				$refWords = $v_Words;
			do {									// Mot de référence
				$thisWordWorks = true;
				do {
					$wordRef = $refWords[array_rand($refWords)];
				} while (in_array($wordRef['id'], $triedWords) && count($refWords) != count($triedWords));
				
				if (count($refWords) == count($triedWords)) {
					//* echo */ echo "Placement de mot impossible<br>";
					break 1;
				}				
					
				array_push($triedWords, $wordRef['id']);
				
				//* echo */ echo '<pre>wordRef '; print_r($wordRef); echo '</pre>'; 
				do {								// Lettre de référence
					$thisLetterWorks = true;
					do {
						$index = rand(0,strlen($wordRef['word'])-1);
						$x = getX($wordRef, $index, $round-1);
						$y = getY($wordRef, $index, $round-1);
						//* echo */ echo $index."e -> (".$y.",".$x.") -> ".getTile($y,$x,'letter')."<br>";						
					} while ( (getTile($y,$x,'letter') == '' || getTile($y,$x,'blocked')) && !blockedWord($wordRef, $round-1) );
					
					if (blockedWord($wordRef, $round-1)) {
						//* echo */ echo "Mot bloqué<br>";
						break 1;
					}

					$fittedWords = array();
					for ($len=2; $len<$maxWordLength; $len++) {		// Longueur mot
						for ($nb=0; $nb<$len; $nb++) {				// Lettre de début
							$searchedWord = "";
							$varInf = $wordRef['cst']-$len+$nb+1;
							for ($i=0; $i<$len; $i++) {				// Lettre courante
								$letter = "";
								if ($round % 2)
									$letter = getTile($varInf+$i, $wordRef['varInf'] + $index, 'letter');
								else
									$letter = getTile($wordRef['varInf'] + $index, $varInf+$i, 'letter');								
								if ($letter == "")
									$letter = "_";
								$searchedWord .= $letter;
							}
							$query = 'SELECT id FROM Words WHERE id NOT IN ('.idWords().')';	// Vérifiez que cette requête est pas trop grande
							$result = $bdd->query($query);
							if ($result->num_rows > 0) {
								$query = 'SELECT id, word FROM Words WHERE id IN ('.$query.')
																	 AND word LIKE "'.$searchedWord.'"';
								//* echo */ echo $query."<br>";										
								$result = $bdd->query($query);
								while ($foundWord = $result->fetch_assoc()) {
									//* echo */ echo $foundWord['word']."<br>";
									$fitWord['id'] = $foundWord['id'];
									$fitWord['word'] = $foundWord['word'];
									$fitWord['cst'] = $wordRef['varInf'] + $index;
									$fitWord['varInf'] = $varInf;
									$fitWord['varSup'] = $varInf + strlen($foundWord['word']);
									array_push($fittedWords, $fitWord);
									//* echo */ echo '<pre>fittedWords '; print_r($fittedWords); echo '</pre>'; 
								}
								$result->close();
							} else {
								//* echo */ echo "Plus de mots<br>";
								break 5;
							}

						}

					}
					//echo "len = ".count($grid);
					if (count($fittedWords) > 0) {
						// Tirage au sort du mot 
						$word = $fittedWords[array_rand($fittedWords)];
						//* echo */ echo '<pre>word '; print_r($word); echo '</pre>'; 
						// Blocage des intersections
						if ($round % 2) {
							$j = $wordRef['cst'];
							$i = $word['cst'];
						} else {
							$j = $word['cst'];
							$i = $wordRef['cst'];
						}
						setTile($j,	  $i,	'blocked', true);
						setTile($j,	  $i-1,	'blocked', true);
						setTile($j,	  $i+1,	'blocked', true);
						setTile($j-1, $i,	'blocked', true);
						setTile($j-1, $i-1,	'blocked', true);
						setTile($j-1, $i+1,	'blocked', true);
						setTile($j+1, $i,	'blocked', true);
						setTile($j+1, $i-1,	'blocked', true);
						setTile($j+1, $i+1,	'blocked', true);
						// Remplissage de la grille 
						for ($index=0; $index<strlen($word['word']); $index++) {
							$i = getX($word, $index, $round);
							$j = getY($word, $index, $round);
							setTile($j,$i,'letter',$word['word'][$index]);
							addId($j,$i,$word['id']);
						}
						// Stockage des mots en mémoire 
						if ($round % 2)
							array_push($v_Words, $word);
						else
							array_push($h_Words, $word);
					
					} else {	// Aucun mot correspondant
						//* echo */ echo "Changement de lettre<br>";
						$thisLetterWorks = false;
						setTile($y, $x, 'blocked', true);
						if (blockedWord($wordRef, $round-1)) {
							//* echo */ echo "Changement de mot<br>";
							$thisWordWorks = false;
						}
					}
					
				} while (!$thisLetterWorks && $thisWordWorks);
				
			} while (!$thisWordWorks);
			
			//* affichage */ displayGrid();
		}
	return $grid;
	}
?>