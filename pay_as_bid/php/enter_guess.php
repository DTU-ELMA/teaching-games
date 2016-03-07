<?php
// define variables and set to empty values
$nameErr = $bidErr = "";
$name = $bid = $id = "";
$message = "";

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (empty($_POST["name"])) {
    $nameErr = "Name is required";
  } else {
    $name = test_input($_POST["name"]);
    // check if name only contains letters and whitespace
    if (!preg_match("/^[a-zA-Z ]*$/",$name)) {
      $nameErr = "Only letters and white space allowed in name field";
    }
  }

if (isset($_COOKIE["userID"])) {
  $userID = $_COOKIE["userID"];
} else {
  $userID = -1;
}

  if (empty($_POST["guess"])) {
    $bidErr = "A price bid is required";
  } else {
    $bid = test_input($_POST["guess"]);
    // check if guess is a number
    if (!is_numeric($bid)) {
      $bidErr = "Price bid must be a number from 0 to 100, not" . $bid;
    }
    if ($bid < 0.0 or $bid > 100.0) {
      $bidErr = "Price bid must be a number from 0 to 100!";
    }

  }

    if(empty($bidErr) and empty($nameErr)) {
      $file = "php/bids.txt";
      file_put_contents($file, $userID . "," . $name . "," . $bid . PHP_EOL, FILE_APPEND | LOCK_EX);
      $message = "Succesfully submitted cost bid " . $bid . " from " . $name . " (ID: " . $userID . ").";
    }

}
?>

<h2>Bid your wind production</h2>

<p>Your wind production will be either 5, 10 or 15 MW. Everyone else's wind production is uncorrelated from yours.
Any uncovered load will be covered by an oil plant at 100&#36;&#47;MW, which is expected to be activated 10% of the time.</p>

<p>You only need to bid a price of your production; The amount you produce will be determined in real time.</p>

<!-- <p>Your marginal cost of production is <b><?php echo number_format(($cookie * 10.0) / 30000 + 5.0, 2, '.', ','); ?> </b>&#36;&#47;MW.</p> -->
<p>Your marginal cost of production is <b>0.00</b>&#36;&#47;MW. As this is a pay-as-bid market, you will have to bid at a higher price than this to make a profit.</p>

<form method="post" action="index.php?page=guess">  <!-- action="<?php echo htmlspecialchars($_SERVER["PHP_SELF "]);?>"> -->
Name: <input type="text" name="name" value="<?php echo $name;?>">
<span class="error"> <?php echo $nameErr;?></span>
<br><br>
Price bid (0.00-100.00): <input type="number" min=0 max=100 step=0.01 name="guess" value="<?php echo $bid;?>">
<span class="error"> <?php echo $bidErr;?></span>
<br><br>
<input type="submit" name="submit" value="Submit">
</form>

<p><?php echo $message;?></p>
