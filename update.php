<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Update</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link href="headstyle.css" rel="stylesheet" type="text/css">
<?php 
if(isset($_POST['updatebtn'])){
	require_once 'db_config.php';
	
	$mysqli = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
	
	if($mysqli->connect_errno) {
		die("Line 15: Error connecting to MySQL: ".$mysqli->connect_errno);
	}
	
	$sql = "UPDATE patrolcar SET patrolcarStatusId = ? WHERE patrolcarId = ?";
	
	if(!($stmt = $mysqli->prepare($sql))) {
		die("Line 21: Error preparing: ".$mysqli->errno);
	}
	
	if (!$stmt->bind_param('ss', $_POST['patrolCarStatus'], $_POST['patrolCarId'])) {
		die("Line 25: Error binding parameter: ".$stmt->errno);
	}
	
	if (!$stmt->execute()){
		die("Line 29: Error updating patrolcar table: ".$stmt->errno);
	}
	
	if ($_POST["patrolCarStatus"] == '4') {
		
		$sql = "UPDATE dispatch SET timeArrived = NOW()
		WHERE timeArrived is NULL AND patrolcarId = ?";
		
		if(!($stmt = $mysqli->prepare($sql))) {
			die("Line 38: Error preparing: ".$mysqli->errno);
		}
		
		if(!$stmt->bind_param('s',$_POST['patrolCarId'])){
			die("Line 42: Error binding parameter: ".$stmt->errno);
		}
		
		if(!$stmt->execute()){
			die("Line 46: Error updating dispatch table: ".$stmt->errno);
		}
	} else if ($_POST["patrolCarStatus"] == '3') {
		$sql = "SELECT incidentId FROM dispatch WHERE timeCompleted IS NULL AND patrolcarId = ?";
		
		if(!($stmt = $mysqli->prepare($sql))) {
			die("Line 52: Error preparing: ".$mysqli->errno);
		}
		
		if (!$stmt->bind_param('s', $_POST['patrolCarId'])) {
			die("Line 56: Error binding parameter: ".$stmt->errno);
		}
		
		if (!$stmt->execute()){
			die("Line 60: Error updating patrolcar table: ".$stmt->errno);
		}
		
		if (!($resultset = $stmt->get_result())) {
			die("Line 64:Error getting result set: ".$stmt->errno);
		}
		
		$incidentId;
		
		while($row = $resultset->fetch_assoc()) {
		$incidentId = $row['incidentId'];
		}
	
		$sql = "UPDATE dispatch SET timeCompleted = NOW()
				WHERE timeCompleted is NULL AND patrolcarId = ?";
	
			if(!($stmt = $mysqli->prepare($sql))) {
				die("Line 77: Error preparing: ".$mysqli->errno);
			}
		
			if (!$stmt->bind_param('s', $_POST['patrolCarId'])) {
				die("Line 81: Error binding parameter: ".$stmt->errno);
			}
		
			if (!$stmt->execute()){
				die("Line 85: Error updating dispatch table: ".$stmt->errno);
			}
		
			$sql = "UPDATE incident SET incidentStatusId = '3' WHERE incidentId = '$incidentId' AND NOT EXISTS (SELECT * FROM dispatch WHERE timeCompleted IS NULL and incidentId = '$incidentId')";
		
			if(!($stmt = $mysqli->prepare($sql))) {
				die("Line 91: Error preparing: ".$mysqli->errno);
			}
			
			if (!$stmt->execute()){
				die("Line 95: Error updating dispatch table: ".$stmt->errno);
			}
			
			$resultset->close();
		
	}
	$stmt->close();
	$mysqli->close();
	?>
	
<script>window.location="logcall.php";</script>
<?php
}
?>
</head>


<body>
<?php  require_once 'nav.php'; ?>
<br>
<br>
<?php
if (!isset($_POST["searchbtn"]))
{
?>

<form name="searchform" method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?> ">
<table border="0" align="center" cellpadding="5" cellspacing="5">
<tr></tr>
<tr>
<td>Patrol Car ID: </td>
<td><input type="text" name="patrolCarId" id="patrolCarId"></td>
<td><input type="submit" name="searchbtn" id="searchbtn" value="Search"></td>
</tr>
</table>
</form>
<?php require_once 'db_config.php'; // configs for database

$mysqli = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);

if ($mysqli->connect_errno)
{
	die("Line 137: Error connecting to MySQL: ". $mysqli->connecterrno);
}

