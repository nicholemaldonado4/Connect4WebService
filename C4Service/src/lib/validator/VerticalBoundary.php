<?php
// Nichole Maldonado
// Lab 1 - LeftDiagonalStrategy
// September 7, 2020
// Dr. Cheon, CS3360
// VerticalBoundary calculates the top and bottom boundary of the board with respect
// to a give row.

require_once dirname(__DIR__)."/game/BoardDimension.php";

/*
 * Keeps track of the top and bottom boundary that are 3 from the provided $row
 */
class VerticalBoundary {
    private int $topBoundary;
    private int $bottomBoundary;

    /*
     * Calculates the top and bottom boundaries.
     * @param: The row that will be used as a basis.
     * @return: None.
     */
    function __construct($row) {
        $this->topBoundary = max($row - 3, 0);
        $this->bottomBoundary = min($row + 3, BoardDimension::HEIGHT - 1);
    }

    /*
     * Getter for the field $topBoundary.
     * @param: None.
     * @return the field $topBoundary.
     */
    public function getTopBoundary() {
        return $this->topBoundary;
    }

    /*
     * Getter for the field $bottomBoundary.
     * @param: None.
     * @return the field $bottomBoundary.
     */
    public function getBottomBoundary() {
        return $this->bottomBoundary;
    }
}
?>