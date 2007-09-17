<?php 
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
 +-------------------------------------------------------------------------+
 | Copyright (c) 2004 - 2007, Kreotek LLC                                  |
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
	
	$querystatement="SELECT displayname,version from modules ORDER BY id";
	$queryresult=$db->query($querystatement);

	function displayVersions($db,$queryresult){
		if($queryresult){
			while($therecord=$db->fetchArray($queryresult)){
				if($therecord["displayname"]!="Base"){
					echo $therecord["displayname"].": ";
					echo "v".$therecord["version"]."<br />";
				} else
					echo "<span class=\"important\">phpBMS version: ".$therecord["version"]."</span><br />";
			}
		}
	}
		
?>
<div class="box" id="helpBox">

	<h1>About This Program</h3>
	<blockquote>
		<p align="right" style="float:right;"><img src="<?php echo APP_PATH?>common/image/logo.png" alt="phpBMS Logo" width="85" height="22"/></p>
		
		<h3>phpBMS - Commercial Open Source Business Management Web Application</h3>
		
		<p class="small"><?php displayVersions($db,$queryresult)?></p>
		
		<p>Copyright &reg; 2004-2007 Kreotek, LLC. All Rights Reserved. phpBMS, and the phpBMS logo are trademarks of Kreotek, LLC.</p>
	
		<p>
			<strong>Kreotek, LLC</strong><br />
			610 Quantum<br />
			Rio Rancho, NM 87124<br />
			<a href="http://www.kreotek.com" target="_blank">http://www.kreotek.com</a><br />
			1-800-731-8026<br />
		</p>
	</blockquote>

	<h1>Keyboard Shortcuts</h1>
	<blockquote>
		<p>
			phpBMS takes advanage of HTML's accesskey property to allow
			you to use your keyboard to navigate pages.  Some browsers and OS's
			might have different modifier keys, so check your
			browser documentation.  In windows, when using Internet Explorer and Firefox &lt 2.0, hold
			down the Alt key followed by the shortcut.  When using Firefox &gt 2.0 in windows hold down Alt-Shift buttons 
			followed by the shortcut. In opera hold down Shift-Esc then
			the shortut.  On a Mac, use the ctrl key in both Firefox &lt; 2.0 and Safari. 
		</p>
	
		<h2>Search/List Screens</h2>
		<div class="fauxP">
			<table border="0" cellpadding="0" cellspacing="0" class="querytable" width="300">
				<tr>
					<th valign="bottom" class="queryheader" align="right" width="100%">Command</th>
					<th valign="bottom" class="queryheader" align="center">Key</th>
				</tr>
				<tr class="qr1" >
					<td align="right">New Record</td>
					<td align="center">N</td>
				</tr>
				<tr class="qr2" >
					<td align="right">Edit Record</td>
					<td align="center">E</td>
				</tr>
				<tr class="qr1" >
					<td align="right">Print</td>
					<td align="center">P</td>
				</tr>
				<tr class="qr2" >
					<td align="right">Delete (where applicable)</td>
					<td align="center">D</td>
				</tr>
				<tr class="qr1" >
					<td align="right">Select All</td>
					<td align="center">A</td>
				</tr>
				<tr class="qr2" >
					<td align="right">Select None</td>
					<td align="center">X</td>
				</tr>
				<tr class="qr1" >
					<td align="right">Keep Highlighted</td>
					<td align="center">K</td>
				</tr>
				<tr class="qr2">
					<td align="right">Omit Highlighted</td>
					<td align="center">O</td>
				</tr>
				<tr class="queryfooter">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
			</table>
		</div>
	
		<h2>Add/Edit Screens</h2>
		<div class="fauxP">
			<table border="0" cellpadding="0" cellspacing="0" class="querytable" width="300">
				<tr>
					<th valign="bottom" class="queryheader" align="right" width="100%">Command</th>
					<th valign="bottom" class="queryheader" align="center">Key</th>
				</tr>
				<tr class="qr1">
					<td align="right">Save Record</td>
					<td align="center">S</td>
				</tr>
				<tr class="qr2">
					<td align="right">Cancel</td>
					<td align="center">X</td>
				</tr>
				<tr class="queryfooter">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
			</table>
		</div>
	</blockquote>


	<h1>Community Support at www.phpbms.org</h1>
	
	<blockquote>
		<ul>
			<li><p><a href="http://www.phpbms.org" target="_blank">phpBMS project Web Site</a> - Main site for phpBMS development, documentation, and user support.</p></li>
			<li><p><a href="http://phpbms.org/wiki/PhpbmsFaq" target="_blank">phpBMS FAQ</a> - Frequently asked questions </p></li>
			<li><p><a href="http://www.phpbms.org/forum" target="_blank">phpBMS User Support forum</a> - A place for user and developer discussions.</p></li>
			<li><p><a href="http://phpbms.org/wiki/PhpbmsGuide" target="_blank">phpBMS Wiki Documentation </a> - Wiki driven user documentation starting point.</p></li>
		</ul>
	</blockquote>

	<h1>Customization and Paid Support Options</h1>

	<blockquote>
		<h2>Paid Technical, Development and Installation Support</h2>
	
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
		<h2>Customizing phpBMS</h2>
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
	</blockquote>
</div>
<p align="right"><button id="helpClose" type="button" class="Buttons" onclick="closeModal()"><span>close</span></button></p>
