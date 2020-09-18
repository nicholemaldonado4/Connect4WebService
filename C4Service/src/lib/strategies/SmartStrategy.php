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
     * @param: The moveValidator, height to place the new piece in the $game's board, the $col to put the new
     *         piece, and the $dirStart [direction, start] if a winning move.
     * @return: None.
     */
    private function getCompMove(MoveValidator $moveValidator, $newHeight, $col, $dirStart, &$compMove, Game $game) {
        $moveValidator->decrHeightForCol($col);
        $game->addPieceToBoard($newHeight, $col, PieceColor::COMPUTER);
        $compMove = $moveValidator->populateMoveFromDirection($dirStart[0], $dirStart[1], $col);
    }

    private function imaginaryCheck(MoveValidator $moveValidator, $origHeight, $col, Game $game) {
        $moveValidator->decrHeightForCol($col);
        $game->addPieceToBoard($origHeight, $col, PieceColor::COMPUTER);
        $newHeight = $moveValidator->getHeightForCol($col) - 1;
        $goodMove = true;

        // Evaluate the position if we can put a piece there.
        if ($newHeight >= 0 && $game->getBoard()[$newHeight][$col] == 0) {
            $directionStart = $moveValidator->validateMove($game->getBoard(), $col, new ValidatorSettings(false, false, PieceColor::USER));
            if ($directionStart[0] != Direction::NONE) {
                echo "Direction does not equal none";
                $goodMove = false;
            }
        }

        $game->addPieceToBoard($origHeight, $col, PieceColor::EMPTY);
        $moveValidator->incrHeightForCol($col);
        echo "good move: ".($goodMove ? "true" : "false")."</br>";
        return $goodMove;
    }

    /*
     * Randomly selects columns to see if they can result in a winning move or if the user can blocked from winning.
     * The computer looks for a block move and winning move. If it finds a winning move, it immediately makes the move
     * and returns. If it finds a block move, then the computer stores the move and waits until all the columns have
     * been evaluated or a winning move has been found. The computer also stores a regular move in the event that
     * a winning move or blocking move could not be found. Populates a Move with the move.
     * @param: The Game, which contains the board, the current MoveValidator to validate the move,
     *          and the Move $compMove which will store the slot, whether the move was a winning move, a
     *          drawing move, and if the move was a winning move the row for the connected 4 pieces.
     * @return: True if a Move was able to be populated. May return false in the event that a file was tampered
     *          with. For example, the game is missing one piece by the time it is the computer's turn. However,
     *          in between calls, someone tampered the game file and added the last piece. In which case the computer
     *          would realize that all the spots were filled even though at least one empty spot was expected.
     */
    public function pickSlot(Game $game, MoveValidator $moveValidator, &$compMove) {
        echo "beginning of smart</br>";
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
                $directionStart = $moveValidator->validateMove($game->getBoard(), $combos[$randIndex], $validatorSettings);

                // If we requested a blocking move and the block reply is true and the direction is not none,
                // then we know that we found a blocking move, so store it in case we can't find a winning move.
                if ($validatorSettings->getBlockRequest() && $validatorSettings->getBlockReply()) {
                    $records->setBlock($directionStart, $combos[$randIndex]);
                    // We already found a block so stop requests. (Decreases runtime).
                    $validatorSettings->setBlockRequest(false);
                    $validatorSettings->setBlockReply(false);
                }
                // Otherwise, if the direction is not NONE, then the move was a winning move. Make the remove and
                // return.
                else if ($directionStart[0] != Direction::NONE) {
                    $this->getCompMove($moveValidator, $newHeight, $combos[$randIndex], $directionStart, $compMove, $game);
                    return true;
                }
                // If we have not found a blocking move or a winning move, then store the move. In the worst case,
                // we will make this move.
                else if (!$records->foundNoWin()) {
                    echo "In else if";
                    if ($this->imaginaryCheck($moveValidator, $newHeight, $combos[$randIndex], $game)) {
                        echo "setting nowin</br>";
                        $records->setNoWin(array(Direction::NONE, $combos[$randIndex]), $combos[$randIndex]);
                    }
                    else if (!$records->foundDefault()) {
                        echo "setting default</br>";
                        $records->setDefault(array(Direction::NONE, $combos[$randIndex]), $combos[$randIndex]);
                    }
                }
            }
            // Replace with last for O(1) deletions.
            $combos[$randIndex] = $combos[BoardDimension::WIDTH - 1 - $i];
            array_pop($combos);
        }

        // Block move has priority over win move.
        $recordNum = $records->getRecord();
        if ($recordNum != -1) {
            $newHeight = $moveValidator->getHeightForCol($records->getRecordCol($recordNum)) - 1;
            $this->getCompMove($moveValidator, $newHeight, $records->getRecordCol($recordNum),
                $records->getRecordDirectionSlot($recordNum), $compMove, $game);
            return true;
        }
        return false;
    }


