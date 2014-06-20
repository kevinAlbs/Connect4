<?php
define("MAX_DEPTH", 6);//Takes about 1.5-2 seconds
define("DEBUG", 1);
class C4AI{
	private $a, $b;
	private $bestMove = NULL;//integer 0-6
	private $ref = array();//Map between column and current index of top SPACE (so if column i is empty, ref[i] = 6
	private $score = 0;
	private static $instance = NULL;
	private function __construct(){}
	public static function getInstance(){
		if(C4AI::$instance == NULL){
			C4AI::$instance = new C4AI();
		}
		return C4AI::$instance;
	}


	private function min(&$board, $depth, $alpha, $beta, $usePruning){
		$min = NULL;
		$won = false;
		$beta = INF;
		for($i = 0; $i < 7; $i++){
			//printf("Min d%d, a%d, b%d\n", $depth, $alpha, $beta);
			if($board[0][$i] != 0){
				//entire column filled
				continue;
			}
			//apply move, calculate the max for the next state
			$this->doMove($board, $i, $this->b);
			if($this->hasWon($board, $i, $this->b)){
				$val = -10000;
				$won = true;
			}
			else if($depth+1 >= MAX_DEPTH){
				$val = $this->score($board);
			}
			else{
				$val = $this->max($board, $depth+1, $alpha, $beta, $usePruning);
			}
			$this->undoMove($board, $i, $this->b);
			if($min == NULL || $val < $min){
				$min = $val;
				$beta = $val;
			}
			if($won){
				break;
			}
			if($usePruning && $beta < $alpha){
				//then this parent will have a smaller value then the current best alpha, no chance
				//printf("Min cut d%d, a%d, b%d\n", $depth, $alpha, $beta);
				return $beta;
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
	private function max(&$board, $depth, $alpha, $beta, $usePruning){
		$max = NULL;
		$won = false;
		$alpha = -1 * INF; //we want to set the alpha on this level, not based on previous alpha
		for($i = 0; $i < 7; $i++){
			//printf("Max d%d, a%d, b%d\n", $depth, $alpha, $beta);
			if($board[0][$i] != 0){
				//entire column filled
				continue;
			}
			//apply move, calculate the min for this state
			$this->doMove($board, $i, $this->a);
			if($this->hasWon($board, $i, $this->a)){
				$val = 10000;
				$won = true;
			}
			else if($depth+1 >= MAX_DEPTH){
				$val = $this->score($board);
			}
			else{
				$val = $this->min($board, $depth+1, $alpha, $beta, $usePruning);
			}
			//$this->printBoard($board);
			$this->undoMove($board, $i, $this->a);
			if($max == NULL || $val > $max){
				$max = $val;
				$alpha = $val;
				if($depth == 0){
					$this->bestMove = $i;//only interested in the actual best move for depth 0
				}
			}
			if($won){ break; }
			if($usePruning && $alpha > $beta && $depth != 0){
				//the current max val is > then parent level beta, so no way will parent be chosen
				//printf("Max cut d%d, a%d, b%d\n", $depth, $alpha, $beta);
				return $alpha;
			}
			if($depth == 0){
				//printf("value: %d, %d\n", $val, $i);
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
	public function doMove(&$board, $i, $player){
		if($this->ref[$i] < 0){
			throw new Exception("Cannot make move");
		}
		$board[$this->ref[$i]][$i] = $player;
		$this->ref[$i] = $this->ref[$i] - 1;
	}
	public function undoMove(&$board, $i, $player){
		if($this->ref[$i] > 6 || $board[$this->ref[$i]+1][$i] != $player){
			throw new Exception("Cannot undo move, unexpected player");
		}
		$board[$this->ref[$i]+1][$i] = 0;
		$this->ref[$i] = $this->ref[$i] + 1;
	}
	//i is the column of the move just made, so ref[i] points just above
	public function hasWon(&$board, $jStart, $player){
		$iStart = $this->ref[$jStart] + 1;
		//check horizontal
		$hCount = 1;
		$i = $iStart;
		$j = $jStart-1;
		while($j >= 0){
			if($board[$i][$j] == $player){
				$hCount++;
			}	
			else{break;}
			$j--;
		}
		$j = $jStart+1;
		while($j < 7){
			if($board[$i][$j] == $player){
				$hCount++;
			}	
			else{break;}
			$j++;
		}
		//check vertical
		$vCount = 1;
		$i = $iStart-1;
		$j = $jStart;
		while($i >= 0){
			if($board[$i][$j] == $player){
				$vCount++;
			}	
			else{break;}
			$i--;
		}
		$i = $iStart+1;
		while($i < 7){
			if($board[$i][$j] == $player){
				$vCount++;
			}	
			else{break;}
			$i++;
		}
		//check diagonal bl-tr
		$d1Count = 1;
		$i = $iStart-1;
		$j = $jStart+1;
		while($i >= 0 && $j < 7){
			if($board[$i][$j] == $player){
				$d1Count++;
			}	
			else{break;}
			$i--;
			$j++;
		}
		$i = $iStart+1;
		$j = $jStart-1;
		while($i < 7 && $j >= 0){
			if($board[$i][$j] == $player){
				$d1Count++;
			}	
			else{break;}
			$i++;
			$j--;
		}
		//check diagonal br-tl
		$d2Count = 1;
		$i = $iStart-1;
		$j = $jStart-1;
		while($i >= 0 && $j >= 0){
			if($board[$i][$j] == $player){
				$d2Count++;
			}	
			else{break;}
			$i--;
			$j--;
		}
		$i = $iStart+1;
		$j = $jStart+1;
		while($i < 7 && $j < 7){
			if($board[$i][$j] == $player){
				$d2Count++;
			}	
			else{break;}
			$i++;
			$j++;
		}
		//$this->printBoard($board);
		//printf("h: %d, v: %d, d1: %d, d2: %d\n", $hCount, $vCount, $d1Count, $d2Count);
		if($hCount >= 4 || $vCount >= 4 || $d1Count >= 4 || $d2Count >= 4){
			//printf("Player %d can win\n", $player);
			return true;
		}
		else{
			return false;
		}

	}
	private function scoreDiff(&$board, $i, $j, $player){

	}
	private function playerWeight($player){
		return $player == $this->a ? 1 : -1;
	}
	//given a step through the i and j, count consecutive lines of pieces and return a score
	private function scorePaths(&$board, $iStart, $jStart, $iStep, $jStep){
		$score = 0;
		//go through each row, count # of possible wins
		$lspaces = 0;
		$rspaces = 0;
		$curPlayer = -1;
		$curPlayerCount = 0;
		$end = false;
		$i = $iStart;
		$j = $jStart;
		while($i < 7 && $j < 7 && $i >= 0 && $j >= 0){
			//printf("i%d,j%d,is%d,js%d\n", $i,$j, $iStep,$jStep);
			if($board[$i][$j] == 0){
				if($curPlayer != -1){
					//end of path
					$end = true;
				}
				else{
					$lspaces++;
				}
			}
			else{
				if($curPlayer != -1){
					if($curPlayer != $board[$i][$j]){
						//end of path
						$end = true;
					}
					else{
						//continuation of path
						$curPlayerCount++;
					}
				}
				else{
					//first path
					$curPlayer = $board[$i][$j];
					$curPlayerCount++;
				}
			}

			if($end){
				//count r spaces
				$ip = $i;
				$jp = $j;//i prime, j prime
				while($ip < 7 && $jp < 7 && $ip >= 0 && $jp >= 0){
					if($board[$ip][$jp] == 0){
						$rspaces++;
					}
					else{
						break;
					}
					$ip += $iStep;
					$jp += $jStep;
				}
				if($rspaces + $lspaces + $curPlayerCount >= 4){
					//printf("Row : %d, Col: %d, Path : %d, Player: %d\n", $i, $j, $curPlayerCount, $curPlayer);
					//possible win
					if($curPlayerCount >= 4){
						//winning move
						$score += 1000 * $this->playerWeight($curPlayer);
					}
					else{
						$score +=  $curPlayerCount * $this->playerWeight($curPlayer);
					}
				}
				$curPlayerCount = 0;
				$curPlayer = -1;
				$lspaces = 0;//reset path
				$rspaces = 0;
				$end = false;
				$j -= $jStep;//want to continue on the piece we skipped
				$i -= $iStep;
			}
			$i += $iStep;
			$j += $jStep;
		}
		if($curPlayerCount >= 4){
			//if player wins without any spaces after, need to update score
			$score += 1000 * $this->playerWeight($curPlayer);
		}
		return $score;

	}
	//Return a score from a x's perspective
	public function score(&$board){
		//count number of possible ways to win, place very high value on 2 possibilities to win in 1 move, place highest value on winning
		$score = 0;
		$curPlayer = -1;
		//go down each column, count number of longest path at top
		for($j = 0; $j < 7; $j++){
			if($this->ref[$j] >= -1 && $this->ref[$j] < 6){
				$pathLength = 0;
				$curPlayer = $board[$this->ref[$j]+1][$j];
				for($i = $this->ref[$j]+1; $i < 7; $i++){
					if($board[$i][$j] != $curPlayer){
						break;
					}
					else{
						$pathLength++;
					}
				}
				if(4 - $pathLength < $this->ref[$j] + 1){
					//if # needed is less than # left, no help to us
				}
				else{
					if($pathLength >= 4){
						//this player won
						$score += 1000 * $this->playerWeight($curPlayer);
					}
					else{
						//player hasn't won, but it's possible
						$score += $pathLength * $this->playerWeight($curPlayer);//longer path is better, scale this by a weight (possibly by using ml)
					}
				}
			}
		}
		//go through rows
		for($i = 0; $i < 7; $i++){
			$score += $this->scorePaths($board, $i, 0, 0, 1);
		}
		//go through the diagonals
		for($i = 3; $i < 7; $i++){
			$score += $this->scorePaths($board, $i, 0, -1, 1);
		}
		for($j = 1; $j <= 3; $j++){
			$score += $this->scorePaths($board, 6, $j, -1, 1);
		}

		for($i = 0; $i <= 3; $i++){
			$score += $this->scorePaths($board, $i, 0, 1, 1);
		}
		for($j = 1; $j <= 3; $j++){
			$score += $this->scorePaths($board, 0, $j, 1, 1);
		}

		return $score;//should be fine, it'll just always select the first move for now
	}
	public function findMove(&$board, $mainPlayer, $usePruning){
		$this->a = $mainPlayer;
		if($this->a != 1 && $this->a != 2){
			throw new Exception("Player should be integer, 1 or 2");
		}
		if($this->a == 1){
			$this->b = 2;
		}
		else{
			$this->b = 1;
		}

		if(sizeof($board) != 7 || sizeof($board[0]) != 7){
			throw new InvalidArgumentException("Board must be 7x7");	
		}
		$this->ref = array(-1,-1,-1,-1,-1,-1,-1);
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
		$this->max($board, 0, 0, 0, $usePruning);
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
?>
