<?php
// Nichole Maldonado
// Lab 1 - PieceColor
// September 7, 2020
// Dr. Cheon, CS3360
// Denotes the "piece color" of the board saved to the file. Essentially, if the
// board has a zero, then the slot is considered empty. If the board has a 1, then the slot is
// considered to have a user's piece. If the board has a 2, then the slot is considered to
// have a computer's piece.

/*
 * Denotes the piece color of the connect four board save to the file.
 */
interface PieceColor {
    const EMPTY = 0;
    const USER = 1;
    const COMPUTER = 2;
}

?>