<?php

//require_once ('../../Config/config.inc.php');
//require_once(PHP_LIBRARY);
?>
<!DOCTYPE html >
<meta charset="utf-8">
<html lang="en">
<html>
<head>
<title>POS Admin Area Login</title>
<style type="text/css">
	html,body,#wrapper
	{padding:0px;margin:0px;background-color: black;font-family:arial;font-size:12px;margin:0px;padding:0px;height:100%;border:0px solid white;text-align:center;}
	#wrapper{margin:0 auto;text-align:left;vertical-align:middle;width:500px;}
	div{font-family:arial;font-size:12px;margin:0px;padding:0px;border:0px solid white;}
	p{margin-top:0px;margin-bottom:7px;padding:0px;}
	.optionheader{clear:both;cursor:pointer;border-bottom:0px solid white;padding-bottom:5px;padding-top:5px;;width:495px;}
	.optioncontent{clear:both;background:#ffffff;padding-top:10px;padding-bottom:10px;padding-left:25px;width:500px;}
	.optionradio{float:left;width:25px;padding-top:3px;text-align:center;}
	.optionradiotitle{float:left;width:450px;padding-top:3px;}
	.companyline{text-align: center;width:465px;clear:both;margin:0px 0px 60px 0px;padding-bottom:7px;height:35px}
	.buttonline {text-align: center;width:465px;clear:both;margin:0px 0px 60px 0px;padding-bottom:0px;height:35px;}
	.inputline{text-align: center;width:465px;clear:both;margin:0px 0px 60px 0px;padding-bottom:0px;height:35px;}
	.inputleft{float:left;width:240px;border:0px solid white;font-size:11px;}
	.inputleft input{width:200px;}
	.inputleft select{width:200px;}
	.inputright{float:left;width:220px;font-size:11px;}
	.inputright input{width:200px;}
	.inputright select{width:200px;}
	.buttonspace{width:500px;clear:both;padding-top:20px;padding-bottom:0px;;text-align:center;}
		
	.loginpart{;padding:30px 0px 0px 0px; border-top:1px solid rgb(240,240,240);border-bottom:1px solid rgb(240,240,240);}
	.button
	{
		width: 100px;
		padding: 2px 8px; 
		margin: 2px 4px 6px 0px; 
	}
	.logo {
        font-size: 2em;
		font-weight: bold;
        font-family: <?PHP ECHO getSetting('logo_font') ?>;
        margin: 0px 0px 0px 0px;
}
.disclaimer
{

		
}
a 
	{
		color:#777;
		text-decoration:none;
	}
.footer  
{
     
   
     color: rgb(200,200,200);
		font:0.8em Arial, Helvetica, "bitstream vera sans", sans-serif;
		text-align: center;
     
     position: absolute;
     bottom: 0px;
     left: 0px;
     width: 100%;
     height: 18px
     }
</style>



</head>

<body>
<table style="border:0px solid white" id="wrapper" cellpadding="0" cellspacing="0">
	<tr>
		<td width="500" <?php if (LIVE) {echo 'bgcolor="white"';} else  {echo 'bgcolor="yellow"';} ?> style="padding:50px;">
			<form action="login.php" method="post">
			<input type="hidden" name="action" value="login"/>
			<div class="optioncontent">
				<div class="companyline">
					<p class="logo"><?PHP echo str_replace(' ', '&nbsp;',getSetting('company_logo')) ?></p>
				<?PHP  if (LIVE)
						{
						}
						else
						{
							echo '<p>TEST SYSTEM IS TURNED ON</p>';
						}
				?>
				</div>
				
					&nbsp;
			<div class="loginpart">

				<div class="buttonline">
					<div class="inputleft">
						<b>Username</b><br/>
						<input type="text" id="login_username" name="login" value=""/>
					</div>
					<div class="inputright">
						<b>Password</b><br/>
						<input type="password" name="password"/>
					</div>
					<!-- BUTTON -->
					<div class="buttonspace">
						<input class = "button" type="submit" value="Login"/>
						<input type="hidden" name="submitted" value="TRUE" />
					</div>
					<script type="text/javascript">
						document.getElementById("login_username").focus();
					</script>
				</div>
			</div>


			</form>
			</div>
			
				&nbsp;
			</div>
			<div class="optioncontent">
				<div class="inputline" style="font-size:11px;color:#999999;">
					Your IP Address is: <?php echo $_SERVER['REMOTE_ADDR']?> <br />
					Your Device is: <?php echo $_SERVER['HTTP_USER_AGENT'] ?> <br />
					This system has been developed using MOZILLA FIREFOX <br/>
					Alternate browsers may not work as expected. <br/>
					Your browser must be set to accept cookies and javascript enabled to access the admin area.<br/>
					POPUP's MUST BE ENABLED FOR THE DOMAIN!<br/>
					

				</div>
			</div>
			
		</td>
	</tr>
</table>
<div class="footer">
<p class ="discalimer">Piece Of Sale Copyright &copy; <a href="#">CraigIannazzi</a> 2012 | Designed and Hand Coded by Craig Iannazzi</a> | Sponsored by <a href="http://www.Embrasse-Moi.com/">Embrasse-Moi</a> </p>
</div>

</body>
</html>
