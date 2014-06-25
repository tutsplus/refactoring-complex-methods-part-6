<?php
function echoln($string) {
	echo $string . "\n";
}

class Game {
	static $minimumNumberOfPlayers = 2;
	static $numberOfCoinsToWin = 6;

	var $players;
	var $places;
	var $purses;
	var $inPenaltyBox;

	var $popQuestions;
	var $scienceQuestions;
	var $sportsQuestions;
	var $rockQuestions;

	var $currentPlayer = 0;
	var $isGettingOutOfPenaltyBox;

	function  __construct() {

		$this->players = array();
		$this->places = array(0);
		$this->purses = array(0);
		$this->inPenaltyBox = array(0);

		$this->popQuestions = array();
		$this->scienceQuestions = array();
		$this->sportsQuestions = array();
		$this->rockQuestions = array();

		$categorySize = 50;
		for ($i = 0; $i < $categorySize; $i++) {
			array_push($this->popQuestions, "Pop Question " . $i);
			array_push($this->scienceQuestions, ("Science Question " . $i));
			array_push($this->sportsQuestions, ("Sports Question " . $i));
			array_push($this->rockQuestions, "Rock Question " . $i);
		}
	}

	function isPlayable() {
		return ($this->howManyPlayers() >= self::$minimumNumberOfPlayers);
	}

	function add($playerName) {
		array_push($this->players, $playerName);
		$this->setDefaultPlayerParametersFor($this->howManyPlayers());

		echoln($playerName . " was added");
		echoln("They are player number " . count($this->players));
		return true;
	}

	function howManyPlayers() {
		return count($this->players);
	}

	function  roll($rolledNumber) {
		$this->displayStatusAfterRoll($rolledNumber);
		if ($this->inPenaltyBox[$this->currentPlayer]) {
			$this->playNextMoveForPlayerInPenaltyBox($rolledNumber);
		} else {
			$this->playNextMove($rolledNumber);
		}
	}

	function  askQuestion() {
		if ($this->currentCategory() == "Pop") {
			echoln(array_shift($this->popQuestions));
		}
		if ($this->currentCategory() == "Science") {
			echoln(array_shift($this->scienceQuestions));
		}
		if ($this->currentCategory() == "Sports") {
			echoln(array_shift($this->sportsQuestions));
		}
		if ($this->currentCategory() == "Rock") {
			echoln(array_shift($this->rockQuestions));
		}
	}

	function currentCategory() {
		$popCategory = "Pop";
		$scienceCategory = "Science";
		$sportCategory = "Sports";
		$rockCategory = "Rock";

		if ($this->places[$this->currentPlayer] == 0) {
			return $popCategory;
		}
		if ($this->places[$this->currentPlayer] == 4) {
			return $popCategory;
		}
		if ($this->places[$this->currentPlayer] == 8) {
			return $popCategory;
		}
		if ($this->places[$this->currentPlayer] == 1) {
			return $scienceCategory;
		}
		if ($this->places[$this->currentPlayer] == 5) {
			return $scienceCategory;
		}
		if ($this->places[$this->currentPlayer] == 9) {
			return $scienceCategory;
		}
		if ($this->places[$this->currentPlayer] == 2) {
			return $sportCategory;
		}
		if ($this->places[$this->currentPlayer] == 6) {
			return $sportCategory;
		}
		if ($this->places[$this->currentPlayer] == 10) {
			return $sportCategory;
		}
		return $rockCategory;
	}

	function wasCorrectlyAnswered() {
		if ($this->inPenaltyBox[$this->currentPlayer]) {
			if ($this->isGettingOutOfPenaltyBox) {
				echoln("Answer was correct!!!!");
				$this->purses[$this->currentPlayer]++;
				echoln($this->players[$this->currentPlayer]
					. " now has "
					. $this->purses[$this->currentPlayer]
					. " Gold Coins.");

				$winner = $this->didPlayerNotWin();
				$this->currentPlayer++;
				if ($this->shouldResetCurrentPlayer()) {
					$this->currentPlayer = 0;
				}

				return $winner;
			} else {
				$this->currentPlayer++;
				if ($this->shouldResetCurrentPlayer()) {
					$this->currentPlayer = 0;
				}
				return true;
			}

		} else {

			echoln("Answer was corrent!!!!");
			$this->purses[$this->currentPlayer]++;
			echoln($this->players[$this->currentPlayer]
				. " now has "
				. $this->purses[$this->currentPlayer]
				. " Gold Coins.");

			$winner = $this->didPlayerNotWin();
			$this->currentPlayer++;
			if ($this->shouldResetCurrentPlayer()) {
				$this->currentPlayer = 0;
			}

			return $winner;
		}
	}

