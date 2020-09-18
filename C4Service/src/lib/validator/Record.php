<?php

class Record{
    private array $directionSlot;
    private int $col;

    function __construct() {
        $this->col = -1;
    }

    function getDirectionSlot(){
        return $this->directionSlot;
    }
    function getCol() {
        return $this->col;
    }
    function setCol($col) {
        $this->col = $col;
    }
    function setDirectionSlot($directionSlot) {
        $this->directionSlot = $directionSlot;
    }
}
?>