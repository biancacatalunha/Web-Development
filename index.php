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
<head>
<link rel="stylesheet" type="text/css" href="form_style.css" />
</head>

<?php
require_once('db_connection.php');//include database connection code
session_start();
//make login validation be different from register
//===== VALIDATIONS
if(isset($_GET['pass']))
{
	echo"Your password has been changed.";
}
$safe=1;//check variable to database insert. 1 = no mistakes. 0 = mistakes

// define variables and set to empty values
$fnameErr = $lnameErr = $emailErr = $phoneErr = $passwordErr = $dobErr = $genderErr = ""; 
$fname = $lname = $email = $phone = $password = $dob = $gender = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
	if (empty($_POST["fname"])) //if the fname variable is empty
	{
		$fnameErr = "First name is required";//returns error message
		$safe=0;//set check variable to 0 = mistake
	} 
    else //if fname is no empty
    {
		$fname = test_input($_POST["fname"]);//sends to function to polish the data
     	
     	if (!preg_match("/^[a-zA-Z ]*$/",$fname))//checks if fname contains only letters and white spaces
     	{
			$fnameErr = "Only letters and white space allowed";//returns error message
      		$safe=0;//set check variable to 0 = mistake
      	}
   }
   
	if (empty($_POST["lname"])) //if lname variable is empty
    {
		$lnameErr = "Last name is required";//returns error message		
		$safe=0;//set check variable to 0 = mistake
	} 
    else //if fname is not empty
    {
		$lname = test_input($_POST["lname"]);//polishes data
		
		if (!preg_match("/^[a-zA-Z ]*$/",$lname)) //checks if lname contains only letters and white spaces
		{
			$lnameErr = "Only letters and white space allowed";//returns error message
			$safe=0;//set check variable to 0 = mistake
		}
	}
	
    if (empty($_POST["email"])) //if email is empty
    {
		$emailErr = "Email is required";//set error message
		$safe=0;//set check variable to 0 = mistake
	}
	else //if email is no empty
	{
		$email = test_input($_POST["email"]);//polishes data
		
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) //if email is invalid
		{
			$emailErr = "Invalid email format"; //sets error message
			$safe=0;//set check variable to 0 = mistake
		}
	}
	
	if (empty($_POST["phone"]))//if phone is empty 
    {
    	$phoneErr = "Phone is required";//sets error message
    	$safe=0;//set check variable to 0 = mistake
    } 
    else //if phone is not empty
    {
    	$phone = test_input($_POST["phone"]);//polishes data
     
     	if (strlen($_POST["phone"]) <= '9') //checks if phone has at least 9 characteres 
     	{
            $passwordErr = "Enter a valid phone number";
            $safe=0;
        }
    }
    
	if (empty($_POST["password"])) //if password is empty
	{
    	$passwordErr = "Password is required";//sets error message
     	$safe=0;//set check variable to 0 = mistake
	} 
	else //if password is no empty
	{
    	$password = test_input($_POST["password"]);//polishes variable
     	
     	if (strlen($_POST["password"]) <= '8') //checks if password has at least 8 characteres
     	{
            $passwordErr = "Your Password Must Contain At Least 8 Characters";//sets error message
            $safe=0;//set check variable to 0 = mistake
        }
   }
     
   if (empty($_POST["day"]) || empty($_POST["month"]) || empty($_POST["year"])) //if day or month or year value is empty
   {
		$dobErr = "Date of birth is required";//sets error message
		$safe=0;//set check variable to 0 = mistake
	} 
	else 
	{
    	$birth_day = test_input($_POST["day"]);//polishes variable
     	$birth_month = test_input($_POST["month"]);//polishes variable
     	$birth_year = test_input($_POST["year"]);//polishes variable
     	
     	$dob=$birth_year.'-'.$birth_month.'-'.$birth_day;//joins date of birth in one variable
     	
     	if(!ctype_digit($birth_day) || !ctype_digit($birth_month) || !ctype_digit($birth_year)) {//if dob is not a number
            $passwordErr = "Only numbers required";//sets error message
            $safe=0;//set check variable to 0 = mistake
        }
	}
   
   if (empty($_POST["gender"])) //if gender is empty
   {
		$genderErr = "Gender is required";//sets error message
		$safe=0;//set check variable to 0 = mistake
   } 
   else 
   {
		$gender = test_input($_POST["gender"]);//polishes variable
   }
	
	//DATABASE INSERTS
	
	if($safe == 1)//if there were no input mistakes, inserts into database
	{
		$sql = "INSERT INTO `users` (`fname`, `lname`, `email`, `password`, `birth_day`, `birth_month`, `birth_year`, `gender`, `dob`, `phone`) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
    	$sth = $DBH->prepare($sql);
	
		$sth->bindParam(1, $fname, PDO::PARAM_INT);
		$sth->bindParam(2, $lname, PDO::PARAM_INT);
		$sth->bindParam(3, $email, PDO::PARAM_INT);
		$sth->bindParam(4, $password, PDO::PARAM_INT);
		$sth->bindParam(5, $birth_day, PDO::PARAM_INT);
		$sth->bindParam(6, $birth_month, PDO::PARAM_INT);
		$sth->bindParam(7, $birth_year, PDO::PARAM_INT);
		$sth->bindParam(8, $gender, PDO::PARAM_INT);
		$sth->bindParam(9, $dob, PDO::PARAM_INT);
		$sth->bindParam(10, $phone, PDO::PARAM_INT);
	
		$sth->execute();
    
    	$last_id = $DBH->lastInsertId();
    	
    	 $_SESSION['id']= $last_id;//sets session with last inserted id
    	
    	header('Location: home.php');//directs to home page
	}
	
	// END DATABASE INSERT
}

