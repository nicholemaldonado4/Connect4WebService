<?php
// Nichole Maldonado
// Lab 1 - play/index.php
// September 7, 2020
// Dr. Cheon, CS3360
// Main page for play/index.php that executes the simulation of the game, starting from creating the Game based
// on the file, making the player and computer move, overwriting the new game, and outputting the moves to the screen.

require_once(dirname(__DIR__)."/lib/game/BoardDimension.php");
require_once(dirname(__DIR__)."/lib/game/Game.php");
require_once(dirname(__DIR__)."/lib/game/GameWriter.php");
require_once(dirname(__DIR__)."/lib/game/GameBuilder.php");
require_once(dirname(__DIR__)."/lib/response/GameResponse.php");
require_once(dirname(__DIR__)."/lib/response/GameResponseMove.php");
require_once(dirname(__DIR__)."/lib/response/GameResponseReason.php");
require_once(dirname(__DIR__)."/lib/strategies/MoveStrategy.php");
require_once(dirname(__DIR__)."/lib/validator/Direction.php");
require_once(dirname(__DIR__)."/lib/validator/MoveValidator.php");
require_once(dirname(__DIR__)."/lib/validator/PieceColor.php");

/*
 * Class that executes the simulation of the game, starting from creating the Game based on the file,
 * making the player and computer move, overwriting the new game, and outputting the moves to the screen.
 */
class GamePlay implements BoardDimension, PieceColor, Direction {
    private string $fileName;
    private Game $game;
    private GameResponse $response;
    private MoveValidator $moveValidator;

    /*
     * Gets the pid and move from the html query.
     * @param: A reference to $move that will store the column that the user wants to put the piece.
     * @return: True if the html query included the pid and a valid move (in board range), false otherwise.
     */
    private function getMoveAndPid(&$move, &$pid) {
        // Check for pid and save to $this->pid.
        if (!array_key_exists("pid", $_GET) || ($pid = trim($_GET["pid"])) === "") {
            $this->response = new GameResponseReason(false, "Pid not specified");
            return false;
        }

        // Check for valid move and save to $move.
        if (!array_key_exists("move", $_GET) || ($move = trim($_GET["move"])) === "") {
            $this->response = new GameResponseReason(false, "Move not specified");
            return false;
        }
        if (!is_numeric($move) || $move < 0 || $move > BoardDimension::WIDTH - 1) {
            $this->response = new GameResponseReason(false, "Invalid slot, ".$move);
            return false;
        }
        $move = intval($move);
        return true;
    }

    /*
     * Calls GameBuilder to construct a Game based on the one stored in <pid>.txt. Also stores the strategy
     * from GameBuilder.
     * @param: A reference to $move that will store the column that the user wants to put the piece.
     * @return: True if the Game was successfully built, false otherwise.
     */
    private function gameSetup(&$move) {
        $pid = null;
        if (!$this->getMoveAndPid($move, $pid)) {
            return false;
        }
        $gameBuilder = new GameBuilder();

        // Create empty response in event that we need to store it if an error occurs.
        $this->response = new GameResponse(true);
        $goodBuild = $gameBuilder->build($pid, $this->response );
        if ($goodBuild) {
            $this->game = $gameBuilder->getGame();
            $this->fileName = $gameBuilder->getFileName();
        }
        return $goodBuild;
    }

    /*
     * Makes the user move based on the $move provided and populates $ackMove with the Move
     * based on the move.
     * @param: A reference to $move that will store the column that the user wants to put the piece. A
     *         reference to $ackMove that will reference the Move.
     * @return: True if the move occurred, otherwise false.
     */
    private function moveUser($move, &$ackMove) {
        $this->moveValidator = new MoveValidator($this->game->getBoard());
        $newHeight = $this->moveValidator->getHeightForCol($move) - 1;

        // Check that there is still room in the column to put the piece. If not,
        // set $this->response to state that the move is invalid.
        if ($newHeight < 0 || $newHeight > BoardDimension::HEIGHT - 1 || $this->game->getBoard()[$newHeight][$move] != 0) {
            $this->response = new GameResponseReason(false, "Invalid move.");
            return false;
        }

        // See if the move is a winning move. Create a Move based on the move and add the piece to the board.
        list($direction, $start) = $this->moveValidator->validateMove($this->game->getBoard(), $move,
                new ValidatorSettings(false, false, PieceColor::USER));
        $this->moveValidator->decrHeightForCol($move);
        $this->game->addPieceToBoard($newHeight, $move, PieceColor::USER);
        $ackMove = $this->moveValidator->populateMoveFromDirection($direction, $start, $move);
        return true;
    }

    /*
     * Executes the strategy for the computer's move.
     * @param: A  reference to $compMove that will reference the Move.
     * @return: True if the move occurred, otherwise false.
     */
    private function executeStrategy(&$compMove) {

        // We are expecting a move to occur or a draw. However, if we find that we cannot move,
        // then this means that the files were tampered with between the checks from the user to the computer.
        // Store error.
        if (!$this->game->getStrategy()->pickSlot($this->game, $this->moveValidator, $compMove)) {
            $this->response = new GameResponseReason(false,
                "Could not find any valid moves for the computer although moves were expected. Corrupt file");
            return false;
        }
        return true;
    }

    /*
     * Simulates the game from creating the game based on the file, making the user and computer game,
     * displaying the game, and writing the updated game to the file.
     * @param: None.
     * @return: None.
     */
    private function simulateGame() {
        $move = 0;
        if (!$this->gameSetup($move)) {
            return;
        }

        // Get the Move for ackMove.
        $ackMove = null;
        if (!$this->moveUser($move, $ackMove)) {
            return;
        }
        // If the user move resulted in a draw or a win, stop game there.
        if ($ackMove->getIsWin() || $ackMove->getIsDraw()) {
            $this->response = new GameResponseMove(true, $ackMove);
            unlink($this->fileName);
            return;
        }

        // Get the Move for compMove
        $compMove = null;
        if (!$this->executeStrategy($compMove)) {
            return;
        }

        // Write updated game to file.
        $this->response = new GameResponseMove(true, $ackMove, $compMove);
        if ($compMove->getIsWin() || $compMove->getIsDraw()) {
            unlink($this->fileName);
            return;
        }
        GameWriter::overwriteGame($this->fileName, $this->game, $this->response);
    }

    /*
     * Calls to simulate the game and displays the response to the screen.
     * @param: None.
     * @return: None.
     */
    public function playGame() {
        $this->simulateGame();
        echo json_encode($this->response);
    }
}

// Start the game, display output of moves, and write updated game to the file.
$gamePlay = new GamePlay();
$gamePlay->playGame();
?>