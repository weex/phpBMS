<?php


class myAccount{

    var $db;
    var $userUUID;

    function myAccount($db, $userUUID){

        $this->db = $db;

        $this->userUUID = $userUUID;

    }//end function init


    function displayRoles(){

	$querystatement = "
		SELECT
			`roles`.`id`,
			`roles`.`name`
		FROM
			`roles` INNER JOIN `rolestousers` ON `rolestousers`.`roleid`=`roles`.`uuid`
		WHERE
			`rolestousers`.`userid` = '".$this->userUUID."'
		";

	$queryresult = $this->db->query($querystatement);

	while($therecord = $this->db->fetchArray($queryresult))
		echo "<li>".$therecord["name"]."</li>";

    }//end function displayRoles


    function changePassword($oldPassword, $newPassword){

        if(DEMO_ENABLED !== "false")
            return "Cannot change password when in demonstration mode.";

        $querystatement = "
            SELECT
                `id`
            FROM
                `users`
            WHERE
                `uuid` = '".$this->userUUID."'
                AND password = ENCODE('".mysql_real_escape_string($oldPassword)."', '".mysql_real_escape_string(ENCRYPTION_SEED)."')";

        $queryresult = $this->db->query($querystatement);

        if($this->db->numRows($queryresult)){

            $updatestatement = "
                UPDATE
                    `users`
                SET
                    `password` = ENCODE('".mysql_real_escape_string($newPassword)."', '".mysql_real_escape_string(ENCRYPTION_SEED)."')
                WHERE
                    `uuid` = '".$this->userUUID."'";

            $this->db->query($updatestatement);

            return "password changed";

        }else
            return "Current password incorrect";

    }//end function changePassword


    function update($variables){

        $updatestatement = "
            UPDATE
                `users`
            SET
                `email` = '".mysql_real_escape_string($variables["email"])."',
                `phone` = '".mysql_real_escape_string($variables["phone"])."'
            WHERE
                `uuid` = '".$this->userUUID."'";

        $this->db->query($updatestatement);

        $_SESSION["userinfo"]["email"] = $variables["email"];
        $_SESSION["userinfo"]["phone"] = $variables["phone"];

        return "Record Updated";

    }//end function update

}//end class

?>
