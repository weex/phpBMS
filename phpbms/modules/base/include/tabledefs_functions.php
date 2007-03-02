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
//format tabs for user admin.
function tabledefs_tabs($selected="none",$id=0) {
	$thetabs=array(
		array(
			"name"=>"General",
			"href"=>($id)?"tabledefs_addedit.php?id=".$id:"/tabledefs_addedit.php"
		),
		array(
			"name"=>"Columns",
			"href"=>($id)?"tabledefs_columns.php?id=".$id:"/tabledefs_columns.php",
			"disabled"=>(($id)?false:true)
		),
		array(
			"name"=>"Options",
			"href"=>($id)?"tabledefs_options.php?id=".$id:"/tabledefs_options.php",
			"disabled"=>(($id)?false:true)
		),
		array(
			"name"=>"Quick Search",
			"href"=>($id)?"tabledefs_quicksearch.php?id=".$id:"/tabledefs_quicksearch.php",
			"disabled"=>(($id)?false:true)
		),
		array(
			"name"=>"Search Fields",
			"href"=>($id)?"tabledefs_searchfields.php?id=".$id:"/tabledefs_searchfields.php",
			"disabled"=>(($id)?false:true)
		)		
	);
	displayTabs($thetabs,$selected);
}//end function

?>