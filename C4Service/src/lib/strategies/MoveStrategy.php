<?php
// Nichole Maldonado
// Lab 1 - Strategies
// September 7, 2020
// Dr. Cheon, CS3360
// Abstract class for strategies that use execute to make a computer move
// for connect four.

require_once(dirname(__DIR__)."/game/BoardDimension.php");
require_once(dirname(__DIR__)."/game/Game.php");
require_once(dirname(__DIR__)."/validator/PieceColor.php");
require_once(dirname(__DIR__)."/validator/Direction.php");
require_once(dirname(__DIR__)."/validator/MoveValidator.php");

abstract class MoveStrategy implements BoardDimension, PieceColor, Direction, JsonSerializable {

    /*
     * Pieck the slot to move the connect four piece.
     * @param: The Game with the board, the MoveValidator to verify the move, and the Move to
     *         be populated.
     * @return: None.
     */
    abstract public function pickSlot(Game $game, MoveValidator $moveValidator, &$compMove);

    /*
     * Serialize a MoveStrategy by assigning name to the class name.
     * @param: None.
     * @return: An associated array with a key 'name' and value of the class name.
     */
    public function jsonSerialize() {
        return array('name'=>get_class($this));
    }
}
?>