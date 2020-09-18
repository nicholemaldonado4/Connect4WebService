<?php
// Nichole Maldonado
// Lab 1 - Game
// September 7, 2020
// Dr. Cheon, CS3360
// Game class stores a board and denotes whether the game is utilizing a smart strategy or random strategy.
// Game also states if the game has already been completed.

require_once(__DIR__."/BoardDimension.php");
require_once(dirname(__DIR__) . "/strategies/MoveStrategy.php");
require_once(dirname(__DIR__)."/strategies/SmartStrategy.php");
require_once(dirname(__DIR__)."/strategies/RandomStrategy.php");

/*
 * Game class stores a board, denotes whether the game is utilizing a smart strategy or random strategy,
 * and states if the game has already been completed. Provides functions to change the board.
 */
class Game implements JsonSerializable, BoardDimension {
    private array $board;
    private MoveStrategy $strategy;

    /*
     * Implements jsonSerialize since the fields are private (can't use json_encode
     * on private fields unless we implement this method).
     * @param: None
     * @return: All the variables ($board, $isSmart, $gameDone).
     */
    public function jsonSerialize() {
        return get_object_vars($this);
    }

    /*
     * Setter for the field $strategy.
     * @param: The MoveStrategy.
     * @return: None.
     */
    public function setStrategy($strategy) {
        $this->strategy = $strategy;
    }

    /*
     * Setter for the field $board.
     * @param: The boolean value that the field will be assigned to.
     * @return: None.
     */
    public function setBoard($board) {
        $this->board = $board;
    }

    /*
     * Getter for the field $gameDone.
     * @param: None.
     * @return: The 2d $board.
     */
    public function getBoard() {
        return $this->board;
    }

    /*
     * Getter for the field $strategy.
     * @param: None.
     * @return: The MoveStrategy for $strategy.
     */
    public function getStrategy() {
        return $this->strategy;
    }

    /*
     * Builds and returns a game with a blank board.
     * @param: The name of the strategy ("Smart" or "Random").
     * @return: The game created.
     */
    public static function buildInitialGame($strategy) {
        $board = array();
        for($i=0; $i < BoardDimension::HEIGHT; $i++){
            $board[$i] = array();
            for($j=0; $j < BoardDimension::WIDTH; $j++){
                $board[$i][$j] = 0;
            }
        }
        return self::buildGame($board, $strategy);
    }

    /*
    * Builds and returns a game with $board set.
    * @param: The name of the strategy ("Smart" or "Random").
    * @return: The game created.
    */
    public static function buildGame(array $board, $strategy) {
        $game = new self();
        $game->setStrategy(new $strategy());
        $game->setBoard($board);
        return $game;
    }

    /*
    * Adds a piece of $pieceColor to the board at the $row and $col.
    * @param: The $row and $col to place the piece colored $pieceColor.
    * @return: None.
    */
    public function addPieceToBoard($row, $col, $pieceColor) {
        $this->board[$row][$col] = $pieceColor;
    }
}
?>