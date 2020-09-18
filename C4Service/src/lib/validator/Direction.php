<?php
// Nichole Maldonado
// Lab 1 - Direction
// September 7, 2020
// Dr. Cheon, CS3360
// Interface that defines the direction that a valid connect 4 move can be made.

/*
 * Interface that defines the direction that a valid connect 4 move can be made.
 */
interface Direction {
    const NONE = 'none';
    const VERTICAL = 'vertical';
    const HORIZONTAL = 'horizontal';
    const LEFTDIAG = 'leftdiag';
    const RIGHTDIAG = 'rightdiag';
}
?>