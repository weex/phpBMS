<?php
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
 +-------------------------------------------------------------------------+
 | Copyright (c) 2004 - 2010, Kreotek LLC                                  |
 | All rights reserved.                                                    |
 +-------------------------------------------------------------------------+
 |                                                                         |
 | Redistribution and use in source and binary forms, with or without      |
 | modification, are permitted provided that the following conditions are  |
 | met:                                                                    |
 |                                                                         |
 | - Redistributions of source code must retain the above copyright        |
 |   notice, this list of conditions and the following disclaimer.         |
 |                                                                         |
 | - Redistributions in binary form must reproduce the above copyright     |
 |   notice, this list of conditions and the following disclaimer in the   |
 |   documentation and/or other materials provided with the distribution.  |
 |                                                                         |
 | - Neither the name of Kreotek LLC nor the names of its contributore may |
 |   be used to endorse or promote products derived from this software     |
 |   without specific prior written permission.                            |
 |                                                                         |
 | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONtrIBUTORS     |
 | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT       |
 | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A |
 | PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT      |
 | OWNER OR CONtrIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,   |
 | SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT        |
 | LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,   |
 | DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY   |
 | THEORY OF LIABILITY, WHETHER IN CONtrACT, StrICT LIABILITY, OR TORT     |
 | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE   |
 | OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.    |
 |                                                                         |
 +-------------------------------------------------------------------------+
*/
	require("../include/session.php");

	class versions{

		var $db;
		var $queryresult;

		function versions($db){

			$this->db = $db;

		}//end function init

		function get(){

			$querystatement = "
				SELECT
					`name`,
					`displayname`,
					`version`
				FROM
					`modules`
				ORDER BY
					`id`";

			$this->queryresult = $this->db->query($querystatement);

		}//end function get


		function show(){

			if(!$this->queryresult)
				return false;

			while($therecord = $this->db->fetchArray($this->queryresult)){

				if($therecord["name"] != "base")
					echo formatVariable($therecord["displayname"]).": v".$therecord["version"]."<br />";
				else
					echo '<strong>v'.$therecord["version"].'</strong><br /><br />';

			}//endwhile

			return true;

		}//end function show

	}//end class

	$versions = new versions($db);
	$versions->get();

?>
<div class="box" id="helpBox">

	<p align="right" style="float:right; text-align:right; padding-left: 10px;" class="small">
		<img src="<?php echo APP_PATH?>common/image/logo.png" alt="phpBMS Logo" width="85" height="22"/><br />
		<?php $versions->show()?>
	</p>

	<h3 style="margin-top:0">phpBMS</h3>
	<p><strong>Commercial Open Source Business Management Web Application</strong>

	<p class="tiny">Copyright &reg; <?php echo date("Y") ?> Kreotek, LLC. All Rights Reserved. phpBMS, and the phpBMS logo are trademarks of Kreotek, LLC.</p>


	<p class="tiny" style="clear: right;float: right;"><a href="http://www.kreotek.com" target="_blank">http://www.kreotek.com</a></p>


	<p class="small">
		<strong>Kreotek, LLC</strong><br />
		610 Quantum Rd. NE<br />
		Rio Rancho, NM 87124 USA
	</p>


	<p class="tiny">
		U.S. and Canada Toll Free<br />
		1-800-731-8026
	</p>

	<p class="tiny">
		Outside US and Canada<br />
		+1.505.349.0437
	</p>

	<h3>Community Support</h3>
	<ul>
		<li class="small"><a href="http://www.phpbms.org" target="_blank">phpBMS project Web Site</a></li>
		<li class="small"><a href="http://www.phpbms.org/forum" target="_blank">phpBMS Community Support forum</a></li>
		<li class="small"> <a href="http://phpbms.org/wiki/PhpbmsGuide" target="_blank">phpBMS Wiki Documentation </a></li>
	</ul>

	<h3>Paid Support and Customization</h3>
	<p class="small">
		Receive paid support directly from the creators of phpBMS, Kreotek.
		We have multiple tiers of support contracts available and can customize
		phpBMS to suit your specific needs.
	</p>

</div>
<p align="right"><button id="helpClose" type="button" class="Buttons" onclick="closeModal()"><span>close</span></button></p>
