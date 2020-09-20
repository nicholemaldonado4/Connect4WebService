<?php
// Nichole Maldonado
// Lab 1 - HorizontalStrategy
// September 7, 2020
// Dr. Cheon, CS3360
// Keeps track of two points and moves the points horizontally within the board's range.

require_once(dirname(__DIR__)."/game/BoardDimension.php");

/*
 * HorizontalStrategy has two points and moves the points horizontally.
 */
class HorizontalStrategy {
    protected int $leftBoundary;
    protected int $rightBoundary;
    protected array $pt1;
    protected array $pt2;

    /*
     * Sets points back to their original position.
     * @param: the $col and $row.
     * @return: None.
     */
    function reset($col, $row){
        $this->pt1 = array("x" => $col - 1, "y" => $row);
        $this->pt2 = array("x" => $col + 1, "y" => $row);
    }

    /*
     * Set the left and right boundary of the board. Set pt1 to the left of the column
     * and pt2 to the right of the col.
     * @param: The column and row of the piece to insert.
     * @return: None
     */
    function __construct($col, $row) {
        $this->leftBoundary = max($col - 3, 0);
        $this->rightBoundary = min($col + 3, BoardDimension::WIDTH - 1);
        self::reset($col, $row);

    }

    /*
     * Gets the starting point if the move will leave to a winning
     * move.
     * @param: None.
     * @return the x position (col) of the starting point.
     */
    public function getWinningStart() {
        return $this->pt1["x"] + 1;
    }

    /*
     * Verify that both the point's x values are within the boundaries.
     * @param: None.
     * @return: True if in the range, false otherwise.
     */
    public function compareBoth() {
        return static::comparePt1() && static::comparePt2();
    }

    /*
     * Verify that the pt1's x values is within the boundary.
     * @param: None.
     * @return: True if in the range, false otherwise.
     */
    public function comparePt1() {
        return $this->pt1["x"] >= $this->leftBoundary;
    }

    /*
     * Verify that the pt2's x values is within the boundary.
     * @param: None.
     * @return: True if in the range, false otherwise.
     */
    public function comparePt2() {
        return $this->pt2["x"] <= $this->rightBoundary;
    }

    /*
     * Move pt1 and pt2.
     * @param: None.
     * @return: None.
     */
    public function updateBoth() {
        static::updatePt1();
        static::updatePt2();
    }

    /*
     * Move pt1 to the left.
     * @param: None.
     * @return: None.
     */
    public function updatePt1() {
        $this->pt1["x"]--;
    }

    /*
     * Move pt2 to the right.
     * @param: None.
     * @return: None.
     */
    public function updatePt2() {
        $this->pt2["x"]++;
    }

    /*
     * Get the game's color at pt1.
     * @param: None.
     * @return: None.
     */
    public function getFromPt1(array $board) {
        return $board[$this->pt1["y"]][$this->pt1["x"]];
    }

    /*
     * Get the game's color at pt2.
     * @param: None.
     * @return: None.
     */
    public function getFromPt2(array $board) {
        return $board[$this->pt2["y"]][$this->pt2["x"]];
    }

    public function check(array $heights) {
//        echo "horizontal check pt1: ({$this->pt1["x"]},{$this->pt1["y"]}), pt2:  ({$this->pt2["x"]},{$this->pt2["y"]})</br>";
//        echo "heights at pt1: {$heights[$this->pt1["x"]]} </br>";
//        echo "heights at pt2: {$heights[$this->pt2["x"]]} </br>";
        if ((static::comparePt1() && $heights[$this->pt1["x"]] == $this->pt1["y"] + 1) ||
            (static::comparePt2() && $heights[$this->pt2["x"]] == $this->pt2["y"] + 1)) {
            return true;
        }
        return false;
    }
}
?>