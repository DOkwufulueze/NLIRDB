<?php
	/*
		DEPARTMENT : COMPUTER SCIENCE
		COURSE CODE : CSC 498
		COURSE TITLE : PROJECT
		PROJECT TITLE :Developing a Flexible Natural Language Interface For Relational Databases
		SUPERVISOR : MR. TOYIN ENIKUOMEHIN
		DEVELOPER : OKWUFULUEZE EMEKA DANIEL
		MATRIC NUMBER : 080591050
		DATE : 21/02/2012
		
	*/
	if($_POST){
		include("class.php");
		nlirdb::login();
	}
	include("top.inc");
	echo "<span style='color:#f00'><b>".$_GET['msg']."</b></span>";
?>
<p>Welcome to the Natural Language Interface for Relational Databases. The interface that appears after a successful login, enables you to query the database without any knowledge of SQL : just your natural language(English Language in this case).</p>
<form action="" method="post">
	<table border="0" cellspacing="20" cellpadding="0">
		<tr>
			<td>Username </td>
			<td><input type="text" id="uname" name="uname" value="<?php if(isset($_GET['uname'])){ echo $_GET['uname'];} else{ echo ""; } ?>"  /></td>
		</tr>
		<tr>
			<td>Password</td>
			<td><input type="password" id="pwd" name="pwd" /></td>
		</tr>
		<tr><td><input type="submit" value="Login" /></td></tr>
	</table>
</form>
<?php
	include("foot.inc") ;
?>