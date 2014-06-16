
Optimized space by passing array as reference.
Optimized by caching space location in an array so additional array traversal is not needed when doing/undoing moves.

Eventually:
- Optimize further by using a single dimensional array (or by flipping columns and rows so cache hits are more probable)
