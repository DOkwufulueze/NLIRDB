<?php
	include("top.inc");
	echo "<u><b>".$_GET['msg']."</b></u><br/><br/>" ;
	include("class.php");
?>
Place your query in the text-box provided below<br /><br />
<form action="nli.php" method="post" name="nli">
<input type="text" name="nl" size="50" value="Enter Your Natural Language Query " onFocus="clears()" />
<br/>
<input type="submit" value="Submit" />
</form>
<div id="headi">

</div>
<div id="message">

</div>
<?php
	if($_POST){
	nlirdb::db();
	$nl = htmlentities($_POST['nl']);
			$howmany="*";
			$token = strtok($nl, " ,.:;?\t\n");
			$holder=array(); $holder2=array(); $holders=array(); $validn=array(); $validv=array(); $qq=array(); $ndescr=array(); $nvalid=array(); $nfname=array(); $nlname=array(); $ndata=array(); $genValid=array(); $speValid=array();
			$i=0; $k=0; $m=0; 
			while ($token !== false) {
				$holder2[$i]=$token;
				$token = strtok(" ,.:;?\t\n");
				$i++;
			}
			$holder = nlirdb::remove_duplicate($holder2, $holder);//Remove duplicates from the user's query
			$noDupText="";$r=0; $s=0;$t=0;$u=0;
			while($r<count($holder)-1){
				$noDupText.=$holder[$r]." ";
				$r++;
			}
			$noDupText.=$holder[$r];
			for($mm=0;$mm<count($holder);$mm++){
				$word = ucwords($holder[$mm]);
				if($word=="students"||$word=="Students"){
					$word="Student";	
				}
				if($word=="names"||$word=="name"){
					$word="Name";	
				}
				if($word=="lecturers"||$word=="Lecturers"){
					$word="Lecturer";
				}
				$q1 = mysql_query("SELECT * FROM test WHERE WORDS ='$word' AND PS='Noun' ") or die(mysql_error());
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
					//echo "Specific".$speValid[$k]."<br/>"."General".$genValid[$k]."<br/>";
					$validn[$k]=$word;//echo"All".$validn[$k];
					$k++; 
				}
			}
			/*nlirdb::delete(' ',$genDescr);	//	I was trying to delete the empty string from my arrays
			nlirdb::delete(' ',$genValid);
			nlirdb::delete(' ',$speDescr);
			nlirdb::delete(' ',$speValid);
			echo count($genDescr)."<br/>"; echo count($speDescr)."<br/>";
			for($i=0;$i<count($genDescr);$i++){
				echo $genDescr[$i]." ".$genValid[$i]."<br/>";
			}
			for($i=0;$i<count($speDescr);$i++){
				if($speDescr[$i]==""){
				echo $speDescr[$i]." ".$speValid[$i]."<br/>";
				}
			}*/
			$intermediate="";$r2=0;
			while($r2<count($validn)-1){
				$intermediate.=$validn[$r2]." ";
				$r2++;
			}
			$intermediate.=$validn[$r2];
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
					//echo"<br/><br/>" ;
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
				$count=0; $counts=0; $mat=array(); $mat2=array();$lec=array(); $lec2=array(); $cc=0; $name=array();
				echo"<table border='1' cellpadding='10' id='alone'><tr>";
				for($i=0;$i<count($genValid);$i++){
					$field=strtoupper($genDescr[$i]); $value=ucwords($genValid[$i]); 
					if($value=="Matric"){
						$qq = mysql_query("SELECT * FROM csc WHERE DESIGNATION='Student'") or die(mysql_error());
					}
					else{
						$qq = mysql_query("SELECT * FROM csc WHERE ".$field."='$value'") or die(mysql_error());
						if($value!="Name"&&$value!="Computer"&&$value!="Science"&&$value!="Department"&&$value!="Sciences"){
							$dsg = "<span style='font-size:20;color:#fe11ff'>".$field." : ".$value."</span><br/><br/>";
						}
					}
					$nu = mysql_num_rows($qq);
					if($nu!=0){
						echo "<td>";
						echo $dsg;
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
							
						}
						echo "</td>";
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
				}
				echo"<script>
						document.getElementById('message').innerHTML='<b>User Query :</b> $noDupText<br/><br/> <b>Intermediate Language Query :</b> $intermediate<br/>';
						document.getElementById('message').style.visibility='visible';
					</script>";
			}
			
			elseif(!empty($genValid)&&!empty($speValid)){
				$count=0; $counts=0; $mat=array(); $mat2=array();$lec=array(); $lec2=array(); $name=array(); $cc=0;
				if(nlirdb::isFind("Matric", $validn)&&nlirdb::isFind("Lecturer", $desigg)){
						$locMat=nlirdb::findIndex("Lecturer", $desigg);
						$warn = $validn[$locMat]; 
						echo"<script type='text/javascript'>
								var headi = document.getElementById('headi');
								headi.style.visibility='visible';
								headi.innerHTML='<b>$warn is not a student and so does not have a matric number!</b><br/>'
							</script>";
					
						echo"<script>
								document.getElementById('message').innerHTML='<b>User Query :</b> $noDupText<br/><br/> <b>Intermediate Language Query :</b> $intermediate<br/>';
								document.getElementById('message').style.visibility='visible';
							</script>";
				}
				else{
					if(!nlirdb::isDuplicate($descr)){
						$d = count($descr);
						$fq=mysql_query("SELECT * FROM csc WHERE $descr[0]='$validn[0]'".nlirdb::nexts($descr, $validn, $d)."") or die(mysql_error());
						$nnn = mysql_num_rows($fq);
						echo"<table border='1' cellpadding='10' id='alone'><tr>";
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
					elseif(nlirdb::isDuplicate($descr)){
						/*for($t=0; $t<count($genDescr);$t++){
							echo $genDescr[$t]." ".$genValid[$t]."<br/>";
							echo $speDescr[$t]." ".$speValid[$t]."<br/>";
						}*/
						$count=0; $counts=0;
						echo"<table border='1' cellpadding='10' id='alone'>";
						for($i=0;$i<count($validn);$i++){
							for($j=0;$j<count($descr); $j++){
								if($genDescr[$i]!=null&&$speDescr[$j]!=null){
									$genfield=strtoupper($genDescr[$i]); strtoupper($genvalue=$genValid[$i]);
									$spefield=strtoupper($speDescr[$j]); strtoupper($spevalue=$speValid[$j]);
									echo $genfield." : ".$genvalue."<br/>".$spefield." : ".$spevalue."<br/>";
									$qq = mysql_query("SELECT * FROM csc WHERE ".$genfield."='$genvalue' AND ".$spefield."='$spevalue'") or die(mysql_error());
									$nu = mysql_num_rows($qq);
									if($nu!=0){
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
