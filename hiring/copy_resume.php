<?php
/* 
Purpose : To copy resume.
Created : Nikitasa
Date : 17-08-2018
*/

// starting session
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

// get current date 
$current_date = $fun->current_date_db();
// get resume id
$smarty->assign('res_id',$_GET['res_id']);

// query to fetch resume candidate name. 
$query = "CALL get_resume_candidate_name('".$_GET['res_id']."')";
try{
	// calling mysql exe_query function
	if(!$result = $mysql->execute_query($query)){
		throw new Exception('Problem in getting resume candidate name');
	}
	while($row = $mysql->display_result($result))
	{
		$candidate_name = ucwords($row['first_name'].' '.$row['last_name']);
	}
	$smarty->assign('candidate_name',$candidate_name);
	// free the memory
	$mysql->clear_result($result);
	// call the next result
	$mysql->next_query();
}catch(Exception $e){
	echo 'Caught exception: ',  $e->getMessage(), "\n";
} 
			
$clients = array();
// query to fetch all clients names. 
$query = "CALL get_clients('".$_SESSION['user_id']."')";
try{
	// calling mysql exe_query function
	if(!$result = $mysql->execute_query($query)){
		throw new Exception('Problem in getting client details');
	}
	while($row = $mysql->display_result($result))
	{
		$clients[$row['id']] = ucwords($row['client_name']);
	}
	$smarty->assign('clients',$clients);
	// free the memory
	$mysql->clear_result($result);
	// call the next result
	$mysql->next_query();
}catch(Exception $e){
	echo 'Caught exception: ',  $e->getMessage(), "\n";
}

$position = array();
// query to fetch position details. 
$query = "CALL get_position('".$_POST['client']."','".$_SESSION['user_id']."')";
try{
	// calling mysql exe_query function
	if(!$result = $mysql->execute_query($query)){
		throw new Exception('Problem in getting requirement');
	}
	while($row = $mysql->display_result($result))
	{
		$position[$row['id']] = ucwords($row['job_title']);
	}
	$smarty->assign('position',$position);
	// free the memory
	$mysql->clear_result($result);
	// call the next result
	$mysql->next_query();
}catch(Exception $e){
	echo 'Caught exception: ',  $e->getMessage(), "\n";
} 
		
		
// query to fetch all clients names. 
$query = "CALL get_clients_position('".$_POST['client']."','".$_POST['position_for']."')";
try{
	// calling mysql exe_query function
	if(!$result = $mysql->execute_query($query)){
		throw new Exception('Problem in getting client and position details');
	}
	$row = $mysql->display_result($result);
	$smarty->assign('client',ucwords($row['client_name']));
	$smarty->assign('position_for',ucwords($row['job_title']));
	$url = $row['resume_type'] == 'F' ? 'edit_formatted_resume.php?id='.$_GET['res_id'].'&copy=1' : 'edit_resume.php?id='.$_GET['res_id'].'&copy=1';
	$smarty->assign('redirect_url',$url);
	// free the memory
	$mysql->clear_result($result);
	// call the next result
	$mysql->next_query();
}catch(Exception $e){
	echo 'Caught exception: ',  $e->getMessage(), "\n";
}		

// assign to session
$_SESSION['client_id'] = $_POST['client'];
$_SESSION['req_id'] = $_POST['position_for'];
		
if($_SERVER["REQUEST_METHOD"] == "POST"){
		
	// validating the required fields
	if($_GET['client_id'] == '' and $_GET['req_id'] == ''){
		if($_POST['client'] == ''){
			$smarty->assign('clientErr', 'Please select the client');	
			$test = 'error';
		}
		if(empty($_POST['position_for'])){
			$smarty->assign('position_forErr', 'Please select the position for');	
			$test = 'error';
		}
	}
	
	
	$query = "CALL get_task_plan_details('".$current_date."','".$_SESSION['user_id']."','".$_POST['position_for']."')";
	try{
		// calling mysql exe_query function
		if(!$result = $mysql->execute_query($query)){
			throw new Exception('Problem in getting client and position details');
		}
		$check_task = $mysql->display_result($result);
		// free the memory
		$mysql->clear_result($result);
		// call the next result
		$mysql->next_query();
	}catch(Exception $e){
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
	if(empty($check_task) && empty($test)){
		$smarty->assign('error_form' , 1);
	}else{

		// assigning the date
		$date =  $fun->current_date();		
			
		// query to fetch the resume type. 
		$query = "CALL get_resumetype_is_exist('".$_POST['position_for']."')";
		try{
			// calling mysql exe_query function
			if(!$result = $mysql->execute_query($query)){
				throw new Exception('Problem in getting resume type details');
			}
			$check = $mysql->display_result($result);
			// free the memory
			$mysql->clear_result($result);
			// call the next result
			$mysql->next_query();
		}catch(Exception $e){
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}

			
		if(empty($test)){ 
			$check['resume_type'] = 4;
			if($check['resume_type'] != ''){
				// get requirement status details
				$query = "CALL get_requirement_status('".$_POST['position_for']."')";
				try{
					if(!$result = $mysql->execute_query($query)){
						throw new Exception('Problem in getting requirement status details');
					}
					$row = $mysql->display_result($result);
					// free the memory
					$mysql->clear_result($result);
					// call the next result
					$mysql->next_query();
				}catch(Exception $e){
					echo 'Caught exception: ',  $e->getMessage(), "\n";
				}
						
				if($row['no_joined'] < $row['no_job']){
					if($row['req_status_id'] == '2' && $row['req_status_id'] == '3' && $row['req_status_id'] == '4'){	
						$smarty->assign('ALERT_MSG', 'Position status is not In-Process. You cannot upload resume');
					}
				}else{
					$smarty->assign('ALERT_MSG1', 'Number of Openings are already closed. You cannot upload resume');
				}
			}else{
				$smarty->assign('typeErr', 'You cannot upload the resume if  resume type has not set for the position. Pls contact your CRM');	
			}
		}
		$smarty->assign('form_sent' , 1);	
	}
}
// closing mysql
$mysql->close_connection();

// assign page title
$smarty->assign('page_title' , 'Copy Resume - Manage Hiring');  
// assigning active class status to smarty menu.tpl
$smarty->assign('resume_active','active');
// display smarty file
$smarty->display('copy_resume.tpl');
?>
