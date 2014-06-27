<?php
require_once("C4AI.php");
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
$i = 0;
$won = false;
while(!$won){
	$i++;
	$player = 2;
	if($i % 2 == 0){
		$player = 1;
	}
	$move = $ai->findMove($board, $player, true);
	$ai->doMove($board, $move, $player);
	printf("Player %d move to %d\n", $player, $move);
	$ai->printBoard($board);
	$won = $ai->hasWon($board, $move, $player);
}

