<?php
if (isset($_COOKIE["userID"])){
	$cookie = $_COOKIE["userID"];
} else {
	$value =  rand(0,30000);
	setcookie("userID",$value, time()+2*3600);
	$cookie = $value;
}
?>

<html>
<head>
<title></title>
<link rel="stylesheet" type="text/css" href="css/style.css">
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<head>
<body>
<div id="wrap">
	<div id="header">
		<h1>Bidding game</h1>
	</div> <!-- End Header -->
	<div id="sidebar">
		<?php include("php/nav.php"); ?>
	</div> <!-- End Sidebar -->
	<div id="content">
		<?php
			$where=$_GET["page"];
			switch($where){
			case "admin":
				require("php/admin.php");
				break;
			case "results":
				require("php/results.php");
				break;
			case "guess":
				require("php/enter_guess.php");
				break;
			default:
				require("php/home.php");
				break;
			}
		?>
	</div> <!-- End Content -->
	<div id="footer"><?php
		echo "userID: " . $cookie . "<br>";
		?>
	</div> <!-- End Footer -->
</div> <!-- End wrap -->
</body>
</html>
