<?php
// Nichole Maldonado
// Lab 1 - MoveValidator
// September 7, 2020
// Dr. Cheon, CS3360
// Verifies if a move could result in a winning move, and if requested a blocking move.
// Also provides the functionality based to create a Move based on the move. Used to
// process moves.

require_once(dirname(__DIR__)."/strategies/Move.php");
require_once(dirname(__DIR__)."/game/BoardDimension.php");
require_once(__DIR__."/Direction.php");
require_once(__DIR__."/ValidatorSettings.php");
require_once(__DIR__."/HorizontalStrategy.php");
require_once(__DIR__."/LeftDiagonalStrategy.php");
require_once(__DIR__."/RightDiagonalStrategy.php");
require_once(__DIR__."/PieceColor.php");

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
//        var_dump($this->heights);
    }

    /*
     * Decrements the height at $col to signify that a piece was added for $col.
     * @param: The $col.
     * @return: None.
     */
    public function incrHeightForCol($col) {
        $this->heights[$col]++;
//        var_dump($this->heights);
    }

    /*
     * Counts the number of $pieceColor pieces from the top of the $col down until at least three consecutive same
     * colored pieces are found or a different piece color is found.
     * @param: The number of same colored, consecutive pieces.
     * @return: None.
     */
    private function verticalMoveHelper(array $board, $col, $pieceColor) {
        $row = $this->heights[$col];
        $count = 0;

        // Count the number of same colored pieces until we find 3 or a different piece color.
        while ($row < BoardDimension::HEIGHT && $count < 3 && $board[$row][$col] == $pieceColor) {
            $row++;
            $count++;
        }
        return $count;
    }

    /*
     * Get the number of consecutive, same colored pieces that exist vertically down the column.
     * If a block is requested and the three consecutive, same colored pieces were not found,
     * recalls to see if three consecutive, user color pieces exist.
     * @param: The $board, $col, and $validatorSettings with the piece color.
     * @return: If three consecutive pieces of the piece color was found, then return true.
     *          If a block was requested and three consecutive pieces of the user color was found,
     *          then return true and set reply to true. Otherwise, return false.
     */
    private function verticalMove(array $board, $col, ValidatorSettings $validatorSettings) {
        $count = $this->verticalMoveHelper($board, $col, $validatorSettings->getPieceColor());

        // If the count was 0, then we know that the first piece down did not match the pieceColor. If a block
        // was requested, check to see a column of three user color pieces exist.
        if ($count == 0 && $validatorSettings->getBlockRequest()) {
            $validatorSettings->togglePieceColor();
            $count = $this->verticalMoveHelper($board, $col, $validatorSettings->getPieceColor());
            $validatorSettings->togglePieceColor();

            // If three consecutive, user color pieces were found, set BlockReply to true.
            if ($count == 3) {
                $validatorSettings->setBlockReply(true);
            }
        }
        return $count == 3;
    }

    /*
     * Radiates outward in a direction specified by $moveStrat. If a block is requested and
     * a possible block could occur, it is evaluated.
     * @param: The board, validator settings, and the move strategy that will be applied.
     * @return: None.
     */
    private function rippleMoveHelper(array $board, ValidatorSettings $validatorSettings, HorizontalStrategy $moveStrat) {
        $count = 1;
//        echo "here</br>";

        // Radiate outwards looking for same colored pieces.
        while ($moveStrat->compareBoth() && $moveStrat->getFromPt1($board) == $moveStrat->getFromPt2($board) &&
            $moveStrat->getFromPt1($board) == $validatorSettings->getPieceColor() && $count < 4) {
            $moveStrat->updateBoth();
            $count+=2;
//            echo "In big loop";
        }
        if ($count == 4) {
            return true;
        }
//        $prevPieceColor = $validatorSettings->getPieceColor();
//        echo "here 2";
//        // If computer and $count == 1, then we know that the left and right had different colors then we were expecting
//        // so change colors and try again (Convenient because users need the two while loops below and are expected
//        // to do one or the other. For color switch, both loops could be used).
//        if ($validatorSettings->getBlockRequest() && !$moveStrat->comparePt1() && !$moveStrat->comparePt2()) {
//            echo "Changed colors";
//            $validatorSettings->togglePieceColor();
//        }

        // If we can evaluate the pieces more to the left or more to the right, then do so.
        while ($moveStrat->comparePt1() && $moveStrat->getFromPt1($board) == $validatorSettings->getPieceColor() &&
            $count < 4) {
            $moveStrat->updatePt1();
            $count++;
//            echo "In left";
        }
        while ($moveStrat->comparePt2() && $moveStrat->getFromPt2($board) == $validatorSettings->getPieceColor() &&
            $count < 4) {
            $moveStrat->updatePt2();
            $count++;
//            echo "In right count: $count";
        }

        // If we changed piece colors, set the reply to true. In all instances set pieceColor
        // back to its original color.
//        if ($prevPieceColor != $validatorSettings->getPieceColor()) {
//            echo "in here: $count";
//            if ($count == 4) {
//                echo "setting block reply";
//                $validatorSettings->setBlockReply(true);
//            }
//            $validatorSettings->togglePieceColor();
//        }
        return $count == 4;
//        if ($count == 4) {
//            if ($prevPieceColor != $validatorSettings->getPieceColor()) {
//                $validatorSettings->setBlockReply(true);
//                $validatorSettings->togglePieceColor();
//            }
//            return true;
//        }
//        if ($prevPieceColor != $validatorSettings->getPieceColor()) {
//            $validatorSettings->togglePieceColor();
//        }
//        return false;
    }

    private function rippleMove(array $board, ValidatorSettings $validatorSettings, HorizontalStrategy $moveStrat) {
        $count = $this->rippleMoveHelper($board, $validatorSettings, $moveStrat);

        // If the count was 0, then we know that the first piece down did not match the pieceColor. If a block
        // was requested, check to see a column of three user color pieces exist.
        if ($count != 4 && $validatorSettings->getBlockRequest()) {
            $validatorSettings->togglePieceColor();
            $count = $this->rippleMoveHelper($board, $validatorSettings, $moveStrat);
            $validatorSettings->togglePieceColor();

            // If three consecutive, user color pieces were found, set BlockReply to true.
            if ($count == 4) {
                $validatorSettings->setBlockReply(true);
            }
        }
        return $count == 4;
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
     * @param: The starting $col where the four pieces are arranged horizontally (left to right).
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
     * @param: The starting $col where the four pieces are arranged diagonally to the right.
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
     * @param: The starting $col where the four pieces are arranged diagonally to the left.
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
        switch ($direction) {
            case Direction::VERTICAL:
                $row = $this->buildVerticalRows($start);
                break;
            case Direction::HORIZONTAL:
                $row = $this->buildHorizontalRows($start, $this->heights[$col]);
                break;
            case Direction::LEFTDIAG:
                $row = $this->buildLeftDiagRows($start, ($this->heights[$col] + ($col - $start)));
                break;
            case Direction::RIGHTDIAG:
                $row = $this->buildRightDiagRows($start, ($this->heights[$col] - ($col - $start)));
                break;
            case Direction::NONE:
                $isWin = false;
        }
        $isDraw = false;
        if (!$isWin) {
            $isDraw = $this->checkDraw();
        }
        return Move::createNewMove($col, $isWin, $isDraw, $row);
    }

    /*
     * Validates if a piece added at $col will lead to win. If a block is requested and found, then
     * will reply that the move will result in a block, if the move will not result in a win.
     * @param: None.
     * @return: [direction, start]. If the direction == NONE and $vaidatorSettings' blockReply is true, then the
     *          move will result in a block. If $validatorSettings' blockReply is false and direction == NONE, then
     *          the move will not result in a move or a block. In all instances, if direction != NONE, then
     *          the move will result in a win, in the returned direction. start denotes where we should populate the
     *          row of winning col,row pairs.
     */
    public function validateMove(array $board, $col, ValidatorSettings $validatorSettings) {
        echo "col: $col</br></br>";

        // Check vertical. If the move is a winning move, return the direction.
        if ($this->verticalMove($board, $col, $validatorSettings)) {
            if ($validatorSettings->getBlockRequest() && $validatorSettings->getBlockReply()) {

                // Found a block move. That is enough for now. See if the move can be a winning move.
                $validatorSettings->setBlockRequest(false);
            }
            else {
                return [Direction::VERTICAL, $col];
            }
        }

//        $moves = array("HorizontalStrategy"=>Direction::HORIZONTAL,
//                        "LeftDiagonalStrategy"=>Direction::LEFTDIAG,
//                        "RightDiagonalStrategy"=>Direction::RIGHTDIAG);

        $moves = array("HorizontalStrategy"=>Direction::HORIZONTAL);
//            "LeftDiagonalStrategy"=>Direction::LEFTDIAG,
//            "RightDiagonalStrategy"=>Direction::RIGHTDIAG);

        // For remaining directions, we do a ripple effect. Start at the piece and radiate out.
        foreach ($moves as $strategy => $direction) {
//            echo "</br></br>";
            // If the move is a winning move, return the direction.
            $moveStrat = new $strategy($col, $this->heights[$col] - 1);
            if ($this->rippleMove($board, $validatorSettings, $moveStrat)) {
                if ($validatorSettings->getBlockRequest() && $validatorSettings->getBlockReply()) {

                    // Found a block move. That is enough for now. See if the move can be a winning move.
                    $validatorSettings->setBlockRequest(false);
                }
                else {
                    return [$direction, $moveStrat->getWinningStart()];
                }
            }
        }

        // If a block was found and a winning move was not found, then restore the
        // request.
        if ($validatorSettings->getBlockReply()) {
            $validatorSettings->setBlockRequest(true);
        }

        // Winning move not found. A block move was found however, if blockReply is true.
        return [Direction::NONE, $col];
    }
}
?>