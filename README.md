# Connect 4 Web Service

This code provides a web service for Connect 4. Users can play
against a computer with a "random" or "smart" strategy. The first to
get 4 discs in a row either vertically, horizontally, or diagonally wins.

The "random" strategy allows the computer to randomly select moves.
The "smart" strategy allows the computer to make "smart" decisions
to try to prevent the user from winning.

To communicate with the web server and play against a computer,
the client must use an HTTP query string. The response will be displayed
in the format of JSON.

Following the REST principles, the web service provides the following three URLs:

1. http://<c4-home>/info,
   where <c4-home> is the address of your Connect Four service
   typically consisting of a host name and a pathname. The server will
   provide the board's width, height, and strategies available. For example:

   {"width":7,"height":6,"strategies":["Smart","Random"]}

2. http://<c4-home>/new?strategy=smart
   Create a new game to play against the specified computer strategy.
   A normal response will be a JSON string like:

     {"response":true,"pid":"57cdc4815e1e5"}
     
   where pid is a unique play identifier generated by the web service. 
   It will be used to play the newly created game

3. http://<c4-home>/play?pid=p&move=x
   Make a move by dropping a disc in the specified column, x, to play
   the specified game, p. Example: .../play/?pid=57cdc4815e1e5&move=3.

   A normal response will be a JSON string like:
     
     {"response": true,
      "ack_move": {
        "slot": 3, 
        "isWin": false,   // winning move?
        "isDraw": false,  // draw?
        "row": []},       // winning row if isWin is true
      "move": {
        "slot": 4, 
        "isWin": false, 
        "isDraw": false, 
        "row": []}}

   where "ack_move" is the acknowledgement and the outcome of the
   requested move of the player, and "move" is the computer move made
   right after the player's; there will be no computer move if the
   player move is a game-ending (win or draw) move. For a winning
   move, the value of "row" is an array of numbers denoting the
   indices of the winning row [x1,y1,x2,y2,...,xn,yn], where x's and
   y's are 0-based column and row indices of places, e.g.,

     "row":[0,5,1,5,2,5,3,5]


# Instructions

Start the PHP server and access the following pages, in order:

Step 1: visit http://<c4-home>/info to find game info\
Step 2: visit http://<c4-home>/new?strategy=s to create a new game\
Step 3: repeatedly visit http://<c4-home>/play?pid=p&move=x\
        to drop a disc
