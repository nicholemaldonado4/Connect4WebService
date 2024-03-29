<?php
// Nichole Maldonado
// Lab 1 - MoveValidator
// September 7, 2020
// Dr. Cheon, CS3360
// Verifies if a move could result in a winning move, and if requested a blocking move.
// Also provides the functionality based to create a Move based on the move. Used to
// process moves.

require_once dirname(__DIR__)."/strategies/Move.php";
require_once dirname(__DIR__)."/game/BoardDimension.php";
require_once __DIR__."/Direction.php";
require_once __DIR__."/ValidatorSettings.php";
require_once __DIR__."/HorizontalStrategy.php";
require_once __DIR__."/LeftDiagonalStrategy.php";
require_once __DIR__."/RightDiagonalStrategy.php";
require_once __DIR__."/PieceColor.php";
require_once __DIR__."/Precedence.php";
require_once __DIR__."/Record.php";

/*
 * Verifies if a move could result in a winning move, and if requested a blocking move.
 * Also provides the functionality based to create a Move based on the move.
 */
class MoveValidator implements BoardDimension, PieceColor, Direction {
    private array $heights;

    /*
     * Constructor that populates $heights with the row number that each column of pieces is currently at.
     * For example, if column 3 of the board has 5 pieces, the $heights[4] = 1.
     * @param: The $board whose heights of pieces will be calculated for each column.
     * @return: None.
     */
    public function __construct(array $board) {
        $this->heights = array();
        for ($col = 0; $col < BoardDimension::WIDTH; $col++) {
            $row = 0;
            while ($row < BoardDimension::HEIGHT && $board[$row][$col] == PieceColor::EMPTY) {
                $row++;
            }
            $this->heights[$col] = $row;
        }
    }

    /*
     * Gets the height for the given $col.
     * @param: The $col.
     * @return: The height for the $col (where the topmost piece for the $col is located on the board).
     */
    public function getHeightForCol($col) {
        return $this->heights[$col];
    }

    /*
     * Decrements the height at $col to signify that a piece was added for $col.
     * @param: The $col.
     * @return: None.
     */
    public function decrHeightForCol($col) {
        $this->heights[$col]--;
    }

    /*
     * Decrements the height at $col to signify that a piece was added for $col.
     * @param: The $col.
     * @return: None.
     */
    public function incrHeightForCol($col) {
        $this->heights[$col]++;
    }

    /*
     * Counts the number of $pieceColor pieces from the top of the $col down until at least three consecutive same
     * colored pieces are found or a different piece color is found.
     * @param: The number of same colored, consecutive pieces.
     * @return: None.
     */
    private function verticalMoveHelper(array $board, $col, $pieceColor) {
        $row = $this->heights[$col];
        $count = 1;

        // Count the number of same colored pieces until we find 3 or a different piece color.
        while ($row < BoardDimension::HEIGHT && $count < 4 && $board[$row][$col] == $pieceColor) {
            $row++;
            $count++;
        }

        // Map to precedence.
        return Precedence::getPrecedence($count);
    }

    /*
     * Get the number of consecutive, same colored pieces that exist vertically down the column.
     * If a block is requested and the four consecutive, same colored pieces were not found,
     * recalls to see if four consecutive, user color pieces exist.
     * @param: The $board, $col, and $validatorSettings with the piece color.
     * @return: Returns the $count which maps to the precedence.
     */
    private function verticalMove(array $board, $col, ValidatorSettings $validatorSettings) {
        $precedence = $this->verticalMoveHelper($board, $col, $validatorSettings->getPieceColor());

        // If the precedence was 1, then we know that the first piece down did not match the pieceColor. If a block
        // was requested, check to see a column of three user color pieces exist.
        if ($precedence <= Precedence::ONE && $validatorSettings->getBlockRequest()) {
            $validatorSettings->togglePieceColor();
            $blockPrecedence = $this->verticalMoveHelper($board, $col, $validatorSettings->getPieceColor());
            $validatorSettings->togglePieceColor();

            // If three consecutive, user color pieces were found, set BlockReply to true.
            if ($blockPrecedence == Precedence::FOUR) {
                $validatorSettings->setBlockReply(true);
            }
        }
        return $precedence;
    }

