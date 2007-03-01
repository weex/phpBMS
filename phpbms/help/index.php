<?php 
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
 +-------------------------------------------------------------------------+
 | Copyright (c) 2005, Kreotek LLC                                         |
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
 | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS     |
 | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT       |
 | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A |
 | PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT      |
 | OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,   |
 | SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT        |
 | LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,   |
 | DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY   |
 | THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT     |
 | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE   |
 | OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.    |
 |                                                                         |
 +-------------------------------------------------------------------------+
*/
	require("../include/session.php");	
	
	$querystatement="SELECT displayname,version from modules ORDER BY id";
	$queryresult=mysql_query($querystatement,$dblink);

	function displayVersions($queryresult){
		if($queryresult){
			while($therecord=mysql_fetch_array($queryresult)){
				if($therecord["displayname"]!="Base"){
					echo $therecord["displayname"].": ";
					echo "v".$therecord["version"]."<br />";
				} else
					echo "<span class=\"important\">phpBMS version: ".$therecord["version"]."</span><br />";
			}
		}
	}
		
?>
<div class="box">
	<h3 class="helpLinks">About This Program</h3>
	<div class="helpDivs">
		<div class="helpSectionDivs">
			<p align="right" style="float:right;"><img src="<?php echo $_SESSION["app_path"]?>common/image/logo.png" alt="phpBMS Logo" width="85" height="22"/></p>
			<h3>phpBMS - Commercial Open Source Business Management Web Application</h3>
			<p class="small">v<?php displayVersions($queryresult)?></p>
			<p>Copyright &reg; 2004-2007 Kreotek, LLC. All Rights Reserved. phpBMS, and the phpBMS logo are trademarks of Kreotek, LLC.</p>
			<p>
				<strong>Kreotek, LLC</strong><br />
				610 Quantum<br />
				Rio Rancho, NM 87124<br />
				<a href="http://www.kreotek.com">http://www.kreotek.com</a><br />
				1-800-731-8026<br />
			</p>
		</div>
	</div>
	<h3 class="helpLinks">Keyboard Shortcuts</h3>
	<div class="helpDivs">
		<div class="helpSectionDivs">
			<p>&nbsp;</p>
			<p>
				phpBMS takes advanage of HTML's accessKey property to allow
				you to use your keyboard to navigate pages.  Some browsers and OS's
				might have different modifier keys, so check your
				browser documentation.  On windows, in Internet Explorer and Firefox, hold
				down the Alt key followed by the shortcut.  On opera hold down shift-esc then
				the shortut.  On a Mac, use the ctrl key in both Firefox and Safari.
			</p>
			<h4>Search/List Screens</h4>
			<div class="fauxP">
			<table border="0" cellpadding="0" cellspacing="0" class="querytable" width="300">
				<TR>
					<th valign="bottom" class="queryheader" align="right" width="100%">Command</th>
					<th valign="bottom" class="queryheader" align="center">Key</th>
				</TR>
				<TR class="qr1" >
					<TD align="right">New Record</TD>
					<td align="center">N</td>
				</TR>
				<TR class="qr2" >
					<TD align="right">Edit Record</TD>
					<td align="center">E</td>
				</TR>
				<TR class="qr1" >
					<TD align="right">Print</TD>
					<td align="center">P</td>
				</TR>
				<TR class="qr2" >
					<TD align="right">Delete (where applicable)</TD>
					<td align="center">D</td>
				</TR>
				<TR class="qr1" >
					<TD align="right">Select All</TD>
					<td align="center">A</td>
				</TR>
				<TR class="qr2" >
					<TD align="right">Select None</TD>
					<td align="center">X</td>
				</TR>
				<TR class="qr1" >
					<TD align="right">Keep Highlighted</TD>
					<td align="center">K</td>
				</TR>
				<TR class="qr2">
					<TD align="right">Omit Highlighted</TD>
					<td align="center">O</td>
				</TR>
				<tr class="queryfooter">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				</table>
			</div>
			<h4>Add/Edit Screens</h4>
			<div class="fauxP">
			<table border="0" cellpadding="0" cellspacing="0" class="querytable" width="300">
				<TR>
					<th valign="bottom" class="queryheader" align="right" width="100">Command</th>
					<th valign="bottom" class="queryheader" align="center">Key</th>
				</TR>
				<TR class="qr1">
					<TD align="right">Save Record</TD>
					<td align="center">S</td>
				</TR>
				<TR class="qr2">
					<TD align="right">Cancel</TD>
					<td align="center">X</td>
				</TR>
				<tr class="queryfooter">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
			</table>
			</div>
		</div>
	</div>
	<h3 class="helpLinks">Community Support at www.phpbms.org</h3>
	<div class="helpDivs">
		<div class="helpSectionDivs">
		<ul>
			<li><p><a href="http://www.phpbms.org" target="_blank">phpBMS project Web Site</a> - Main site for phpBMS development, documentation, and user support.</p></li>
			<li><p><a href="http://phpbms.org/wiki/PhpbmsFaq" target="_blank">phpBMS FAQ</a> - Frequently asked questions </p></li>
			<li><p><a href="http://www.phpbms.org/forum" target="_blank">phpBMS User Support forum</a> - A place for user and developer discussions.</p></li>
			<li><p><a href="http://phpbms.org/wiki/PhpbmsGuide" target="_blank">phpBMS Wiki Documentation </a> - Wiki driven user documentation starting point.</p></li>
		</ul>
		</div>
	</div>
	<h3 class="helpLinks">Customization and Paid Support Options</h3>
	<div class="helpDivs">
		<div class="helpSectionDivs">
		<h4>Paid Technical, Development and Installation Support</h4>
		<p>
			Know that your mission critical business software is backed by
			toll-free phone and e-mail support provided by the very people
			who created the software.  Choose a paid support contract
			provided by Kreotek that suits your need and budget.
		</p>
		<p>
			Visit <a href="http://www.kreotek.com" target="_blank">http://www.kreotek.com</a> or call
			<strong>1-800-731-8026</strong> for more information.
		</p>
		<h4>Customizing phpBMS</h4>
		<p>
			No two businesses are run the exact same way. Every individual buiness has uniques needs. Don't conform
			your business processes to your software, make your software work the way your business does.
		</p>
		<p>
			Kreotek can provide for all of  your company's customization needs.  From custom reports, importing, adding fields,
			or integrating with legacy systems, let the people who created the software tailor phpBMS to work within your
			specific business processes.
		</p>
		<p>
			Visit <a href="http://www.kreotek.com" target="_blank">http://www.kreotek.com</a> or call
			<strong>1-800-731-8026</strong> for more information.
		</p>
		</div>
	</div>
</div>
<p align="right"><button type="button" class="Buttons" onclick="closeModal()"><span> done </span></button></p>