	function wrongAnswer() {
		echoln("Question was incorrectly answered");
		echoln($this->players[$this->currentPlayer] . " was sent to the penalty box");
		$this->inPenaltyBox[$this->currentPlayer] = true;

		$this->currentPlayer++;
		if ($this->shouldResetCurrentPlayer()) {
			$this->currentPlayer = 0;
		}
		return true;
	}

	function didPlayerNotWin() {
		return !($this->purses[$this->currentPlayer] == self::$numberOfCoinsToWin);
	}

	private function isOdd($roll) {
		return $roll % 2 != 0;
	}

	private function playerShouldStartANewLap() {
		$lastPositionOnTheBoard = 11;
		return $this->places[$this->currentPlayer] > $lastPositionOnTheBoard;
	}

	private function shouldResetCurrentPlayer() {
		return $this->currentPlayer == count($this->players);
	}

	private function setDefaultPlayerParametersFor($playerId) {
		$this->places[$playerId] = 0;
		$this->purses[$playerId] = 0;
		$this->inPenaltyBox[$playerId] = false;
	}

	private function movePlayer($rolledNumber) {
		$boardSize = 12;
		$this->places[$this->currentPlayer] = $this->places[$this->currentPlayer] + $rolledNumber;
		if ($this->playerShouldStartANewLap()) {
			$this->places[$this->currentPlayer] = $this->places[$this->currentPlayer] - $boardSize;
		}
	}

	private function displayPlayersNewLocation() {
		echoln($this->players[$this->currentPlayer] . "'s new location is " . $this->places[$this->currentPlayer]);
	}

	private function displayCurrentCategory() {
		echoln("The category is " . $this->currentCategory());
	}

	private function displayPlayerGettingOutOfPenaltyBox() {
		echoln($this->players[$this->currentPlayer] . " is getting out of the penalty box");
	}

	private function displayPlayerStaysInPenaltyBox() {
		echoln($this->players[$this->currentPlayer] . " is not getting out of the penalty box");
	}

	private function displayCurrentPlayer() {
		echoln($this->players[$this->currentPlayer] . " is the current player");
	}

	private function displayRolledNumber($rolledNumber) {
		echoln("They have rolled a " . $rolledNumber);
	}

	private function displayStatusAfterRoll($rolledNumber) {
		$this->displayCurrentPlayer();
		$this->displayRolledNumber($rolledNumber);
	}

	private function displayStatusAfterPlayerGettingOutOfPenaltyBox() {
		$this->displayPlayerGettingOutOfPenaltyBox();
		$this->displayPlayersNewLocation();
		$this->displayCurrentCategory();
	}

	private function displayStatusAfterNonPenalizedPlayerMove() {
		$this->displayPlayersNewLocation();
		$this->displayCurrentCategory();
	}

	private function getPlayerOutOfPenaltyBoxAndPlayNextMove($rolledNumber) {
		$this->isGettingOutOfPenaltyBox = true;
		$this->movePlayer($rolledNumber);
		$this->displayStatusAfterPlayerGettingOutOfPenaltyBox();
		$this->askQuestion();
	}

	private function keepPlayerInPenaltyBox() {
		$this->displayPlayerStaysInPenaltyBox();
		$this->isGettingOutOfPenaltyBox = false;
	}

	private function playNextMove($rolledNumber) {
		$this->movePlayer($rolledNumber);
		$this->displayStatusAfterNonPenalizedPlayerMove();
		$this->askQuestion();
	}

	private function playNextMoveForPlayerInPenaltyBox($rolledNumber) {
		if ($this->isOdd($rolledNumber)) {
			$this->getPlayerOutOfPenaltyBoxAndPlayNextMove($rolledNumber);
		} else {
			$this->keepPlayerInPenaltyBox();
		}
	}
}
