Features
========
- Min-max optimized by caching space location of moves made, so doing/undoing a move is constant

Working on
==========
- More phpunit tests
- Experimenting with different features to see how much I can optimize and how it effects the running time
- Rewriting C4AI in C

Eventually
==========
- Optimize further by using a single dimensional array (or by flipping columns and rows so cache hits are more probable)
- Optimize scoring by placing depth limit in loop and only calculating score difference and updating
- Clean up code, make C4AI static class
