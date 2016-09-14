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
		nlirdb::nliprocessor();
	}
	include("top.inc");
	echo "<u><b>".$_GET['msg']."</b></u><br/><br/>" ;
?>
<?php
	$i=0;	
	$fname=$_GET['fname'];
	$lname=$_GET['lname'];
	$descr=$_GET['descr'];
	$data=$_GET['data'];
	/*if(!empty($fname)){
		while($i<count($fname)){
			echo "FIRSTNAME : ".$fname[2]."<br/>";
			echo "LASTNAME : ".$lname[1]."<br/>";
			echo $descr[$i]." : ".$data[$i]."<br/>";
			$i++;
		}
	}
	else{*/
		while($i<count($fname)){
			echo $descr[$i]." : ".$data[$i]."<br/>";
			$i++;
		}
	//}
?>
<?php
	include("foot.inc");
?>
