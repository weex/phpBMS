<?php 
	$loginNoKick=true;
	$loginNoDisplayError=true;
	require_once("session.php");
	require_once("common_functions.php");
	
	
	//phone formating
	?>phoneRegExpression=<?php 
	switch($_SESSION["phone_format"]){
		case "US - Loose":
			?>/^(?:[\+]?(?:[\d]{1,3})?(?:\s*[\(\.-]?(\d{3})[\)\.-])?\s*(\d{3})[\.-](\d{4}))(?:(?:[ ]+(?:[xX]|(?:[eE][xX][tT][\.]?)))[ ]?[\d]{1,5})?$/;<?php
		break;
		case "US - Strict":
			?>/^[2-9]\d{2}-\d{3}-\d{4}$/;<?php		
		break;
		case "UK - Loose":
			?>/^((\(?0\d{4}\)?\s?\d{3}\s?\d{3})|(\(?0\d{3}\)?\s?\d{3}\s?\d{4})|(\(?0\d{2}\)?\s?\d{4}\s?\d{4}))(\s?\#(\d{4}|\d{3}))?$/;<?php
		break;	
	} echo "\n\n";	
	
	//date formating
	?>APP_PATH="<?php echo htmlQuotes($_SESSION["app_path"])?>";<?php
	echo "\n\n";

	//date formating
	?>DATE_FORMAT="<?php echo htmlQuotes($_SESSION["date_format"])?>";<?php
	echo "\n\n";
	
	//time formating
	?>TIME_FORMAT="<?php echo htmlQuotes($_SESSION["time_format"])?>";<?php
	echo "\n\n";

	//currency formating
	?>CURRENCY_SYMBOL="<?php echo htmlQuotes($_SESSION["currency_symbol"])?>";<?php
	echo "\n";

	?>CURRENCY_ACCURACY=<?php echo $_SESSION["currency_accuracy"]?>;<?php
	echo "\n";

	?>DECIMAL_SYMBOL="<?php echo htmlQuotes($_SESSION["decimal_symbol"])?>";<?php
	echo "\n";

	?>THOUSANDS_SEPARATOR="<?php echo htmlQuotes($_SESSION["thousands_separator"])?>";<?php
	echo "\n";

	
 	if(isset($_SESSION["includedJS"])){
		echo "\n\n".$_SESSION["includedJS"];
		$_SESSION["includedJS"]="";
	}
?>
