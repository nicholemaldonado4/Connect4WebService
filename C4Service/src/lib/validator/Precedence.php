<?php
// Nichole Maldonado
// Lab 1 - PieceColor
// September 20, 2020
// Dr. Cheon, CS3360
// Denotes the precedence of a move

/*
 * The precedence correlates to the number of pieces in a row.
 * Highest Precedence:
 *  FOUR - four in a row
 *  THREE - three in a row
 *  TWO_LARGE - user has two in a row and placing a computer piece next to
 *    the two in a row will prevent the user from getting a three in a row on one side
 *  TWO_HALF - user has two in a row.
 *  TWO - two in a row
 *  ONE - one in a row
 *  NONE - nothing.
 */
abstract class Precedence {
    const FOUR = 6;
    const THREE = 5;
    const TWO_LARGE = 4;
    const TWO_HALF = 3;
    // TWO = 2, currently not used, but could be implemented in future versions.
    const ONE = 1;
    const NONE = 0;

    /*
     * Since we have TWO_LARGE and TWO_HALF, we need to map those above two to their
     * corresponding precedence, so add two to the values.
     * @param: A count integer.
     * @return: A precedence mapping 0 - 6.
     */
    static function getPrecedence($count) {
        if ($count > 4) {
            $count = 4;
        }
        return ($count >= 3) ? $count + 2 : $count;
    }
}
?>
