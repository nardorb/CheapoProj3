window.onload = preload;
function preload()
{
	document.getElementById('cancel').onclick = messageboardredirect;
	var inputTags = document.getElementsByTagName('input');
	inputTags[2].onblur = validate;
}

function validate()
{
	//Regular Expressions -- REGEX for ensuring that pssword have at least
	//one number one letter, and a captial letter and have to be at least 8 
	//characters long 

	//Returns True if the key is using the right paradigm stated
	var patt1 = /\d/;
	var patt2 = /[A-Z]+/;
	var patt3 = /[a-z]/;
	
	if(!patt1.test(document.getElementsByTagName('input')[2].value))
	{
		alert('Invalid Password\n Password should contain at least one digit');
	}
	else if(!patt2.test(document.getElementsByTagName('input')[2].value))
	{
		alert('Invalid Password\n Password should contain at least one capital letter');
	}
	else if(!patt3.test(document.getElementsByTagName('input')[2].value))
	{
		alert('Invalid Password\n Password should contain at least one letter');
	}
	else if(document.getElementsByTagName('input')[2].value.length >= 8 )
	{
		alert('Invalid Password Length\nEnsure that your password is 8 characters or greater');
	}
	else 
	{
		return true;
	}
}

function messageboardredirect()
{
	location.replace('message_board.php');
}