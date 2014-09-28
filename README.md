# Connect Four AI #
This repo contains:
- Connect Four AI API written in PHP
- Implemented front-end game in html/javascript

You can see a live demo here: [http://kevinalbs.com/connect4/](http://kevinalbs.com/connect4/).

The simplistic API accepts a board state and replies with a list of possible moves and their scores (calculated using the minimax algorithm and a heuristic for the board score).
Information about using the API is here [http://kevinalbs.com/connect4/back-end/](http://kevinalbs.com/connect4/back-end/).

Working on
==========
- Phpunit tests
- Experimenting with different features to see how much I can optimize and how it effects the running time
- Rewriting C4AI in C
- Further optimize by using a single dimensional array (or by flipping columns and rows so cache hits are more probable)
- Optimize scoring by placing depth limit in loop and only calculating score difference and updating
