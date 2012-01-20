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
                `phone` = '".mysql_real_escape_string($variables["phone"])."'
            WHERE
                `uuid` = '".$this->userUUID."'";

        $this->db->query($updatestatement);

        $_SESSION["userinfo"]["phone"] = $variables["phone"];

        return "Record Updated";

    }//end function update

    
    function updateEmail($variables){

        $updatestatement = "
            UPDATE
                `users`
            SET
                `email` = '".mysql_real_escape_string($variables["email"])."',
                `mailer` = '".mysql_real_escape_string($variables["mailer"])."',
                `sendmail` = '".mysql_real_escape_string($variables["sendmail"])."',
                `smtphost` = '".mysql_real_escape_string($variables["smtphost"])."',
                `smtpport` = ".(int)mysql_real_escape_string($variables["smtpport"]).",
                `smtpauth` = ".(int)mysql_real_escape_string($variables["smtpauth"]).",
                `smtpuser` = '".mysql_real_escape_string($variables["smtpuser"])."',
                `smtppass` = '".mysql_real_escape_string($variables["smtppass"])."',
                `smtpsecure` = '".mysql_real_escape_string($variables["smtpsecure"])."'
            WHERE
                `uuid` = '".$this->userUUID."'";

        $this->db->query($updatestatement);

        $_SESSION["userinfo"]["email"] = $variables["email"];
        $_SESSION["userinfo"]["mailer"] = $variables["mailer"];
        $_SESSION["userinfo"]["sendmail"] = $variables["sendmail"];
        $_SESSION["userinfo"]["smtphost"] = $variables["smtphost"];
        $_SESSION["userinfo"]["smtpport"] = (int)$variables["smtpport"];
        $_SESSION["userinfo"]["smtpauth"] = (int)$variables["smtpauth"];
        $_SESSION["userinfo"]["smtpuser"] = $variables["smtpuser"];
        $_SESSION["userinfo"]["smtppass"] = $variables["smtppass"];
        $_SESSION["userinfo"]["smtpsecure"] = $variables["smtpsecure"];
        

        return "Email Settings Updated";

    }//end function updateEmail
    
}//end class

?>
