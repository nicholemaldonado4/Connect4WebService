<?php
// Nichole Maldonado
// Lab 1 - Move
// September 7, 2020
// Dr. Cheon, CS3360
// GameBuilder stores the $slot, $isWin, $isDraw, and $row based on a move.

/*
 * Move stores the slot the piece was inserted in, whether or not the game has resulted in a win,
 * if the game has resulted in a draw, and if the game resulted in a win, the rows and columns of the
 * four pieces.
 */
class Move implements JsonSerializable {
    private int $slot;
    private bool $isWin;
    private bool $isDraw;
    private array $row;

    /*
     * Constructor for Move.
     * @param: The $slot, $isWin, $isDraw, and array $rows to populate the Move object.
     * @return: A new Move object based on the arguments passed in.
     */
    function __construct($slot, $isWin, $isDraw, $rows){
        $this->slot = $slot;
        $this->isWin = $isWin;
        $this->isDraw = $isDraw;
        $this->row = $rows;
    }

    /*
     * Getter for the field $isWin.
     * @param: None.
     * @return: The boolean value assigned to $isWin.
     */
    public function getIsWin() {
        return $this->isWin;
    }

    /*
     * Getter for the field $isWin.
     * @param: None.
     * @return: The boolean value assigned to $isDraw.
     */
    public function getIsDraw() {
        return $this->isDraw;
    }

    /*
     * Implements jsonSerialize since the fields are private (can't use json_encode
     * on private fields unless we implement this method).
     * @param: None
     * @return: All the variables ($slog, $isWin, $isDraw, $row).
     */
    public function jsonSerialize() {
        return get_object_vars($this);
    }
}
?>