function test_input($data) //function to polish data
{
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}
//=====END VALIDATIONS

//=====LOGIN
if(isset ($_GET['id']))//if data was passed through get method
{
	$email = $_POST['email'];
	$password = $_POST['password'];
		
	$q = $DBH->prepare("select * from users where email = :email and password = :password LIMIT 1");
    $q->bindValue(':email', $email);
    $q->bindValue(':password',  $password);
    $q->execute();
    $check = $q->fetch(PDO::FETCH_ASSOC);
 
    $message = '';
    if (!empty($check))
    {
        $email = $check['email'];
        $_SESSION['id'] = $check['id'];
   
        header('Location: home.php');
    } 
    else 
    {
    	$message= 'Sorry your log in details are not correct';
    }
}
//======END LOGIN

?>
<h2>Login</h2><br></br>
<form action="index.php?id=1" method="post">
Email <input type="text" name="email"/>
Password <input type="password" name="password"/>
<input type="submit"/>
<?php
    if(!empty($message)){
     echo '<br>';
     echo $message;
    }
 ?>
</form>
<a href="resetpassword.php">Forgot your password?</a>

<h2>Register</h2></br>
<p><span class="error">* required field.</span></p>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
	<label for="fname">First name </label>
		<input type="text" name="fname"/> 
		<span class="error">* <?php echo $fnameErr;?></span><br/>
	<label for="lname">Last name </label>
		<input type="text" name="lname"/>
		<span class="error">* <?php echo $lnameErr;?></span><br/>
	<label for="email">E-mail </email>
		<input type="text" name="email"/>
		<span class="error">* <?php echo $emailErr;?></span><br/>
	<label for="phone">Phone </email>
		<input type="text" name="phone"/><br/>
	<label for="password">Password: </label>
		<input type="password" name="password">
		<span class="error">* <?php echo $passwordErr;?></span><br/>
	<label for="day">Date of Birth:</label>
		<div id="date1" class="datefield">
    		<input name="day" type="tel" maxlength="2" placeholder="DD"/> /              
    		<input name="month" type="tel" maxlength="2" placeholder="MM"/>/
    		<input name="year" type="tel" maxlength="4" placeholder="YYYY"/>
		</div></br>
		<span class="error">* <?php echo $dobErr;?></span><br/>
	<label for="gender">Gender </label>
		<input type="radio" name="gender" value="male">Male
		<input type="radio" name="gender" value="female">Female
		<span class="error">* <?php echo $genderErr;?></span><br/>
		
		<input type="submit" value="Register">
</form>

