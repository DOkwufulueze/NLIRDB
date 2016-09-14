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
	session_start() ;
	class nlirdb{
		public static function db(){
			@mysql_connect("localhost", "root", "");
			@mysql_select_db("nlirdb");
		}
		
		public static function admin(){
			nlirdb::db();
			$uname=htmlentities($_POST['uname']);
			$pwd=htmlentities($_POST['pwd']);
			$q=mysql_query("SELECT * FROM admin WHERE USERNAME='$uname' AND PASSWORD='$pwd'")or die(mysql_error());
			$n=mysql_num_rows($q);
			if($n==0){
				echo"<script>document.location.href='.?page=admin login&msg=Inavalid Admin Username or Password!'</script>";
			}
			else{
				$_SESSION['user']=$uname;
				header("Location:.?page=admin&msg=Welcome $uname");
			}
		}
		
		public static function login(){
			nlirdb::db();
			$uname= htmlentities($_POST['uname']);
			$pwd = htmlentities($_POST['pwd']);
			$q1 = mysql_query("SELECT * FROM users WHERE USERNAME='$uname' AND PASSWORD='$pwd'") or die(mysql_error());
			$num = mysql_num_rows($q1);
			if($num==0){
				header("Location:.?page=home&msg=Invalid username or password");
			}
			else{
				$_SESSION['user']=$uname;
				header("Location:.?page=prenli&msg=Welcome $uname to the NLIRDB");
			}
		}
		
		public function signup(){
			nlirdb::db();
			$uname=htmlentities($_POST['uname']);
			$pwd=htmlentities($_POST['pwd']);
			$q=mysql_query("SELECT * FROM users WHERE USERNAME='$uname' AND PASSWORD='$pwd'")or die(mysql_error());
			$n=mysql_num_rows($q);
			if($n!=0){
				header("Location:.?page=admin&msg=Username $uname already exists!");
			}
			else{
				if($uname==""||$pwd==""){
					echo"<script>document.location.href'.?page=admin&msg=Please Fill the Two Fields(Username and Password)'</script>";
				}
				else{
					mysql_query("INSERT INTO users(USERNAME, PASSWORD) VALUES('$uname','$pwd')")or die (mysql_error());
					header("Location:.?page=admin&msg=User with Username $uname was successfully created!",true);
				}
			}
		}
		
		public static function nexts($a, $b, $n){// creates the remaining parts of a valid SQL syntax by inserting 'AND' between each statement for which an element in a equals an element in b
			$q="";
			while($n>1){
				$aa=$a[$n-1];$bam=$b[$n-1] ;
				$q.="AND ".$aa."='".$bam."' ";
				$n-- ;
			}
			return $q ;
		}
		
		public static function concatWithSymbol($a, $sym){// concatenates the elements of an array a with the symbol sym in-between
			$q=$a[0]; $n=count($a);
			while($n>1){
				$bam=$a[$n-1];
				$q.=" ".$sym." ".$bam." ";
				$n-- ;
			}
			return $q ;
		}
		
		public static function findIndex($a, $b){//returns the index of string a in array B
			$i=0;
			$n=count($b);
			while($i<$n && $a!=$b[$i]){
				$i++;
			}
			if($a==$b[$i]){
				return $i;
			}
		}
		
		public static function isFind($a, $b){
			$i=0; $n=count($b);
			while(($i<$n)&&($a!=$b[$i])){
				$i++;
			}
			if($a==$b[$i]){
				return true;
			}
			else{
				return false;
			}
		}
		
		public static function delete($a, $b){// Deletes element a from the array b
			$i=0; 
			while(nlirdb::isFind($a, $b)){
				$n=count($b);
				while(($i<$n)&&($a!=$b[$i])){
					$i++;
				}
				$pos=$i;
				while($pos<$n){
					$b[$pos]=$b[$pos+1];
					$pos++;
				}
				unset($b[$n-1]);
			}
			return $b;
		}
		
		public static function removeDuplicate($arr1, $arr2){//$arr1 is the input array, $arr2 is the output array
			$arr2[0] = $arr1[0] ; 
			$n=count($arr1);
			for($i=1;$i<$n;$i++){
				$dup=0 ;
				$current_value=$arr1[$i] ;
				$m=count($arr2); 
				for($j=0;$j<$m;$j++){
					if($current_value==$arr2[$j]){
						$dup=1;
					}
				}
				if($dup==0){
					$arr2[$m]=$current_value ;
				}
			}
			return $arr2 ;
		}
		
		public static function isDuplicate($a){// Returns true if there are duplicates in the array $a, returns false otherwise
			$n=count($a); $j=0;$k=0; $dups=array();
			for($i=0;$i<(count($a)-1);$i++){
				$curr_val=$a[$i];
				$j=$i+1;
				while($j<(count($a))&&$curr_val!=$a[$j]){
					$j++;
				}
				if($curr_val==$a[$j]){
					$dups[$k]=$a[$j];
					$k++;
				}
			}
			if(!empty($dups)){
				return true;
			}
			else{
				return false;
			}
		}
		
		public static function logout(){
			$uname=$_SESSION['user'];
			if(session_start()){
				session_destroy();
			}
			header("Location:.?page=home&msg=$uname logged out!");
		}
	}	
?>