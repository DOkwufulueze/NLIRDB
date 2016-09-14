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
	//else{
	include("top.inc");
	echo "<u><b>".$_GET['msg']."</b></u><br/><br/>" ;
?>
<span style="float:right" ><a href='.?page=logout'>Logout</a></span>
<span style="float:left" >
The Natural Language Interface presented here allows you to ask questions about both Students and Lecturers of The Department of Computer Science, Lagos State University, Ojo Lagos, Nigeria using the natural language : English.<br /><br />
<form action="nli.php" method="post" name="nli">
<input type="text" name="nl" size="70" value="Enter Your Natural Language Query " onFocus="clears()" />
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
			$nl1 = stripslashes(htmlentities($_POST['nl'])); //
			$nl=str_replace("'"," ",$nl1); //
			$howmany="*";
			$token = strtok($nl, " ,.:;?\t\n"); $tokens;//Tokenization of input string
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
				$norm[$i] = $token ;
				$upper[$i] = ucwords($tokens);//This array contains the words with the first letter of each capitalised
				$token = strtok(" ,.:;?\t\n");//Further tokenization of input string
				$i++;
			}
			$holder = nlirdb::removeDuplicate($holder2, $holder);//Remove duplicates from the user's query
			$holders = nlirdb::removeDuplicate($toUse, $holders);//Remove duplicates of standard words from the user's query
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
				if($num1!=0){//To check if each token is in the corpus
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
			$poss=0;
			for($req=0;$req<count($holder);$req++){
				$word2=ucwords($holders[$req]);
				$qq1=mysql_query("SELECT * FROM requests where TERM='$word2'") or die(mysql_error());
				$num2 = mysql_num_rows($qq1);
				if($num2!=0){//To check for the occurence of Non-Entity-Referencing words(NER)
					while($roow=mysql_fetch_array($qq1)){
						$term[$poss]=$roow['TERM'];
						$request_word[$poss]=$roow['REQUESTABLE'];
						$poss++;
					}
				}
			}
			$rr2=0;
			/*while($rr2<count($request_word)){
				echo $request_word[$rr2]."<br/>";
				$rr2++;
			}*/
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
				echo"<table id='alone' border='0px' cellpadding='20'>";
				for($i=0;$i<count($speValid);$i++){
					$field=$speDescr[$i]; $value=$speValid[$i]; 
						if(!empty($request_word)){
							echo"<tr><td>";
							$reqq=nlirdb::concatWithSymbol($request_word, ","); 
							$qq = mysql_query("SELECT FIRSTNAME, LASTNAME, MATRIC_NUMBER, LECTURER_NUMBER, ".$reqq." FROM csc WHERE ".$field."='$value'") or die(mysql_error());
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
										echo"NAME : ".$roww['FIRSTNAME']." ".$roww['LASTNAME']."<br/>";
										if(count($request_word)>1){
											$here=0;
											while($here<count($request_word)){
												if($roww[$request_word[$here]]!=""){
													echo strtoupper($term[$here])." : ".$roww[$request_word[$here]]."<br/>";
												}
												else{
													echo "<span style='color:#ff0000'>A Lecturer has no matric number<br/></span>";
												}
												$here++;
											}
										}
										else{
											$here=0;
											if($roww[$request_word[$here]]!=""){
												echo strtoupper($term[$here])." : ".$roww[$request_word[$here]]."<br/>";
											}
											else{
												echo "<span style='color:#ff0000'>A Lecturer has no matric number</span>";
											}
										}
										if($roww['MATRIC_NUMBER']!=""){
											$count++;
										}
										if($roww['LECTURER_NUMBER']!=""){
											$counts++;
										}
									
									}
									else{
										$mat=nlirdb::removeDuplicate($mat,$mat2);
										$lec=nlirdb::removeDuplicate($lec,$lec2);
									}
								}		
							}
						echo "</td></tr>";
						}
						else{
							echo"<tr><td>";
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
										$mat=nlirdb::removeDuplicate($mat,$mat2);
										$lec=nlirdb::removeDuplicate($lec,$lec2);
									}
								}		
							}
						echo "</td></tr>";
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
						document.getElementById('message').innerHTML='<b>User Query :</b> $noDupText<br/><br/> <b>Relevant Words :</b> $intermediate<br/>';
						document.getElementById('message').style.visibility='visible';
					</script>";
			}
			
			elseif(!empty($genValid)&&empty($speValid)){
				$count=0; $counts=0; $mat=array(); $mat2=array();$lec=array(); $lec2=array(); $cc=0; $name=array();$qryy="";
				echo"<table border='1' cellpadding='10' id='alone'><tr>";
				for($i=0;$i<count($genValid);$i++){
					$field=strtoupper($genDescr[$i]); $value=ucwords($genValid[$i]); 
						$dsg = "<span style='font-size:20;color:#fe11ff'>".$field." : ".$value."<br/></span>";
						if(!empty($request_word)){
							echo"<td>"; //echo "HERE";
							$reqq=nlirdb::concatWithSymbol($request_word, ","); 
							$qq = mysql_query("SELECT FIRSTNAME, LASTNAME, MATRIC_NUMBER, LECTURER_NUMBER, ".$reqq." FROM csc WHERE ".$field."='$value'") or die(mysql_error());
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
										echo"NAME : ".$roww['FIRSTNAME']." ".$roww['LASTNAME']."<br/>";
										if(count($request_word)>1){
											$here=0;
											while($here<count($request_word)){
												if($roww[$request_word[$here]]!=""){
													echo strtoupper($term[$here])." : ".$roww[$request_word[$here]]."<br/>";
												}
												else{
													echo "<span style='color:#ff0000'>A Lecturer has no matric number<br/></span>";
												}
												$here++;
											}
										}
										else{
											$here=0;
											echo strtoupper($term[$here])." : ".$roww[$request_word[$here]]."<br/>";
										}
										echo"<br/>";
										if($roww['MATRIC_NUMBER']!=""){
											$count++;
										}
										if($roww['LECTURER_NUMBER']!=""){
											$counts++;
										}
									
									}
									else{
										$mat=nlirdb::removeDuplicate($mat,$mat2);
										$lec=nlirdb::removeDuplicate($lec,$lec2);
									}
								}		
							}
							echo "</td>";
						}
						else{
							$qq = mysql_query("SELECT * FROM csc WHERE ".$field."='$value'") or die(mysql_error());
							$nu = mysql_num_rows($qq);
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
									$mat=nlirdb::removeDuplicate($mat,$mat2);
									$lec=nlirdb::removeDuplicate($lec,$lec2);
								}
							}
							echo"</td>";	
						}
					}//End of for loop
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
						document.getElementById('message').innerHTML='<b>User Query :</b> $noDupText<br/><br/> <b>Relevant Words :</b> $intermediate<br/>';
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
								document.getElementById('message').innerHTML='<b>User Query :</b> $noDupText<br/><br/> <b>Relevant Words :</b> $intermediate<br/>';
								document.getElementById('message').style.visibility='visible';
							</script>";
				}
				
				else{
					if(!nlirdb::isDuplicate($descr)){
						$d = count($descr); $ii=0;
						echo"<table border='1' cellpadding='10' id='alone'><tr>";
						if(!empty($request_word)){
							echo"<td>"; //echo "HERE";
							$reqq=nlirdb::concatWithSymbol($request_word, ","); 
							for($i=0;$i<count($descr); $i++){
							$qq = mysql_query("SELECT FIRSTNAME, LASTNAME, MATRIC_NUMBER, LECTURER_NUMBER, ".$reqq." FROM csc WHERE ".$descr[$i]."='".$validn[$i]."'") or die(mysql_error());
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
										if(nlirdb::isFind($roww['FIRSTNAME'],$upper)||nlirdb::isFind($roww['LASTNAME'],$upper)||nlirdb::isFind($roww['MATRIC_NUMBER'],$upper)){;
											$name[$cc] = $roww['FIRSTNAME'] ;$cc++;
											echo"NAME : ".$roww['FIRSTNAME']." ".$roww['LASTNAME']."<br/>";
											if(count($request_word)>1){
												$here=0;
												while($here<count($request_word)){
													if($roww[$request_word[$here]]!=""){
														echo strtoupper($term[$here])." : ".$roww[$request_word[$here]]."<br/>";
													}
													else{
														echo "<span style='color:#ff0000'>A Lecturer has no matric number<br/></span>";
													}
													$here++;
												}
											}
											else{
												$here=0;
												echo strtoupper($term[$here])." : ".$roww[$request_word[$here]]."<br/>";
											}
											echo"<br/>";
											if($roww['MATRIC_NUMBER']!=""){
												$count++;
											}
											if($roww['LECTURER_NUMBER']!=""){
												$counts++;
											}
										}
									}
									else{
										$mat=nlirdb::removeDuplicate($mat,$mat2);
										$lec=nlirdb::removeDuplicate($lec,$lec2);
									}
								}//End of while loop for db		
							}//echo "HERE";//End of if
							}
							echo "</td>";
						}
						else{
							//$qq = mysql_query("SELECT * FROM csc WHERE $descr[0]='$validn[0]'".nlirdb::nexts($descr, $validn, $d)."") or die(mysql_error());
							echo"<td valign='top'>".$dsg."<br/>";
							for($i=0;$i<count($descr);$i++){
								$qq = mysql_query("SELECT * FROM csc WHERE ".$descr[$i]."='".$validn[$i]."'") or die(mysql_error());
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
											if(nlirdb::isFind($roww['FIRSTNAME'],$upper)||nlirdb::isFind($roww['LASTNAME'],$upper)||nlirdb::isFind($roww['MATRIC_NUMBER'],$upper)||nlirdb::isFind($roww['PHONE'],$upper)){
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
										}
										else{
											$mat=nlirdb::removeDuplicate($mat,$mat2);
											$lec=nlirdb::removeDuplicate($lec,$lec2);
										}
									}//End of while loop
									
								}//End of if 
							}//End of for loop
							echo"</td>";
						}
						echo"</tr></table>";
						if(!empty($name)){
							echo"<script type='text/javascript'>
								var headi = document.getElementById('headi');
								headi.style.visibility='visible';
								headi.innerHTML='<b>The Result of Your query is Presented Below!</b><br/>'
							</script>";
					
							echo"<script>
								document.getElementById('message').innerHTML='<b>User Query :</b> $noDupText<br/><br/> <b>Relevant Words :</b> $intermediate<br/>';
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
								document.getElementById('message').innerHTML='<b>User Query :</b> $noDupText<br/><br/> <b>Relevant Words :</b> $intermediate<br/>';
								document.getElementById('message').style.visibility='visible';
							</script>";
						}
					}// End of the !isDuplicate if
					elseif(nlirdb::isDuplicate($descr)){
						$count=0; $counts=0;
						echo"<table border='1' cellpadding='10' id='alone'>";
						for($i=0;$i<count($validn);$i++){
							for($j=0;$j<count($descr); $j++){
								if($genDescr[$i]!=null&&$speDescr[$j]!=null){
									if(!empty($request_word)){
										//echo"<td>"; //echo "HERE";
										$reqq=nlirdb::concatWithSymbol($request_word, ","); 
										$genfield=strtoupper($genDescr[$i]); strtoupper($genvalue=$genValid[$i]);
										$spefield=strtoupper($speDescr[$j]); strtoupper($spevalue=$speValid[$j]);
										//echo $genfield." : ".$genvalue."<br/>".$spefield." : ".$spevalue."<br/>";
										$qq = mysql_query("SELECT FIRSTNAME, LASTNAME, MATRIC_NUMBER, LECTURER_NUMBER, ".$reqq." FROM csc WHERE ".$genfield."='$genvalue' AND ".$spefield."='$spevalue'") or die(mysql_error());
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
													echo"NAME : ".$roww['FIRSTNAME']." ".$roww['LASTNAME']."<br/>";
													if(count($request_word)>1){
														$here=0;
														while($here<count($request_word)){
															if($roww[$request_word[$here]]!=""){
																echo strtoupper($term[$here])." : ".$roww[$request_word[$here]]."<br/>";
															}
															else{
																echo "<span style='color:#ff0000'>A Lecturer has no matric number<br/></span>";
															}
															$here++;
														}
													}
													else{
														$here=0;
														echo strtoupper($term[$here])." : ".$roww[$request_word[$here]]."<br/>";
													}
													if($roww['MATRIC_NUMBER']!=""){
														$count++;
													}
													if($roww['LECTURER_NUMBER']!=""){
														$counts++;
													}
													echo"</td></tr>";
												}
												else{
													$mat=nlirdb::removeDuplicate($mat,$mat2);
													$lec=nlirdb::removeDuplicate($lec,$lec2);
												}	
											}//End of while loop
										}
									}
									else{
										$genfield=strtoupper($genDescr[$i]); strtoupper($genvalue=$genValid[$i]);
										$spefield=strtoupper($speDescr[$j]); strtoupper($spevalue=$speValid[$j]);
										//echo $genfield." : ".$genvalue."<br/>".$spefield." : ".$spevalue."<br/>";
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
														echo"MATRIC NUMBER :".$roww['MATRIC_NUMBER']."<br/><br/>";
														$count++;
													}
													if($roww['LECTURER_NUMBER']!=""){
														echo"LECTURER NUMBER :".$roww['LECTURER_NUMBER']."<br/><br/>";
														$counts++;
													}
												}
												else{
													$mat=nlirdb::removeDuplicate($mat,$mat2);
													$lec=nlirdb::removeDuplicate($lec,$lec2);
												}
											}//End of while loop
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
								document.getElementById('message').innerHTML='<b>User Query :</b> $noDupText<br/><br/> <b>Relevant Words :</b> $intermediate<br/>';
								document.getElementById('message').style.visibility='visible';
							</script>";
					}
				}
			}
	}
?>
</span>
<?php
	include("foot.inc");
	//}
?>
