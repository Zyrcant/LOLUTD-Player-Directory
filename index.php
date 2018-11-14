<?php include "./dbinfo.inc"; ?>

<html>
<head>
    <link rel="stylesheet" type="text/css" href="dylanstyle.css" />
    <title>LOLUTD Player Directory</title>
</head>
<body>
<div class="image">
  <img src="./logo.png">
</div>
<h1>LOLUTD Player Directory</h1>
<?php
  if(isset($_POST['sortrole'])){
      header("Location: http://lolutd.epizy.com" . $_SERVER['SCRIPT_NAME'] . '?sortBy=role'); 
      exit;
  }
  if(isset($_POST['sortrank'])){
      header("Location: http://lolutd.epizy.com" . $_SERVER['SCRIPT_NAME'] . '?sortBy=rank'); 
	  exit;
  }
  /* Connect to MySQL and select the database. */
  $connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

  if (mysqli_connect_errno()) echo "Failed to connect to MySQL: " . mysqli_connect_error();

  $database = mysqli_select_db($connection, DB_DATABASE);

  /* Ensure that the Players table exists. */
  VerifyPlayersTable($connection, DB_DATABASE); 

  /* If input fields are populated, add a row to the Players table. */
  $players_netID = htmlentities($_POST['NetID']);
  $players_name = htmlentities($_POST['Name']);
  $players_summoner = htmlentities($_POST['Summoner']);
  $primaryrole = htmlentities($_POST['PrimaryRole']);
  $secondaryrole = htmlentities($_POST['SecondaryRole']);
  $discord = htmlentities($_POST['Discord']);
  if(isset($_POST['addplayer'])){
    if(strlen($players_netID) && strlen($players_summoner) && strlen($players_name))
        AddPlayers($connection, $players_netID, $players_name, $players_summoner, $primaryrole, $secondaryrole, $discord);
    else
        echo "<style> 
#rainbow {
   /* Chrome, Safari, Opera */
  -webkit-animation: rainbow 3s infinite; 
  
  /* Internet Explorer */
  -ms-animation: rainbow 3s infinite;
  
  /* Standar Syntax */
  animation: rainbow 3s infinite; 
}

/* Chrome, Safari, Opera */
@-webkit-keyframes rainbow{
    0%{color: red;}
    20%{color: orange;}
    40%{color: yellow;}
	60%{color: green;}
	80%{color: blue;}    
    90%{color: purple;}
    100%{color: red;}	
}
/* Internet Explorer */
@-ms-keyframes rainbow{    
	0%{color: red;}
    20%{color: orange;}
	40%{color: yellow;}
	60%{color: green;}
	80%{color: blue;}    
    90%{color: purple;}
    100%{color: red;}	
}

/* Standar Syntax */
@keyframes rainbow{
    0%{color: red;}
    20%{color: orange;}
    40%{color: yellow;}
	60%{color: green;}
	80%{color: blue;}    
    90%{color: purple;}
    100%{color: red;}
}
</style>
<p id=\"rainbow\"><marquee width=\"337\" behavior=\"alternate\" scrolldelay=\"45\" truespeed=\"true\">Please input all required fields.</marquee></p>";
  }
  if(isset($_POST['update'])){
      updateAllPlayers($connection);
  }
?>
<!-- some type of blurb about instructions lul--> 
If you want to edit your data (name change, role change, etc.), resubmit with your NetID and your entry will be automatically updated.

<br>
<br>

<!-- Input form -->
<form action="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
<table class="none" border="0">
    <tr>
      <td>NetID</td>
      <td>Name</td>
      <td>Summoner Name</td>
      <td>Primary</td>
      <td>Secondary</td>
      <td>Discord tag</td>
    </tr>
    <tr>
      <td>
        <input type="text" name="NetID" maxlength="20" size="20" />
      </td>
      <td>
        <input type="text" name="Name" maxlength="30" size="20" />
      </td>
      <td>
        <input type="text" name="Summoner" maxlength="30" size="20" />
      </td>
      <td>
        <select name="PrimaryRole">
            <option value="Fill">Fill</option>
            <option value="Top">Top</option>
		    <option value="Jg">Jungle</option>
		    <option value="Mid">Middle</option>
	    	<option value="Bot">Bot</option>
	    	<option value="Supp">Support</option>
        </select>
      </td>
      <td>
        <select name="SecondaryRole">
            <option value="Fill">Fill</option>
		    <option value="Top">Top</option>
		    <option value="Jg">Jungle</option>
		    <option value="Mid">Middle</option>
	    	<option value="Bot">Bot</option>
	    	<option value="Supp">Support</option>
            <option value="None">None</option>
        </select>
      </td>
      <td>
        <input type="text" name="Discord" maxlength="30" size="20" />
      </td>
      <td>
        <input type="submit" name="addplayer" value="Add Data" />
      </td>
    </tr>
    </table>
