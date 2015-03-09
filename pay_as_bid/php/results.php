<h2>Results</h2>

<p><img src="pic/out.png" alt="Mountain View" style="width:800px;height:500px"></p>

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


if (isset($_COOKIE["userID"])) {
  $userID = $_COOKIE["userID"];
} else {
  $userID = -1;
}

$guessarray = array();
$file = fopen("php/stats.txt", "r") or exit("Unable to open file!");
//Output a line of the file until the end is reached
while(!feof($file))
  {
	$line=fgets($file);
	if(!empty($line)){
		$part=explode(",",$line);
		$guessarray[$part[0]] = array($part[1], $part[2], $part[3], $part[4]);
	}
  }
fclose($file);

if(array_key_exists($userID,$guessarray)){
    echo "<p><font size = \"4\" >ID: " . number_format($userID,0) . " last produced " . number_format($guessarray[$userID][1],0) . " MWh of which " . number_format($guessarray[$userID][2],0) . " was scheduled at " . number_format($guessarray[$userID][0],2) ."&#36;&#47;MW. </p> <p>A total of " . number_format($guessarray[$userID][3],2) . "&#36; earned so far.</font></p>";
} else {

    echo "<p><font size = \"4\" >ID: " . number_format($userID,2) . " not found.</font></p>";
}

?>
