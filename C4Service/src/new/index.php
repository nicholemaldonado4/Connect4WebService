<?php
// Nichole Maldonado
// Lab 1 - new/index.php
// September 7, 2020
// Dr. Cheon, CS3360
// Main page for new that creates a new game and stores the game in <pid>.txt, where pid is a uniquely
// calculated pid and stores the strategy denoted based on the html query. Does this by
// calling GameWriter's createGame.

require_once dirname(__DIR__)."/lib/game/GameWriter.php";

// Create game and display creation (or error if it occurs).
$gameWriter = new GameWriter;
$gameWriter->createGame();
?>