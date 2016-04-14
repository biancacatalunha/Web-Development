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
require_once('db_connection.php');//includes database connection
require_once('header.php');

$userid=$_SESSION['id'];//gets user id from session
$profileid=$_GET['profileid'];//gets profile id that user wants to visit from get

//SAVE COMMENTS 
if($_POST){
	if($_GET['act'] == 'msg'){

		$comment=$_POST['message'];
	
		$sql = "INSERT INTO `comments` (`id_from`, `id_to`,`comment`) VALUES (?, ?, ?);";
    	$sth = $DBH->prepare($sql);
	
		$sth->bindParam(1, $userid, PDO::PARAM_INT);
		$sth->bindParam(2, $profileid, PDO::PARAM_INT);
		$sth->bindParam(3, $comment, PDO::PARAM_INT);
	
		$sth->execute();
    	
    	header('Location: profile.php?profileid='.$profileid);
	}
}
//END OF SAVES COMMENTS

//LIKES
if(isset($_GET['act'])){
	if($_GET['act'] == 'like'){
	
		$comment_id=$_GET['commentid'];
		
		//user can like comment only once
		$q = $DBH->prepare("select * from likes where id_from = :id_from and id_comment = :id_comment");
		$q->bindValue(':id_from', $userid);
		$q->bindValue(':id_comment', $comment_id);
		$q->execute();
    	$check = $q->fetch(PDO::FETCH_ASSOC);
 
    	$message = '';
    	if (empty($check))
    	{ 

			$sql = "INSERT INTO `likes` (`id_from`, `id_comment`) VALUES (?, ?);";
    		$sth = $DBH->prepare($sql);
	
			$sth->bindParam(1, $userid, PDO::PARAM_INT);
			$sth->bindParam(2, $comment_id, PDO::PARAM_INT);
	
			$sth->execute();
	
			header('Location: profile.php?profileid='.$profileid);
		}
	
	}
}
//END LIKE

//LOAD PROFILE
$q = $DBH->prepare("select * from users where id = :profileid");
$q->bindValue(':profileid', $profileid);
    
$q->execute();
$row = $q->fetch(PDO::FETCH_ASSOC);
    
$fname=$row['fname']; 
$lname=$row['lname'];
$dob=$row['dob'];
$email=$row['email'];
$phone=$row['phone'];
//END OF PROFILE 

?>
<body>
<a href="home.php">Home</a>
<a href="home.php?logout=on">Logout</a>

	
	<h1><?php echo $fname.' '.$lname ?>'s Profile</h1><br/>
	<h1>Birthday: <?php echo $dob ?> </h1></br>
	<h1>Phone: <?php echo $phone ?> </h1></br>
	<h1>Email: <?php echo $email ?> </h1></br>
	
	<h2>Write your message here:</h2>
	<form action="profile.php?profileid=<?php echo $profileid ?>&act=msg" method="post"> 
		<textarea rows=10 cols=50 maxlength=255 name="message">
		
		</textarea>
		<input type="submit" value="Send">
</body>
<br/><br/>
<?php
//COMMENTS
try{
	$q = $DBH->prepare("select * from comments where id_to= :id_to");
    $q->bindValue(':id_to', $profileid);
    $q->execute();
    $check = $q->fetchAll(PDO::FETCH_ASSOC);
    
    foreach($check as $row){
    	
    	$id_from=$row['id_from'];
    	
    	$t = $DBH->prepare("select * from users where id= :id_from");
   	 	$t->bindValue(':id_from', $id_from);
    	$t->execute();
    	$user = $t->fetch(PDO::FETCH_ASSOC);
    	
    	$id_comment=$row['id'];
    	
    	$l = $DBH->prepare("select count(*) as total from likes where id_comment =:id_comment");
   	 	$l->bindValue(':id_comment', $id_comment);
    	$l->execute();
    	$likes = $l->fetch(PDO::FETCH_ASSOC);
    
		
		echo '<a href="profile.php?profileid=' . $user['id'] .'">'. $user['lname']. '</a>'. ": ".$row['comment']. "<br/>";
		echo $likes['total'] . ' people liked this. --->'.'<a href="profile.php?commentid=' . $row['id'] . '&act=like&profileid='.$profileid.'"> Like </a> </BR>';
		
    }
    } catch(PDOException $e) {echo 'Error' . $e;}
?>