<?php
// Nichole Maldonado
// Lab 1 - MoveRecords
// September 18, 2020
// Dr. Cheon, CS3360
// Keeps records for block move, no win move, and default move.

require_once(__DIR__."/Record.php");

/*
 * Keeps records for block move, no win move, and default move,
 * where block move has the highest precedence.
 * and default move has the lowest precedence. If a move with higher precedence is found, the
 * other moves are not saved. In the end, returns the move with the highest precedence.
 * Block move - prevent the user from winning.
 * No win move - this move will not result in a win for the computer but it is
 *               also a safe move since it ensures that the user cannot put a piece ontop and win.
 * Default move - move if all the above are not met.
 */
class MoveRecords {
    private array $records;

    /*
     * Constructor that stores the three records. Block move is at
     * index 0, no win move is at index 1, and default move is at index 2.
     * @param: None.
     * @return: None.
     */
    function __construct() {
        $this->records = array(new Record(), new Record(), new Record);
    }

    /*
     * Given an index, stores the $directionSlot and $col at the index.
     * @param: An index from 0 - 2, the $directionSlot and $col.
     * @return: None.
     */
    private function setRecord($index, Record $record) {
        $this->records[$index]->setCol($record->getCol());
        $this->records[$index]->setPrecedence($record->getPrecedence());
    }

    /*
     * Sets the block move.
     * @param: The $directionSlot and $col associated with the move.
     * @return: None.
     */
    function setBlock(Record $record){
        $this->setRecord(0, $record);
    }

    /*
     * Sets the no win move.
     * @param: The $directionSlot and $col associated with the move.
     * @return: None.
     */
    function setNoWin(Record $record){
        $this->setRecord(1, $record);
    }

    /*
     * Sets the default move.
     * @param: The $directionSlot and $col associated with the move.
     * @return: None.
     */
    function setDefault(Record $record){
        $this->setRecord(2, $record);
    }

    function getBlock() {
        return $this->records[0];
    }

    function getNoWin() {
        return $this->records[1];
    }

    function getDefault() {
        return $this->records[2];
    }

    /*
     * Gets the $col for a given record.
     * @param: The $index of the record.
     * @return: None.
     */
    function getRecordCol($index) {
        return $this->records[$index]->getCol();
    }


    /*
     * Returns the index of the record that is populated. The record has the
     * highest priority.
     * @param: None.
     * @return: The index of the highest priority record that exists.
     */
    function getHighestPriorityRecord() {
        $count = sizeOf($this->records);
        for ($i = 0; $i < $count; $i++) {
            if ($this->records[$i]->getCol() != -1) {
                return $i;
            }
        }
        return -1;
    }
}

?>
