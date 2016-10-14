<?php
    // TODO - Need to add interface documentation headers to classes and functions.
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
                    $whoGoesFirst = FALSE;
                }               
            }

            if ($numPlayers === 1 || $numPlayers === 0){
            $difficulty = FALSE;
                while (! $difficulty) {
                    $difficulty = readline("Enter AI difficulty (Normal, Hard) ");
                 
                    if (! in_array($difficulty, array('Normal','Hard'))){
                        echo "Invalid input. Try again.";
                        $difficulty = FALSE;
                    }
                }
            }

            // Set up the players.
            $player1 = new player($player_pieces[0], $player_types[0]);
            $player2 = new player($player_pieces[1], $player_types[1]);

            $active_player   = ($player1->getPiece() === 'x') ? $player1 : $player2;
            $inactive_player = ($active_player === $player1) ? $player2 : $player1;

            // The core game loop - players taking turns adding pieces.
            $gameWinner = FALSE;

            while (! $gameWinner) {
                //First, draw the board.
                $this->drawBoard();

                // Active player gets prompted to take a move. 
                $move = $this->promptMove($active_player);

                // Then the move is executed.
                $this->addPiece($active_player, $move);

                // Then the win condition is checked. Technically this isn't needed until the
                // 7th move at the earliest, but that's probably an unnecessary optimization. 
                if ($this->checkForWin($active_player->getPiece())){
                    $gameWinner = $active_player;
                }
              
                // If the game is not over, the inactive player becomes active and we repeat.
                $tmp_player      = $active_player;
                $active_player   = $inactive_player;
                $inactive_player = $tmp_player;
            }

            $this->drawBoard();
            echo "\nPlayer " . $gameWinner->getPiece() . " wins!\n";
        }

        // Ask player for their move and process input into game actions.
        function promptMove($player) {
            // Check if player is human or ai. If human, prompt for a move. If ai, call the 
            // calcMove function, passing in the difficulty. Moves are returned from the 
            // players as a single character string ('a'-'g') representing a column.
            if ($player->getType() === 'human') {
                $move_valid  = '';
                $move_letter = '';
                while (!($move_valid) && !($move_letter)) { 
                    $move_letter = readline("Input column letter for next move. (a, b, c ... g) ");

                    if (! in_array($move_letter, array('a','b','c','d','e','f','g'))) {
                        echo "\nInvalid move. Please input a valid column letter.\n"; 
                        $move_letter = '';   
                    } else {
                        // Map input to relevant column using ascii chars and 'ord' function.
                        // Ascii char 'a' is 97; $move_col is 0 for str 'a'
                        $move_col = ord($move_letter) - 97; 
                        $move_valid = (! ($this->col_array[$move_col]->checkFull()));

                        if (!($move_valid)){
                            echo "\nInvalid move. That column is full. Choose another column.\n";
                            $move_letter = '';
                        }
                    }
                } //end while loop
            } else {
                  //AI Player case.
//                $move_letter = player->calcMove(); // TODO
            }

            // Map input to relevant column use ascii characters and 'ord' function (dep?)

            // Return mapped input.
            return $move_col;
        }

        function addPiece($player, $col_num) {
            $outcome = $this->col_array[$col_num]->addPiece($player);
            if (! $outcome) {
                return FALSE;
            } else {
                return TRUE;
            } 
        }

        /**  A function to see if the active player has won the game. 
         *
         *   Technically four sub-checks are needed: Checking across, checking down, and checking
         *   diagonally up and down. However, it is possible to use the row to col conversion to
         *   only use two total functions to do all the necessary checks.
         */
        function checkForWin($piece) { 
            //Format column values
            foreach ($this->col_array AS $column){
                $values[] = $column->getColValues();
            }

            // Check 'down'             
            $check_down    = $this->checkDown($values, $piece);

            // Use the colsToRows function to 'rotate the board', to check 'across'.
            $check_across  = $this->checkDown($this->colsToRows(), $piece);
             
            $check_diag_up   = $this->checkDiagonalUp($values, $piece);
            $check_diag_down = $this->checkDiagonalDown($values,$piece);

	    if ($check_down || $check_across || $check_diag_up || $check_diag_down) {
                return TRUE;
            }
        }

        function checkDown($input, $piece) {
            // Need four in a row to win, but this could be adjusted. But why would you? 
            // It kind of breaks the design and balance of the game. You monster.
            $numToWin = 4;

            foreach ($input AS $column) {
                //Number of times encountered the piece consecutively
                $count = 0;

                //Traversing through a single 'column'
                $i = 0; //Start with 'first' column
                while (($i < count($column)) && (! ((count($column)-1) - $i) < $numToWin - $count)) {
                //for ($i = 0 ; $i < count($column) ; $i++) {
                    // As an optimization, if count is less than distance from the 'end' of the col,
                    // then there can't be 'numToWin' 
                    if ($column[$i] === $piece) {
                        $count++;
                        if ($count === $numToWin) {
                            return TRUE;
                        }
                    } else {
                        $count = 0;
                    }

                    ++$i;

                }
            }
            
            return FALSE;
        }

        function createPlayer($piece, $type) {

        }

        function checkDiagonalUp($input, $piece, $threatDetect = FALSE) {
            $numToWin = 4;
            
            // Traverse diagonally upward keeping track of 'count'.

            // Had some trouble coming up with a good way to handle this. My first instinct
            // was to use one loop with some kind of way to 'speculate' on points below the 
            // board and traverse diagonally upwards checking validity until reaching the
            // board. Instead I decided to use two loops: one travelling downwards from the
            // 'top' and then one travelling 'rightwards' from the left, checking diagonally
            // upwards each time. 
            //
            // There is almost certainly a more optimal way to do this, but I have yet to
            // think of any obvious improvments and time is a factor.

            // First half, traversing downwards from highest possible diagonal.
            $diagonalLength = $numToWin;
            for ($i = (count($input[0]) - $numToWin) ; $i > -1 ; $i--){
                $count = 0;

                for ($j = 0 ; $j < $diagonalLength ; $j++) {
                    if ($input[$j][$i + $j] === $piece) {
                        $count++;
                        if ($count === $numToWin) {
                            return TRUE;
                        }
                    } else {
                        $count = 0;
                    }      
                }
                if (! (($i + $j + 1) > (count($input[0])))) {
                    ++$diagonalLength;
                }
            }

            // Second half, traversing right from second left-most possible lower diagonal.
            $diagonalLength = $numToWin;
            for ($j = 1 ; $j < (count($input) - $numToWin) ; $j++){
                $count = 0;

                for ($i = 0; $i < $diagonalLength ; $i++) {
                    if ($input[$j][$i+$j] === $piece) {
                        $count++;
                        if ($count === $numToWin) {
                            return TRUE;
                        }
                    } else { 
                        $count = 0;
                    }
                }
            } 
            if (! (($i + $j + 1) > (count($input)))) {
                ++$diagonalLength;
            }

        }

        // Basically a similar function to checkDiagonal, but going downards. Originally wanted to
        // use only one diagonal function and manipulate input - in the end had to just add
        // this as an entirely separate function due to debugging difficulties and time 
        // considerations. Also using this as a prototype for adapting these methods to detecting
        // threats. On the upside, once you don't have to worry about semi-unpredictable input
        // anymore, these functions get way easier to code!
        function checkDiagonalDown($cols, $piece, $threatDetect = FALSE) {
            $numToWin = 4;

            // First half, traversing up from lowest possible lower downward diagonal
            $diagonalLength = $numToWin;
            for ($j = $numToWin - 1 ; $j < count($cols[0]) -1 ; $j++){
                $count = 0;

                for ($i = 0 ; $i < $diagonalLength ; $i++){
                    if ($cols[$i][$j - $i] === $piece){
                        $count++;
                        if ($count === $numToWin) {
                            return TRUE;
                        } elseif ($threatDetect && $count === $numToWin - 1) {
                            if (FALSE){// Check validity of address and whether threat's imminent.
                                return array($i-1, $j-1); //Address of threat
                            }
                        }
                    } else {
                        $count = 0;
                    }
                }

                if (! (($i + $j + 1) > (count($cols)))) {
                    ++$diagonalLength;
                }
            } // End first half

            // Second half, traversing 'left' from rightmost possible upper downward diagonal
            $diagonalLength = $numToWin;
            for ($i = $numToWin - 1 ; $i > -1; $i--){
                $count = 0;

                for ($j = count($cols[0]) - 1 ; $j > ((count($cols[0]) - $diagonalLength) - 1); $j--) {
                    // Had some brief trouble mentally defining this relationship. This is the
                    // kind of function that would probably want more thorough documentation.
	            $i_value = $i + $j - (count($cols[0])-1);

                    if($i_value < 0) { echo "i value: $i j value: $j ";}

                    if ($cols[$i_value][$j] === $piece) {
                        $count++;
                        if ($count === $numToWin) {
                            return TRUE;
                        } elseif ($threatDetect && $count === $numToWin - 1) {
                            if (FALSE){ // Check validity of address and whether threa's imminent.
                                return array($i-1, $j-1); //Address of threat
                            }
                        }
    
                     } else { 
                         $count = 0;
                     }
                }
//Believe this condition is wrong
                if (! (($i + $j + 1) > (count($cols[0])))) {
                    ++$diagonalLength;
                }
            } // End second half.
        } // End checkDiagonalDown

        // A function to detect threats
        function detectThreat() { }

        //One of the first things I'll implement.
        function drawBoard() {
            // Break columns out into rows
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
            //This is temporarily hard-coded but could be changed.
            return (($this->col_values[5] !== '.') ? TRUE : FALSE);
        }

        public function nextRow() {
            //What row index will next piece dropped in on this column have?
            for ( $i = 0 ; $i < count($this->col_values) ; $i++ ) {
                // Check to see if a piece is present. On the first row for which one is not, 
                // return the index. 
                if ($this->col_values[$i] === '.') {
                    return $i;
                }
            } 
        }
    }

    class player {
        private $piece;
        private $ptype;

        function __construct($piece,$type) {
            $this->piece = $piece;
            $this->ptype = $type;
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

