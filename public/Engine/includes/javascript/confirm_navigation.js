//Set the script up to prevent leaving with unsaved changes
//You need to use this to turn off the confirmation (usually before an include)
/*
echo '<script type="text/javascript">
					needToConfirm=false;
					</script>' ;
*/
// and you need to set the submit button up to turn off the confimation
//<input type="submit" name="submit" value="Add Manufacturer" onclick="needToConfirm=false;" />

needToConfirm = false;
window.onbeforeunload = askConfirm;

function askConfirm()
	{
		 if (needToConfirm)
		 {
			 return "You have unsaved changes.";
		 }
	}

