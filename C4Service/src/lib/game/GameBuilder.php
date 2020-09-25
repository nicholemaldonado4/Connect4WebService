<?php
// Nichole Maldonado
// Lab 1 - GameBuilder
// September 7, 2020
// Dr. Cheon, CS3360
// GameBuilder reads a .txt file name after the pid and constructs a Game
// object based on the file's contents.

require_once __DIR__."/Game.php";
require_once dirname(__DIR__)."/response/GameResponseReason.php";
require_once dirname(__DIR__)."/strategies/RandomStrategy.php";
require_once dirname(__DIR__)."/strategies/SmartStrategy.php";
require_once dirname(__DIR__)."/strategies/MoveStrategy.php";
require_once dirname(__DIR__)."/fileutils/FileConstants.php";

/*
 * Reads a .txt file located in src/writable and name after the pid.
 * The file's contents are used to construct a Game object and Strategy.
 */
class GameBuilder {
    private Game $game;
    private string $fileName;

    /*
     * Getter for the field $game.
     * @param: None.
     * @return: The $game.
     */
    public function getGame() {
        return $this->game;
    }

    /*
     * Getter for the field $fileName.
     * @param: None.
     * @return: The $fileName.
     */
    public function getFileName() {
        return $this->fileName;
    }

    /*
     * Builds a game and strategy based on the $gameTxt.
     * @param: The $gameTxt that describes the board and a reference to $response that will be updated
     *         if an error occurs.
     * @return: True if the game was able to be created, false otherwise.
     */
    private function parseGame($gameTxt, &$response) {
        if (!isset($gameTxt) || !property_exists($gameTxt, "board") ||
                !property_exists($gameTxt, "strategy") || !property_exists($gameTxt->strategy, "name")) {
            $response = new GameResponseReason(false, "The game file contained malformed data.");
            return false;
        }
        $this->game = Game::buildGame($gameTxt->board, $gameTxt->strategy->name);
        return true;
    }

    /*
     * Reads a file named <pid>.txt in GameFiles and calls to build the game
     * based on the text from the file.
     * @param: The $pid of the game and the $response which will be set if a problem occurs.
     * @return: True if the operation was successful, false otherwise.
     */
    public function build($pid, &$response) {
        $this->fileName = DATA_DIR.$pid.DATA_EXT;

        // Check that the file name does not already exist to prevent races.
        if (file_exists($this->fileName) && !is_dir($this->fileName)) {
            $fp = fopen($this->fileName, "r");
            if (!$fp) {
                $response = new GameResponseReason(false, "Failed to read the game in the file.");
                return false;
            }

            // Read from the file and use the information to construct a Game object.
            $gameTxt = fread($fp, filesize($this->fileName));
            if ($gameTxt === false) {
                $response = new GameResponseReason(false, "Failed to read the game in the file.");
                return false;
            }
            fclose($fp);
            return $this->parseGame(json_decode($gameTxt), $response);
        }
        $response = new GameResponseReason(false, "The game file does not exist.");
        return false;
    }
}
?>