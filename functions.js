// JavaScript Document
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
window.onload=starts;
function starts(){
	var uname = document.getElementById("uname") ;	
	var pwd = document.getElementById("pwd") ;
	uname.onclick=function(){uname.value="";}
	pwd.onclick=function(){pwd.value="";}
}

function clears(){
	document.nli.nl.value="";
}
