<?php
// Nichole Maldonado
// Lab 1 - GameResponseMoveAck
// September 7, 2020
// Dr. Cheon, CS3360
// Response that stores the GameStats $ackMove and is derived from GameResponse, so it
// also stores the $response.

require_once(__DIR__."/GameResponse.php");
require_once(dirname(__DIR__) . "/strategies/Move.php");

/*
 * Response that stores the Move $ackMove and is derived from GameResponse, so it
 * also stores the $response.
 */
class GameResponseMove extends GameResponse {
    private Move $ackMove;
    private Move $move;

    /*
     * Constructor that calls the parent constructor for $response and assigns
     * its field to $ackMove.
     * @param: The $response and Move $ackMove.
     * @return: None.
     */
    public function __construct($response, Move $ackMove, Move $move = null) {
        parent::__construct($response);
        $this->ackMove = $ackMove;
        if (isset($move)) {
            $this->move = $move;
        }
    }

    /*
     * Overrides jsonSerialize and calls the parent first to have the $response before $ackMove.
     * @param: None.
     * @return: An array with the $response before the GameStats for moveAck.
     */
    public function jsonSerialize() {
        $response = parent::jsonSerialize();
        $response["ack_move"] = $this->ackMove;
        if (isset($this->move)) {
            $response["move"] = $this->move;
        }
        return $response;
    }
}

?>