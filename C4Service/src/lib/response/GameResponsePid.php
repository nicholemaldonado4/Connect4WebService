<?php
// Nichole Maldonado
// Lab 1 - GameResponsePid
// September 7, 2020
// Response that stores the string $pid.

require_once __DIR__."/GameResponse.php";

/*
 * Response that stores the string $pid and is derived from GameResponse, so it
 * also stores the $response.
 */
class GameResponsePid extends GameResponse {
    public string $pid;

    /*
     * Constructor that calls the parent constructor for $response and assigns
     * its field to $pid.
     * @param: The $response and string $pid.
     * @return: None.
     */
    public function __construct($response, $pid) {
        parent::__construct($response);
        $this->pid = $pid;
    }

    /*
     * Overrides jsonSerialize and calls the parent first to have the $response before $pid.
     * @param: None.
     * @return: An array with the $response before the $pid.
     */
    public function jsonSerialize() {
        return parent::jsonSerialize() + get_object_vars($this);
    }
}
?>