    /*
     * Radiates outward in a direction specified by $moveStrat.
     * @param: The board, piece color, and the move strategy that will be applied.
     * @return: The count of consecutive same colored pieces.
     */
    private function rippleMoveHelper(array $board, $pieceColor, HorizontalStrategy $moveStrat) {
        $count = 1;

        // Radiate outwards looking for same colored pieces.
        while ($moveStrat->compareBoth() &&
                $moveStrat->getFromPt1($board) == $moveStrat->getFromPt2($board) &&
                $moveStrat->getFromPt1($board) == $pieceColor && $count < 4) {
            $moveStrat->updateBoth();
            $count+=2;
        }
        if ($count >= 4) {
            return Precedence::FOUR;
        }

        // If we can evaluate the pieces more to the left or more to the right, then do so.
        while ($moveStrat->comparePt1() && $moveStrat->getFromPt1($board) == $pieceColor &&
                $count < 4) {
            $moveStrat->updatePt1();
            $count++;
        }
        while ($moveStrat->comparePt2() && $moveStrat->getFromPt2($board) == $pieceColor &&
                $count < 4) {
            $moveStrat->updatePt2();
            $count++;
        }
        return Precedence::getPrecedence($count);
    }

    /*
     * Get the number of consecutive, same colored pieces that exist based on the move strategy.
     * If a block is requested and four consecutive, same colored pieces were not found,
     * recalls to see if four consecutive, user color pieces exist.
     * @param: The $board, $col, $validatorSettings with the piece color, and $moveStrat.
     * @return: Returns the $count which maps to the precedence.
     */
    private function rippleMove(array $board, ValidatorSettings $validatorSettings,
                                HorizontalStrategy $moveStrat, $col) {
        $precedence = $this->rippleMoveHelper($board, $validatorSettings->getPieceColor(), $moveStrat);

        //  If a block was requested and we could not find a regular win move,
        // check to see a block move could occur.
        if ($precedence < Precedence::FOUR && $validatorSettings->getBlockRequest()) {
            $validatorSettings->togglePieceColor();
            $moveStrat->reset($col, $this->heights[$col] - 1);
            $blockPrecedence = $this->rippleMoveHelper($board, $validatorSettings->getPieceColor(), $moveStrat);
            $validatorSettings->togglePieceColor();

            // If found a move, set block reply to true.
            if ($blockPrecedence == Precedence::FOUR) {
                $validatorSettings->setBlockReply(true);
            }

            // If the count is three, then we know that the user is trying to build a set of three.
            // So set the precedence based on whether the next move would be a fall through.
            else if ($blockPrecedence == Precedence::THREE && $precedence < $blockPrecedence) {
                $precedence = ($moveStrat->checkFallThrough($this->heights)) ?
                        Precedence::TWO_LARGE : Precedence::TWO_HALF;
            }
        }
        return $precedence;
    }

    /*
     * Creates an array of (col,row, col, row + 1, ....) for four pieces.
     * @param: The $col where the top four pieces are arranged vertically.
     * @return: The array of the columns and rows of the top four pieces
     *          arranged vertically.
     */
    private function buildVerticalRows($col) {
        $rows = array();
        for ($i = $this->heights[$col]; $i < $this->heights[$col] + 4; $i++) {
            $rows[] = $col;
            $rows[] = $i;
        }
        return $rows;
    }

    /*
     * Creates an array of (col,row, col + 1, row, ....) for four pieces.
     * @param: The starting $col where the four pieces are arranged
     *         horizontally (left to right) and the $row.
     * @return: The array of the columns and rows of the four pieces arranged horizontally.
     */
    private function buildHorizontalRows($col, $row) {
        $rows = array();
        for ($i = $col; $i < $col + 4; $i++) {
            $rows[] = $i;
            $rows[] = $row;
        }
        return $rows;
    }

    /*
     * Creates an array of (col,row, col + 1, row + 1, ....) for four pieces.
     * @param: The starting $col where the four pieces are arranged
     *         diagonally to the right and the $row.
     * @return: The array of the columns and rows of the four pieces arranged diagonally to the right.
     */
    private function buildRightDiagRows($col, $row) {
        $rows = array();
        for ($i = $col; $i < $col + 4; $i++, $row++) {
            $rows[] = $i;
            $rows[] = $row;
        }
        return $rows;
    }

    /*
     * Creates an array of (col,row, col - 1, row - 1, ....) for four pieces.
     * @param: The starting $col where the four pieces are arranged diagonally
     *         to the left and the $row.
     * @return: The array of the columns and rows of the four pieces arranged diagonally to the left.
     */
    private function buildLeftDiagRows($col, $row) {
        $rows = array();
        for ($i = $col; $i < $col + 4; $i++, $row--) {
            $rows[] = $i;
            $rows[] = $row;
        }
        return $rows;
    }

    /*
     * Checks the heights to see if the board is completely filled.
     * @param: None.
     * @return: True if the board is completely filled, false otherwise.
     */
    private function checkDraw() {
        for ($i = 0; $i < BoardDimension::WIDTH; $i++) {
            if ($this->heights[$i] != 0) {
                return false;
            }
        }
        return true;
    }

