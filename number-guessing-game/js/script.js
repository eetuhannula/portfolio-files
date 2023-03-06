const mysteryNumber = Math.ceil(Math.random()*100);
let playerGuess = 0; 
let guessesRemaining = 10;
let guessesMade = 0;
let gameState = "";
let gameWon = false;

const input = document.querySelector("#input");
const output = document.querySelector("#output");
const button = document.querySelector("button");
button.style.cursor = "pointer";
button.addEventListener("click", clickHandler, false);
window.addEventListener("keydown", keydownHandler, false);


function keydownHandler (e) {
    if(e.keyCode === 13) {
        validateInput();
    }
}

function clickHandler() {
    validateInput();
}

function validateInput() {
    playerGuess = parseInt(input.value)
    if (isNaN(playerGuess)) {
        output.innerHTML = "Syötä vain numeroita!"
    }
    else {
        playGame();
    }
}

function playGame() {
    guessesRemaining--;
    guessesMade++;
    gameState = "Guess nr: " + guessesMade + ", you have " + guessesRemaining + " attempts remaining";
    playerGuess = parseInt(input.value);

    if (playerGuess > mysteryNumber) {
        output.innerHTML = "WHOAH! Thats too much! " + gameState;
        if (guessesRemaining < 1) {
            endGame();
        }
    }
    else if(playerGuess < mysteryNumber) {
        output.innerHTML = "NOPE! That's not enough! " + gameState;
        if (guessesRemaining < 1) {
            endGame();
        }
    }
    else {
        gameWon = true;
        endGame();
        } 
    
}

function endGame() {
    if(gameWon) {
        output.innerHTML = "Thats right! Secret number was " + mysteryNumber + ". Refresh page to start new game.";
    }
    else {
        output.innerHTML = "GAME OVER! You failed. Secret number was " + mysteryNumber + ". Refresh page to start new game.";
    }
    const button = document.querySelector("button");
    button.disabled = true;
    const input = document.querySelector("#input");
    input.disabled = true;

}