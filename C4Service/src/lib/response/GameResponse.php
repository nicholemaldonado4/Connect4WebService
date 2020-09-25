<?php
// Nichole Maldonado
// Lab 1 - GameResponse
// September 7, 2020
// Dr. Cheon, CS3360
// GameResponse stores a response boolean that determines if an interaction was
// successful or not.

/*
 * GameResponse stores a response boolean that determines if an interaction was
 * successful or not. Overrides jsonSerialize to maintain order of derived responses.
 */
class GameResponse implements JsonSerializable {
    private bool $response;

    /*
     * Default constructor that sets the $response.
     * @param: A boolean value to be assigned to the field $response.
     * @return: None.
     */
    public function __construct($response) {
        $this->response = $response;
    }

    /*
     * Since $response is a private field, override jsonSerialize. Additionally,
     * GameResponse serves as a base class for other responses. We order these responses
     * by concatenating arrays of fields. Otherwise, the order of responses would vary.
     * @param: None.
     * @return: An array with the field $response.
     */
    public function jsonSerialize() {
        // NOTE: Do not use get_object_vars(), because we want them ordered. (response first).
        return array("response" => $this->response);
    }
}
?>