//    public function pickSlot(Game $game, MoveValidator $moveValidator, &$compMove) {
//        // While searching for a winning move, we also keep track of if we can block a user from winning. If we
//        // unable to find a winning move, then we use the block move. If we are unable to block, then we just
//        // move to any location that we found.
//        $noWinMoves = array("block"=> array(), "noWin" => array(), "col" => -1);
//        $backup = array("helpsWin" => array(), "col" => -1);
//        $combos = [0,1,2,3,4,5,6];
//        $validatorSettings = new ValidatorSettings(true, false, PieceColor::COMPUTER);
//
//        // Repeatedly look at columns for a winning move. Randomize the columns we search for, but unlike random,
//        // we will look at all the columns unless we find a winning move.
//        for ($i = 0; $i < BoardDimension::WIDTH; $i++) {
//            $randIndex = rand(0, BoardDimension::WIDTH - 1 - $i);
//            $newHeight = $moveValidator->getHeightForCol($combos[$randIndex]) - 1;
//
//            // Evaluate the position if we can put a piece there.
//            if ($newHeight >= 0 && $game->getBoard()[$newHeight][$combos[$randIndex]] == 0) {
//                $directionStart = $moveValidator->validateMove($game->getBoard(), $combos[$randIndex], $validatorSettings);
//
//                // If we requested a blocking move and the block reply is true and the direction is not none,
//                // then we know that we found a blocking move, so store it in case we can't find a winning move.
//                if ($validatorSettings->getBlockRequest() && $validatorSettings->getBlockReply()) {
//                    $noWinMoves["block"] = $directionStart;
//                    $noWinMoves["col"] = $combos[$randIndex];
//                    // We already found a block so stop requests. (Decreases runtime).
//                    $validatorSettings->setBlockRequest(false);
//                    $validatorSettings->setBlockReply(false);
//                }
//                // Otherwise, if the direction is not NONE, then the move was a winning move. Make the remove and
//                // return.
//                else if ($directionStart[0] != Direction::NONE) {
//                    $this->getCompMove($moveValidator, $newHeight, $combos[$randIndex], $directionStart, $compMove, $game);
//                    return true;
//                }
//                // If we have not found a blocking move or a winning move, then store the move. In the worst case,
//                // we will make this move.
//                else if ($noWinMoves["col"] == -1) {
//
//                    if ($this->imaginaryCheck($moveValidator, $newHeight, $combos[$randIndex], $game)) {
//                        $noWinMoves["col"] = $combos[$randIndex];
//                        $noWinMoves["noWin"] = array(Direction::NONE, $combos[$randIndex]);
//                    }
//                    else if ($backup["col"] == -1) {
//                        $backup["col"] = $combos[$randIndex];
//                        $backup["noWin"] = array(Direction::NONE, $combos[$randIndex]);
//                    }
//                }
//            }
//            // Replace with last for O(1) deletions.
//            $combos[$randIndex] = $combos[BoardDimension::WIDTH - 1 - $i];
//            array_pop($combos);
//        }
//
//        // Block move has priority over win move.
//        if (sizeOf($noWinMoves["block"]) > 0) {
//            $newHeight = $moveValidator->getHeightForCol($noWinMoves["col"]) - 1;
//            $this->getCompMove($moveValidator, $newHeight, $noWinMoves["col"], $noWinMoves["block"], $compMove, $game);
//            return true;
//        }
//        if ($noWinMoves["col"] != -1) {
//            $newHeight = $moveValidator->getHeightForCol($noWinMoves["col"]) - 1;
//            $this->getCompMove($moveValidator, $newHeight, $noWinMoves["col"], $noWinMoves["noWin"], $compMove, $game);
//            return true;
//        }
//        if ($backup["col"] != -1) {
//            $newHeight = $moveValidator->getHeightForCol($backup["col"]) - 1;
//            $this->getCompMove($moveValidator, $newHeight, $backup["col"], $backup["helpsWin"], $compMove, $game);
//            return true;
//        }
//        return false;
//    }
}
?>