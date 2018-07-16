<?php
/*
Purpose : To get cv rejection analysis degree.
Created : Nikitasa
Date : 16-7-2018
*/
session_start();
// including smarty config
include 'configs/smartyconfig.php';
// including Database class file
include('classes/class.mysql.php');
$mysql->connect_database();
// Validating fields using class.function.php
include('classes/class.function.php');
// add menu count
include('menu_count.php');
// mailing class
include('classes/class.mailer.php');
// content class
include('classes/class.content.php');

// role based validation
$module_access = $fun->check_role_access('59',$modules);
$smarty->assign('module',$module_access);

// query to fetch all client details. 
$query = 'CALL get_client_details()';
try{
	// calling mysql exe_query function
	if(!$result = $mysql->execute_query($query)){
		throw new Exception('Problem in getting client details');
	}	
	$i = '0';
	while($row = $mysql->display_result($result))
	{	
		$client_name[$row['id']] = ucwords($row['client_name']);
 		$data[] = $row;
 		$i++;
	}
	$smarty->assign('client_name',$client_name);
	$smarty->assign('data',$data);
	// free the memory
	$mysql->clear_result($result);
	// call the next result
	$mysql->next_query();
}catch(Exception $e){
	echo 'Caught exception: ',  $e->getMessage(), "\n";
}

// query to fetch all roles details. 
$query = 'CALL get_roles()';
try{
	// calling mysql exe_query function
	if(!$result = $mysql->execute_query($query)){
		throw new Exception('Problem in getting roles details');
	}
	while($row = $mysql->display_result($result))
	{
 		$role_name[$row['id']] = ucwords($row['role_name']);
	}
	$smarty->assign('role_name',$role_name);
	// free the memory
	$mysql->clear_result($result);
	// call the next result
	$mysql->next_query();
}catch(Exception $e){
	echo 'Caught exception: ',  $e->getMessage(), "\n";
}


// query to fetch all branch details. 
$query = 'CALL get_branch()';
try{
	// calling mysql exe_query function
	if(!$result = $mysql->execute_query($query)){
		throw new Exception('Problem in getting branch details');
	}
	while($row = $mysql->display_result($result))
	{
 		$branch_name[$row['id']] = ucwords($row['branch']);
	}
	$smarty->assign('branch_name',$branch_name);
	// free the memory
	$mysql->clear_result($result);
	// call the next result
	$mysql->next_query();
}catch(Exception $e){
	echo 'Caught exception: ',  $e->getMessage(), "\n";
}


// query to fetch all employee details. 
$query = 'CALL get_employee()';
try{
	// calling mysql exe_query function
	if(!$result = $mysql->execute_query($query)){
		throw new Exception('Problem in getting employee details');
	}
	while($row = $mysql->display_result($result))
	{
 		$emp_name[$row['id']] = ucwords($row['emp_name']);
	}
	$smarty->assign('emp_name',$emp_name);
	// free the memory
	$mysql->clear_result($result);
	// call the next result
	$mysql->next_query();
}catch(Exception $e){
	echo 'Caught exception: ',  $e->getMessage(), "\n";
}

// closing mysql
$mysql->close_connection();

$smarty->assign('client_id',$_POST['client_id']);
// assign page title
$smarty->assign('page_title' , 'CV Rejection Analysis - Manage Hiring');  
// assigning active class status to smarty menu.tpl
$smarty->assign('report_menu','active');	  
// display smarty file
$smarty->display('cv_rejection_analysis.tpl');
?>