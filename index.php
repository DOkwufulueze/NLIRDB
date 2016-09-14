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
	/*if(!(isset($_GET['page']))){
		$_GET['page'] = 'home' ;
	}
	else{*/
		switch($_GET['page']){
			case 'home' : 
			//session_destroy ;
			include('home.php') ;
			break ;

			case 'prenli' : include('prenli.php') ;
			break ;
			
			case 'nlicntr' : include('nlicntr.php') ;
			break ;

			case 'nli' : include('nli.php') ;
			break ;

			case 'result' : include('result.php') ;
			break ;
			
			case 'admin login' : 
			//session_destroy;
			include('admin_login.php') ;
			break ;
			
			case 'admin' : include('admin.php') ;
			break ;
			
			case 'logout' : 
			include('class.php') ;
			nlirdb::logout() ;
			break ;

			default : include('home.php') ;
			break ;
		}
	//}
?>