$sql = "SELECT patrolcarId, statusDesc FROM patrolcar JOIN patrolcar_status ON patrolcar.patrolcarStatusId=patrolcar_status.StatusId";
	// check sql command in $sql if works, if error display an error message and exit
if (!($stmt = $mysqli->prepare($sql)))
{
	die("Line 144: Error Preparing: ".$mysqli->errno);
}
// check if it can run the command
if (!$stmt->execute())
{
	die("Line 149: Error Executing: ".$stmt->errno);
}
// check any data in result set
if (!($resultset = $stmt->get_result())) {
	die("Line 153: Error getting result set : ".$stmt->errno);
}

$patrolcarArray; // an array var

while($row = $resultset->fetch_assoc()) {
	//creates an associative array of $patrolcarArray [patrolcarID, statusDesc]
	$patrolcarArray[$row['patrolcarId']] = $row['statusDesc'];
}

$stmt->close();

$resultset->close();

$mysqli->close();
?>
<br><br><table border="1" align="center">
	<tr>
	<td colspan="3">Patrolcar Panel</td>
	</tr>
	<?php
		foreach($patrolcarArray as $key=>$value){
	?>
	<tr>
	<td><?php echo $key ?></td>
	<td><?php echo $value ?></td>
	</tr>
	<?php } ?> 
<?php }
	
else {
	require_once 'db_config.php';
	
	$mysqli = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
	
	if ($mysqli->connect_errno) {
		die("Line 189: Error connecting to MySQL: ".$mysqli->connect_errno);
	}
	
	$sql = "SELECT * FROM patrolcar WHERE patrolcarId = ?";
	
	if (!($stmt = $mysqli->prepare($sql))){
		die("Line 195: Error preparing: ".$mysqli->errno);
	}
	
	if (!$stmt->bind_param('s', $_POST['patrolCarId'])){
		die("Line 199: Error binding parameters: ".$stmt->errno);
	}
	
	if (!$stmt->execute()){
		die("Line 203: Error executing: ".$stmt->errno);
	}
	if (!($resultset = $stmt->get_result())) {
		die("Line 206: Error getting result set: ".$stmt->errno);
	}
	
	if($resultset->num_rows == 0) {
		?>
		<script>window.location="update.php";
				alert("Invalid Patrol Car ID");</script>
		<?php
	}
	
	$patrolCarId;
	$patrolCarStatusId;
	
	while($row = $resultset->fetch_assoc()) {
		$patrolCarId = $row['patrolcarId'];
		$patrolCarStatusId = $row['patrolcarStatusId'];
	}
	
	$sql = "SELECT * FROM patrolcar_status";
	if(!($stmt = $mysqli->prepare($sql))) {
		die("Line 226: Error Preparing: ".$mysqli->errno);
	}
	
	if (!$stmt->execute()) {
		die("Line 230: Error Executing: ".$stmt->errno);
	}
	if (!($resultset = $stmt->get_result())) {
		die("Line 233: Error getting result set: ".$stmt->errno);
	}
	
	$patrolCarStatusArray;;
	
	while ($row = $resultset->fetch_assoc()) {
	$patrolCarStatusArray[$row['statusId']] =$row['statusDesc'];
	}
	
	$stmt->close();
	
	$resultset->close();
	
	$mysqli->close();
?>
	
<form name="updateform" method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?> ">

	<table border="0" align="center" cellpadding="4" cellspacing="4">
	<tr></tr>
	<tr>
	<td>ID :</td>
	<td><?php echo $patrolCarId ?>
		<input type="hidden" name="patrolCarId" id="patrolCarId" value="<?php echo $patrolCarId ?>">
	</td>
	</tr>
	<tr>
		<td>Status :</td>
		<td><select name="patrolCarStatus" id="patrolCarStatus">
		<?php foreach($patrolCarStatusArray as $key => $value){?>
		<option value="<?php echo $key ?>"
				<?php if ($key==$patrolCarStatusId) {?> selected="selected"
		<?php } ?>
	>
		<?php echo $value ?>
		</option>
		<?php } ?>
		</select></td>
	</tr>
	<tr>
		<td><input type="reset" name="cancelbtn" id="cancelbtn" value="reset"></td>
		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="updatebtn" id="updatebtn" value="update"></td>
	</tr>
	</table>
	
</form>
<?php } ?>
</body>
</html>