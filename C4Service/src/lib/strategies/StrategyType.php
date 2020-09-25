<?php
// Nichole Maldonado
// Lab 1 - StrategyType
// September 15, 2020
// Dr. Cheon, CS3360
// Contains a constant array that maps the strategy name with its corresponding
// class name.

abstract class StrategyType {
    const STRATEGIES = array("Smart" => "SmartStrategy", "Random" => "RandomStrategy");
}
?>