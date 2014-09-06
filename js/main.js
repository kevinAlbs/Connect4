var CONFIG = {
  endpoint : "http://localhost/connect4/back-end/index.php/",
};

/* Module for game logic + API calls
 *
 * There is duplicated logic here as in the API, e.g. win checking.
 * However, it reduces requests made.
 */
var GAME = (function(){
  var that = {};
  var board = [];
  var ref = [];
  var cur_player = 1, winning_player = -1;
  var winning_pieces = [];
    //initialize board
    for(var i = 0; i < 7; i++){
        ref[i] = 6;//should be ref[col] but deal with it
        board[i] = [];
        for(var j = 0; j < 7; j++){
          board[i][j] = 0;
        }
      }
    /* Returns a string of 49 characters representing board
    */
    function flattenedBoard(){
      var str = "";
      for(var i = 0; i < board.length; i++){
        for(var j = 0; j < board.length; j++){
          str += board[i][j]; 
        }
      }
      return str;
    }

    function validIndex(i){
      return i >= 0 && i <= 6;
    }

    function switchPlayer(){
      if (cur_player == 1){
        cur_player = 2;
      } else {
        cur_player = 1;
      }
    }

    //debugging
    that.won = function(player, iStart, jStart){
      return won(player, iStart, jStart);
    }
    function won(player, iStart, jStart){
      if(board[iStart][jStart] != player) return false;
      winning_pieces = [];
      winning_pieces.push({i: iStart, j: jStart});
      var count = 1;
      //check vertical 
      for(var i = iStart + 1; i < 7; i++){
        if(board[i][jStart] == player){
          count++;
          winning_pieces.push({i : i, j: jStart});
        }
        else {
          break;
        }
      }
      for(var i = iStart - 1; i >= 0; i--){
        if(board[i][jStart] == player){
          count++;
          winning_pieces.push({i : i, j: jStart});
        }
        else {
          break;
        }
      }

      if(count >= 4) {
        return true;
      }
      else {
        count = 1;
        winning_pieces = winning_pieces.slice(0, 1);
      }

      //check horizontal
      for(var j = jStart + 1; j < 7; j++){
        if(board[iStart][j] == player){
          count++;
          winning_pieces.push({i : iStart, j: j});
        }
        else{
          break;
        }
      }
      for(var j = jStart - 1; j >= 0; j--){
        if(board[iStart][j] == player) {
          count++;
          winning_pieces.push({i : iStart, j: j});
        }
        else {
          break;
        }
      }
      
      if(count >= 4) {
        return true;
      }
      else {
        count = 1;
        winning_pieces = winning_pieces.slice(0, 1);
      }

      //check diagonals
      for(var j = jStart+1, i = iStart+1; j < 7 && i < 7; i++, j++){
        if(board[i][j] == player) {
          count++;
          winning_pieces.push({i : i, j: j});
        }
        else {
          break;
        }
      }
      for(var j = jStart-1, i = iStart-1; j >= 0 && i >= 0; i--, j--){
        if(board[i][j] == player) {
          count++;
          winning_pieces.push({i : i, j: j});
        }
        else {
          break;
        }
      }

      if(count >= 4) {
        return true;
      }
      else {
        count = 1;
        winning_pieces = winning_pieces.slice(0, 1);
      }

      //check diagonals
      for(var j = jStart-1, i = iStart+1; j >= 0 && i < 7; i++, j--){
        if(board[i][j] == player) {
          count++;
          winning_pieces.push({i : i, j: j});
        }
        else {
          break;
        }
      }
      for(var j = jStart+1, i = iStart-1; j < 7 && i >= 0; i--, j++){
        if(board[i][j] == player) {
          count++;
          winning_pieces.push({i : i, j: j});
        }
        else {
          break;
        }
      }

      if(count >= 4) {
        return true;
      }
      return false;
    }
    that.getBoard = function() {return board;}
    that.getCurPlayer = function() { return cur_player; }
    that.getWinningPlayer = function() { return winning_player; }
    that.getWinningPieces = function() { return winning_pieces; }
    that.getNumSpaces = function(j){
      if(!validIndex(j)){
        throw "Invalid index for column";
      }
      return ref[j] + 1;//lol
    }

      /* Places piece in 2d array, switches player */
      that.placePiece = function(j){
        if(!validIndex(j)){
          throw "Invalid index for column";
        }
        if(ref[j] <= -1){
          throw "No spaces left in column";
        }
        board[ref[j]][j] = cur_player;
        //check if player won
        console.log("Calling won with " + cur_player + " " + ref[j] + " " + j);
        console.log(board);
        if(won(cur_player, ref[j], j)){
          winning_player = cur_player;
          return true;
        }
        ref[j]--;
        switchPlayer();
        return false;
      }
      that.AIGetMove = function(callback){
        var data = {
          "board_data" : flattenedBoard(),
          "player" : cur_player
        };
        $.ajax({
          url: CONFIG.endpoint + "getMoves",
          data: data,
          dataType: "json",
          success: function(data){
            console.log(data);
            if (typeof(data) != "object") {
                console.log("Return data not object");
                return;
            }
            //select a random from best
            var best = [], bestVal = Number.NEGATIVE_INFINITY;
            for (var j in data){
                if(data.hasOwnProperty(j)){
                  if (data[j] == bestVal) {
                    best.push(parseInt(j));
                  }
                  else if (data[j] > bestVal) {
                    best = [];
                    best.push(parseInt(j));
                    bestVal = data[j];
                  }
                }
            }
            var finalVal = best[Math.floor(best.length * Math.random())];
            if(callback){
              callback(finalVal);
            }
          }
        });
      }
      that.init = function(settings){
        that.GAME_TYPE = settings.GAME_TYPE;
      }
      return that;
    }());

