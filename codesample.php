<?php
/*
    Simple PHP Code Sample: Monty Hall Problem
    Michael Barklage, Aug 2016
*/

function MontyHall() {

    // get form data, if applicable
    // casting to int turns string into 0, essentially validating user input
    // default values are 100 iterations and not verbose
    (isset($_POST['iterations'])) ? $iterations = (int)$_POST['iterations'] : $iterations = 100;
    (isset($_POST['verbose']) && $_POST['verbose'] == "on") ? $verbose = true : $verbose = false;
    
    $output = "";
    $wins = 0;
    $losses = 0;

    // if form data is invalid, then don't bother
    if ($iterations > 0 && $iterations <= 10000) {
        for ($n = 0; $n < $iterations; $n++) {
            // randomize car location and player choice
            $car = rand(1, 3);
            $choice = rand(1, 3);
            
            if ($verbose) { $output .= "Car is behind door #".$car.", player chooses door #".$choice.". Switching results in "; }
            if ($car == $choice) {
                // if the player chose the car the first time around, then switching loses
                if ($verbose) { $output .= "LOSS.<br>"; }
                $losses++;
            }
            else {
                // switching to the other door results in winning the car
                if ($verbose) { $output .= "WIN.<br>"; }
                $wins++;
            }
        }

        // round win/loss percentages to one decimal point
        $output .= "Wins by switching: ".$wins." / ".$iterations." (". round(($wins/$iterations) * 100, 1) . "%)<br>";
        $output .= "Wins by staying: ".$losses." / ".$iterations." (". round(($losses/$iterations) * 100, 1) ."%)<br><br>";

    }
	else {
		$output .= "Sorry, you can't do ".$iterations." iterations.<br>";
	}

    // output the form
    $output .= "<form method='POST' id='monty' name='monty' action='./codesample.php'>";
    $output .= "Simulate the Monty Hall Problem <input type='text' id='iterations' name='iterations' size='3' value='" . $iterations . "'> times. (1-10000)<br>";
    $output .= "<input type='checkbox' name='verbose' id='verbose'> Verbose<br>";
    $output .= "<input type='submit' id='submit' value='Submit'>";
    $output .= "</form>";

    return $output;
}

echo MontyHall();

?>