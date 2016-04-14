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
//do a session id check on the top of every page
session_start();

$time= time();
$expirelenght= 1 * 1; //60 seconds times 30 minutes
$_SESSION['expiretime']= $time + $expirelenght;

if($time > $_SESSION['expiretime'])
{
	echo "Sorry login again";
}