</form>

<br>
<table class="none" border="0">
	<td>
	    <div class="button">
		<form action="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
			<input type="submit" name="<?PHP $sort = $_GET['sortBy']; if(empty($sort)) $sort = 'rank'; if($sort === 'rank') echo "sortrole"; else echo "sortrank";?>" value="<?PHP $sort = $_GET['sortBy']; if(empty($sort)) $sort = 'rank'; if($sort === 'rank') echo "Sort by role"; else echo "Sort by rank";?>" />
		</form>
		</div>
	</td>
	<!-- <td>
		<div class="button">
		<form action="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
			<input type="submit" name="update" value="Update Ranks" />
		</form>
		</div>
	</td> -->
</table>
<?php
$sort = $_GET['sortBy'];
if(empty($sort)) $sort = 'rank';
if( $sort === "rank" ) { ?>
<!-- Display table data by rank. -->
<h2>Diamond+</h2>
<table class="diamond" border="0" cellpadding="2" cellspacing="2">
  <col width="200">
  <col width="200">
  <col width="200">
  <col width="100">
  <col width="80">
  <col width="200">
  <tr>
    <th><b>Name</b></th>
    <th><b>Summoner Name</b></th>
    <th><b>Rank</b></th>
    <th><b>Role</b></th>
    <th><b>op.gg</b></th>
    <th><b>Discord</b></th>
  </tr>
<?php
$result = mysqli_query($connection, "SELECT * FROM Players WHERE Rank='Challenger' ORDER BY Name ASC"); 

while($query_data = mysqli_fetch_row($result)) {
  if($query_data[5] === "None" || $query_data[4] === "Fill" || $query_data[4] === $query_data[5])
    $both = $query_data[4]; 
  else
    $both = $query_data[4] . '/' . $query_data[5];
  echo "<tr>";
  echo "<td>",$query_data[1], "</td>",
       "<td>",$query_data[2], "</td>",
       "<td>",$query_data[3], "</td>",
       "<td>",$both, "</td>",
       "<td> <a href='http://na.op.gg/summoner/userName=$query_data[2]' target='_blank'> Link </a> </td>",
       "<td>",$query_data[6], "</td>";
  echo "</tr>";
}
$result = mysqli_query($connection, "SELECT * FROM Players WHERE Rank='Master' ORDER BY Name ASC"); 

while($query_data = mysqli_fetch_row($result)) {
  if($query_data[5] === "None" || $query_data[4] === "Fill" || $query_data[4] === $query_data[5])
    $both = $query_data[4]; 
  else
    $both = $query_data[4] . '/' . $query_data[5];
  echo "<tr>";
  echo "<td>",$query_data[1], "</td>",
       "<td>",$query_data[2], "</td>",
       "<td>",$query_data[3], "</td>",
       "<td>",$both, "</td>",
       "<td> <a href='http://na.op.gg/summoner/userName=$query_data[2]' target='_blank'> Link </a> </td>",
       "<td>",$query_data[6], "</td>";
  echo "</tr>";
}
$result = mysqli_query($connection, "SELECT * FROM Players WHERE Rank='Diamond' ORDER BY Division, Name ASC"); 

while($query_data = mysqli_fetch_row($result)) {
  if($query_data[5] === "None" || $query_data[4] === "Fill" || $query_data[4] === $query_data[5])
    $both = $query_data[4]; 
  else
    $both = $query_data[4] . '/' . $query_data[5];
  $rank = $query_data[3] . " " . $query_data[7];
  echo "<tr>";
  echo "<td>",$query_data[1], "</td>",
       "<td>",$query_data[2], "</td>",
       "<td>",$rank, "</td>",
       "<td>",$both, "</td>",
       "<td> <a href='http://na.op.gg/summoner/userName=$query_data[2]' target='_blank'> Link </a> </td>",
       "<td>",$query_data[6], "</td>";
  echo "</tr>";
}
?>
</table>

<h2>Platinum</h2>
<table class="platinum" border="0" cellpadding="2" cellspacing="2">
  <col width="200">
  <col width="200">
  <col width="200">
  <col width="100">
  <col width="80">
  <col width="200">
  <tr>
    <th><b>Name</b></th>
    <th><b>Summoner Name</b></th>
    <th><b>Rank</b></th>
    <th><b>Role</b></th>
    <th><b>op.gg</b></th>
    <th><b>Discord</b></th>
  </tr>

<?php
$result = mysqli_query($connection, "SELECT * FROM Players WHERE Rank='Platinum' ORDER BY Division, Name ASC"); 

while($query_data = mysqli_fetch_row($result)) {
  if($query_data[5] === "None" || $query_data[4] === "Fill" || $query_data[4] === $query_data[5])
    $both = $query_data[4]; 
  else
    $both = $query_data[4] . '/' . $query_data[5];
  $rank = $query_data[3] . " " . $query_data[7];
  echo "<tr>";
  echo "<td>",$query_data[1], "</td>",
       "<td>",$query_data[2], "</td>",
       "<td>",$rank, "</td>",
       "<td>",$both, "</td>",
       "<td> <a href='http://na.op.gg/summoner/userName=$query_data[2]' target='_blank'> Link </a> </td>",
       "<td>",$query_data[6], "</td>";
  echo "</tr>";
}
?>
</table>

<h2>Gold</h2>
<table class="gold" border="0" cellpadding="2" cellspacing="2">
  <col width="200">
  <col width="200">
  <col width="200">
  <col width="100">
  <col width="80">
  <col width="200">
  <tr>
    <th><b>Name</b></th>
    <th><b>Summoner Name</b></th>
    <th><b>Rank</b></th>
    <th><b>Role</b></th>
    <th><b>op.gg</b></th>
    <th><b>Discord</b></th>
  </tr>

<?php
$result = mysqli_query($connection, "SELECT * FROM Players WHERE Rank='Gold' ORDER BY Division, Name ASC"); 

while($query_data = mysqli_fetch_row($result)) {
  if($query_data[5] === "None" || $query_data[4] === "Fill" || $query_data[4] === $query_data[5])
    $both = $query_data[4]; 
  else
    $both = $query_data[4] . '/' . $query_data[5];
  $rank = $query_data[3] . " " . $query_data[7];
  echo "<tr>";
  echo "<td>",$query_data[1], "</td>",
       "<td>",$query_data[2], "</td>",
       "<td>",$rank, "</td>",
       "<td>",$both, "</td>",
       "<td> <a href='http://na.op.gg/summoner/userName=$query_data[2]' target='_blank'> Link </a> </td>",
       "<td>",$query_data[6], "</td>";
  echo "</tr>";
}
?>
</table>

<h2>Silver</h2>
<table class="silver" border="0" cellpadding="2" cellspacing="2">
  <col width="200">
  <col width="200">
  <col width="200">
  <col width="100">
  <col width="80">
  <col width="200">
  <tr>
    <th><b>Name</b></th>
    <th><b>Summoner Name</b></th>
    <th><b>Rank</b></th>
    <th><b>Role</b></th>
    <th><b>op.gg</b></th>
    <th><b>Discord</b></th>
  </tr>

<?php
$result = mysqli_query($connection, "SELECT * FROM Players WHERE Rank='Silver' ORDER BY Division, Name ASC"); 

while($query_data = mysqli_fetch_row($result)) {
  if($query_data[5] === "None" || $query_data[4] === "Fill" || $query_data[4] === $query_data[5])
    $both = $query_data[4]; 
  else
    $both = $query_data[4] . '/' . $query_data[5];
  $rank = $query_data[3] . " " . $query_data[7];
  echo "<tr>";
  echo "<td>",$query_data[1], "</td>",
       "<td>",$query_data[2], "</td>",
       "<td>",$rank, "</td>",
       "<td>",$both, "</td>",
       "<td> <a href='http://na.op.gg/summoner/userName=$query_data[2]' target='_blank'> Link </a> </td>",
       "<td>",$query_data[6], "</td>";
  echo "</tr>";
}
?>
</table>

<h2>Bronze</h2>
<table class="bronze" border="0" cellpadding="2" cellspacing="2">
  <col width="200">
  <col width="200">
  <col width="200">
  <col width="100">
  <col width="80">
  <col width="200">
  <tr>
    <th><b>Name</b></th>
    <th><b>Summoner Name</b></th>
    <th><b>Rank</b></th>
    <th><b>Role</b></th>
    <th><b>op.gg</b></th>
    <th><b>Discord</b></th>
  </tr>

<?php
$result = mysqli_query($connection, "SELECT * FROM Players WHERE Rank='Bronze' ORDER BY Division, Name ASC"); 

while($query_data = mysqli_fetch_row($result)) {
  if($query_data[5]=== "None" || $query_data[4] === "Fill" || $query_data[4] === $query_data[5])
    $both = $query_data[4]; 
  else
    $both = $query_data[4] . '/' . $query_data[5];
  $rank = $query_data[3] . " " . $query_data[7];
  echo "<tr>";
  echo "<td>",$query_data[1], "</td>",
       "<td>",$query_data[2], "</td>",
       "<td>",$rank, "</td>",
       "<td>",$both, "</td>",
       "<td> <a href='http://na.op.gg/summoner/userName=$query_data[2]' target='_blank'> Link </a> </td>",
       "<td>",$query_data[6], "</td>";
  echo "</tr>";
}
?>
</table>

<h2>N/A</h2>
<table class="unranked" border="0" cellpadding="2" cellspacing="2">
  <col width="200">
  <col width="200">
  <col width="200">
  <col width="100">
  <col width="80">
  <col width="200">
  <tr>
    <th><b>Name</b></th>
    <th><b>Summoner Name</b></th>
    <th><b>Rank</b></th>
    <th><b>Role</b></th>
    <th><b>op.gg</b></th>
    <th><b>Discord</b></th>
  </tr>

<?php
$result = mysqli_query($connection, "SELECT * FROM Players WHERE Rank='None' ORDER BY Name ASC"); 

while($query_data = mysqli_fetch_row($result)) {
  if($query_data[5] === "None" || $query_data[4] === "Fill" || $query_data[4] === $query_data[5])
    $both = $query_data[4]; 
  else
    $both = $query_data[4] . '/' . $query_data[5];
  $rank = $query_data[3] . " " . $query_data[7];
  echo "<tr>";
  echo "<td>",$query_data[1], "</td>",
       "<td>",$query_data[2], "</td>",
       "<td>",$rank, "</td>",
       "<td>",$both, "</td>",
       "<td> <a href='http://na.op.gg/summoner/userName=$query_data[2]' target='_blank'> Link </a> </td>",
       "<td>",$query_data[6], "</td>";
  echo "</tr>";
}
?>
</table>
<?php }
$sort = $_GET['sortBy'];
if(empty($sort)) $sort = 'rank';
if( $sort === "role" ) { 
?>
<!-- Display table data by role. -->
<h2>Top</h2>
<table class="role" border="0" cellpadding="2" cellspacing="2">
  <col width="200">
  <col width="200">
  <col width="200">
  <col width="100">
  <col width="80">
  <col width="200">
  <tr>
    <th><b>Name</b></th>
    <th><b>Summoner Name</b></th>
    <th><b>Rank</b></th>
    <th><b>Role</b></th>
    <th><b>op.gg</b></th>
    <th><b>Discord</b></th>
  </tr>

<?php
$result = mysqli_query($connection, "SELECT * FROM Players WHERE PrimaryPosition='Top' ORDER BY Name ASC"); 

while($query_data = mysqli_fetch_row($result)) {
  if($query_data[5] === "None" || $query_data[4] === $query_data[5])
    $both = $query_data[4]; 
  else
    $both = $query_data[4] . '/' . $query_data[5];
  if($query_data[3] === "Challenger" || $query_data[3] === "Master")
      $rank = $query_data[3];
  else
      $rank = $query_data[3] . " " . $query_data[7];
  echo "<tr>";
  echo "<td>",$query_data[1], "</td>",
       "<td>",$query_data[2], "</td>",
       "<td>",$rank, "</td>",
       "<td>",$both, "</td>",
       "<td> <a href='http://na.op.gg/summoner/userName=$query_data[2]' target='_blank'> Link </a> </td>",
       "<td>",$query_data[6], "</td>";
  echo "</tr>";
}
$result = mysqli_query($connection, "SELECT * FROM Players WHERE SecondaryPosition='Top' AND NOT PrimaryPosition='Top' ORDER BY Name ASC"); 

while($query_data = mysqli_fetch_row($result)) {
  $both = $query_data[4] . '/' . $query_data[5];
  if($query_data[3] === "Challenger" || $query_data[3] === "Master")
      $rank = $query_data[3];
  else
      $rank = $query_data[3] . " " . $query_data[7];
  echo "<tr>";
  echo "<td>",$query_data[1], "</td>",
       "<td>",$query_data[2], "</td>",
       "<td>",$rank, "</td>",
       "<td>",$both, "</td>",
       "<td> <a href='http://na.op.gg/summoner/userName=$query_data[2]' target='_blank'> Link </a> </td>",
       "<td>",$query_data[6], "</td>";
  echo "</tr>";
}
?>
</table>

<h2>Jungle</h2>
<table class="role" border="0" cellpadding="2" cellspacing="2">
  <col width="200">
  <col width="200">
  <col width="200">
  <col width="100">
  <col width="80">
  <col width="200">
  <tr>
    <th><b>Name</b></th>
    <th><b>Summoner Name</b></th>
    <th><b>Rank</b></th>
    <th><b>Role</b></th>
    <th><b>op.gg</b></th>
    <th><b>Discord</b></th>
  </tr>

<?php
$result = mysqli_query($connection, "SELECT * FROM Players WHERE PrimaryPosition='Jg' ORDER BY Name ASC"); 

while($query_data = mysqli_fetch_row($result)) {
  if($query_data[5] === "None" || $query_data[4] === $query_data[5])
    $both = $query_data[4]; 
  else
    $both = $query_data[4] . '/' . $query_data[5];
  if($query_data[3] === "Challenger" || $query_data[3] === "Master")
      $rank = $query_data[3];
  else
      $rank = $query_data[3] . " " . $query_data[7];
  echo "<tr>";
  echo "<td>",$query_data[1], "</td>",
       "<td>",$query_data[2], "</td>",
       "<td>",$rank, "</td>",
       "<td>",$both, "</td>",
       "<td> <a href='http://na.op.gg/summoner/userName=$query_data[2]' target='_blank'> Link </a> </td>",
       "<td>",$query_data[6], "</td>";
  echo "</tr>";
}
$result = mysqli_query($connection, "SELECT * FROM Players WHERE SecondaryPosition='Jg' AND NOT PrimaryPosition='Jg' ORDER BY Name ASC"); 

while($query_data = mysqli_fetch_row($result)) {
  $both = $query_data[4] . '/' . $query_data[5];
  if($query_data[3] === "Challenger" || $query_data[3] === "Master")
      $rank = $query_data[3];
  else
      $rank = $query_data[3] . " " . $query_data[7];
  echo "<tr>";
  echo "<td>",$query_data[1], "</td>",
       "<td>",$query_data[2], "</td>",
       "<td>",$rank, "</td>",
       "<td>",$both, "</td>",
       "<td> <a href='http://na.op.gg/summoner/userName=$query_data[2]' target='_blank'> Link </a> </td>",
       "<td>",$query_data[6], "</td>";
  echo "</tr>";
}
?>
</table>

<h2>Middle</h2>
<table class="role" border="0" cellpadding="2" cellspacing="2">
  <col width="200">
  <col width="200">
  <col width="200">
  <col width="100">
  <col width="80">
  <col width="200">
  <tr>
    <th><b>Name</b></th>
    <th><b>Summoner Name</b></th>
    <th><b>Rank</b></th>
    <th><b>Role</b></th>
    <th><b>op.gg</b></th>
    <th><b>Discord</b></th>
  </tr>

<?php
$result = mysqli_query($connection, "SELECT * FROM Players WHERE PrimaryPosition='Mid' ORDER BY Name ASC"); 

while($query_data = mysqli_fetch_row($result)) {
  if($query_data[5] === "None" || $query_data[4] === $query_data[5])
    $both = $query_data[4]; 
  else
    $both = $query_data[4] . '/' . $query_data[5];
  if($query_data[3] === "Challenger" || $query_data[3] === "Master")
      $rank = $query_data[3];
  else
      $rank = $query_data[3] . " " . $query_data[7];
  echo "<tr>";
  echo "<td>",$query_data[1], "</td>",
       "<td>",$query_data[2], "</td>",
       "<td>",$rank, "</td>",
       "<td>",$both, "</td>",
       "<td> <a href='http://na.op.gg/summoner/userName=$query_data[2]' target='_blank'> Link </a> </td>",
       "<td>",$query_data[6], "</td>";
  echo "</tr>";
}
$result = mysqli_query($connection, "SELECT * FROM Players WHERE SecondaryPosition='Mid' AND NOT PrimaryPosition='Mid' ORDER BY Name ASC"); 

while($query_data = mysqli_fetch_row($result)) {
  $both = $query_data[4] . '/' . $query_data[5];
  if($query_data[3] === "Challenger" || $query_data[3] === "Master")
      $rank = $query_data[3];
  else
      $rank = $query_data[3] . " " . $query_data[7];
  echo "<tr>";
  echo "<td>",$query_data[1], "</td>",
       "<td>",$query_data[2], "</td>",
       "<td>",$rank, "</td>",
       "<td>",$both, "</td>",
       "<td> <a href='http://na.op.gg/summoner/userName=$query_data[2]' target='_blank'> Link </a> </td>",
       "<td>",$query_data[6], "</td>";
  echo "</tr>";
}
?>
</table>

<h2>Bot</h2>
<table class="role" border="0" cellpadding="2" cellspacing="2">
  <col width="200">
  <col width="200">
  <col width="200">
  <col width="100">
  <col width="80">
  <col width="200">
  <tr>
    <th><b>Name</b></th>
    <th><b>Summoner Name</b></th>
    <th><b>Rank</b></th>
    <th><b>Role</b></th>
    <th><b>op.gg</b></th>
    <th><b>Discord</b></th>
  </tr>

<?php
$result = mysqli_query($connection, "SELECT * FROM Players WHERE PrimaryPosition='Bot' ORDER BY Name ASC"); 

while($query_data = mysqli_fetch_row($result)) {
  if($query_data[5] === "None" || $query_data[4] === $query_data[5])
    $both = $query_data[4]; 
  else
    $both = $query_data[4] . '/' . $query_data[5];
  if($query_data[3] === "Challenger" || $query_data[3] === "Master")
      $rank = $query_data[3];
  else
      $rank = $query_data[3] . " " . $query_data[7];
  echo "<tr>";
  echo "<td>",$query_data[1], "</td>",
       "<td>",$query_data[2], "</td>",
       "<td>",$rank, "</td>",
       "<td>",$both, "</td>",
       "<td> <a href='http://na.op.gg/summoner/userName=$query_data[2]' target='_blank'> Link </a> </td>",
       "<td>",$query_data[6], "</td>";
  echo "</tr>";
}
$result = mysqli_query($connection, "SELECT * FROM Players WHERE SecondaryPosition='Bot' AND NOT PrimaryPosition='Bot' ORDER BY Name ASC"); 

while($query_data = mysqli_fetch_row($result)) {
  $both = $query_data[4] . '/' . $query_data[5];
  if($query_data[3] === "Challenger" || $query_data[3] === "Master")
      $rank = $query_data[3];
  else
      $rank = $query_data[3] . " " . $query_data[7];
  echo "<tr>";
  echo "<td>",$query_data[1], "</td>",
       "<td>",$query_data[2], "</td>",
       "<td>",$rank, "</td>",
       "<td>",$both, "</td>",
       "<td> <a href='http://na.op.gg/summoner/userName=$query_data[2]' target='_blank'> Link </a> </td>",
       "<td>",$query_data[6], "</td>";
  echo "</tr>";
}
?>
</table>

<h2>Support</h2>
<table class="role" border="0" cellpadding="2" cellspacing="2">
  <col width="200">
  <col width="200">
  <col width="200">
  <col width="100">
  <col width="80">
  <col width="200">
  <tr>
    <th><b>Name</b></th>
    <th><b>Summoner Name</b></th>
    <th><b>Rank</b></th>
    <th><b>Role</b></th>
    <th><b>op.gg</b></th>
    <th><b>Discord</b></th>
  </tr>

<?php
$result = mysqli_query($connection, "SELECT * FROM Players WHERE PrimaryPosition='Supp' ORDER BY Name ASC"); 

while($query_data = mysqli_fetch_row($result)) {
  if($query_data[5] === "None" || $query_data[4] === $query_data[5])
    $both = $query_data[4]; 
  else
    $both = $query_data[4] . '/' . $query_data[5];
  if($query_data[3] === "Challenger" || $query_data[3] === "Master")
      $rank = $query_data[3];
  else
      $rank = $query_data[3] . " " . $query_data[7];
  echo "<tr>";
  echo "<td>",$query_data[1], "</td>",
       "<td>",$query_data[2], "</td>",
       "<td>",$rank, "</td>",
       "<td>",$both, "</td>",
       "<td> <a href='http://na.op.gg/summoner/userName=$query_data[2]' target='_blank'> Link </a> </td>",
       "<td>",$query_data[6], "</td>";
  echo "</tr>";
}
$result = mysqli_query($connection, "SELECT * FROM Players WHERE SecondaryPosition='Supp' AND NOT PrimaryPosition='Supp' ORDER BY Name ASC"); 

while($query_data = mysqli_fetch_row($result)) {
  $both = $query_data[4] . '/' . $query_data[5];
  if($query_data[3] === "Challenger" || $query_data[3] === "Master")
      $rank = $query_data[3];
  else
      $rank = $query_data[3] . " " . $query_data[7];
  echo "<tr>";
  echo "<td>",$query_data[1], "</td>",
       "<td>",$query_data[2], "</td>",
       "<td>",$rank, "</td>",
       "<td>",$both, "</td>",
       "<td> <a href='http://na.op.gg/summoner/userName=$query_data[2]' target='_blank'> Link </a> </td>",
       "<td>",$query_data[6], "</td>";
  echo "</tr>";
}
?>
</table>
<h2>Fill</h2>
<table class="role" border="0" cellpadding="2" cellspacing="2">
  <col width="200">
  <col width="200">
  <col width="200">
  <col width="100">
  <col width="80">
  <col width="200">
  <tr>
    <th><b>Name</b></th>
    <th><b>Summoner Name</b></th>
    <th><b>Rank</b></th>
    <th><b>Role</b></th>
    <th><b>op.gg</b></th>
    <th><b>Discord</b></th>
  </tr>

<?php
$result = mysqli_query($connection, "SELECT * FROM Players WHERE PrimaryPosition='Fill' ORDER BY Name ASC"); 

while($query_data = mysqli_fetch_row($result)) {
    $both = $query_data[4]; 
  if($query_data[3] === "Challenger" || $query_data[3] === "Master")
      $rank = $query_data[3];
  else
      $rank = $query_data[3] . " " . $query_data[7];
  echo "<tr>";
  echo "<td>",$query_data[1], "</td>",
       "<td>",$query_data[2], "</td>",
       "<td>",$rank, "</td>",
       "<td>",$both, "</td>",
       "<td> <a href='http://na.op.gg/summoner/userName=$query_data[2]' target='_blank'> Link </a> </td>",
       "<td>",$query_data[6], "</td>";
  echo "</tr>";
}
?>
</table>
<?php } ?>
<br>
<br>
<small>Developed by Tiffany Do, Dylan Yu @ UT Dallas and George Du @ UT Austin</small>
<!-- Clean up. -->
<?php

  mysqli_free_result($result);
  mysqli_close($connection);

