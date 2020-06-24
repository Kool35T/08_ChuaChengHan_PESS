<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>PESS</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link href="headstyle.css" rel="stylesheet" type="text/css">
</head>

<body>
<script>
function validation()
{
	var a=document.forms["frmLogCall"]["callername"].value;
	var b=document.forms["frmLogCall"]["contactno"].value;
	var c=document.forms["frmLogCall"]["location"].value;
	var d=document.forms["frmLogCall"]["description"].value;
	
	if(a==null || a=="")
	{
		alert("Please enter a caller name");
		return false;
	}
	else if(!isNaN(a))
	{
		alert("Please enter a valid name");
	}
	else if(b==null || b=="")
	{
		alert("Please enter a contact number");
	} 
	else if(isNaN(b))
	{
		alert("Please enter a valid number");
	}
		else if(c==null || c=="")
	{
		alert("Please enter a Location");
		return false;
	}
	else if(d==null || d=="")
	{
		alert("Please enter a description");
		return false;
	}
}
</script>
<?php require_once 'nav.php'?> <!-- nav bar -->
<?php require_once 'db_config.php'; // configs for database

$mysqli = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);

if ($mysqli->connect_errno)
{
	die("Line 54: Error connecting to MySQL: ". $mysqli->connecterrno);
}

$sql = "SELECT * from incidenttype";
	// check sql command in $sql if works, if error display an error message and exit
if (!($stmt = $mysqli->prepare($sql)))
{
	die("Line 61: Error Preparing: ".$mysqli->errno);
}
// check if it can run the command
if (!$stmt->execute())
{
	die("Line 66: Error Executing: ".$stmt->errno);
}
// check any data in result set
if (!($resultset = $stmt->get_result())) {
	die("Line 70: Error getting result set : ".$stmt->errno);
}

$incidentType; // an array var

while($row = $resultset->fetch_assoc()) {
	//creates an associative array of $incidentType [incidentTypeId, incidentTypeDesc]
	$incidentType[$row['incidentTypeId']] = $row['incidentTypeDesc'];
}

$stmt->close();

$resultset->close();

$mysqli->close();
	
?>
<fieldset>
<legend>Log Call</legend>
<form name="frmLogCall" method="post" action="dispatch.php">
<table id="logcall" width="40%" align="center" cellpadding="5" cellspacing="5">
<tr>
<td width="50%">Caller's Name :</td>
<td width="50%"><input type="text" id="callername" name="callername" required></td>
</tr>
<tr>
<td width="50%">Contact No :</td>
<td width="50%"><input type="number" id="contactno" name="contactno" maxlength="8" required></td>
</tr>
<tr>
<td width="50%">Location :</td>
<td width="50%"><input type="text" id="location" name="location" required></td>
</tr>
<tr>
<td width="50%">Incident Type :</td>
<td width="50%"><select id="incidenttype" name="incidenttype" required>
<option value="" selected disabled>Please Select an option</option>
<?php
foreach($incidentType as $key => $value) {?>
<option value="<?php echo $key ?> ">
	<?php echo $value ?> </option>
<?php } ?>
	</select>
</td>
	</tr>
	</tr>
<tr>
<td width="50%">Description :</td>
	<td width="50%"><textarea id="description" name="description" cols="45" rows="5" required<></textarea></td>
</tr>
<tr>
<td><input type="reset" name="reset" id="reset" value="reset"></td>
<td><input type="submit" name="submit" id="submit" value="submit"  onClick="return validation()"></td>
</tr>
</table>	
</form>
</fieldset>
</body>
</html>
