<?php

require_once(__DIR__."/Record.php");

class MoveRecords {
    private array $records;
    function __construct() {
        $this->records = array(new Record(), new Record(), new Record);
    }

    private function setRecord($index, $directionSlot, $col) {
        $this->records[$index]->setDirectionSlot($directionSlot);
        $this->records[$index]->setCol($col);
    }

    function setBlock($directionSlot, $col){
        $this->setRecord(0, $directionSlot, $col);
    }
    function setNoWin($directionSlot, $col){
        $this->setRecord(1, $directionSlot, $col);
    }
    function setDefault($directionSlot, $col){
        $this->setRecord(2, $directionSlot, $col);
    }

    function getRecordDirectionSlot($index) {
        return $this->records[$index]->getDirectionSlot();
    }
    function getRecordCol($index) {
        return $this->records[$index]->getCol();
    }

    function foundNoWin() {
        return $this->records[1]->getCol() != -1;
    }
    function foundDefault() {
        return $this->records[2]->getCol() != -1;
    }

    function getRecord() {
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