?>

</body>
</html>


<?php
/* Find a player's league from their summoner name. */
function getSummonerLeagueByName($name){
    $baseURL = "https://na1.api.riotgames.com/lol/";
    $apiKey = "RGAPI-4bf1c886-caf1-494b-ac4a-b5e09c0066d4";
	    $encodename = rawurlencode(html_entity_decode($name));
        //echo "URL ENCODED NAME: ", $encodename, "<br>";
		$summonerURL = $baseURL . "summoner/v3/summoners/by-name/" . $encodename . "?api_key=". $apiKey;
        //echo "THIS IS THE SUMMONER URL: " . $summonerURL . "<br>";
        $summonerCONTENTS = file_get_contents($summonerURL);
		$summonerJSON = json_decode($summonerCONTENTS, true);
		$id = $summonerJSON['id'];
    	//echo "THIS IS YOUR ID: " . $id . "<br>";
		 
		$leagueURL = $baseURL . "league/v3/positions/by-summoner/". $id ."?api_key=". $apiKey;
        //echo "THIS IS THE LEAGUE URL: " . $leagueURL . "<br>";
        $leagueCONTENTS = file_get_contents($leagueURL);
		$leagueJSON = json_decode($leagueCONTENTS, true);

		$index=-1;
		for ($x = 0; $x < 3; $x++) {
    	    //echo "THIS IS X: " . $x . "<br>THIS IS THE QUEUE TYPE: " . $leagueJSON[$x]['queueType'] . "<br>";
			if ($leagueJSON[$x]['queueType'] === "RANKED_SOLO_5x5") {
				$index = $x;
				break;
			}
		} 
		
    	//echo "THIS IS INDEX: " . $index . "<br>";
		if ($index > -1) {
			$league = $leagueJSON[$index]['tier'];
			$tier = $leagueJSON[$index]['rank'];
		}
		else {
			$league = "None";
		}
        $league = ucfirst(strtolower($league));
        
        //echo "THE LEAGUE IS: " . $league . " " . $tier . "<br>";
		return $league . " " . $tier;
}

