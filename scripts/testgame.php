<?php

    $game = new board();

    //A class to describe and represent the full xfour board.
    class board {
        //private $col_array = array();
        private $col_array = array();
        private $player1;
        private $player2;
        private $gameRecord = '';

        function __construct(){
            //Set up the board.
            for ($i = 0 ; $i < 7 ; $i++) {
               $this->col_array[] = new column();
            }

	    //Debug Setup
            $this->setup();
        }

        // Prompt player for certain info. (Num players, who goes first, difficulty), and set up
        // the game.
        function setup() {
            echo "\nWelcome to xfour!\n\n";

            $numPlayers = FALSE;
            while (! $numPlayers) {
	        $numPlayers  = readline("How many players? (1, 2) ");

                if ($numPlayers === '1'){
                    $player_types = array('human','ai');
                } elseif ($numPlayers === '2') { 
                    $player_types = array('human','human');
                } elseif ($numPlayers === '0') {
                    echo "i\"An interesting game. The only winning move is not to play.\" Or in this case, go first.\n";
                    $player_types = array('ai','ai');
                } else {
                    echo "Invalid number of players selected. Try again.\n";
                    unset($numPlayers);
                }
            }

            $whoGoesFirst = FALSE;
            while (! $whoGoesFirst) {
		$whoGoesFirst = readline("Current user is player 1. Which player goes first? (1,2) ");
               
                if ($whoGoesFirst === '1') {
                    $player_pieces = array('x', 'o');
                } elseif ($whoGoesFirst === '2') {
                    $player_pieces = array('o', 'x');
                } else {
                    echo "Invalid input. Try again.\n";
                    unset($whoGoesFirst);
                }               
	    }

            if ($numPlayers === 1 || $numPlayers === 0){
	        $difficulty = FALSE;
                while (! $difficulty) {
                    $difficulty = readline("Enter AI difficulty (Normal, Hard) ");
                 
                    if (! in_array($difficulty, array('Normal','Hard'))){
                        echo "Invalid input. Try again.";
                        unset($difficulty);
                    }
                }
            }

	    // Set up the players.
            $player1 = new player($player_pieces[0], $player_types[0]);
            $player2 = new player($player_pieces[1], $player_types[1]);

            $active_player   = ($player->getPiece() === 'x') ? $player1 : $player2;
            $inactive_player = ($active_player === $player1) ? $player2 : $player1;

	    // The core game loop - players taking turns adding pieces.
            $gameWinner = FALSE;

            while (! $gameWinner) {
                // Active player gets prompted to take a move. 
                $this->promptMove($active_player);

                // Then the move is executed.
                
                // Then the win condition is checked.
                $gameWinner = $this->checkForWin();

                // If the game is not over, the inactive player becomes active.
		$tmp_player      = $active_player;
                $active_player   = $inactive_player;
                $inactive_player = $tmp_player;
            } 
        }

        // Ask player for their move and process input into game actions.
        function promptMove($player) {
            // Check if player is human or ai. If human, prompt for a move. If ai, call the 
            // calcMove function, passing in the difficulty. Moves are returned from the 
            // players as a single character string ('a'-'g') representign a column.
            if ($player->getType === 'human') {

            } else {

            }

	    // Map input to relevant column use ascii characters and 'ord' function (dep?)

            // Return mapped input.
            // Add a piece. Calls to the relevant column's function. Stores the result in the
            // gameRecord variable if successful.
        }

        function addPiece($player, $col_num) {
            //TODO temporarily using x and o to represent players.
            $outcome = $this->col_array[$col_num]->addPiece($player);
            if (! $outcome) {
	        return FALSE;
            } else {
                return TRUE;
            } 
        }

        // A function to see if either player has won the game.
        function checkForWin() { }

        // A function to detect threats (may need some sophistication)
        function detectThreat() { }

        //One of the first things I'll implement.
        function drawBoard() {
            // Break columns out into rows - this should be abstracted away somehow. ??

            $rows = $this->colsToRows();

            $row_str = array();

            foreach ($rows AS $row) {
                $row_str[] = implode(' ', $row);
            }

            echo "\nBoard State: \n\n";
            echo "    a b c d e f g \n";
            echo '  6 ' . $row_str[5] . "\n";
            echo '  5 ' . $row_str[4] . "\n";
            echo '  4 ' . $row_str[3] . "\n";
            echo '  3 ' . $row_str[2] . "\n";
            echo '  2 ' . $row_str[1] . "\n";
            echo '  1 ' . $row_str[0] . "\n\n";
        }

        function drawBoardSideways() {
	    echo "    1 2 3 4 5 6 \n";
	    echo "  a " . implode(' ', $this->col_array[0]->getColValues()) . "\n";
            echo "  b " . implode(' ', $this->col_array[1]->getColValues()) . "\n";
            echo "  c " . implode(' ', $this->col_array[2]->getColValues()) . "\n";
            echo "  d " . implode(' ', $this->col_array[3]->getColValues()) . "\n";
            echo "  e " . implode(' ', $this->col_array[4]->getColValues()) . "\n";
            echo "  f " . implode(' ', $this->col_array[5]->getColValues()) . "\n";
            echo "  g " . implode(' ', $this->col_array[6]->getColValues()) . "\n";
        }

        // Converts columns to rows.
        function colsToRows() {
            $rows = array();

            // Nested for loop for conversion.
            for ($i = 0 ; $i < count($this->col_array) ; $i++ ) {
                $col = $this->col_array[$i]->getColValues();

                for ($j = 0 ; $j < count($col) ; $j++) {
                    $rows[$j][] = $col[$j];
                }
            }

            return $rows;
        }

        // A function that takes in a properly formatted string and generates a game in that state.
        function recreateState() { }

        // A function that takes the current board state and generates a formatted string from which
        // users may recreate that game state.
        function exportState() { }

    }

    class column {
        // After 5 minutes of research on stackoverflow via Google, it was determined that
        // implementing this using splFixedArray or any other data structure wouldn't
        // realistically offer any major benefits. In the interest of getting it done quickly,
        // plain old vanilla arrays will be used.

        // "First value" of the array is the "bottom". This is arbitrary.
        private $col_values;

        public function __construct() {
            // Initialize $col_values to the 'empty' values of '.'
            $this->col_values = array('.','.','.','.','.','.');
        }

        public function getColValues() {
            return $this->col_values;
        }

        public function addPiece(&$player){
            // First check to see if the column is full.
            if ($this->checkFull()) {
                return FALSE;
            } else {
                for ($i = 0 ; $i < count($this->col_values) ; $i++) {
                    if ($this->col_values[$i] === '.') {
                        $this->col_values[$i] = $player->getPiece();
                        return $i;
                    }
                }
            }
        }

        public function checkFull() {
            return (($this->col_values[5] !== '.') ? TRUE : FALSE);
        }
    }

    class player {
        private $piece;
        private $ptype;

        function __construct($piece,$type) {
            $this->piece = $piece;
        }

	function getPiece(){
	    return $this->piece;
	}

	function getType(){
	    return $this->ptype;
	}

    }

    class ai extends player {
        private $difficulty;
    }

?>
