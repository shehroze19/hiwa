<?php
require 'config.phplib';

$msg="";
if (!isset($_SESSION['user'] || !isset($_SESSION['role']){ // if session is not set it will redirect to the main page
	Header("Location: login.php");
	exit();
}

$role=$_SESSION['hiwa-role'];
if ($role != 'admin') Header("Location: menu.php");

if (array_key_exists('action', $_REQUEST) &&
    array_key_exists('user', $_REQUEST) &&
    $_REQUEST['action'] == 'delete') {
	if ($_REQUEST['user'] != 'guest') {
		$conn = pg_connect('user='.$CONFIG['username'].
			' dbname='.$CONFIG['database']);
		$res = pg_prepare($conn,"delete_query", "DELETE FROM users WHERE login='".
			mysql_real_escape_string(htmlentities($_REQUEST['user']))."'");
		$result=pg_execute($conn,"delete_query"); // executing 
		if ($result === False) {
			$msg = "Unable to remove user";
		} 
	} else $msg = "Do not remove guest; it would break the game.";
}

else if (array_key_exists('username', $_REQUEST) &&
    array_key_exists('password1', $_REQUEST) &&
    array_key_exists('password2', $_REQUEST) &&
    array_key_exists('role', $_REQUEST)) {


	if ($_REQUEST['password1'] != $_REQUEST['password2']) {
		$msg = "Passwords do not match!";
	} else {
		$conn = pg_connect('user='.$CONFIG['username'].
			' dbname='.$CONFIG['database']);
		$res = pg_prepare($conn,"insert_user", "INSERT INTO USERS
			(login, password, role) VALUES
			('".mysql_real_escape_string(htmlentities($_REQUEST['username']))."', '".
           		$_REQUEST['password1']."', '".
			$_REQUEST['role']."')");
		$result=pg_execute($conn,"insert_user"); // executing 
		if ($result === False) {
			$msg="Unable to create user.";
		}
	}
}
?>

<html>
<head>
<title>HIWA Manage Users</title>
<link rel="stylesheet" type="text/css" href="style.css"/>
</head>

<body>
<?php require 'header.php';?>
<div class="title">HIWA Manage Users</div>
<div class="subtitle">Logged in as <?php echo $_SESSION['hiwa-user'];?>
	(<?php echo $role; ?>)
</div>

<?php
$conn = pg_connect("user=".$CONFIG['username']." dbname=".$CONFIG['database']);
$res = pg_query("SELECT * FROM users");
?>
<table class="users">
<tr>
	<th>Login</th>
	<th>Role</th>
	<th>Action</th>
</tr>
<?php
$count=1;
while (($row = pg_fetch_assoc($res)) !== False) {
	if ($count % 2 == 0) $class="even"; else $class="odd";
	$count++;
	echo "<tr class=\"$class\">";
	echo "<td>".$row['login']."</td>";
	echo "<td>".$row['role']."</td>";
	echo "<td><a href=\"".$_SERVER['SCRIPT_NAME'].
		"?action=delete&user=".$row['login']."\">delete</a></td>";
	echo "</tr>";
}
pg_free_result($res);
pg_close($conn);
?>
</table>	
<p>
<?php if ($msg != "") echo '<div class="err">'.$msg.'</div>'; ?>
<form method="post">
<div class="section">Add user</div>
<table>
<tr>
	<td>Username:</td>
	<td><input type="text" name="username" size="25"></td>
</tr>
<tr>
	<td>Password:</td>
	<td><input type="password" name="password1" size="25"></td>
</tr>
<tr>
	<td>Password (again):</td>
	<td><input type="password" name="password2" size="25"></td>
</tr>
<tr>
	<td>Role:</td>
	<td><select name="role">
		<option value="admin">Administrator</option>
		<option value="manager">Manager</option>
		<option value="user">User</option>
	</select></td>
</tr>
</table>
<p>
<input type="submit" name="Create user">
</form>
<p>
Flag: <i>flag{ffbe209affbe}</i>
</body>
</body>
</html>
