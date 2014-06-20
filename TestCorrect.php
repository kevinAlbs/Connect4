<?php
require_once("ConnectFourAI.php");
require_once("BoardUtils.php");
class TestCorrect extends PHPUnit_Framework_TestCase{
	public function testPruning(){
		//play an entire game against itself, ensure that the move with/without the pruning matches
		$board = array(
			array(0, 0, 0, 0, 0, 0, 0),
			array(0, 0, 0, 0, 0, 0, 0),
			array(0, 0, 0, 0, 0, 0, 0),
			array(0, 0, 0, 0, 0, 0, 0),
			array(0, 0, 0, 0, 0, 0, 0),
			array(0, 0, 0, 0, 0, 0, 0),
			array(0, 0, 0, 0, 0, 0, 0)
		);
		$ai = ConnectFourAI::getInstance();
		for($i = 0; $i < 10; $i++){
			$player = 2;
			if($i % 2 == 0){
				$player = 1;
			}
			$pruneMove = $ai->findMove($board, $player, true);
			$noPruneMove = $ai->findMove($board, $player, false);
			$this->assertEquals($pruneMove, $noPruneMove);
			BoardUtils::doMove($board, $pruneMove, $player);
			printf("Player %d move to %d\n", $player, $pruneMove);
			$ai->printBoard();
		}
	}
}

