<?php
// Nichole Maldonado
// Lab 1 - PieceColor
// September 7, 2020
// Dr. Cheon, CS3360
// Denotes the "piece color" of the board saved to the file. Essentially, if the
// board has a zero, then the slot is considered empty. If the board has a 1, then the slot is
// considered to have a user's piece. If the board has a 2, then the slot is considered to
// have a computer's piece.

require_once(__DIR__."/PieceColor.php");
require_once(__DIR__."/Direction.php");

class ValidatorSettings implements PieceColor {
    private bool $blockRequest;
    private bool $blockReply;
    private int $pieceColor;

    /*
     * Constructor that stores the $blockRequest, $blockReply, and $pieceColor.
     * @param: Whether or not we are requesting to see if a move will be blocking,
     *         the reply of this request, and piece color of the piece.
     * @return: None.
     */
    public function __construct($blockRequest, $blockReply, $pieceColor) {
        $this->blockRequest = $blockRequest;
        $this->blockReply = $blockReply;
        $this->pieceColor = $pieceColor;
    }

    /*
     * Setter for the field $blockRequest
     * @param: The $blockRequest to assign to the field.
     * @return: None.
     */
    public function setBlockRequest($blockRequest) {
        $this->blockRequest = $blockRequest;
    }

    /*
     * Setter for the field $blockReply
     * @param: The $blockReply to assign to the field.
     * @return: None.
     */
    public function setBlockReply($blockReply) {
        $this->blockReply = $blockReply;
    }

    /*
     * Alternates the piece color between the computer's and user's color.
     * @param: None
     * @return: None.
     */
    public function togglePieceColor() {
        $this->pieceColor = ($this->pieceColor == PieceColor::USER) ? PieceColor::COMPUTER : PieceColor::USER;
    }

    /*
     * Getter for the field $blockRequest.
     * @param: None
     * @return: The boolean value of $blockRequest.
     */
    public function getBlockRequest() {
        return $this->blockRequest;
    }

    /*
     * Getter for the field $blockRequest.
     * @param: None
     * @return: The boolean value of $blockReply.
     */
    public function getBlockReply() {
        return $this->blockReply;
    }

    /*
     * Getter for the field $pieceColor.
     * @param: None
     * @return: The PieceColor assigned to $pieceColor.
     */
    public function getPieceColor() {
        return $this->pieceColor;
    }
}

?>