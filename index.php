<?php
header("Access-Control-Allow-Origin: *");
require_once("C4AI.php");

function getParam($field, $required=false, $default=null){
    if (!isset($_REQUEST[$field])) {
        if ($required) {
            throw new InvalidArgumentException(sprintf("Required field '%s' not passed", $field));
        } else {
            return $default;
        }
    }
    return $_REQUEST[$field];
}

//quick and dirty router
$path_parts = explode("/", $_SERVER["PATH_INFO"]);
if (count($path_parts) < 2) {
    throw new InvalidArgumentException(sprintf("No route specified, must be either getMoves or hasWon"));
}

$route = $path_parts[1];

//all routes have board data
$raw_board_data = getParam("board_data", true);
//Parse
//Data can be POST/GET. Just numeric digits, no separators
if (!preg_match('/^[012]{49}$/', $raw_board_data)) {
    throw new InvalidArgumentException("Invalid board data given. Should be string of 49 characters. Characters must be 0 (for space), 1 (for player 1), and 2 (for player 2).");
}
$board= array_chunk(str_split($raw_board_data), 7);//damn, that was easy

if ($route == "getMoves") {
    $player = intval(getParam("player", false, 1));
    $ai = C4AI::getInstance();
    echo json_encode($ai->findAllMoves($board, $player, true), JSON_FORCE_OBJECT);
} else if($route == "hasWon"){ 
    echo "Maybe";
} else{
    throw new InvalidArgumentException("Invalid route, must be either getMoves or hasWon");
}

?>
