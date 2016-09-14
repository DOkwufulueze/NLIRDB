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
	/*$uname=$_SESSION['user'];
	if($uname==""){
		header("Location:.?&msg=Invalid Access to Protected Page");
	}*/
	if($_POST){
		include("class.php");
		nlirdb::signup();
	}
	include("top.inc");
	echo "<u><b>".$_GET['msg']."</b></u>" ;
?>
	<span style="float:right"><a href='.?page=logout&msg=Logged Out!'>Logout</a></span>
	<p>Enter the Username and Password for the new User you want to create.</p>
	<form action="" method="post">
	Username&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" id="uname" name="uname"/><br /><br />
	Password&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="password" id="pwd" name="pwd"/><br /><br />
	<input type="submit" value="Create User" />
</form>
<?php
	include("foot.inc");
?>