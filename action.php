<?php
session_start();
header('Access-Control-Allow-Origin:*');
$connect = mysql_connect('localhost','admin','admin123');
$recipient_id; 
if(!mysql_select_db('cheapousers'))
{
	die('Failure selecting Database'. mysql_error());
}



if($_GET['a'] == 'login')
{
	$cred_verify_qstring =  "SELECT * 
			         	   FROM users 
			         	   WHERE username = '$_POST[username]' AND pword ='$_POST[password]'";  // Login query 
	$cred_verify_query = mysql_query($cred_verify_qstring,$connect);
	if(!$cred_verify_query) 			//Verification of the query
	{
		die('Query Failure'.mysql_error($connect));
	}
	else
	{
		if($row =mysql_fetch_array($cred_verify_query,MYSQL_ASSOC))
		{
			while($row)
			{
				if(($_POST['username'] == $row['username']) && ($_POST['password'] == $row['pword']))
				{
					$_SESSION['Name'] = $row['firstname'].' '.$row['lastname']; 
					$_SESSION['ID'] = $row['id'];
					$_SESSION['Username'] = $row['username'];
					echo ('Login Successful');
					echo('<script>location.replace("message_board.php")</script>');
				}
			}
		}
		else
		{
			echo "<script>alert('Incorrect Credentials');
					location.replace('cheapomail.html');</script>";
		}
	}
}
else if ($_GET['a'] == 'register')
{
	$insert_qstring = "INSERT INTO users 
			(
				firstname,
				lastname,
				pword,
				username
			) 
			VALUES
			(
				'$_POST[fname]',
				'$_POST[lname]',
				'$_POST[pword]',
				'$_POST[username]')";
	$register_query = mysql_query($insert_qstring, $connect);
	if(!$register_query)
	{
		die('Query error'.mysql_error($connect));
	}
	else
	{
		echo"<script>alert('Registration complete')</script>";
		echo "<script>location.replace('register.php')</script>";
	}
}
else if($_GET['a']=='logout')
{
	echo '<script>alert("Logging you out '.$_SESSION['Name'].'")</script>';
	session_destroy();
	echo '<script>location.replace("cheapomail.html");</script>';
}
else if($_GET['a'] == 'getmessage')
{//control sequence to get all the msesage for the user that is logged ins
	header('Content-Type: text/xml');
	$querystring = "SELECT *
			 FROM message, users
			 WHERE  message.recipient_id = '$_SESSION[ID]'";
	$XML_GETquery = mysql_query($querystring, $connect);
	if(!$XML_GETquery)
	{
		die('Query Error'.mysql_error($connect));
	}
	else
	{
		$xml = new DOMDocument('1.0', 'iso-8859-1');
		//Creating the XML file with the root node <MESSAGESTORE>
		$xml->formatOutput = true;
		$beginningNode = $xml->createElement('MESSAGESSTORE');
		$xml->appendChild($beginningNode);
		while($row = mysql_fetch_array($XML_GETquery, MYSQL_ASSOC))
		{
			//Tag that should house all the details for a message
			$messageTag =  $xml ->createElement('MESSAGE'); 
			$beginningNode->appendChild($messageTag);
			$senderIDTag = $xml->createElement('ID', $row['id']);
			$toTag= $xml->createElement('TO',$row['recipient_id']);
			$fromTag = $xml->createElement('FROM',$row['user_id']);
			$subjectTag = $xml->createElement('SUBJECT',$row['subject']);
			$contentTag = $xml->createElement('BODY',$row['body']);
			
			//Appending the created Tags to the XML document under  <MESSAGE>
			$messageTag->appendChild($senderIDTag);
			$messageTag->appendChild($toTag);
			$messageTag->appendChild($fromTag);
			$messageTag->appendChild($subjectTag);
			$messageTag->appendChild($contentTag);
		}
		$xml->save('cheapo1.xml');
		echo $xml->saveXML();
	}

}
else if($_GET['a']=='messagecompose')
{
	$recipient_check="SELECT id
			         FROM users
			         WHERE username = '$_REQUEST[to]'";
	$check_query = mysql_query($recipient_check, $connect);
	if(!$check_query || $_REQUEST['to'] == NULL)
	{
		die('User not Found\n'.mysql_error($connect));
	}
	else
	{
		while($row = mysql_fetch_array($check_query,MYSQL_ASSOC))
		{
			$recipient_id = $row['id'];
		}
		$send_message_string = "INSERT INTO message
					       (
					       	body,
					       	subject,
					       	user_id,
					       	recipient_id
					       )
					       VALUES
					       (
					       	'$_REQUEST[body]',
					       	'$_REQUEST[subject]',
					       	'$_SESSION[ID]',
					       	'$recipient_id'
					       )";
		$send_query = mysql_query($send_message_string,$connect);
		if(!$send_query)
		{
			die('Send Failure<br>'.mysql_error($connect));
		}
		else
		{
			echo 'Success';
		}
	}

}
else if(!isset($_GET['a']))
{
	echo '<script>alert("Error occured \nReturning you to the Message Board")</script>';
	echo '<script>location.replace("message_board.php")</script>';
}
?>