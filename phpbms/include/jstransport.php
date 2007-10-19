<?php 
	$loginNoKick=true;
	$loginNoDisplayError=true;
	require_once("session.php");	
	
	//phone formating
	?>phoneRegExpression=<?php 
	switch(PHONE_FORMAT){
		case "US - Loose":
			?>/^(?:[\+]?(?:[\d]{1,3})?(?:\s*[\(\.-]?(\d{3})[\)\.-])?\s*(\d{3})[\.-](\d{4}))(?:(?:[ ]+(?:[xX]|(?:[eE][xX][tT][\.]?)))[ ]?[\d]{1,5})?$/;<?php
		break;
		case "US - Strict":
			?>/^[2-9]\d{2}-\d{3}-\d{4}$/;<?php		
		break;
		case "UK - Loose":
			?>/^((\(?0\d{4}\)?\s?\d{3}\s?\d{3})|(\(?0\d{3}\)?\s?\d{3}\s?\d{4})|(\(?0\d{2}\)?\s?\d{4}\s?\d{4}))(\s?\#(\d{4}|\d{3}))?$/;<?php
		break;	
		case "International":
		    ?>/^(\(?\+?[0-9]*\)?)?[0-9_\- \(\)]*$/;<?php
		break;	
	} 
		
	//date formating
	?>APP_PATH="<?php echo htmlQuotes(APP_PATH)?>";<?php

	//date formating
	?>DATE_FORMAT="<?php echo htmlQuotes(DATE_FORMAT)?>";<?php
	
	//time formating
	?>TIME_FORMAT="<?php echo htmlQuotes(TIME_FORMAT)?>";<?php

	//currency formating
	?>CURRENCY_SYMBOL="<?php echo htmlQuotes(CURRENCY_SYMBOL)?>";<?php

	?>CURRENCY_ACCURACY=<?php echo CURRENCY_ACCURACY?>;<?php

	?>DECIMAL_SYMBOL="<?php echo htmlQuotes(DECIMAL_SYMBOL)?>";<?php

	?>THOUSANDS_SEPARATOR="<?php echo htmlQuotes(THOUSANDS_SEPARATOR)?>";<?php

	?>LOGIN_REFRESH=<?php echo LOGIN_REFRESH?>;<?php

	?>TERM1_DAYS=<?php echo TERM1_DAYS?>;<?php
	
	?>MONTH_NAMES_LONG= [ <?php 
		
		$mNames = "";
		for($i=0; $i < 11; $i++)
			$mNames .= ', "'.strftime("%B", mktime(0, 0, 0, $i+1, 1, 1974)).'"';
		$mNames = substr($mNames, 2);
		
		echo $mNames;
	
	?> ];<?php 

	if(isset($phpbms->modules["bms"])){
	
		?>TERM1_DAYS=<?php echo TERM1_DAYS?>;<?php
	
	}//end if
	

?>