    /*
     * Populates a Move. If $direction != NONE, then a win occurred and
     * row will be populated based on the $direction (starting from $col).
     * @param: The $direction and $start. If $direction is not NONE, then
     *         populate $row from start and based on direction. Also $col
     *         is the selected slot.
     * @return: The Move.
     */
    public function populateMoveFromDirection($direction, $start, $col) {
        $row = [];
        $isWin = true;

        // HORIZONTAL, LEFT_DIAG, and RIGHT_DIAG, all require the height of the
        // $col to populate the row.
        switch ($direction) {
            case Direction::VERTICAL:
                $row = $this->buildVerticalRows($start);
                break;
            case Direction::HORIZONTAL:
                $row = $this->buildHorizontalRows($start, $this->heights[$col]);
                break;
            case Direction::LEFT_DIAG:
                $row = $this->buildLeftDiagRows($start, ($this->heights[$col] + ($col - $start)));
                break;
            case Direction::RIGHT_DIAG:
                $row = $this->buildRightDiagRows($start, ($this->heights[$col] - ($col - $start)));
                break;
            case Direction::NONE:
                $isWin = false;
        }
        $isDraw = false;
        if (!$isWin) {
            $isDraw = $this->checkDraw();
        }
        return new Move($col, $isWin, $isDraw, $row);
    }

    /*
     * Validates if a piece added at $col will lead to win. If a block is requested and found, then
     * will reply that the move will result in a block, if the move will not result in a win.
     * @param: None.
     * @return: [direction, start] if direction != NONE. Means that the move is a winning move.
     *          start denotes where we should populate the row of winning col,row pairs.
     *          Otherwise returns [direction, record]. If the direction == NONE and $vaidatorSettings'
     *          blockReply is true, then the move will result in a block. If $validatorSettings'
     *          blockReply is false and direction == NONE, then the move will not result in a move or a
     *          block. In all instances, if direction != NONE, then the move will result in a win, in the
     *           returned direction. start denotes where we should populate the row of winning col,row pairs.
     */
    public function validateMove(array $board, $col, ValidatorSettings $validatorSettings) {
        $maxBlockPrecedence = Precedence::NONE;
        $maxPrecedence = Precedence::NONE;
        $validatorSettingsCopy = clone $validatorSettings;

        // Check vertical.
        $precedence = $this->verticalMove($board, $col, $validatorSettingsCopy);

        // If block found, and precedence is three, don't look for anymore blocks. Else just store if
        // largest block precedence.
        if ($validatorSettingsCopy->getBlockRequest() && $validatorSettingsCopy->getBlockReply()) {
            if ($precedence == Precedence::THREE) {
                $validatorSettingsCopy->setBlockRequest(false);
            }
            $maxBlockPrecedence = max($maxBlockPrecedence, $precedence);
            $validatorSettingsCopy->setBlockReply(true);
        }

        // Winning move found.
        else if ($precedence >= Precedence::FOUR) {
            return [Direction::VERTICAL, $col];
        }

        // Store highest precedence non-winning/block move.
        else {
            $maxPrecedence = max($maxPrecedence, $precedence);
        }

        $moves = array("HorizontalStrategy"=>Direction::HORIZONTAL,
            "LeftDiagonalStrategy"=>Direction::LEFT_DIAG,
            "RightDiagonalStrategy"=>Direction::RIGHT_DIAG);

        // For remaining directions, we do a ripple effect. Start at the piece and radiate out.
        foreach ($moves as $strategy => $direction) {
            // If the move is a winning move, return the direction.
            $moveStrat = new $strategy($col, $this->heights[$col] - 1);
            $precedence = $this->rippleMove($board, $validatorSettingsCopy, $moveStrat, $col);

            // Block move.
            if ($validatorSettingsCopy->getBlockRequest() && $validatorSettingsCopy->getBlockReply()) {
                if ($precedence == Precedence::THREE) {
                    $validatorSettingsCopy->setBlockRequest(false);
                }
                $maxBlockPrecedence = max($maxBlockPrecedence, $precedence);
                $validatorSettingsCopy->setBlockReply(false);
            }

            // Win move.
            else if ($precedence >= Precedence::FOUR) {
                return [$direction, $moveStrat->getWinningStart()];
            }

            // No-win/block move.
            else {
                $maxPrecedence = max($maxPrecedence, $precedence);
            }
        }

        // If a block was found and a winning move was not found, then set reply.
        if ($maxBlockPrecedence > Precedence::NONE) {
            $validatorSettings->setBlockReply(true);
            $maxPrecedence = $maxBlockPrecedence;
        }

        // Winning move not found. A block move was found however, if blockReply is true.
        return [Direction::NONE, Record::PopulateRecord($col, $maxPrecedence)];
    }
}
?>