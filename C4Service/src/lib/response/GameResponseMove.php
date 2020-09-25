<?php
// Nichole Maldonado
// Lab 1 - GameResponseMoveAck
// September 7, 2020
// Dr. Cheon, CS3360
// Response that stores the Moves

require_once __DIR__."/GameResponse.php";
require_once dirname(__DIR__)."/strategies/Move.php";

/*
 * Response that stores the Moves $ackMove and $move. Since it is derived from
 * GameResponse, it also stores the $response.
 */
class GameResponseMove extends GameResponse {
    private Move $ackMove;
    private Move $move;

    /*
     * Constructor that calls the parent constructor for $response and assigns
     * its fields to $ackMove and $move.
     * @param: The $response and Move $ackMove. The $move parameter is optional.
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
     * Overrides jsonSerialize and calls the parent first to have the
     * $response before $ackMove $move if it exists.
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