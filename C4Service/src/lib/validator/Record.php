<?php
// Nichole Maldonado
// Lab 1 - Record
// September 18, 2020
// Dr. Cheon, CS3360
// Stores a directionSlot and col.

require_once(__DIR__."/Precedence.php");
require_once(__DIR__."/Direction.php");

/*
 * Record stores a $directionSlot with consists of a [Direction, slotNumStart]
 * and a col.
 */
class Record{
    private int $col;
    private float $precedence;

    /*
     * Default constructor that set $col to -1.
     */
    function __construct() {
        $this->col = -1;
        $this->precedence = Precedence::NONE;
    }

    static function populateRecord($col, $precedence = Precedence::FOUR) {
        $record = new Record();
        $record->setCol($col);
        $record->setPrecedence($precedence);
        return $record;
    }

    /*
     * Getter for field $col.
     * @param: None.
     * @return: The $col.
     */
    function getCol() {
        return $this->col;
    }

    function getPrecedence() {
        return $this->precedence;
    }

    /*
     * Setter for field $col.
     * @param: The $col to set the field to.
     * @return: None.
     */
    function setCol($col) {
        $this->col = $col;
    }


    function setPrecedence($precedence) {
        $this->precedence = $precedence;
    }
}
?>