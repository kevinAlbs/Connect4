<?php
// Run test with: `php -f TestCorrect.php.
require_once("C4AI.php");

function testWorks(){
	// Test playing an entire game.
	$board = array(
		array(0, 0, 0, 0, 0, 0, 0),
		array(0, 0, 0, 0, 0, 0, 0),
		array(0, 0, 0, 0, 0, 0, 0),
		array(0, 0, 0, 0, 0, 0, 0),
		array(0, 0, 0, 0, 0, 0, 0),
		array(0, 0, 0, 0, 0, 0, 0),
		array(0, 0, 0, 0, 0, 0, 0)
	);
	$ai = C4AI::getInstance();
	for ($moveIter = 0; $moveIter < 100; $moveIter++) {
		$player = 2;
		if($moveIter % 2 == 0){
			$player = 1;
		}
		$moves = $ai->findAllMoves($board, $player);
		# Pick the highest score move.
		$bestMove = -1;
		$bestScore = -1 * INF;
		foreach ($moves as $move => $score) {
			if ($score > $bestScore) {
				$bestMove = $move;
				$bestScore = $score;
			}
		}

		// Play the highest score move.
		$ai->doMove($board, $bestMove, $player);
		
		// Check if the board is won.
		$moveJ = $bestMove;
		$moveI = 0;
		for ($i = 0; $i < 7; $i++) {
			if ($board[$i][$moveJ] != 0) {
				$moveI = $i;
				break;
			}
		}
		if ($ai->hasWon($board, $moveI, $moveJ, $player)) {
			printf ("Player %d has won:\n", $player);
			$ai->printBoard($board);
			break;
		}
		
		printf("Player %d moved to %d\n", $player, 0);
		$ai->printBoard($board);
	}
}

testWorks();
printf ("Tests passed");