function updateAllPlayers($connection) {
    $result = mysqli_query($connection, "SELECT * FROM Players"); 

    while($query_data = mysqli_fetch_row($result)) {
	    $i = $query_data[0];
		$n = $query_data[1];
		$sn = $query_data[2];
		$rank = explode(" ", getSummonerLeagueByName($sn));
		$p = $query_data[4];
		$s = $query_data[5];
		$d = $query_data[6];
		$r = $rank[0];
		$t = $rank[1];
        if ($r != $query_data[3]) { 
	        $query = "INSERT INTO `Players` (`netID`, `Name`, `SummonerName`, `Rank`, `PrimaryPosition`, `SecondaryPosition`, `DiscordTag`, `Division`) VALUES ('$i', '$n', '$sn', '$r', '$p', '$s', '$d', '$t') ON DUPLICATE KEY UPDATE Name='$n', SummonerName='$sn', Rank='$r', PrimaryPosition='$p', SecondaryPosition='$s', DiscordTag='$d', Division='$t';";

            if(!mysqli_query($connection, $query)) echo("<p>Error updating data.</p>");
        }
	}
}

/* Add a player to the table. */
function AddPlayers($connection, $players_netID, $players_name, $players_summoner, $primaryrole, $secondaryrole, $discord) {
   $i = mysqli_real_escape_string($connection, $players_netID);
   $n = mysqli_real_escape_string($connection, $players_name);
   $sn = mysqli_real_escape_string($connection, $players_summoner);
   //$r = mysqli_real_escape_string($connection, $rank);
   $rank = explode(" ", getSummonerLeagueByName($sn));
   $p = mysqli_real_escape_string($connection, $primaryrole);
   $s = mysqli_real_escape_string($connection, $secondaryrole);
   $d = mysqli_real_escape_string($connection, $discord);
   $r = $rank[0];
   $t = $rank[1];
   
   $query = "INSERT INTO `Players` (`netID`, `Name`, `SummonerName`, `Rank`, `PrimaryPosition`, `SecondaryPosition`, `DiscordTag`, `Division`) VALUES ('$i', '$n', '$sn', '$r', '$p', '$s', '$d', '$t') ON DUPLICATE KEY UPDATE Name='$n', SummonerName='$sn', Rank='$r', PrimaryPosition='$p', SecondaryPosition='$s', DiscordTag='$d', Division='$t';";

   if(!mysqli_query($connection, $query)) echo("<p>Error adding data.</p>");
 
   //updateAllPlayers($connection);
}

