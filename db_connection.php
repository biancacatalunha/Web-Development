<!--
@author Bianca Catalunha
@student 2014305

Social Networking Website
Web Development Main Assignment 
College Of Computer Technology
December 2015

References:
http://stackoverflow.com/questions/339956/whats-the-best-ui-for-entering-date-of-birth
http://www.w3schools.com/php/func_mail_mail.asp
http://php.net/manual/en/function.include.php

--!>
<?php
 try {
        $host = '127.0.0.1';
        $dbname = 'eyekey';
        $user = 'root';
        $pass = '';
        # MySQL with PDO_MYSQL
        $DBH = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    } catch(PDOException $e) {echo 'Error';} 
    
?>