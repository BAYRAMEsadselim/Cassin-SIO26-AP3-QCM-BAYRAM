import { ApiClient } from './ApiClient.js';
import { QuizPlayer } from './QuizPlayer.js';

// Une page qui contient <div id="quiz" data-tentative="12" data-questions='[...]'> lance le lecteur de QCM.
const quiz = document.getElementById('quiz');
if (quiz) {
    const questions = JSON.parse(quiz.dataset.questions);
    new QuizPlayer(quiz, questions, new ApiClient(), Number(quiz.dataset.tentative)).start();
}
