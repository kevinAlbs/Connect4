<?php
// Run test with: `php -f TestCorrect.php.
require_once("C4AI.php");

function testWorks($nrows=7){
	printf ("Testing with $nrows rows\n");
	// Test playing an entire game.
	$board = array();
	for ($i = 0; $i < $nrows; $i++) {
		$board[$i] = array(0,0,0,0,0,0,0);
	}
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
		for ($i = 0; $i < $nrows; $i++) {
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

// Set error handler to throw on warnings (e.g. "Undefined array key")
function errHandle($errNo, $errStr, $errFile, $errLine) {
    $msg = "$errStr in $errFile on line $errLine";
    if ($errNo == E_NOTICE || $errNo == E_WARNING) {
        throw new ErrorException($msg, $errNo);
    } else {
        echo $msg;
    }
}

set_error_handler('errHandle');
testWorks(7); // 7 rows.
testWorks(6); // 6 rows.

function assertEqual($a, $b) {
	if ($a != $b) {
		throw new AssertionError("$a != $b");
	}
}

// Test hasWon:
{
	$board = array(
		array(0,0,0,2,0,0,0),
		array(0,0,1,1,0,2,0),
		array(0,0,2,2,2,1,0),
		array(0,0,1,2,2,2,0),
		array(0,0,2,1,1,1,0),
		array(0,0,1,2,2,1,0),
		array(0,0,1,2,1,1,0),
	);
	$ai = C4AI::getInstance();
	// Player 2 has won at index (3,3):
	$got = $ai->hasWon($board, 3, 3, 2);
	assertEqual($got, true);
	// Player 2 did not win at index (6,2):
	$got = $ai->hasWon($board, 6, 2, 2);
	assertEqual($got, false);
}
printf ("Tests passed\n");