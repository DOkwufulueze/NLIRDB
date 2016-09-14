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
	include("class.php");
	if(!isset($_SESSION['user'])){
		header("Location:.?page=home&msg=Invalid Access to the NLI Page!");
	}
	else{
	include("top.inc");
	echo "<u><b>".$_GET['msg']."</b></u><br/><br/>" ;
?>
<span style="float:right" ><a href='.?page=logout'>Logout</a></span>
<span style="float:left" >
	Please click on any of the NLIDB that you require for querying below.<br/><br/>
	<a href='.?page=nli&msg=Welcome <?php echo $_SESSION['user']; ?> to the NLIDB for the Department of Computer Science, LASU! '>Computer Science Department</a><br/>
	<a href='.?page=nlicntr&msg=Welcome <?php echo $_SESSION['user']; ?> to the NLIDB for the Countries of the world! '>Countries</a><br/>
</span>

<?php
	include("foot.inc");
	}
?>