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
	include("top.inc");
	echo "<u><b>".$_GET['msg']."</b></u><br/><br/>" ;
	include("class.php");
?>
The User Interface presented here allows you to ask questions about both Students and Lecturers of The Department of Computer Science.<br /><br />
<form action="nli.php" method="post" name="nli">
<input type="text" name="nl" size="50" value="Enter Your Natural Language Query " onFocus="clears()" />
<br/>
<input type="submit" value="Submit" />
</form>
<div id="headi">

</div>
<div id="message">

</div>
<div id="sql">

</div>
<?php
	if($_POST){
			nlirdb::db();
			$nl1 = stripslashes(htmlentities($_POST['nl'])); //echo $nl1;
			$nl=str_replace("'"," ",$nl1); //echo $nl;
			$howmany="*";
			$token = strtok($nl, " ,.:;?\t\n"); $tokens;
			$holder=array(); $holder2=array(); $holders=array(); $validn=array(); $ndescr=array(); $nvalid=array(); $genValid=array(); $speValid=array(); $upper=array(); $toUse=array(); 
			$i=0; $k=0; $m=0; 
			while ($token !== false) {
				$tokens=$token;
				if($tokens=="students"||$tokens=="Students"||$tokens=="students"){
					$tokens="Student";	
				}
				if($tokens=="names"||$tokens=="name"){
					$tokens="Name";	
				}
				if($tokens=="number"||$tokens=="Number"||$tokens=="numbers"||$tokens=="Numbers"){
					$tokens="Number";	
				}
				if($tokens=="lecturers"||$tokens=="Lecturers"||$tokens=="lecturer"){
					$tokens="Lecturer";
				}
				$toUse[$i]=$tokens;//This array contains standard words found in the user's query
				$holder2[$i]=$token;
				$upper[$i] = ucwords($tokens);
				$token = strtok(" ,.:;?\t\n");
				$i++;
			}
			$holder = nlirdb::remove_duplicate($holder2, $holder);//Remove duplicates from the user's query
			$holders = nlirdb::remove_duplicate($toUse, $holders);//Remove duplicates of standard words from the user's query
			$noDupText="";$r=0; $s=0;$t=0;$u=0;
			while($r<count($holder)-1){
				$noDupText.=$holder[$r]." ";
				$r++;
			}
			$noDupText.=$holder[$r];
			for($mm=0;$mm<count($holder);$mm++){
				$word = ucwords($holders[$mm]);
				$q1 = mysql_query("SELECT * FROM corpus WHERE WORDS ='$word' AND PS='Noun' ") or die(mysql_error());
				$num1=mysql_num_rows($q1);
				if($num1!=0){
					while($rows=mysql_fetch_array($q1)){//This will execute once since each word in the dictionary is unique
						$descr[$k]=$rows['DESCRIPTION'];
						$desigg[$k]=$rows['DESIGNATION'];
						$cat[$k]=$rows['CATEGORY'];
						if($cat[$k]=="General"){//Seperating the general words from the specific words
							$genDescr[$k]=$rows['DESCRIPTION'];
							$genDesigg[$k]=$rows['DESIGNATION'];
							$genValid[$k] = $word;//Array of General Words
						}
						elseif($cat[$k]=="Specific"){
							$speDescr[$k]=$rows['DESCRIPTION'];
							$speDesigg[$k]=$rows['DESIGNATION'];
							$speValid[$k] = $word;//Array of Specific Words
						}
					}
					$validn[$k]=$word;//echo"All".$validn[$k];
					$k++; 
				}
			}
			$intermediate="";$r2=0;
			while($r2<count($validn)-1){
				$intermediate.=$validn[$r2]." ";
				$r2++;
			}
			$intermediate.=$validn[$r2]; $rrrr=0;
			
			/************************************************
			 *There are Four cases that I notice can arise   *
			 *with the arrays of Specific and General Words  *
			 *in the query supplied by the user. These are : *
			 *                                               *
			 *size(genValid)==0; size(speValid)==0           * 
			 *size(genValid)==0; size(speValid)!=0           *                                      
			 *size(genValid)!=0; size(speValid)==0           *
			 *size(genValid)!=0; size(speValid)!=0           *
			 *                                               *
			 *************************************************/
			 
			if(empty($genValid)&&empty($speValid)){
				echo"<script type='text/javascript'>
							var headi = document.getElementById('headi');
							headi.style.visibility='visible';
							headi.innerHTML='<b>Your Query Was not Valid!</b><br/>'
					</script>";
			}
			elseif(empty($genValid)&&!empty($speValid)){
				$count=0; $counts=0; $mat=array(); $mat2=array(); $cc=0;$name=array();
				echo"<table id='alone' border='1px' cellpadding='20'>";
				for($i=0;$i<count($speValid);$i++){
					$field=$speDescr[$i]; $value=$speValid[$i];
					$qq = mysql_query("SELECT * FROM csc WHERE ".$field."='$value'") or die(mysql_error());
					$nu = mysql_num_rows($qq);
					if($nu!=0){
						if(((nlirdb::isFind("Number",$upper)&&nlirdb::isFind("Phone",$upper))||(nlirdb::isFind("Phone",$upper)))&&(!nlirdb::isFind("Matric",$upper))){
							while($rows=mysql_fetch_array($qq)){
								if($rows['MATRIC_NUMBER']!=""){
									$mat[$count] = $rows['MATRIC_NUMBER']; 
								}
								if($rows['LECTURER_NUMBER']!=""){
									$lec[$counts] = $rows['LECTURER_NUMBER'];
								}
								if((!nlirdb::isDuplicate($mat)&&$mat[$count]!="")||(!nlirdb::isDuplicate($lec)&&$lec[$counts]!="")){// This prevents printing an entity more than once
									$name[$cc] = $rows['FIRSTNAME'] ;$cc++;
									echo"<tr><td>";
									echo"NAME : ".$rows['FIRSTNAME']." ".$rows['LASTNAME']."<br/>";
									echo"PHONE NUMBER :".$rows['PHONE']."<br/>";
									if($rows['MATRIC_NUMBER']!=""){
										$count++;
									}
									if($rows['LECTURER_NUMBER']!=""){
										$counts++;
									}
									echo"</td></tr>";
								}
								else{
									$mat=nlirdb::remove_duplicate($mat,$mat2);
									$lec=nlirdb::remove_duplicate($lec,$lec2);
								}
									
							}
						}
						elseif((nlirdb::isFind("Number",$upper)&&nlirdb::isFind("Phone",$upper)&&nlirdb::isFind("Matric",$upper)&&!nlirdb::isFind("Lecturer",$desigg))||(nlirdb::isFind("Phone",$upper)&&nlirdb::isFind("Matric",$upper))&&nlirdb::isFind("Student",$desigg)&&!nlirdb::isFind("Lecturer",$desigg)){
							while($rows=mysql_fetch_array($qq)){
								if($rows['MATRIC_NUMBER']!=""){
									$mat[$count] = $rows['MATRIC_NUMBER']; 
								}
								if($rows['LECTURER_NUMBER']!=""){
									$lec[$counts] = $rows['LECTURER_NUMBER'];
								}
								if((!nlirdb::isDuplicate($mat)&&$mat[$count]!="")||(!nlirdb::isDuplicate($lec)&&$lec[$counts]!="")){// This prevents printing an entity more than once
									$name[$cc] = $rows['FIRSTNAME'] ;$cc++;
									echo"<tr><td>";
									echo"NAME : ".$rows['FIRSTNAME']." ".$rows['LASTNAME']."<br/>";
									echo"MATRIC NUMBER :".$rows['MATRIC_NUMBER']."<br/>";
									echo"PHONE NUMBER :".$rows['PHONE']."<br/>";
									if($rows['MATRIC_NUMBER']!=""){
										$count++;
									}
									if($rows['LECTURER_NUMBER']!=""){
										$counts++;
									}
									echo"</td></tr>";
								}
								else{
									$mat=nlirdb::remove_duplicate($mat,$mat2);
									$lec=nlirdb::remove_duplicate($lec,$lec2);
								}
									
							}
						}
						elseif(nlirdb::isFind("Number",$upper)&&nlirdb::isFind("Matric",$upper)&&!nlirdb::isFind("Lecturer",$desigg)&&!nlirdb::isFind("Phone",$upper)){
							while($rows=mysql_fetch_array($qq)){
								if($rows['MATRIC_NUMBER']!=""){
									$mat[$count] = $rows['MATRIC_NUMBER']; 
								}
								if($rows['LECTURER_NUMBER']!=""){
									$lec[$counts] = $rows['LECTURER_NUMBER'];
								}
								if((!nlirdb::isDuplicate($mat)&&$mat[$count]!="")||(!nlirdb::isDuplicate($lec)&&$lec[$counts]!="")){// This prevents printing an entity more than once
									$name[$cc] = $rows['FIRSTNAME'] ;$cc++;
									echo"<tr><td>";
									echo"NAME : ".$rows['FIRSTNAME']." ".$rows['LASTNAME']."<br/>";
									echo"MATRIC NUMBER :".$rows['MATRIC_NUMBER']."<br/>";
									if($rows['MATRIC_NUMBER']!=""){
										$count++;
									}
									if($rows['LECTURER_NUMBER']!=""){
										$counts++;
									}
									echo"</td></tr>";
								}
								else{
									$mat=nlirdb::remove_duplicate($mat,$mat2);
									$lec=nlirdb::remove_duplicate($lec,$lec2);
								}
									
							}
						}
						elseif(((nlirdb::isFind("Number",$upper)&&nlirdb::isFind("Lecturer",$desigg)))&&(!nlirdb::isFind("Student",$desigg))&&(!nlirdb::isFind("Matric",$upper))&&!nlirdb::isFind("Phone",$upper)){
							while($rows=mysql_fetch_array($qq)){
								if($rows['MATRIC_NUMBER']!=""){
									$mat[$count] = $rows['MATRIC_NUMBER']; 
								}
								if($rows['LECTURER_NUMBER']!=""){
									$lec[$counts] = $rows['LECTURER_NUMBER'];
								}
								if((!nlirdb::isDuplicate($mat)&&$mat[$count]!="")||(!nlirdb::isDuplicate($lec)&&$lec[$counts]!="")){// This prevents printing an entity more than once
									$name[$cc] = $rows['FIRSTNAME'] ;$cc++;
									echo"<tr><td>";
									echo"NAME : ".$rows['FIRSTNAME']." ".$rows['LASTNAME']."<br/>";
									if($rows['MATRIC_NUMBER']!=""){
										$count++;
									}
									if($rows['LECTURER_NUMBER']!=""){
										$counts++;
										echo"LECTURER NUMBER :".$rows['LECTURER_NUMBER']."<br/>";
									}
									echo"</td></tr>";
								}
								else{
									$mat=nlirdb::remove_duplicate($mat,$mat2);
									$lec=nlirdb::remove_duplicate($lec,$lec2);
								}
									
							}
						}
						elseif(nlirdb::isFind("Matric",$upper)&&nlirdb::isFind("Lecturer",$desigg)){
							echo "<script>document.location.href='?page=nli&msg=There is a mismatch between the entity you supplied and the requested number!'</script>";
						}
						else{
							while($roww=mysql_fetch_array($qq)){
								if($roww['MATRIC_NUMBER']!=""){
									$mat[$count] = $roww['MATRIC_NUMBER'];
								}
								if($roww['LECTURER_NUMBER']!=""){
									$lec[$counts] = $roww['LECTURER_NUMBER'];
								}
								if((!nlirdb::isDuplicate($mat)&&$mat[$count]!="")||(!nlirdb::isDuplicate($lec)&&$lec[$counts]!="")){// This prevents printing an entity more than once
									$name[$cc] = $roww['FIRSTNAME'] ;$cc++;
									echo"<tr><td>";
									echo"FIRST NAME :".$roww['FIRSTNAME']."<br/>".
										"LAST NAME  :".$roww['LASTNAME']."<br/>";
										echo"DESIGNATION :".$roww['DESIGNATION']."<br/>";
									if($roww['MATRIC_NUMBER']!=""){
										echo"MATRIC NUMBER :".$roww['MATRIC_NUMBER']."<br/>";
										$count++;
									}
									if($roww['LECTURER_NUMBER']!=""){
										echo"LECTURER NUMBER :".$roww['LECTURER_NUMBER']."<br/>";
										$counts++;
									}
									echo"</td></tr>";
								}
								else{
									$mat=nlirdb::remove_duplicate($mat,$mat2);
									$lec=nlirdb::remove_duplicate($lec,$lec2);
								}	
							}
						}
					}
				}
				echo"</table>";
				if(empty($name)){
					echo"<script type='text/javascript'>
							var headi = document.getElementById('headi');
							headi.style.visibility='visible';
							headi.innerHTML='<b>Your Query Was not Clear Enough!</b><br/>'
						</script>";
				}
				else{
					echo"<script type='text/javascript'>
							var headi = document.getElementById('headi');
							headi.style.visibility='visible';
							headi.innerHTML='<b>The result of your query is presented below :</b><br/>'
						</script>";
				}
				echo"<script>
						document.getElementById('message').innerHTML='<b>User Query :</b> $noDupText<br/><br/> <b>Intermediate Language Query :</b> $intermediate<br/>';
						document.getElementById('message').style.visibility='visible';
					</script>";
			}
			
			elseif(!empty($genValid)&&empty($speValid)){
				$count=0; $counts=0; $mat=array(); $mat2=array();$lec=array(); $lec2=array(); $cc=0; $name=array();$qryy="";
				echo"<table border='1' cellpadding='10' id='alone'><tr>";
				for($i=0;$i<count($genValid);$i++){
					$field=strtoupper($genDescr[$i]); $value=ucwords($genValid[$i]); 
					$qry = "SELECT * FROM csc WHERE ".$field."='$value'";
					$qq = mysql_query($qry) or die(mysql_error());
					if($value!="Name"&&$value!="Computer"&&$value!="Science"&&$value!="Department"&&$value!="Sciences"){
						$dsg = "<span style='font-size:20;color:#fe11ff'>".$field." : ".$value."</span>";
					}
					$nu = mysql_num_rows($qq);
					if($nu!=0){
						//echo $dsg;
						if(((nlirdb::isFind("Number",$upper)&&nlirdb::isFind("Phone",$upper))||(nlirdb::isFind("Phone",$upper)))&&(!nlirdb::isFind("Matric",$upper))){
							while($rows=mysql_fetch_array($qq)){
								echo"<tr><td>";
								echo $dsg."<br/>";
								$name=$rows['FIRSTNAME']." ".$rows['LASTNAME'];
								echo "NAME : ".$name."<br/>";
								echo "PHONE NUMBER : ".$rows['PHONE']."<br/>"; 
								echo"</td></tr>";
							}
						}
						elseif(((nlirdb::isFind("Number",$upper)||nlirdb::isFind("Matric",$upper))&&(nlirdb::isFind("Student",$desigg)))&&(!nlirdb::isFind("Lecturer",$upper)&&!nlirdb::isFind("Phone",$upper))){
							while($rows=mysql_fetch_array($qq)){
								echo"<tr><td>";
								echo $dsg."<br/>";
								$name=$rows['FIRSTNAME']." ".$rows['LASTNAME'];
								echo "NAME : ".$name."<br/>";
								if($rows['MATRIC_NUMBER']!=""){
									echo "MATRIC NUMBER : ".$rows['MATRIC_NUMBER']."<br/>";
								}
								echo"</td></tr>";
							}
						}
						elseif(((nlirdb::isFind("Number",$upper)&&nlirdb::isFind("Matric",$upper))&&(nlirdb::isFind("Student",$desigg)))&&(!nlirdb::isFind("Lecturer",$desigg)&&nlirdb::isFind("Phone",$upper))){
							while($rows=mysql_fetch_array($qq)){
								echo"<tr><td>";
								echo $dsg."<br/>";
								$name=$rows['FIRSTNAME']." ".$rows['LASTNAME'];
								echo "NAME : ".$name."<br/>";
								if($rows['MATRIC_NUMBER']!=""){
									echo "MATRIC NUMBER : ".$rows['MATRIC_NUMBER']."<br/>";
								}
								echo "PHONE NUMBER : ".$rows['PHONE']."<br/>";
								echo"</td></tr>";
							}
						}
						elseif(((nlirdb::isFind("Number",$upper)&&nlirdb::isFind("Lecturer",$upper)))&&(!nlirdb::isFind("Student",$desigg))&&(!nlirdb::isFind("Matric",$upper)&&!nlirdb::isFind("Phone",$upper))){
							while($rows=mysql_fetch_array($qq)){
								echo"<tr><td>";
								echo $dsg."<br/>";
								$name=$rows['FIRSTNAME']." ".$rows['LASTNAME'];
								echo "NAME : ".$name."<br/>";
								if($rows['LECTURER_NUMBER']!=""){
									echo "LECTURER NUMBER : ".$rows['LECTURER_NUMBER']."<br/>";
								}
								echo"</td></tr>";
							}
						}
						elseif((nlirdb::isFind("Number",$upper)&&nlirdb::isFind("Lecturer",$upper))&&(nlirdb::isFind("Matric",$upper))&&(!nlirdb::isFind("Student",$upper))||(nlirdb::isFind("Number",$upper)&&nlirdb::isFind("Student",$upper))&&(nlirdb::isFind("Lecturer",$desigg))&&(!nlirdb::isFind("Matric",$upper))){
							echo "<script>document.location.href='?page=nli&msg=There is a mismatch between the entities you supplied and their requested numbers!'</script>";
						}
						elseif((nlirdb::isFind("Number",$upper)&&nlirdb::isFind("Lecturer",$upper))&&(nlirdb::isFind("Matric",$upper))&&(nlirdb::isFind("Student",$upper))){
							while($rows=mysql_fetch_array($qq)){
								echo"<tr><td>";
								echo $dsg."<br/>";
								$name=$rows['FIRSTNAME']." ".$rows['LASTNAME'];
								echo "NAME : ".$name."<br/>";
								if($rows['MATRIC_NUMBER']!=""){
									echo "MATRIC NUMBER : ".$rows['MATRIC_NUMBER']."<br/>";
								}
								if($rows['LECTURER_NUMBER']!=""){
									echo "LECTURER NUMBER : ".$rows['LECTURER_NUMBER']."<br/>";
								}
								echo"</td></tr>";
							}
						}
						else{
							echo"<td valign='top'>".$dsg."<br/>";
							while($roww=mysql_fetch_array($qq)){
								if($roww['MATRIC_NUMBER']!=""){
									$mat[$count] = $roww['MATRIC_NUMBER'];
								}
								if($roww['LECTURER_NUMBER']!=""){
									$lec[$counts] = $roww['LECTURER_NUMBER'];
								}
								if((!nlirdb::isDuplicate($mat)&&$mat[$count]!="")||(!nlirdb::isDuplicate($lec)&&$lec[$counts]!="")){
									$name[$cc] = $roww['FIRSTNAME'] ;$cc++;
									echo"FIRST NAME :".$roww['FIRSTNAME']."<br/>".
										"LAST NAME  :".$roww['LASTNAME']."<br/>";
									echo"DESIGNATION :".$roww['DESIGNATION']."<br/>";
									if($roww['MATRIC_NUMBER']!=""){
										echo"MATRIC NUMBER :".$roww['MATRIC_NUMBER']."<br/><br/>";
										$count++;
									}
									if($roww['LECTURER_NUMBER']!=""){
										echo"LECTURER NUMBER :".$roww['LECTURER_NUMBER']."<br/><br/>";
										$counts++;
									}
									
								}
								else{
									$mat=nlirdb::remove_duplicate($mat,$mat2);
									$lec=nlirdb::remove_duplicate($lec,$lec2);
								}
							}echo"</td>";	
						}
					}
				}
				echo"</tr></table>";
				if(empty($name)){
					echo"<script type='text/javascript'>
							var headi = document.getElementById('headi');
							headi.style.visibility='visible';
							headi.innerHTML='<b>Your Query Was not Clear Enough!</b><br/>'
						</script>";
				}
				else{
					echo"<script type='text/javascript'>
							var headi = document.getElementById('headi');
							headi.style.visibility='visible';
							headi.innerHTML='<b>The result of your query is presented below :</b><br/>'
						</script>";
					echo"<script>
						document.getElementById('message').innerHTML='<b>User Query :</b> $noDupText<br/><br/> <b>Intermediate Language Query :</b> $intermediate<br/>';
						document.getElementById('message').style.visibility='visible';
					</script>";
					
				}
			}
			
			elseif(!empty($genValid)&&!empty($speValid)){
				$count=0; $counts=0; $mat=array(); $mat2=array();$lec=array(); $lec2=array(); $name=array(); $cc=0;
				if((nlirdb::isFind("Matric", $upper)&&nlirdb::isFind("Lecturer", $desigg))||(nlirdb::isFind("Number", $upper)&&nlirdb::isFind("Lecturer", $desigg)&&nlirdb::isFind("Student",$desigg))){
						//$locMat=nlirdb::findIndex("Lecturer", $desigg);
						//$warn = $validn[$locMat]; 
						echo"<script type='text/javascript'>
								var headi = document.getElementById('headi');
								headi.style.visibility='visible';
								headi.innerHTML='<b>The Number you requested does not match the status of the name you supplied!</b><br/>'
							</script>";
					
						echo"<script>
								document.getElementById('message').innerHTML='<b>User Query :</b> $noDupText<br/><br/> <b>Intermediate Language Query :</b> $intermediate<br/>';
								document.getElementById('message').style.visibility='visible';
							</script>";
				}
				
				else{
					if(!nlirdb::isDuplicate($descr)){
						$d = count($descr); $ii=0;
						$fq=mysql_query("SELECT * FROM csc WHERE $descr[0]='$validn[0]'".nlirdb::nexts($descr, $validn, $d)."") or die(mysql_error());
						$nnn = mysql_num_rows($fq);
						echo"<table border='1' cellpadding='10' id='alone'><tr>";
						if($nnn!=0){
						if((nlirdb::isFind("Number",$upper)&&nlirdb::isFind("Matric",$upper)&&!nlirdb::isFind("Phone",$upper)&&nlirdb::isFind("Student",$desigg)&&!nlirdb::isFind("Lecturer",$desigg))||(nlirdb::isFind("Number",$upper)&&nlirdb::isFind("Lecturer",$upper)&&!nlirdb::isFind("Phone",$upper)&&!nlirdb::isFind("Student",$desigg)&&nlirdb::isFind("Lecturer",$desigg))){
							while($rows=mysql_fetch_array($fq)){
								echo"<td>";
								$name=$rows['FIRSTNAME']." ".$rows['LASTNAME'];
								echo "NAME : ".$name."<br/>";
								if($rows['MATRIC_NUMBER']!=""){
									echo "MATRIC NUMBER : ".$rows['MATRIC_NUMBER']."<br/>";
								}
								if($rows['LECTURER_NUMBER']!=""){
									echo "LECTURER NUMBER : ".$rows['LECTURER_NUMBER']."<br/>";
								}
								echo"</td>";
							}
						}
						elseif((nlirdb::isFind("Number",$upper)&&nlirdb::isFind("Phone",$upper))||(nlirdb::isFind("Phone",$upper))){
							while($rows=mysql_fetch_array($fq)){
								echo"<td>";
								$name=$rows['FIRSTNAME']." ".$rows['LASTNAME'];
								echo "NAME : ".$name."<br/>";
								echo "PHONE NUMBER : ".$rows['PHONE']."<br/>"; 
								echo"</td>";
							}
						}
						else{
							while($rows=mysql_fetch_array($fq)){
								echo"<td>";
								$name=$rows['FIRSTNAME'];
								echo "FIRSTNAME : ".$rows['FIRSTNAME']."<br/>";
								echo "LASTNAME : ".$rows['LASTNAME']."<br/>";
								echo "DESIGNATION : ".$rows['DESIGNATION']."<br/>";
								if($rows['MATRIC_NUMBER']!=""){
									echo "MATRIC NUMBER : ".$rows['MATRIC_NUMBER']."<br/>";
								}
								if($rows['LECTURER_NUMBER']!=""){
									echo "LECTURER NUMBER : ".$rows['LECTURER_NUMBER']."<br/>";
								}
								echo"</td>";
							}
						}
						echo"</tr></table>";
						echo"<script type='text/javascript'>
								var headi = document.getElementById('headi');
								headi.style.visibility='visible';
								headi.innerHTML='<b>The Result of Your query is Presented Below!</b><br/>'
							</script>";
					
						echo"<script>
								document.getElementById('message').innerHTML='<b>User Query :</b> $noDupText<br/><br/> <b>Intermediate Language Query :</b> $intermediate<br/>';
								document.getElementById('message').style.visibility='visible';
							</script>";
						}
						else{
							echo"<script type='text/javascript'>
								var headi = document.getElementById('headi');
								headi.style.visibility='visible';
								headi.innerHTML='<b>Please, Rephrase your query. Perharps remove general terms like Student, Lecturer etc.</b><br/>'
							</script>";
					
						echo"<script>
								document.getElementById('message').innerHTML='<b>User Query :</b> $noDupText<br/><br/> <b>Intermediate Language Query :</b> $intermediate<br/>';
								document.getElementById('message').style.visibility='visible';
							</script>";
						}
					}
					elseif(nlirdb::isDuplicate($descr)){
						$count=0; $counts=0;
						echo"<table border='1' cellpadding='10' id='alone'>";
						for($i=0;$i<count($validn);$i++){
							for($j=0;$j<count($descr); $j++){
								if($genDescr[$i]!=null&&$speDescr[$j]!=null){
									$genfield=strtoupper($genDescr[$i]); strtoupper($genvalue=$genValid[$i]);
									$spefield=strtoupper($speDescr[$j]); strtoupper($spevalue=$speValid[$j]);
									//echo $genfield." : ".$genvalue."<br/>".$spefield." : ".$spevalue."<br/>";
									$qq = mysql_query("SELECT * FROM csc WHERE ".$genfield."='$genvalue' AND ".$spefield."='$spevalue'") or die(mysql_error());
									$nu = mysql_num_rows($qq);
									if($nu!=0){
										if(((nlirdb::isFind("Number",$upper)&&nlirdb::isFind("Phone",$upper))||(nlirdb::isFind("Phone",$upper)))&&!nlirdb::isFind("Matric",$upper)){
											while($roww=mysql_fetch_array($qq)){
												if($roww['MATRIC_NUMBER']!=""){
													$mat[$count] = $roww['MATRIC_NUMBER'];
												}
												if($roww['LECTURER_NUMBER']!=""){
													$lec[$counts] = $roww['LECTURER_NUMBER'];
												}
												if((!nlirdb::isDuplicate($mat)&&$mat[$count]!="")||(!nlirdb::isDuplicate($lec)&&$lec[$counts]!="")){	
													$name[$cc] = $roww['FIRSTNAME'] ;$cc++;
													echo"<tr><td>";
													echo"NAME : ".$roww['FIRSTNAME']." ".$roww['LASTNAME']."<br/>";
													echo"PHONE NUMBER :".$roww['PHONE']."<br/>";
													if($roww['MATRIC_NUMBER']!=""){
														$count++;
													}
													if($roww['LECTURER_NUMBER']!=""){
														$counts++;
													}
													echo"</td></tr>";
												}
												else{
													$mat=nlirdb::remove_duplicate($mat,$mat2);
													$lec=nlirdb::remove_duplicate($lec,$lec2);
												}	
											}
										}
										elseif(((nlirdb::isFind("Number",$upper)&&nlirdb::isFind("Matric",$upper)&&nlirdb::isFind("Student",$desigg))||(nlirdb::isFind("Matric",$upper)&&nlirdb::isFind("Student",$desigg))||(nlirdb::isFind("Number",$upper)&&nlirdb::isFind("Lecturer",$desigg)))&&!nlirdb::isFind("Phone",$upper)){
											while($roww=mysql_fetch_array($qq)){
												if($roww['MATRIC_NUMBER']!=""){
													$mat[$count] = $roww['MATRIC_NUMBER'];
												}
												if($roww['LECTURER_NUMBER']!=""){
													$lec[$counts] = $roww['LECTURER_NUMBER'];
												}
												if((!nlirdb::isDuplicate($mat)&&$mat[$count]!="")||(!nlirdb::isDuplicate($lec)&&$lec[$counts]!="")){	
													$name[$cc] = $roww['FIRSTNAME'] ;$cc++;
													echo"<tr><td>";
													echo"NAME : ".$roww['FIRSTNAME']." ".$roww['LASTNAME']."<br/>";
													if($roww['MATRIC_NUMBER']!=""){
														echo"MATRIC NUMBER :".$roww['MATRIC_NUMBER']."<br/>";
														$count++;
													}
													if($roww['LECTURER_NUMBER']!=""){
														echo"LECTURER NUMBER :".$roww['LECTURER_NUMBER']."<br/>";
														$counts++;
													}
													echo"</td></tr>";
												}
												else{
													$mat=nlirdb::remove_duplicate($mat,$mat2);
													$lec=nlirdb::remove_duplicate($lec,$lec2);
												}	
											}
										}
										elseif(nlirdb::isFind("Number",$upper)&&nlirdb::isFind("Matric",$upper)&&nlirdb::isFind("Student",$desigg)&&nlirdb::isFind("Phone",$upper)&&!nlirdb::isFind("Lecturer",$desigg)){
											while($roww=mysql_fetch_array($qq)){
												if($roww['MATRIC_NUMBER']!=""){
													$mat[$count] = $roww['MATRIC_NUMBER'];
												}
												if($roww['LECTURER_NUMBER']!=""){
													$lec[$counts] = $roww['LECTURER_NUMBER'];
												}
												if((!nlirdb::isDuplicate($mat)&&$mat[$count]!="")||(!nlirdb::isDuplicate($lec)&&$lec[$counts]!="")){	
													$name[$cc] = $roww['FIRSTNAME'] ;$cc++;
													echo"<tr><td>";
													echo"NAME : ".$roww['FIRSTNAME']." ".$roww['LASTNAME']."<br/>";
													if($roww['MATRIC_NUMBER']!=""){
														echo"MATRIC NUMBER :".$roww['MATRIC_NUMBER']."<br/>";
														$count++;
													}
													echo"PHONE NUMBER :".$roww['PHONE']."<br/>";
													if($roww['LECTURER_NUMBER']!=""){
														$counts++;
													}
													echo"</td></tr>";
												}
												else{
													$mat=nlirdb::remove_duplicate($mat,$mat2);
													$lec=nlirdb::remove_duplicate($lec,$lec2);
												}	
											}
										}
										else{
											while($roww=mysql_fetch_array($qq)){
												if($roww['MATRIC_NUMBER']!=""){
													$mat[$count] = $roww['MATRIC_NUMBER'];
												}
												if($roww['LECTURER_NUMBER']!=""){
													$lec[$counts] = $roww['LECTURER_NUMBER'];
												}
												if((!nlirdb::isDuplicate($mat)&&$mat[$count]!="")||(!nlirdb::isDuplicate($lec)&&$lec[$counts]!="")){	
													$name[$cc] = $roww['FIRSTNAME'] ;$cc++;
													echo"<tr><td>";
													echo"FIRST NAME :".$roww['FIRSTNAME']."<br/>".
														"LAST NAME  :".$roww['LASTNAME']."<br/>";
														echo"DESIGNATION :".$roww['DESIGNATION']."<br/>";
													if($roww['MATRIC_NUMBER']!=""){
														echo"MATRIC NUMBER :".$roww['MATRIC_NUMBER']."<br/>";
														$count++;
													}
													if($roww['LECTURER_NUMBER']!=""){
														echo"LECTURER NUMBER :".$roww['LECTURER_NUMBER']."<br/>";
														$counts++;
													}
													echo"</td></tr>";
												}
												else{
													$mat=nlirdb::remove_duplicate($mat,$mat2);
													$lec=nlirdb::remove_duplicate($lec,$lec2);
												}	
											}
										}
									}
								}//End of the if for empty array elements
							}//End of Inner For loop
						}//End of the for loop
						echo"</table>";
						echo"<script type='text/javascript'>
								var headi = document.getElementById('headi');
								headi.style.visibility='visible';
								headi.innerHTML='<b>The Result of Your query is Presented Below!</b><br/>'
							</script>";
					
						echo"<script>
								document.getElementById('message').innerHTML='<b>User Query :</b> $noDupText<br/><br/> <b>Intermediate Language Query :</b> $intermediate<br/>';
								document.getElementById('message').style.visibility='visible';
							</script>";
					}
				}
			}
	}
?>
<?php
	include("foot.inc");
?>
