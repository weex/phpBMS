<?php
    if(!isset($phpbms))
        exit();

    if($phpbms->showFooter)
{?>
<div id="footer">
	<p id="footerAbout"><a href="http://www.phpbms.org" target="_blank">phpBMS</a> By <a href="http://www.kreotek.com" target="_blank">Kreotek, LLC</a></p>
	<p id="footerTop"><a href="#toptop">top</a></p>
</div>
<?php }//end if ?>
<?php $phpbms->showExtraJs($phpbms->bottomJS) ?>
</body>
</html>
