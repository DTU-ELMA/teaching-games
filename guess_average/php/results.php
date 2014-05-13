<h2>Results</h2>

<p></p>

<?php


function aasort (&$array, $key) {
    $sorter=array();
    $ret=array();
    reset($array);
    foreach ($array as $ii => $va) {
        $sorter[$ii]=$va[$key];
    }
    asort($sorter);
    foreach ($sorter as $ii => $va) {
        $ret[$ii]=$array[$ii];
    }
    $array=$ret;
}

$guessarray = array();
$userarray = array();
$file = fopen("php/guesses.txt", "r") or exit("Unable to open file!");
//Output a line of the file until the end is reached
while(!feof($file))
  {
	$line=fgets($file);
	$part=explode(",",$line);
	$guessarray[$part[0]] = array($part[1], $part[2]);
  }
fclose($file);

//Sort array by guess
aasort($guessarray,1);

//Calculate average
$average = 0.0;
$n = 0;
foreach ($guessarray as $part){
  $average += $part[1];
  $n += 1;
}

$average = $average *2/(3*$n);

$bestguess = 0.0;
$bestdist = 200.0;
$bestnames = array();
foreach ($guessarray as $part){
  $dist = abs($part[1] - $average);
  if($dist <= $bestdist){
    if($part[1]==$bestguess){
      $bestnames[] = $part[0];
    } else {
      $bestnames = array($part[0]);
    }
    $bestdist = $dist;
    $bestguess = $part[1];
  }
}



echo "<p><font size = \"6\" >2/3rds of average: " . number_format($average,2) . "</font></p>";

echo "<p><font size = \"6\" >Best guess: " . number_format($bestguess,2) . " by ";
foreach ($bestnames as $thename) {
  echo $thename . ", ";
}
echo "</font></p>";


echo "<table>";
  echo "<tr><td>"."Name"."</td><td>"."Guess"."</td></tr>";

foreach ($guessarray as $userID => $part){
  echo "<tr><td>".$part[0]."</td><td>".$part[1]."</td></tr>";
}
echo "</table>";
?>
