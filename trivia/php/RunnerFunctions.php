<?php

include_once __DIR__ . '/Game.php';

const WRONG_ANSWER_ID = 7;
const MIN_ANSWER_ID = 0;
const MAX_ANSWER_ID = 9;

function isCurrentAnswerCorrect($minAnswerId = MIN_ANSWER_ID, $maxAnswerId = MAX_ANSWER_ID) {
	return rand($minAnswerId, $maxAnswerId) != WRONG_ANSWER_ID;
}

function run() {

	$aGame = new Game();
	$aGame->add("Chet");
	$aGame->add("Pat");
	$aGame->add("Sue");

	do {
		$dice = rand(0, 5) + 1;
		$aGame->roll($dice);
	} while (!didSomebodyWin($aGame, isCurrentAnswerCorrect()));
}

function didSomebodyWin($aGame, $isCurrentAnswerCorrect) {
	if ($isCurrentAnswerCorrect) {
		return ! $aGame->wasCorrectlyAnswered();
	} else {
		return ! $aGame->wrongAnswer();
	}
}
