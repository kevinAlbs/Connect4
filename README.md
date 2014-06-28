Finished
========
- Optimized space by passing array as reference.
- Optimized by caching space location in an array so additional array traversal is not needed when doing/undoing moves

Now
===
- I need to decide how I want to release this. Having everything on the API side will be detrimental to the running time of my front-end (having to do requests for win-checking). But if I have some front-end and some back-end I'll end up writing some redundant code. Here are my options:
	- <b>Have just the AI as an API, nothing else.</b> This will result in some redundant code on the front-end, but it will be a simple and straightforward API.
	- <b>Flesh out the API to include board win checks</b> <- leaning towards this, animation of the chip falling will give enough time for requests to send anyway. I can also rewrite the hasWon method in JS to reduce the API calls. But it will at least make the API more full-fledged.
	- <b>Rewrite what I have to be entirely in JS</b> Could potentially slow the running time, also makes it less available to other programs. I'd rather rewrite in C.
- Create API, have it return all values for moves (so user can choose random of equal value moves)

Eventually
==========
- Optimize further by using a single dimensional array (or by flipping columns and rows so cache hits are more probable)
- Optimize scoring by placing depth limit in loop and only calculating score difference and updating
- Clean up code, make C4AI static class
