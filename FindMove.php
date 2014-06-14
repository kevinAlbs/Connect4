<?php
define("MAX_DEPTH", 7);//Takes about 1.5-2 seconds
class FindMove{
	private $a, $b;
	private $bestMove = NULL;//integer 0-6
	private $ref = array();//Map between column and current index of space (so if column i is empty, ref[i] = 6

	public function __construct($a){
		$this->a = $a;
		if($this->a != 1 && $this->a != 2){
			throw new Exception("Player should be integer, 1 or 2");
		}
		if($this->a == 1){
			$this->b = 2;
		}
		else{
			$this->b = 1;
		}
	}

	private function min(&$board, $depth){
		if($depth >= $this->MAX_DEPTH){
			return $this->score($board);
		}
		$min = NULL;
		for($i = 0; $i < 7; $i++){
			if($board[0][$i] != 0){
				//entire column filled
				continue;
			}
			//apply move, calculate the max for the next state
			$this->doMove($board, $i, $this->b);
			$val = $this->max($board, $depth+1);
			$this->undoMove($board, $i, $this->b);
			if($min == NULL || $val < $min){
				$min = $val;
			}
		}
		if($min == NULL){
			//there were no moves
			return $this->score($board);
		}
		else{
			return $min;
		}
	}
	//board must be passed by reference, apparently PHP copies arrays by default :/
	//Goes through all player a's options, returns the maximum value (of the mins of those options)
	private function max(&$board, $depth){
		if($depth >= $this->MAX_DEPTH){
			return $this->score($board);
		}
		$max = NULL;
		for($i = 0; $i < 7; $i++){
			if($board[0][$i] != 0){
				//entire column filled
				continue;
			}
			//apply move, calculate the min for this state
			$this->doMove($board, $i, $this->a);
			$val = $this->min($board, $depth+1);
			$this->undoMove($board, $i, $this->a);
			if($max == NULL || $val > $max){
				$max = $val;
				$this->bestMove = $i;//this should be fine since the first called function will reach this last
			}
		}
		if($max == NULL){
			//there were no moves
			return $this->score($board);
		}
		else{
			return $max;
		}
	}

	//helpers
	private function doMove(&$board, $i, $player){
		if($this->ref[$i] < 0){
			throw new Exception("Cannot make move");
		}
		$board[$this->ref[$i]][$i] = $player;
		$this->ref[$i] = $this->ref[$i] - 1;
	}
	private function undoMove(&$board, $i, $player){
		if($this->ref[$i] > 6 || $board[$this->ref[$i]+1][$i] != $player){
			throw new Exception("Cannot undo move, unexpected player");
		}
		$board[$this->ref[$i]+1][$i] = 0;
		$this->ref[$i] = $this->ref[$i] + 1;
	}
	//Return a score from a x's perspective
	public function score(&$board){
		return rand();//should be fine, it'll just always select the first move for now
	}
	public function findMove(&$board){
		if(sizeof($board) != 7 || sizeof($board[0]) != 7){
			throw new InvalidArgumentException("Board must be 7x7");	
		}
		//set up ref (gives index of current top space)
		for($j = 0; $j < 7; $j++){
			for($i = 6; $i >= 0; $i--){
				if($board[$i][$j] == 0){
					$this->ref[$j] = $i;
					break;
				}
			}
		}
		//given a 2d array, calculate the best possible move
		$this->max($board, 0);
		return $this->bestMove;
	}
	public function printBoard(&$board){
		for($i = 0; $i < 7; $i++){
			for($j = 0; $j < 7; $j++){
				printf("%d ", $board[$i][$j]);
			}
			printf("\n");
		}
	}
}

$start = microtime(true);
$ex = array(
	array(0, 0, 0, 0, 0, 0, 0),
	array(0, 0, 0, 0, 0, 0, 0),
	array(0, 0, 0, 0, 0, 0, 0),
	array(0, 0, 0, 0, 0, 0, 0),
	array(0, 0, 0, 0, 0, 0, 0),
	array(0, 0, 2, 1, 0, 0, 0),
	array(0, 1, 1, 2, 2, 0, 0)
	);
$mover = new FindMove(1);
$move = $mover->findMove($ex);
printf("Best move is %d\n", $move);
$end = microtime(true);
printf("Time taken : %f\n", $end-$start);
?>
