<?php
// Nichole Maldonado
// Lab 1 - LeftDiagonalStrategy
// September 7, 2020
// Dr. Cheon, CS3360
// Keeps track of two points and moves the points in a right diagonal motion.


require_once(__DIR__."/HorizontalStrategy.php");
require_once(__DIR__."/VerticalBoundary.php");

/*
 * RightDiagonalStrategy has a verticalBoundary to keep track of the board's vertical boundary.
 * Inherits from HorizontalStrategy points that it will use to move to the left diagonally.
 */
class RightDiagonalStrategy extends HorizontalStrategy {
    private VerticalBoundary $verticalBoundary;

    /*
     * Sets points back to their original position.
     * @param: the $col and $row.
     * @return: None.
     */
    function reset($row, $col) {
        parent::reset($row, $col);
        $this->setInitialY();
//        echo "rightdiag: pt1: ({$this->pt1["x"]},{$this->pt1["y"]}), pt2:  ({$this->pt2["x"]},{$this->pt2["y"]})";
    }

    /*
     * Sets the y coordinates initially.
     * @param: None.
     * @return: None.
     */
    private function setInitialY() {
        $this->pt1["y"]--;
        $this->pt2["y"]++;
    }

    /*
     * Set all boundaries of the board and create the points. Since we are
     * moving to the right diagonally, the left point goes up in the board and
     * the right point starts lower in the board.
     * @param: The column and row of the piece to insert.
     * @return: None.
     */
    function __construct($col, $row) {
        parent::__construct($col, $row);
        $this->verticalBoundary = new VerticalBoundary($row);
        $this->setInitialY();
    }

    /*
     * Verify that pt1's x values is within the upper left boundary.
     * @param: None.
     * @return: True if in the range, false otherwise.
     */
    public function comparePt1() {
        return parent::comparePt1() && $this->pt1["y"] >= $this->verticalBoundary->getTopBoundary();
    }

    /*
     * Verify that pt1's x values is within the lower right boundary.
     * @param: None.
     * @return: True if in the range, false otherwise.
     */
    public function comparePt2() {
        return parent::comparePt2() && $this->pt2["y"] <= $this->verticalBoundary->getBottomBoundary();
    }

    /*
     * Move pt1 to the upper left.
     * @param: None.
     * @return: None.
     */
    public function updatePt1() {
        parent::updatePt1();
        $this->pt1["y"]--;
    }

    /*
     * Move pt2 to the lower right.
     * @param: None.
     * @return: None.
     */
    public function updatePt2() {
        parent::updatePt2();
        $this->pt2["y"]++;
    }
}
?>