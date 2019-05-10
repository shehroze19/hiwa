<?php
require 'config.phplib';

$msg="";
if (!isset($_SESSION['user'] || !isset($_SESSION['role']){ // if session is not set it will redirect to the main page
	Header("Location: login.php");
	exit();
}

$role=$_SESSION['hiwa-role'];

if (array_key_exists('a', $_REQUEST)) {
$conn = pg_connect("user=".$CONFIG['username']." dbname=".$CONFIG['database']);
	
	// used prepared statements and parameterized queries. These are SQL statements that are sent to
	//and parsed by the database server separately from any parameters. This way it is 
	//impossible for an attacker to inject malicious SQL
	
$res = pg_prepare($conn,"insert_query","INSERT INTO orders
	(orderid, customerid, status)
	VALUES ($1, $2, $3)", array( 
		$_REQUEST['orderid'], 
		$_REQUEST['custid'],
		$_REQUEST['status'] ) );
$result = pg_execute($conn, "insert_query"); // separate execution
	
pg_free_result($result);
pg_close($conn);	
}
?>


<html>
<head>
<title>HIWA Manage Orders</title>
<link rel="stylesheet" type="text/css" href="style.css"/>
</head>

<body>
<?php require 'header.php';?>
<div class="title">HIWA Manage Orders</div>
<div class="subtitle">Logged in as <?php echo $_SESSION['hiwa-user'];?>
	(<?php echo $role; ?>)
</div>

<?php
$conn = pg_connect("user=".$CONFIG['username']." dbname=".$CONFIG['database']);
$res = pg_query("SELECT * FROM orders, customers
	WHERE orders.customerid = customers.customerid
	ORDER BY orderid DESC");
?>
<table class="users">
<tr>
	<th>Order Id</th>
	<th>Customer Name</th>
	<th>Status</th>
	<th>Action</th>
</tr>
<?php
$count=1;
while (($row = pg_fetch_assoc($res)) !== False) {
	if ($count % 2 == 0) $class="even"; else $class="odd";
	$count++;
	echo "<tr class=\"$class\">";
	echo "<td>".$row['orderid']."</td>";
	echo "<td>".$row['customername']."</td>";
	echo "<td>".$row['status']."</td>";
	echo "<td><a href=\"details.php?orderid=".$row['orderid'].
		"\">details</a></td>";
	echo "</tr>";
}
pg_free_result($res);
?>
</table>	
<p>
<?php if ($msg != "") echo '<div class="err">'.$msg.'</div>'; ?>
<form method="post">
<div class="section">Add order</div>
<table>
<tr>
	<td>Order ID:</td>
	<td><input type="text" name="orderid" size="25"></td>
</tr>
<tr>
	<td>Customer Name:</td>
	<td><?php
	echo '<select name="custid">';
	$res = pg_query($conn,"SELECT customerid, customername 
		FROM customers ORDER BY customerid");
	while (($row=pg_fetch_assoc($res)) !== False) {
		echo '<option value="'.$row['customerid'].'">'.
			$row['customername'].'</option>';
	}
	pg_close($conn);
	echo '</select></td>';?>
</tr>
<tr>
	<td>Status</td>
	<td><select name="status">
<option value="new">New</option>
<option value="prepared">Prepared</option>
<option value="shipped">Shipped</option>
<option value="billed">Billed</option>
<option value="paid">Paid</option>
</select></td>
</tr>
</table>
<p>
<input type="submit" name="a" value="Create order">
</form>
</body>
</html>
