<?php
if (array_key_exists('login', $_REQUEST) &&
    array_key_exists('password', $_REQUEST)) {
 	require 'config.phplib';
	
	$conn = pg_connect("user=".$CONFIG['username'].
	    " dbname=".$CONFIG['database']);
	
	$user=mysql_real_escape_string($_REQUEST['login']);// using mysql real escape so that ' is not passed in the input
	$pass=mysql_real_escape_string($_REQUEST['password']); // same as above the input is cleared of '
	// we could have also used the md5() function to encrypt the password but for that it should be stored in encrypted form in db
	// used prepared statements and parameterized queries. These are SQL statements that are sent to
	//and parsed by the database server separately from any parameters. This way it is 
	//impossible for an attacker to inject malicious SQL.
	$result = pg_prepare($conn,"my_query","SELECT * from users // using
	    WHERE login='".$user."'
	    AND password='".$pass."'");
	
	
	
	$executed_result = pg_execute($conn, 'my_query');
	
	$row = pg_fetch_assoc($executed_result);
	if ($row === False) {
		require 'header.php';
		print '<div class="err">Incorrect username/password</div>';
		exit();
	}
	// we should never use coockies, instead sessions should be used
	// also we can encrypt the cookie value using md5() function so that user can not change its value easily.
	$_SESSION("hiwa-user", $_REQUEST['login']);
	$_SESSION("hiwa-role", $row['role']);
	Header("Location: menu.php");
	exit();
}
?>

<html>
<head>
<title>HIWA Login Screen</title>
<link rel="stylesheet" type="text/css" href="style.css"/>
</head>

<body>
<?php require 'header.php'; ?>

<div class="login">
<p>Welcome to the Horribly Insecure Web Application.</p>


<form method="POST">
<div class="loginfield">
	<div class="loginlabel">Username</div>
	<div class="logininput">
		<input type="text" size="30" name="login">
	</div>
</div>
<div class="loginfield">
	<div class="loginlabel">Password</div>
	<div class="logininput">
		<input type="password" size="30" name="password">
	</div>
</div>
<p/><input type="submit" name="Login"/>
</form>
<p><a href="reset.php">Forgot password?</a></p>
<p/>
Flag: <i>423320a19a2256ba8c8dac04f3bd329f</i>
</div><!-- login -->

</body>
</html>

