<?php

require_once __DIR__ . '/../trivia/php/RunnerFunctions.php';

class RunnerFunctionsTest extends PHPUnit_Framework_TestCase {

	function testItCanFindCorrectAnswer() {
		$this->assertAnswersAreCorrectFor($this->getCorrectAnswerIDs());
	}

	function testItCanFindWrongAnswer() {
		$this->assertFalse(isCurrentAnswerCorrect(WRONG_ANSWER_ID, WRONG_ANSWER_ID));
	}

	function testItCanTellIfThereIsNoWinnerWhenACorrectAnswerIsProvided() {
		$this->assertTrue(didSomebodyWin($this->aFakeGame(), $this->aCorrectAnswer()));
	}

	function testItCanTellIfThereIsNoWinnerWhenAWrongAnswerIsProvided() {
		$this->assertFalse(didSomebodyWin($this->aFakeGame(), $this->aWrongAnswer()));
	}

	private function assertAnswersAreCorrectFor($correctAnserIDs) {
		foreach ($correctAnserIDs as $id) {
			$this->assertTrue(isCurrentAnswerCorrect($id, $id));
		}
	}

	private function getCorrectAnswerIDs() {
		return array_diff(range(MIN_ANSWER_ID,MAX_ANSWER_ID), [WRONG_ANSWER_ID]);
	}

	private function aFakeGame() {
		return new FakeGame();
	}

	private function aCorrectAnswer() {
		$isCurrentAnswerCorrect = true;
		return $isCurrentAnswerCorrect;
	}

	private function aWrongAnswer() {
		$isCurrentAnswerCorrect = false;
		return $isCurrentAnswerCorrect;
	}

}

class FakeGame {

	function wasCorrectlyAnswered() {
		return false;
	}

	function wrongAnswer() {
		return true;
	}
}
 