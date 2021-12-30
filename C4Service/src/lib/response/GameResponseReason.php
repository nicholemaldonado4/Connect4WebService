<?php
// Nichole Maldonado
// Lab 1 - GameResponseReason
// September 7, 2020
// Response that stores the string $reason.

require_once __DIR__."/GameResponse.php";

/*
 * Response that stores the string $reason and is derived from GameResponse, so it
 * also stores the $response.
 */
class GameResponseReason extends GameResponse {
    private string $reason;

    /*
     * Constructor that calls the parent constructor for $response and assigns
     * its field to $reason.
     * @param: The $response and string $reason.
     * @return: None.
     */
    public function __construct($response, $reason) {
        parent::__construct($response);
        $this->reason = $reason;
    }

    /*
     * Overrides jsonSerialize and calls the parent first to have the $response before $reason.
     * @param: None.
     * @return: An array with the $response before the $reason.
     */
    public function jsonSerialize() {
        return parent::jsonSerialize() + get_object_vars($this);
    }
}
?>
