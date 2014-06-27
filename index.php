<?php
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

$raw_board_data = getParam("board_data", true);
$player = intval(getParam("player", false, 1));

//Data can be POST/GET. Just numeric digits, no separators.
//Parse
if (!preg_match('/^[012]{49}$/', $raw_board_data)) {
    throw new InvalidArgumentException("Invalid board data given. Should be string of 49 characters. Characters must be 0 (for space), 1 (for player 1), and 2 (for player 2).");
}
$board_data = array_chunk(str_split($raw_board_data), 7);//damn, that was easy

echo nl2br(print_r($board_data, true));
?>
