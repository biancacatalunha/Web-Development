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

if($_GET){
session_destroy();
header('Location: index.php');
}

include('header.php');
require_once('db_connection.php');


//PROFILE PART
$id= $_SESSION["id"];

$q = $DBH->prepare("select * from users where id = :id");
$q->bindValue(':id', $id);
    
$q->execute();
$row = $q->fetch(PDO::FETCH_ASSOC);
    
$fname=$row['fname']; 
?>
<a href="home.php?logout=on">Logout</a>
<a href="profile.php?profileid=<?php echo $_SESSION['id'];?>">Your Profile</a>

<h1>Welcome <?php echo $fname; ?></h1><br/><br/>
<?php

//Prints messages that were left for this user
$q = $DBH->prepare("select * from comments where id_to= :id_to");
$q->bindValue(':id_to', $_SESSION['id']);
$q->execute();
$check = $q->fetchAll(PDO::FETCH_ASSOC);

if(!empty($check))//if there is a comment
{ 
    ?>
    	<h2> Messages that were left in your profile: </h2><br/>
    <?php
    	
    foreach($check as $row)
    {
    	
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
	echo $likes['total'] . ' people liked this. --->'.'<a href="profile.php?commentid=' . $row['id'] . '&act=like&profileid='.$_SESSION['id'].'"> Like </a> </BR>';		
    } 
}
else
{
   echo "No messages :(";
}
//END OF PROFILE
//END OF MESSAGES   
?>
	
<h1>People on EyeKey</h1>

<?php
//OTHERS PROFILE	
	  
// selecting the row from the database
try
{
    $q2 = $DBH->prepare("select * from users");
    $q2->execute();
    $check2 = $q2->fetchAll(PDO::FETCH_ASSOC);

   foreach($check2 as $row2)
   {
       echo '<a href="profile.php?profileid=' . $row2['id'] . '">'.$row2['fname'].'</a> </BR>';
    }
} catch(PDOException $e) {echo 'Error' . $e;}  
//END OF OTHERS PROFILE    
?>

