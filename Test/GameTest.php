<?php

require_once __DIR__ . '/../trivia/php/Game.php';

class GameTest extends PHPUnit_Framework_TestCase {

	private $game;

	function setUp() {
		$this->game = new Game;
	}

	function testAGameWithNotEnoughPlayersIsNotPlayable() {
		$this->assertFalse($this->game->isPlayable());
		$this->addJustNothEnoughPlayers();
		$this->assertFalse($this->game->isPlayable());
	}

	function testAfterAddingEnoughPlayersToANewGameItIsPlayable() {
		$this->addEnoughPlayers($this->game);
		$this->assertTrue($this->game->isPlayable());
	}

	function testItCanAddANewPlayer() {
		$this->assertEquals(0, count($this->game->players));
		$this->game->add('A player');
		$this->assertEquals(1, count($this->game->players));
		$this->assertDefaultPlayerParametersAreSetFor(1);
	}

	function testWhenAPlayerEntersAWrongAnswerItIsSentToThePenaltyBox() {
		$this->game->add('A player');
		$this->game->currentPlayer = 0;
		$this->game->wrongAnswer();
		$this->assertTrue($this->game->inPenaltyBox[0]);
		$this->assertEquals(0, $this->game->currentPlayer);
	}

	function testCurrentPlayerIsNotResetAfterWrongAnswerIfOtherPlayersDidNotYetPlay() {
		$this->addManyPlayers(2);
		$this->game->currentPlayer = 0;
		$this->game->wrongAnswer();
		$this->assertEquals(1, $this->game->currentPlayer);
	}

	function testTestPlayerWinsWithTheCorrectNumberOfCoins() {
		$this->game->currentPlayer = 0;
		$this->game->purses[0] = Game::$numberOfCoinsToWin;
		$this->assertFalse($this->game->didPlayerNotWin());
	}

	function testAPlayersNextPositionIsCorrectlyDeterminedWhenNoNewLapIsInvolved() {
		$currentPlace = 2;
		$rolledNumber = 1;

		$this->setAPlayerThatIsNotInThePenaltyBox();
		$this->setCurrentPlayersPosition($currentPlace);

		$this->game->roll($rolledNumber);

		$this->assertEquals('3', $this->getCurrentPlayersPosition(), 'Player was expected at position 3');
	}

	function testAPlayerWillStartANewLapWhenNeeded() {
		$currentPlace = 11;
		$rolledNumber = 2;

		$this->setAPlayerThatIsNotInThePenaltyBox();
		$this->setCurrentPlayersPosition($currentPlace);

		$this->game->roll($rolledNumber);

		$this->assertEquals('1', $this->getCurrentPlayersPosition(), 'Player was expected at position 3');
	}

	function testAPlayerWhoIsPenalizedAndRollsAnEvenNumberWillStayInThePenaltyBox() {
		$rolledNumber = 2;
		$this->setAPlayerThatIsInThePenaltyBox();

		$this->game->roll($rolledNumber);

		$this->assertFalse($this->game->isGettingOutOfPenaltyBox);
	}

	function testAPlayerWhoIsPenalizedAndRollsAnOddNumberWillGetOutOfThePenaltyBox() {
		$rolledNumber = 1;
		$this->setAPlayerThatIsInThePenaltyBox();

		$this->game->roll($rolledNumber);

		$this->assertTrue($this->game->isGettingOutOfPenaltyBox);
	}

	function testRockCategoryCanBeDetermined() {
		$currentPlaces = [3];
		$expectedCategory = 'Rock';
		$this->assertCorrectCategoryForGivenPlaces($expectedCategory, $currentPlaces);
	}

	function testScienceCategoryCanBeDetermined() {
		$currentPlaces = [1];
		$expectedCategory = 'Science';
		$this->assertCorrectCategoryForGivenPlaces($expectedCategory, $currentPlaces);
	}

	function testPlayerGettingOutOfPenaltyNextPositionWithoutNewLap() {
		$currentPlace = 2;
		$numberRequiredToGetOutOfPenaltyBox = 1;

		$this->setAPlayerThatIsInThePenaltyBox();
		$this->setCurrentPlayersPosition($currentPlace);

		$this->game->roll($numberRequiredToGetOutOfPenaltyBox);

		$this->assertEquals('3', $this->getCurrentPlayersPosition(), 'Player was expected at position 3');
	}

	function testPlayerGettingOutOfPenaltyNextPositionWithNewLap() {
		$currentPlace = 11;
		$numberRequiredToGetOutOfPenaltyBox = 3;

		$this->setAPlayerThatIsInThePenaltyBox();
		$this->setCurrentPlayersPosition($currentPlace);

		$this->game->roll($numberRequiredToGetOutOfPenaltyBox);

		$this->assertEquals('2', $this->getCurrentPlayersPosition(), 'Player was expected at position 3');
	}

	private function addEnoughPlayers() {
		$this->addManyPlayers(Game::$minimumNumberOfPlayers);
	}

	private function addJustNothEnoughPlayers() {
		$this->addManyPlayers(Game::$minimumNumberOfPlayers - 1);
	}

	private function addManyPlayers($numberOfPlayers) {
		for ($i = 0; $i < $numberOfPlayers; $i++) {
			$this->game->add('A Player');
		}
	}

	private function assertDefaultPlayerParametersAreSetFor($playerId) {
		$this->assertEquals(0, $this->game->places[$playerId]);
		$this->assertEquals(0, $this->game->purses[$playerId]);
		$this->assertFalse($this->game->inPenaltyBox[$playerId]);
	}

	private function setAPlayerThatIsNotInThePenaltyBox() {
		$this->game->currentPlayer = 0;
		$this->game->players[$this->game->currentPlayer] = 'John';
		$this->game->inPenaltyBox[$this->game->currentPlayer] = false;
	}

	private function setAPlayerThatIsInThePenaltyBox() {
		$this->game->currentPlayer = 0;
		$this->game->players[$this->game->currentPlayer] = 'John';
		$this->game->inPenaltyBox[$this->game->currentPlayer] = true;
	}

	private function setCurrentPlayersPosition($currentPlace) {
		$this->game->places[$this->game->currentPlayer] = $currentPlace;
	}

	private function getCurrentPlayersPosition() {
		return $this->game->places[$this->game->currentPlayer];
	}

	private function assertCorrectCategoryForGivenPlaces($expectedCategory, $currentPlaces) {
		foreach ($currentPlaces as $currentPlace) {
			$this->setAPlayerThatIsNotInThePenaltyBox();
			$this->setCurrentPlayersPosition($currentPlace);
			$foundCategory = $this->game->currentCategory();
			$this->assertEquals($expectedCategory, $foundCategory,
				'Expected ' . $expectedCategory . ' category for position ' . $currentPlace .
				' but got ' . $foundCategory);
		}
	}

}
 