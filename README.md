# Advent of code 2022
This year i'm doing advent of code in PHP 8.1. I'm pretty ok in PHP.
Used composer to pull in some helpful additions

## Running
Start by running `composer update` do download dependencies
Copy `.env.example` to `.env` and set the environment variable called `AOC_SESSION` with the session cookie of adventofcode.com
To advance a day you can user `php download.php <DAYNUMBER>` to download the puzzle input in it's own directory
It will also copy `template.php` to the directory and name it `run.php`, you may freely change the template file to your own needs 
