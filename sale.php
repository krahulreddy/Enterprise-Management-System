<?php
	session_start();

	if(!isset($_SESSION['Eid']))
		header('location:login.php');

	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "supermarket";
?>

<?php include "head.php"; ?>
<!DOCTYPE html>
<html>
<head>
<style>

</style>
</head>
<body>


<div class = "container-fluid row padding" >
    <div class="col-lg-4 col-md-6 col-sm-6" >
        <h1 style = "padding-left: 25%; padding-top: 10%"><br>Sales : </h1>
    </div>

    <div class="col-lg-8 col-md-6 col-sm-6" >
		<form method="post">
			<label><h5>Customer ID</h5></label>
			<br>
			<select name="Cid">
			<?php
				$mysqli = new mysqli($servername, $username, $password, $dbname);
				$sqlSelect="SELECT * FROM customer";
				$result = $mysqli-> query ($sqlSelect);
				while ($row = mysqli_fetch_array($result)) {
					$rows[] = $row;
				}
				foreach ($rows as $row) {
					print "<option value='" . $row['Customer_ID'] . "'>" .$row['Customer_ID']."(". $row['Customer_Name'] . ")</option>";
				}
			?>
			</select>
			<br><br>
			<label><h5>Showroom ID</h5></label>
			<br>
			<select name="Sid">
			<?php
				$mysqli = new mysqli($servername, $username, $password, $dbname);
				$sqlSelect="SELECT * FROM showroom";
				$result = $mysqli-> query ($sqlSelect);
				while ($row = mysqli_fetch_array($result)) {
					$rows1[] = $row;
				}
				foreach ($rows1 as $row) {
					print "<option value='" . $row['Showroom_ID'] . "'>" .$row['Showroom_ID']."(". $row['Showroom_Location'] . ")</option>";
				}
			?>
			</select>
			<br><br>
			<label><h5>Date of Sale</h5></label>
			<br>
			<input type="Date" name="date">
			<br><br>
			<button type="submit" name="button1">Add</button>

		</form>
	</div>
</div>

<?php
	function isShowroomManager($mid, $sid)
	{
		$q = "select * from showroom where Showroom_ID = $sid and Manager_ID = $mid";

		$con = mysqli_connect("127.0.0.1","root","");
		mysqli_select_db($con, "supermarket");
		
		$result = mysqli_query($con, $q);

		$n = mysqli_num_rows($result);
		return $n > 0;
	}
	
	if(isset($_POST['button1']))
	{
		$Sid = $_POST['Sid'];
		$Cid = $_POST['Cid'];
		$input_date = $_POST['date'];
		$date=date("Y-m-d",strtotime($input_date));
		if(1 ||isShowroomManager($_SESSION['Eid'], $Sid))
		{
			$con = mysqli_connect("127.0.0.1","root","");
			mysqli_select_db($con, "supermarket");

			$q = "select * from sale where Showroom_ID = '$Sid' and Customer_ID = '$Cid' and DOS = '$date'";			
			$result = mysqli_query($con, $q);
			$n = mysqli_num_rows($result);
			// echo $n."<br><br>";

			if($n != 0)
			{

				$q = "select Sale_ID from sale where Showroom_ID = '$Sid' and Customer_ID = '$Cid' and DOS = '$date' limit 1";
				$result = mysqli_query($con, $q);
				$Saleid = mysqli_fetch_row($result)[0];

				$servername = "localhost";
				$username = "root";
				$password = "";
				$dbname = "supermarket";

				$conn = new mysqli($servername, $username, $password, $dbname);
				if ($conn->connect_error) {
				    die("Connection failed: " . $conn->connect_error);
				}
				$sql = "SELECT * FROM sale_item_details natural join item where Sale_ID = $Saleid";

				$result = $conn->query($sql);

				if ($result->num_rows > 0) {
				    echo "<table class = 'table table-hover table-striped'><tr><th>Item ID</th><th>Item Name</th><th>Quantity</th><th>Price per unit</th><th>Total price</th></tr>";

				    while($row = $result->fetch_assoc()) {
				        echo "<tr><td>" . $row["Item_ID"]. "</td><td>" . $row["Item_Name"]. "</td><td>" . $row["Quantity"] . "</td><td>Rs." . $row["Item_Unit_Price"] . " per " . $row["Item_Units"] ."</td><td>" . $row["Item_Unit_Price"] * $row["Quantity"]. "</td></tr>";
				    }
				    echo "</table>";
				} else {
				    echo "0 results";
				}


			}
			else
			{
				$q = "INSERT INTO `sale`(`Showroom_ID`, `Customer_ID`, `DOS`) VALUES ($Sid, $Cid, '$date')";
				// echo $q;

				if(mysqli_query($con, $q))
				{
					echo "Sale Added Successfully!!";
				}
				else
				{
					echo "Sale cannot be added!! Check Details again";
				}
			}
			$q = "select Sale_ID from sale where Showroom_ID = '$Sid' and Customer_ID = '$Cid' and DOS = '$date' limit 1";
			$result = mysqli_query($con, $q);
			$Saleid = mysqli_fetch_row($result)[0];
			echo "<br><br<b>Sale exists!! Sale id is : $Saleid</b><br><br>";
			echo "<a href='editsale.php?Saleid=".$Saleid."&Sid=".$Sid."&dos=".$date."&Cid=".$Cid."''>Edit this sale</a><br><br>";
		}
		else
		{
			echo "You are not permitted to add Sale!!";
		}
	}

	
?>

<br>
<center><h3><a href='home.php' style = "color : white; font-weight : bold; padding-left : 50px; text-decoration: underline">Back</a></h3></center>
<br><br><br>

</body>
</html>