var UI = (function(){ 
  var boardLocked = false;
  var board = $("#board");
  var that = {};

  function lock(){
    boardLocked = true;
    board.removeClass("unlocked").addClass("locked");
  }
  function unlock(){
    boardLocked = false;
    board.removeClass("locked").addClass("unlocked");
  }

  function highlight(pieces) {
    //remove highlight if exists
    board.find(".piece").removeClass("highlight");
    if (!pieces) {
      return;
    }
    for (var k = 0; k < pieces.length; k++) {
      var piece = pieces[k];
      var piece_dom = $(board.find("[data-i=" + piece.i + "][data-j=" + piece.j + "]")[0]);
      piece_dom.addClass("highlight");
    }
  }

  function getColor(num){
    return num == 1 ? "Green" : "Red";
  }
  function showWon(){
    $("#winner").html(getColor(GAME.getWinningPlayer()) + " wins");
    var pieces = GAME.getWinningPieces();
    highlight(pieces);
  }
  /* Creates and drops piece */
  that.dropPiece = function(player, index, callback){
    var col = $(board.find(".col")[index]);
    var num_spaces = GAME.getNumSpaces(index);
    if(num_spaces == 0){
      return;
    }
    var new_piece = $("<div class='piece'></div>").css({top: "-20px"}).addClass("pl_" + player).attr("data-i" , num_spaces-1).attr("data-j", index);
    new_piece.animate({
      top: ((num_spaces-1) * 21) + "px"
    }, 1000 - ((6-num_spaces)*100), "easeOutBounce", function(){callback();});
    col.append(new_piece);
  };

  board.find(".col").click(function(){
    if (boardLocked) return;
    var col = $(this);
    var index = board.find(".col").index(col);
    if(GAME.getNumSpaces(index) <= 0){
      return;
    }
    lock();
    if (GAME.GAME_TYPE == "AI") {
      var ai_ajax_complete = false, piece_drop_complete = false, ai_ajax_move = null, ai_won = false, player_won = false;
        function ifDoneThenAIMove(){
          if (ai_ajax_complete && piece_drop_complete){
            //move AI
            function onAIMove(){
              if(ai_won){
                showWon();
              } else {
                unlock()
              }
            }
            that.dropPiece(GAME.getCurPlayer(), ai_ajax_move, onAIMove);
            ai_won = GAME.placePiece(ai_ajax_move);
            console.log("Value of ai_won " + ai_won);
          }
          if (player_won){
            showWon();
          }
        }

        //Not sure if dropPiece or ajax call will finish first, both must be finished before board can unlock
        that.dropPiece(GAME.getCurPlayer(), index, function(){ piece_drop_complete = true; ifDoneThenAIMove(); });
        player_won = GAME.placePiece(index);
        if(!player_won){
          GAME.AIGetMove( function(move) { ai_ajax_move = move; ai_ajax_complete = true; ifDoneThenAIMove(); });
        } 

      } else {
        var player_won = false;
        function switchPlayer() {
          if (player_won) {
            showWon();
          } else {
            unlock();
          }
        }
        that.dropPiece(GAME.getCurPlayer(), index, switchPlayer);
        player_won = GAME.placePiece(index);
      }
    });
    return that;
}());
