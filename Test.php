<?php
require_once("C4AI.php");
$start = microtime(true);
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
$move = $ai->findMove($board,1, true);
printf("Best move is %d\n", $move);
$ai->printBoard($board);
$end = microtime(true);
printf("Time taken : %f\n", $end-$start);
