<?php
header("Access-Control-Allow-Origin: *");
require_once("C4AI.php");

class RouteException extends Exception {}

class Router {
    static function getParam($field, $required=false, $default=null){
        if (!isset($_REQUEST[$field])) {
            if ($required) {
                throw new InvalidArgumentException(sprintf("Required field '%s' not passed", $field));
            } else {
                return $default;
            }
        }
        return $_REQUEST[$field];
    }
    static function route() {
        //quick and dirty router
        $path_parts = explode("/", $_SERVER["PATH_INFO"]);
        if (count($path_parts) < 2) {
            //show info page
            throw new RouteException("No route given");
        } 
        $route = $path_parts[1];

        //all routes have board data
        $raw_board_data = static::getParam("board_data", true);
        //Parse
        //Data can be POST/GET. Just numeric digits, no separators
        $len = strlen($raw_board_data);
        if (!($len == 7*7 || $len = 6*7) || !preg_match('/^[012]+$/', $raw_board_data)) {
            throw new InvalidArgumentException("Invalid board data given. Expected string of 42 or 49 characters (got $len). Characters must be 0 (for space), 1 (for player 1), and 2 (for player 2).");
        }
        $board = array_chunk(str_split($raw_board_data), 7);
        if ($route == "getMoves") {
            $player = intval(static::getParam("player", false, 1));
            $ai = C4AI::getInstance();
            echo json_encode($ai->findAllMoves($board, $player), JSON_FORCE_OBJECT);
        } else if($route == "hasWon"){ 
            $i = intval(static::getParam("i", true));
            $j = intval(static::getParam("j", true));
            $player = intval(static::getParam("player", false, 1));
            $ai = C4AI::getInstance();
            echo $ai->hasWon($board, $i, $j, $player) ? "true" : "false";
        } else{
            throw new RouteException("Route must be either getMoves or hasWon");
        }
        
    }
}

try {
    Router::route();
} catch (RouteException $re) {
    printf("Invalid route for the connect four endpoint. Error message: %s. To view more information visit <a href='%s'>%s</a>", $re->getMessage(), "info.html", "the documentation page");
} catch (Exception $e) {
    printf("Exception thrown: %s ", $e->getMessage());
    printf("To view more information visit <a href='%s'>%s</a>", "info.html", "the documentation page");
}

