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
		nlirdb::admin();
	}
	include("top.inc");
	echo "<u><b>".$_GET['msg']."</b></u>" ;
?>
	<p>In order to add users who could also possess rights to query the database, you must be an administrator. If you're an administrator, please enter your password to gain access to the admin page.</p>
	<form action="" method="post">
	Username&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" id="uname" name="uname" /><br /><br />
	Password&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="password" id="pwd" name="pwd" /><br /><br />
	<input type="submit" value="Enter" />
</form>
<?php
	include("foot.inc");
?>