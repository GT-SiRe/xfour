## Xfour - An open-source PHP web application game!

Xfour is an open-source PHP game allowing users to play Connect Four (also known as Captain's Mistress, Four Up, Plot Four, Find Four, Four in a Row, Four in a Line, and Gravitrips.

The game also offers an AI player (not yet implemented) with optional difficulties which allows players to test their skills. 

The game of connect four is mathematically solved. This project is *not* an effort to demonsrtate a working 'perfect' AI. 

### Known Bugs

There are currently several bugs with the functions used to detect game-winning conditions. The upper diagonals do not work. Any consecutive winning sequence on the four right most columns do not win.

### AI

The solving functions were written in a general way in order to adapt the program to possibly use different board sizes or different size of winning sequence (three-in-a-row, five-in-a-row). They were also intended to be re-used in order to assist with AI detection of threats. numToWin - 1 in a row for an opposing player indicates a threat, and the next position in line, should be denied. 
