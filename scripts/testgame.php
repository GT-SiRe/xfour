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
            
            $this->player1 = new player('x', 'human');
            $this->player2 = new player('o', 'ai');
        /*   //$this->drawBoardSideways();
            $this->addPiece($this->player1, 0);
            $this->addPiece($this->player2, 0);
            $this->addPiece($this->player1, 1);
            $this->addPiece($this->player2, 3);
            //$this->drawBoardSideways();
            $this->addPiece($this->player1, 0);
            $this->addPiece($this->player1, 0);
            $this->addPiece($this->player1, 0);
            $this->addPiece($this->player1, 0);
            $this->addPiece($this->player1, 0);
            $this->addPiece($this->player1, 0);*/

	    // 
            $this->addPiece($this->player1, 6);
            $this->addPiece($this->player2, 3);
            $this->addPiece($this->player1, 3);
            $this->addPiece($this->player2, 5);

//            $this->addPiece($this->player1, 6);
  //          $this->addPiece($this->player1, 6);
    //        $this->addPiece($this->player1, 6);


//            $this->drawBoard();

            $this->setup();
        }

        // Prompt player for certain info. (Num players, who goes first, difficulty), and set up
        // the game.
        function setup() {
            echo "\nWelcome to xfour!\n\n";

            $numPlayers = FALSE;
            while (! $numPlayers) {
	        $numPlayers  = readline("How many players? (1, 2) ");

                if ($numPlayers == '1'){
                    $numPlayers = 1;
                } elseif ($numPlayers == '2') { 
                    $numPlayers = 2;
                } elseif ($numPlayers == '0') {
                    echo "i\"An interesting game. The only winning move is not to play.\" Or in this case, go first.\n";
                    $numPlayers = 0;
                } else {
                    echo "Invalid number of players selected. Try again.\n";
                    unset($numPlayers);
                }
            }

            $whoGoesFirst = FALSE;
            while (! $whoGoesFirst) {
		$whoGoesFirst = readline("You are player 1. Who goes first? (1,2) ");
               
                if ($whoGoesFirst == '1') {
                    $whoGoesFirst = 1;
                } elseif ($whoGoesFirst == '2') {
                    $whoGoesFirst = 2;
                } else {
                    echo "Invalid input. Try again.\n";
                    unset($whoGoesFirst);
                }               
	    }

            if ($numPlayers == 1 || $numPlayers == 0){
	        $difficulty = FALSE;
                while (! $difficulty) {
                    $difficulty = readline("Enter AI difficulty (Normal, Hard) ");
                 
                    if (! in_array($difficulty, array('Normal','Hard' ))){
                        echo "Invalid input. Try again.";
                        unset($difficulty);
                    }
                }
            }

	    // numPlayers whoGoesFirst difficulty
            if ($numPlayers == 0) {
                $player1 = new player('x', 'ai'); 
                $player2 = new player('o', 'ai');
            } elseif ($numPlayers == 1 && $whoGoesFirst) {
                $player1 = new player('x','');
            } 
	    
            var_dump('end');
            
        }

        // Ask player for their move and process input into game actions.
        function promptMove($player) {}
            // Check if player is human or ai

        // Add a piece. Calls to the relevant column's function. Stores the result in the
        // gameRecord variable if successful.
        function addPiece($player, $col_num) {
            //TODO temporarily using x and o to represent players.
            $outcome = $this->col_array[$col_num]->addPiece($player);
            if (! $outcome) {

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
                    if ($this->col_values[$i] == '.') {
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
