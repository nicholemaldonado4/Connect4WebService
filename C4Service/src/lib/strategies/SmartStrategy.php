<?php
// Nichole Maldonado
// Lab 1 - SmartStrategy
// September 7, 2020
// Dr. Cheon, CS3360
// Evaluates putting a piece at the top of each column. Tries to make a winning move first. If one does not exist,
// then makes a blocking move. If neither exists, then just makes a regular move.

require_once(__DIR__ . "/MoveStrategy.php");
require_once(dirname(__DIR__)."/validator/MoveRecords.php");
require_once(dirname(__DIR__)."/game/Game.php");
require_once(dirname(__DIR__)."/strategies/Move.php");
require_once(dirname(__DIR__)."/validator/MoveValidator.php");
require_once(dirname(__DIR__)."/validator/ValidatorSettings.php");

/*
 * Evaluates putting a piece at the top of each column. If the computer can win, makes this move. If
 * the computer cannot win but can block the user from winning (user currently has 3 pieces in a row), then the
 * computer makes a blocking move. If the computer cannot make a winning or blocking move, then the computer
 * just moves to a random location.
 */
class SmartStrategy extends MoveStrategy {
    /*
     * Adds piece to board and populates a Move.
     * @param: The moveValidator, height to place the new piece in the $game's
     *         board, the $col to put the new piece, and the $dirStart
     *         [direction, start] if a winning move.
     * @return: None.
     */
    private function getCompMove(MoveValidator $moveValidator, $newHeight, $col, $dir, $start, &$compMove, Game $game) {
        $moveValidator->decrHeightForCol($col);
        $game->addPieceToBoard($newHeight, $col, PieceColor::COMPUTER);
        $compMove = $moveValidator->populateMoveFromDirection($dir, $start, $col);
    }

    /*
     * Add-on of the Smart strategy. Determines if we put the piece in $col, will the user
     * be able to win on the next game.
     * @param: The $moveValidator, $originalHeight to put the piece, the $col, and $game.
     * @return: False if placing a piece in column will allow the user to win next time.
     *          True otherwise.
     */
    private function imaginaryCheck(MoveValidator $moveValidator, $origHeight, $col, Game $game) {
        $moveValidator->decrHeightForCol($col);
        $game->addPieceToBoard($origHeight, $col, PieceColor::COMPUTER);
        $goodMove = true;

        // See that if the computer does put a piece in col, if the user will be
        // able to make a winning move above it.
        $directionStart = $moveValidator->validateMove($game->getBoard(), $col, new ValidatorSettings(false, false, PieceColor::USER));
        if ($directionStart[0] != Direction::NONE) {
            $goodMove = false;
        }


        $game->addPieceToBoard($origHeight, $col, PieceColor::EMPTY);
        $moveValidator->incrHeightForCol($col);
        return $goodMove;
    }

    /*
     * Randomly selects columns to see if they can result in a winning move. While doing so saves
     * block move, no win move, or default move. See MoveRecords for more details.
     * @param: The Game, which contains the board, the current MoveValidator to validate the move,
     *          and the Move $compMove which will store the slot, whether the move was a winning move, a
     *          drawing move, and if the move was a winning move the row for the connected 4 pieces.
     * @return: True if a Move was able to be populated. May return false in the event that a file was tampered
     *          with. For example, the game is missing one piece by the time it is the computer's turn. However,
     *          in between calls, someone tampered the game file and added the last piece. In which case the computer
     *          would realize that all the spots were filled even though at least one empty spot was expected.
     */
    public function pickSlot(Game $game, MoveValidator $moveValidator, &$compMove) {

        // While searching for a winning move, we also keep track of if we can block a user from winning. If we
        // unable to find a winning move, then we use the block move. If we are unable to block, then we just
        // move to any location that we found.
        $records = new MoveRecords();
        $combos = [0,1,2,3,4,5,6];
        $validatorSettings = new ValidatorSettings(true, false, PieceColor::COMPUTER);

        // Repeatedly look at columns for a winning move. Randomize the columns we search for, but unlike random,
        // we will look at all the columns unless we find a winning move.
        for ($i = 0; $i < BoardDimension::WIDTH; $i++) {
            $randIndex = rand(0, BoardDimension::WIDTH - 1 - $i);
            $newHeight = $moveValidator->getHeightForCol($combos[$randIndex]) - 1;

            // Evaluate the position if we can put a piece there.
            if ($newHeight >= 0 && $game->getBoard()[$newHeight][$combos[$randIndex]] == 0) {
                $resultRecord = $moveValidator->validateMove($game->getBoard(), $combos[$randIndex], $validatorSettings);

                // If precedence > records->blockPrecedence(), then save as block request.
                // If precedence == 3, then turn of blockRequest and set block reply to false,
                // If precedence < 3, set blockReply to false.

                // If we requested a blocking move and the block reply is true and the direction is not none,
                // then we know that we found a blocking move, so store it in case we can't find a winning move.
                if ($validatorSettings->getBlockRequest() && $validatorSettings->getBlockReply()) {
                    $validatorSettings->setBlockReply(false);
                    if ($resultRecord[1]->getPrecedence() == Precedence::THREE) {
                        $records->setBlock($resultRecord[1]);
                        // We already found a block so stop requests. (Decreases runtime).
                        $validatorSettings->setBlockRequest(false);
//                        echo "final block</br>";
                    }
                    else if ($resultRecord[1]->getPrecedence() > $records->getBlock()->getPrecedence()) {
                        $records->setBlock($resultRecord[1]);
//                        echo "higher precedence block</br>";
                    }
                }
                // Otherwise, if the direction is not NONE, then the move was a winning move. Make the remove and
                // return.
                else if ($resultRecord[0] != Direction::NONE) {
                    $this->getCompMove($moveValidator, $newHeight, $combos[$randIndex], $resultRecord[0],
                            $resultRecord[1], $compMove, $game);
                    return true;
                }

                // From here rather than foundNoWin, instead check to see if precdence > foundNoWin's precedence.

                // If we have not found a blocking move or a winning move, then store the move. In the worst case,
                // we will make this move.
                else if ($resultRecord[1]->getPrecedence() > $records->getNoWin()->getPrecedence()) {
//                    echo "looking at precedence</br>";
                    if ($newHeight > 0 && !$this->imaginaryCheck($moveValidator, $newHeight, $combos[$randIndex], $game)) {
//                        echo "Imaginary check did not work</br>";
                        if ($resultRecord[1]->getPrecedence() > $records->getDefault()->getPrecedence()) {
//                            echo "setting default";
                            $records->setDefault($resultRecord[1]);
                        }
                    }
                    else {
//                        echo "Setting no win</br>";
                        $records->setNoWin($resultRecord[1]);
                    }
                }
            }
            // Replace with last for O(1) deletions.
            $combos[$randIndex] = $combos[BoardDimension::WIDTH - 1 - $i];
            array_pop($combos);
        }

        // Block move has priority over no win move and default move.
        $recordNum = $records->getHighestPriorityRecord();
        if ($recordNum != -1) {
            $newHeight = $moveValidator->getHeightForCol($records->getRecordCol($recordNum)) - 1;
            $this->getCompMove($moveValidator, $newHeight, $records->getRecordCol($recordNum), Direction::NONE,
                $records->getRecordCol($recordNum), $compMove, $game);
            return true;
        }
        return false;
    }
}
?>