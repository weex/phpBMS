<?php 
/*
 $Rev: 254 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-08-07 18:38:38 -0600 (Tue, 07 Aug 2007) $
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

// HERE IS WEHERE YOU CAN DEFINE SETTINGS TO BE USED BY YOUR MODULE
// FOLLOW THE CLASS TEMPLATE BELOW FOR CONFIGURATION

/*
	//if we had specific update code for the module, we would create a class
	//called [module]Update with a method called updateSettings($variables)
	
	class [module]Update{
		function updateSettings($variables){
			
			the variables array is a non escaped array from the _POST
		
		}
	}
	
	
	// if you want to display fields on the configuration screen
	// follow the class template below
	
	class [module]Display{
		
		function getFields($therecord){
			// here you define any special fields you may need
			//$therecord is tthe array of settings

			$fields = array();
			
			//sample field
			$theinput = new inputField("shipping_markup",$therecord["shipping_markup"],"shipping markup",false,"real",4,4);
			$fields[] = $theinput;		
		
			return $fields;
		}//end method
		
		function display($theform, $therecord){
			//$theform is the passed form object
			//$therecord is tthe array of settings
			
			//sample output
			?>

<h1 class="newModule">Module: Business Management System</h1>
<fieldset>
	<legend>shipping</legend>
	<p class="notes"><br />
		<strong>Note:</strong> The shipping information below is used when connecting to <br />
		UPS to calculate shipping costs for product.  Current tests show that the UPS <br />
		shipping calculator only works when shipping to and from the Unites States.<br />
	</p>
	
	<p>
		<?php $theform->fields["shipping_markup"]->display();?>
		<br />
		<span class="notes"><strong>Note:</strong> Enter the number to multiply the calculated shipping cost. <br />
		For example to mark up shipping costs by 10%, enter 1.1</span>
	</p>
</fieldset>
			
			<?php 
		}
	}
	
*/
?>