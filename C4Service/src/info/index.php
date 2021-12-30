<?php
// Nichole Maldonado
// Lab 1 - info/index.php
// September 7, 2020
// Main page for info that prints out the size of the game board
// and the strategies available.

require_once dirname(__DIR__)."/lib/game/BoardDimension.php";
require_once dirname(__DIR__)."/lib/strategies/StrategyType.php";

/*
 * Stores the width and height of the board. Also stores the available strategies
 * that a user can implement. NOTE: We only use this class to json_encode.
 * The constants defined will be implemented by the classes that want to use them.
 * This is crucial because it would be wasteful to create new class each time we want
 * to access some constant.
 */
class GameInfo {
    public int $width;
    public int $height;
    public array $strategies;

    /*
     * Default constructor that assigns $strategies, $width, and $height to const values.
     * @param: None.
     * @return: None.
     */
    public function __construct($width, $height, $strategies) {
        $this->width = $width;
        $this->height = $height;
        $this->strategies = $strategies;
    }
}

// Encode a GameInfo object and display it the screen.
$gameInfo = new GameInfo(BoardDimension::WIDTH, BoardDimension::HEIGHT, array_keys(StrategyType::STRATEGIES));
echo json_encode($gameInfo);
?>
