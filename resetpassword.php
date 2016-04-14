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
require_once('db_connection.php');
$passwordErr="";

//1---This block validates the random code sent by email
//Without a valid code user can't access this page
if(isset ($_GET['rand']))
{	
	$rand=$_GET['rand'];
	
	$q = $DBH->prepare("select * from resetpassword where rand = :rand");
    $q->bindValue(':rand', $rand);
    $q->execute();
    $check = $q->fetch(PDO::FETCH_ASSOC);
//1---- End    
  
    if(!empty($check))//if code is valid
    {  
    	$userid=$check['userid']; 
    	
    	//2---- If get id was passed by the URL means that input has to be validade   
        if (isset ($_GET['id'])) 
		{
			//$id=$_GET['id'];
			$safe=1;
			
			$_SESSION['pass1']= $_POST["pass1"];
			$_SESSION['pass2']= $_POST["pass2"];
			
	
			if (empty($_POST["pass1"]) || empty($_POST["pass2"])) //if password is empty
			{
    			$passwordErr = "Password is required";//sets error message
     			$safe=0;//set check variable to 0 = mistake
			} 
			else //if password is no empty
			{	  	
     			if($_POST["pass1"] == $_POST["pass2"])
     			{
     				$password = test_input($_POST["pass1"]);//polishes variable
     	
     				if (strlen($_POST["pass1"]) <= '8') //checks if password has at least 8 characteres
     				{
            			$passwordErr = "Your Password Must Contain At Least 8 Characters";//sets error message
            			$safe=0;//set check variable to 0 = mistake
        			}
     		}
     		else
     		{
     			$passwordErr = "Password does not match";//sets error message
     			$safe=0;//set check variable to 0 = mistake
     		}    	
		}
		//2---- End of new password input validation
		
		//3---- If all validations were accepted, saves new password into database
		if($safe == 1)
		{     	
        	$sql2 = "update users set password = :newpass where id = :userid";
        	$sth2 = $DBH->prepare($sql2);
        	$sth2->bindValue(':newpass', $_SESSION['pass1']);
        	$sth2->bindValue(':userid', $userid);
       		$sth2->execute();
       		
       		$sql = "delete from resetpassword where rand = '$rand'";//delets random number from database
       		$sth = $DBH->prepare($sql);
        	$sth->execute();
        	
        	header('Location: index.php?pass=1');//redirects to login page		
		}
		//3---- End of database input
	}
       
    	$userid="?id=validate&&rand=".$rand;
        
    	?>
        <html>
        <body>
        	<form action="<?php echo $_SERVER["PHP_SELF"].$userid; ?>" method="post">
        	<label>Enter your new password: </label><br/>
        	<input type="password" name="pass1"><br/>
        	<label>Confirm password:</label> <br/>
        	<input type="password" name="pass2"> <br/>
        	<span class="error"><?php echo $passwordErr;?></span><br/>
        	<input type="submit" value="Enter">      	
        	</form>
        </body>
        </html>
    	<?php
    } 
    else 
    {      
        echo 'Sorry invalid link';
    }
}
//4--- If the email was submitted, 
//checks if it is valid and sends an email to user 
//with a link back to this page with a security randon number
else if(isset($_POST['email']))
{
	$email=$_POST['email'];
	
	$q = $DBH->prepare("select * from users where email = :email LIMIT 1");
    $q->bindValue(':email', $email);
    $q->execute();
    $check = $q->fetch(PDO::FETCH_ASSOC);
 
    $message = '';
    if (!empty($check))
    {
        $email = $check['email'];
        $id = $check['id'];
   		$rand= rand(0,10000);
   		
   		$sql = "INSERT INTO `resetpassword` (`rand`, `userid`) VALUES (?, ?);";
    	$sth = $DBH->prepare($sql);
		$sth->bindParam(1, $rand, PDO::PARAM_INT);
		$sth->bindParam(2, $id, PDO::PARAM_INT);
		$sth->execute();
		
		$to = $email;
		$subject = "Reset your password";
		$link="resetpassword.php?rand=".$rand;

		$emessage = "
		<html>
		<head>
		<title>Reset Password Requested</title>
		</head>
		<body>
		<p><a href='<?=$link?>'>Click here to reset your password</a></p>
		</body>
		</html>
		";

		// Always set content-type when sending HTML email
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		
		//I redirected the page to simulate as if the user had clicked the link in the email that was sent
		header('Location:'.$link);
		
		//The line bellow was comment to avoid server error because of mail function
		
		//mail($to,$subject,$emessage,$headers);
		
		
    } 
    else 
    {
    	$message= 'Sorry, your email is not registered.';
    }
}
else
{
?>

<html>
<body>
	<form action="resetpassword.php" method="post">
		<label>Please, enter your email and you will be able to reset your password</label><br/>
		<input type="text" name="email">
		<input type="submit" name="submit" value="Enter">
		
		<?php
    	if(!empty($message))
    	{
    		echo '<br>';
     		echo $message;
    	}
 		?>
</body>
</html>
<?php
}
function test_input($data) //function to polish data
{
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}
?>