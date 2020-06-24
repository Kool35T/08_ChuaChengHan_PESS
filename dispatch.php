<?php
if(!isset($_POST['callername'])){
    header("Location: logcall.php");
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Dispatch</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link href="headstyle.css" rel="stylesheet" type="text/css">
</head>

<body>

<?php require_once 'nav.php'?> <!-- nav bar -->
<div class="all">
<?php
if(isset($_POST["dispatchbtn"]))
{
	require_once 'db_config.php';
	
	$mysqli = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
	if($mysqli->connect_errno)
	{
		die("Line 27: Error connecting to MySQL: ".$mysqli->errno);
	}
	
	$patrolcarDispatched = $_POST["checkpatrolcar"]; 
	$numOfPatrolcarDispatched = count($patrolcarDispatched);
	
	$incidentStatus;
	if($numOfPatrolcarDispatched > 0) {
		$incidentStatus='2';
	} else {
		$incidentStatus='1';
	}
	
	$sql = "INSERT INTO incident (callerName, phoneNumber, incidentTypeId, incidentLocation, incidentDesc, incidentStatusId) VALUES (?, ? , ? ,? , ?, ?)";
	
	if(!($stmt = $mysqli->prepare($sql)))
	{
		die("Line 44: Error Preparing: ".$mysqli->errno);
	}
	if(!$stmt->bind_param('ssssss', $_POST['callername'],
						$_POST['contactno'],
						$_POST['incidenttype'],
						$_POST['location'],
						$_POST['description'],
						$incidentStatus))
	{
		die("Line 53: Error Binding parameters: ".$stmt->errno);
	}
	if(!$stmt->execute())
	{
		die("Line 57: Error Inserting incident table: ".$stmt->errno);
	}
	
	$incidentId=mysqli_insert_id($mysqli);
	
	for($i=0; $i < $numOfPatrolcarDispatched; $i++)
	{
		$sql = "UPDATE patrolcar SET patrolcarStatusId = '1' WHERE patrolcarId = ?";
		
		if(!($stmt = $mysqli->prepare($sql))) {
			die("Line 67: Error Preparing: ".$stmt->errno);
		}
		
		if(!($stmt->bind_param('s', $patrolcarDispatched[$i]))){
			die("Line 71: Error binding parameter: ".$stmt->errno);
		}
		
		if(!$stmt->execute()){
			die("Line 75: Error updating patrolcar_status table: ".$stmt->errno);
		}
		
		$sql = "INSERT INTO dispatch (incidentId, patrolcarId, timeDispatched) VALUES (?, ?, NOW())";
	
		if(!($stmt = $mysqli->prepare($sql))){
			die("Line 81: Error Preparing: ".$mysqli->errno);
		}
		if(!$stmt->bind_param('ss', $incidentId, $patrolcarDispatched[$i])){
			die("Line 84: Error binding parameters: ".$stmt->errno);
		}
		if(!$stmt->execute()) {
			die("Line 87: Error inserting table: ".$stmt->errno);
		}
	
	}
		$stmt->close();
		$mysqli->close();
	
}

?>
<form name="dispatch" method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?> ">
<table width="40%" align="center" cellpadding="5" cellspacing="5">
<tr>
<td colspan="2">Incident Detail</td>
</tr>
<tr>
<td width="50%">Caller's Name :</td>
<td width="50%"><?php echo $_POST['callername'] ?>
	<input type="hidden" name="callername" id="callername" value="<?php echo $_POST['callername']?>"</td>
</tr>
<tr>
<td width="50%">Contact No :</td>
<td width="50%"><?php echo $_POST['contactno'] ?>
	<input type="hidden" name="contactno" id="contactno" value="<?php echo $_POST['contactno']?>"</td>
</tr>
<tr>
<td width="50%">Location :</td>
<td width="50%"><?php echo $_POST['location'] ?>
	<input type="hidden" name="location" id="location" value="<?php echo $_POST['location'] ?>"</td>
</tr>
<tr>
<td width="50%">Incident Type :</td>
<td width="50%"><?php echo $_POST['incidenttype'] ?>
	<input type="hidden" name="incidenttype" id="incidenttype" value="<?php echo $_POST['incidenttype']?>"</td>
</tr>
<tr>
<td width="50%">Description :</td>
<td width="50%"><textarea name="description" cols="45" rows="5" readonly id="description"><?php echo $_POST['description']?></textarea>
	<input type="hidden" name="description" id="description" value="<?php echo $_POST['description']?>"</td>
</tr>
	</table>
<?php require_once 'db_config.php'; // configs for database

$mysqli = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);

if ($mysqli->connect_errno)
{
	die("Line 134: Error connecting to MySQL: ". $mysqli->connecterrno);
}

$sql = "SELECT patrolcarId, statusDesc FROM patrolcar JOIN patrolcar_status ON patrolcar.patrolcarStatusId=patrolcar_status.StatusId WHERE patrolcar.patrolcarStatusId='2' OR patrolcar.patrolcarStatusId='3'";
	// check sql command in $sql if works, if error display an error message and exit
if (!($stmt = $mysqli->prepare($sql)))
{
	die("Line 141: Error Preparing: ".$mysqli->errno);
}
// check if it can run the command
if (!$stmt->execute())
{
	die("Line 146: Error Executing: ".$stmt->errno);
}
// check any data in result set
if (!($resultset = $stmt->get_result())) {
	die("Line 150: Error getting result set : ".$stmt->errno);
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
	<td colspan="3">Dispatch Patrolcar Panel</td>
	</tr>
	<?php
		foreach($patrolcarArray as $key=>$value){
	?>
	<tr>
	<td><input type="checkbox" name="checkpatrolcar[]" 
			   value="<?php echo $key?>"></td>
	<td><?php echo $key ?></td>
	<td><?php echo $value ?></td>
	</tr>
	<?php } ?> 
	<tr>
	<td><input type="reset" name="resetbtn" id="resetbtn" value="reset"></td>
	<td colspan="2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="dispatchbtn" id="dispatchbtn" value="dispatch"></td>
	</tr>
	</table>
	</form>
	</div>
</body>
</html>