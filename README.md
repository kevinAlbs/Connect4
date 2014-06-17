Finished
========
- Optimized space by passing array as reference.
- Optimized by caching space location in an array so additional array traversal is not needed when doing/undoing moves

Now
===
- Scoring does not take into account skips, e.g. 1 1 0 1, that zero is the best move for 1

Eventually
==========
- Optimize further by using a single dimensional array (or by flipping columns and rows so cache hits are more probable)
- Optimize scoring by placing depth limit in loop and only calculating score difference and updating
