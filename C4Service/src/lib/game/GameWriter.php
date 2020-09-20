<?php
// Nichole Maldonado
// Lab 1 - GameWriter
// September 7, 2020
// Dr. Cheon, CS3360
// GameWriter creates a game and stores the game in <pid>.txt, where pid is a uniquely
// calculated pid. Provides static write file functions to be used by play/index.php.

require_once(dirname(__DIR__)."/game/Game.php");
require_once(dirname(__DIR__)."/response/GameResponsePid.php");
require_once(dirname(__DIR__)."/response/GameResponseReason.php");
require_once(dirname(__DIR__)."/strategies/StrategyType.php");
require_once(dirname(__DIR__)."/fileutils/FileConstants.php");

define('STRATEGY', "strategy");

/*
 * Creates a game and stores the game in <pid>.txt, where pid is a uniquely calculated pid.
 * Also stores the strategy denoted based on the html query. Also provides static write file
 * functions.
 */
class GameWriter {

    /*
     * Overwrites the old game with the new game data in <pid>.txt
     * @param: the file to write the game to, the $game and a reference to
     *         a response in the event that an error occurs.
     * @return: None.
     */
    public static function overwriteGame($fileName, Game $game, &$response) {
        if (file_exists($fileName) && !is_dir($fileName)) {
            self::writeToFile($fileName, $game, $response);
            return;
        }

        // If we cannot find the file, DO NOT store the game and DO NOT register the move.
        // This is a weird instance in which we started off with the game file and suddenly
        // it disappeared while making the move. We do not create new file to store the
        // game because the file seems finicky/user's are trying to cause the program to crash.
        // Requires a stable file.
        $response = new GameResponseReason(false, "Failed to find the file for the Pid.");
    }

    /*
     * Utility write the file function. Writes the game to the file
     * If an error occurs, $response is set to a GameResponseReason.
     * @param: The file to write the game to, the game, and the GameResponse.
     * @return: None.
     */
    public static function writeToFile($fileName, Game $game, &$response) {
        $filePointer = fopen($fileName, "w");
        if (!$filePointer) {
            $response = new GameResponseReason(false, "Failed to store game data.");
        }
        fwrite($filePointer, json_encode($game));
        fclose($filePointer);
    }

    /*
     * Prepares for a new file creation of <pid>.txt and then calls to create and
     * write to the file.
     * @param: The strategy that the game will contain and the $pid.
     * @return: The response of the game write operation.
     */
    private function createFile($strategy, $pid) {
        $fileName = DATA_DIR.$pid.DATA_EXT;
        $gameResponse = null;

        // Prevent races. If the file already exists, then return error.
        if (file_exists($fileName) && !is_dir($fileName)) {
            $gameResponse =  new GameResponseReason(false,
                "The file to store your game already exists. Please make a new request");
        }
        else {
            self::writeToFile($fileName, Game::buildInitialGame($strategy), $gameResponse);
            if (!isset($gameResponse)) {
                $gameResponse = new GameResponsePid(true, $pid);
            }
        }
        return $gameResponse;
    }

    /*
     * Creates a game as long as the user provides the strategy (random or smart) to be queried.
     * Writes the game to the file. Displays created game to the screen, or if an error occurs,
     * displays it to the screen.
     * @param: None.
     * @return None.
     */
    public function createGame() {
        $gameResponse = null;

        // If ?strategy= is not specified or the user user types "?strategy=" but nothing after
        // it, we denote this as a strategy not specified.
        if (!array_key_exists(STRATEGY, $_GET) || ($strategy = trim($_GET[STRATEGY])) === "") {
            $gameResponse = new GameResponseReason(false, "Strategy not specified");

        }
        // Otherwise, analyze.
        else {
            $strategy = ucfirst(strtolower($strategy));

            // User must provide a strategy to create a new game.
            if (array_key_exists($strategy, StrategyType::STRATEGIES)) {
                $pid = uniqid();
                $gameResponse = $this->createFile(StrategyType::STRATEGIES[$strategy], $pid);
            } else {
                $gameResponse = new GameResponseReason(false, "Unknown strategy");
            }
        }

        // Display the response.
        echo json_encode($gameResponse);
    }
}
?>