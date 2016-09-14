<?php
	/*$uname=$_SESSION['user'];
	if($uname==""){
		header("Location:.?&msg=Invalid Access to Protected Page");
	}
	if($_POST){
		include("class.php");
		nlirdb::db();
		nlirdb::nliprocessor();
	}*/
	include("top.inc");
	echo "<u><b>".$_GET['msg']."</b></u><br/><br/>" ;
	include("class.php");
	//nlirdb::db();
?>
Place your query in the text-box provided below<br /><br />
<form action="nli.php" method="post" name="nli">
<input type="text" name="nl" size="50" value="Enter Your Natural Language Query " onFocus="clears()" />
<br/>
<input type="submit" value="Submit" />
</form>
<?php
	if($_POST){
	nlirdb::db();
	$nl = htmlentities($_POST['nl']);
			$howmany="*";
			$token = strtok($nl, " ?\t\n");
			$holder=array(); $holder2=array(); $holders=array(); $validn=array(); $validv=array(); $qq=array(); $ndescr=array(); $nvalid=array(); $nfname=array(); $nlname=array(); $ndata=array();
			$i=0; $k=0; $m=0; 
			while ($token !== false) {
				$holder2[$i]=$token;
				$token = strtok(" ?\t\n");
				$i++;
			}
			$holder = nlirdb::remove_duplicate($holder2, $holder);//Remove duplicates from the user's query
			for($mm=0;$mm<count($holder);$mm++){
				$word = $holder[$mm];
				if($word=="students"||$word=="Students"){
					$word="student";	
				}
				if($word=="names"||$word=="name"){
					$word="name";	
				}
				if($word=="lecturers"||$word=="Lecturers"){
					$word="lecturer";
				}
				$q1 = mysql_query("SELECT * FROM test WHERE WORDS ='$word' AND PS='Noun'") or die(mysql_error());
				$num1=mysql_num_rows($q1);
				if($num1!=0){
					while($rows=mysql_fetch_array($q1)){
						$descr[$k]=$rows['DESCRIPTION'];
					}
					$validn[$k]=$word;
					$k++;
				}
				$q2 = mysql_query("SELECT * FROM test WHERE WORDS ='$word' AND PS='Verb'") or die(mysql_error());
				$num2=mysql_num_rows($q2);
				if($num2!=0){
					$validv[$m]=$word;
					$m++;
				}
			}
			$count=0; $counts=0;
			for($o=0;$o<count($validn);$o++){//To get only words that have values in the csc db
				$value=$validn[$o]; $field=strtoupper($descr[$o]);
				$qq[$o] = mysql_query("SELECT ".$howmany." FROM csc WHERE ".$field."='$value'") or die(mysql_error());
				$nu = mysql_num_rows($qq[$o]);
				if($nu!=0){
					$ndescr[$count]=$field; //Values of $ndescr are already in upper-case
					$nvalid[$count]=$value; 
					$count++;
				}
			}
			$count2=0;
			$numDescr=count($ndescr);
			$values=$nvalid[0]; $fields=strtoupper($ndescr[0]);
			if(!empty($nvalid)&& !(nlirdb::isDuplicate($ndescr))){
			echo "SELECT * FROM csc WHERE ".$fields."='".$values."'"." ".nlirdb::nexts($ndescr, $nvalid, $numDescr)."<br/>";
			$finalQ=mysql_query("SELECT * FROM csc WHERE ".$fields."='".$values."'"." ".nlirdb::nexts($ndescr, $nvalid, $numDescr)) or die(mysql_error());
			$nn=mysql_num_rows($finalQ);
			if($nn==0){
				echo"<script>document.location.href='.?page=nli&msg=Sorry, Your Answer to your query could not be gotten! $nvalid[0] $ndescr[0] $nvalid[1] $ndescr[1]'</script>";
			}
			else{
				while($rrr=mysql_fetch_array($finalQ)){
					if((nlirdb::isFind("who", $holder))||(nlirdb::isFind("name", $holder))||(nlirdb::isFind("names", $holder))||(nlirdb::isFind("people", $holder))||(nlirdb::isFind("person", $holder))||(nlirdb::isFind("persons", $holder))||(nlirdb::isFind("those", $holder))||(nlirdb::isFind("lecturer", $holder))||(nlirdb::isFind("student", $holder))||(nlirdb::isFind("lecturers", $holder))||(nlirdb::isFind("students", $holder))){
						$nfname[$count2]=$rrr['FIRSTNAME'];
						$nlname[$count2]=$rrr['LASTNAME'];
						$ndesig[$count2]=$rrr['DESIGNATION'];
						if(nlirdb::isFind("FIRSTNAME", $ndescr)){
							$ndescr=nlirdb::delete("FIRSTNAME", $ndescr);
						}
						if(nlirdb::isFind("LASTNAME", $ndescr)){
							$ndescr=nlirdb::delete("LASTNAME", $ndescr);
						}
					}
					$ndata[$count2]=$rrr[$ndescr[$count2]];
					if(!empty($nfname)||!empty($nlname)){
						echo "FIRSTNAME : ".$nfname[$count2]."<br/>";
						echo "LASTNAME : ".$nlname[$count2]."<br/>";
						
					}
					if(!empty($ndescr)){
						echo $ndescr[$count2]." : ".$ndata[$count2]."<br/>";
						//if(!empty($ndesig)){
							echo "DESIGNATION : ".$ndesig[$count2]."<br/>";
						//}
						if($rrr['MATRIC_NUMBER']!=""){
							$nmatric[$count2]=$rrr['MATRIC_NUMBER'];
							echo "MATRIC_NUMBER : ".$nmatric[$count2]."<br/>";
						}
						
					}
					$count2++;
				}
				//header("Location:.?page=result&fname=$nfname&lname=$nlname&descr=$ndescr[]&data=$ndata[]&msg=The result of your query is presented below:");
			}
			}
			elseif(!empty($nvalid)&&(nlirdb::isDuplicate($ndescr))){
				
			}
			else{
				echo"<script>document.location.href='.?page=nli&msg=You submitted an empty query!'</script>";
			}
	}
?>
<?php
	include("foot.inc");
?>
