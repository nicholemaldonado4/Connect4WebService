<?php
// Nichole Maldonado
// Lab 1 - RandomStrategy
// September 7, 2020
// Dr. Cheon, CS3360
// RandomStrategy randomly selects a column to place a connect four piece and returns the
// resulting Move based on the move.

require_once(__DIR__ . "/MoveStrategy.php");
require_once(dirname(__DIR__)."/game/Game.php");
require_once(dirname(__DIR__) . "/strategies/Move.php");
require_once(dirname(__DIR__)."/validator/MoveValidator.php");
require_once(dirname(__DIR__)."/validator/ValidatorSettings.php");

/*
 * RandomStrategy randomly selects a column to place a connect four piece and returns the
 * resulting Move based on the move.
 */
class RandomStrategy extends MoveStrategy {

    /*
     * Randomly picks a column on the board to put the piece. Puts the piece on the board and
     * Populates the Move compMove with the stats of the move.
     * @param: The Game, which contains the board, the current MoveValidator to validate the move,
     *          and the Move $compMove which will store the slot, whether the move was a winning move, a
     *          drawing move, and if the move was a winning move the row for the connected 4 pieces.
     * @return: True if a Move was able to be populated. May return false in the event that a file was tampered
     *          with. For example, the game is missing one piece by the time it is the computer's turn. However,
     *          in between calls, someone tampered the game file and added the last piece. In which case the computer
     *          would realize that all the spots were filled even though at least one empty spot was expected.
     */
    public function pickSlot(Game $game, MoveValidator $moveValidator, &$compMove) {
        $combos = [0,1,2,3,4,5,6];

        // Continuously pick a random index from $combos, until we find a column that we can put a piece in.
        // We use combos to ensure that we never look for the same height twice.
        for ($i = 0; $i < BoardDimension::WIDTH; $i++) {
            $randIndex = rand(0, BoardDimension::HEIGHT - $i);
            $newHeight = $moveValidator->getHeightForCol($combos[$randIndex]) - 1;

            // If we found where we can put a piece we validate the move (see if it results in a win),
            // add the piece to the board, update the current heights, and create a new Move.
            // Once we find a place to put the piece we return.
            if ($newHeight >= 0 && $game->getBoard()[$newHeight][$combos[$randIndex]] == 0) {
                list($direction, $start) = $moveValidator->validateMove($game->getBoard(),
                    $combos[$randIndex], new ValidatorSettings(false, false,
                        PieceColor::COMPUTER));
                $moveValidator->decrHeightForCol($combos[$randIndex]);
                $game->addPieceToBoard($newHeight, $combos[$randIndex], PieceColor::COMPUTER);
                $compMove = $moveValidator->populateMoveFromDirection($direction, $start, $combos[$randIndex]);
                return true;
            }
            // Replace with last for O(1) deletions.
            $combos[$randIndex] = $combos[BoardDimension::HEIGHT - $i];
            array_pop($combos);
        }
        return false;
    }
}
?>