/* Check whether the table exists and, if not, create it. */
function VerifyPlayersTable($connection, $dbName) {
  if(!TableExists("Players", $connection, $dbName)) 
  { 
     $query = "CREATE TABLE `Players` (
         `netID` varchar(30) NOT NULL,
         `Name` varchar(45) DEFAULT NULL,
         `SummonerName` varchar(90) DEFAULT NULL,
         `Rank` varchar(90) DEFAULT NULL,
         `PrimaryPosition` varchar(10) DEFAULT NULL,
         `SecondaryPosition` varchar(10) DEFAULT NULL,
         `DiscordTag` varchar(30) DEFAULT NULL,
		 `Division` varchar(10) DEFAULT NULL,
         PRIMARY KEY (`netID`),
         UNIQUE KEY `ID_UNIQUE` (`netID`)
       ) ENGINE=InnoDB DEFAULT CHARSET=latin1";

     if(!mysqli_query($connection, $query)) echo("<p>Error creating table.</p>");
  }
}

/* Check for the existence of a table. */
function TableExists($tableName, $connection, $dbName) {
  $t = mysqli_real_escape_string($connection, $tableName);
  $d = mysqli_real_escape_string($connection, $dbName);

  $checktable = mysqli_query($connection, 
      "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME = '$t' AND TABLE_SCHEMA = '$d'");

  if(mysqli_num_rows($checktable) > 0) return true;

  return false;
}
?>