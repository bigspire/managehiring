<?php
/* 
Purpose : To edit resume api.
Created : Nikitasa
Date : 22-01-2018
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
// mailing class
include('classes/class.mailer.php');
// content class
include('classes/class.content.php');

// role based validation
$module_access = $fun->check_role_access('51',$modules);
$smarty->assign('module',$module_access);

$getid = $_GET['id'];
$smarty->assign('getid',$getid);
// validate url 
if(($fun->isnumeric($getid)) || ($fun->is_empty($getid)) || ($getid == 0)){
  header('Location: ../?access=invalid');
}

// if id is not in database then redirect to view page
if($getid !=''){
	$query = "CALL check_valid_api('".$getid."')";
	try{
		// calling mysql execute query function
		if(!$result = $mysql->execute_query($query)){ 
			throw new Exception('Problem in checking api details');
		}
		$row = $mysql->display_result($result);
		$total = $row['total'];
		if($total == 0){ 
			// header("Location:view_resume_api.php?current_status=msg");
			header('Location: ../?access=invalid');
		}
		// free the memory
		$mysql->clear_result($result);
		// next query execution
		$mysql->next_query();
	}catch(Exception $e){
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
} 

// get database values
if(empty($_POST)){
	$query = "CALL get_resume_api_byid('$getid')";
	try{
		// calling mysql exe_query function
		if(!$result = $mysql->execute_query($query)){ 
			throw new Exception('Problem in executing get grade');
		}
		$row = $mysql->display_result($result);
		$smarty->assign('rows',$row);
		// assign the db values into session
		foreach($row as $key => $record){
			$smarty->assign($key,$record);		
		}   
		// free the memory
		$mysql->clear_result($result);
		// next query execution
		$mysql->next_query();
	}catch(Exception $e){
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}	
}

if(!empty($_POST)){
	// Validating the required fields  
	// array for printing correct field name in error message
	$fieldtype = array('0', '0','0');
	$actualfield = array('HTML2PDF Rocket API key','ILOVEPDF secret key','ILOVEPDF public key');
    $field = array('api_key' => 'api_keyErr', 'secret_key' => 'secret_keyErr', 'public_key' => 'public_keyErr');
	$j = 0;
	foreach ($field as $field => $er_var){ 
		if(empty($_POST[$field]) && $_POST[$field] != '0'){
			$error_msg = $fieldtype[$j] ? ' select the ' : ' enter the ';
			$actual_field =  $actualfield[$j];
			$er[$er_var] = 'Please'. $error_msg .$actual_field;
			$test = 'error';
			$smarty->assign($er_var,$er[$er_var]);
		}else{
			$smarty->assign($field,$_POST[$field]);	
		}
		$j++;
	}
	// assigning the date
	$date =  $fun->current_date();

	if(empty($test)){
		// query to insert api. 
		$query = "CALL edit_resume_api('".$mysql->real_escape_str($getid)."','".$date."')";
		try{
	    	// calling mysql exe_query function
			if(!$result = $mysql->execute_query($query)){
				throw new Exception('Problem in executing edit api query');
			}
			$row = $mysql->display_result($result);
			$affected_rows = $row['affected_rows'];
			// clear the results	    			
			$mysql->clear_result($result);
			// next query execution
			$mysql->next_query();
		}catch(Exception $e){
			echo 'Caught exception: ',  $e->getMessage(), "\n";
			die;
		}
		// query to insert api. 
		$query = "CALL add_resume_api('".$_POST['public_key']."','".$_POST['secret_key']."','".$_POST['api_key']."','".$date."')";
		try{
	    	// calling mysql exe_query function
			if(!$result = $mysql->execute_query($query)){
				throw new Exception('Problem in executing add api query');
			}
			$row = $mysql->display_result($result);
			$inserted_id = $row['inserted_id'];
			// clear the results	    			
			$mysql->clear_result($result);
			// next query execution
			$mysql->next_query();
		}catch(Exception $e){
			echo 'Caught exception: ',  $e->getMessage(), "\n";
			die;
		}
			
		// query to fetch admin details. 
		$query = "CALL get_admin_director_details('A','".$_SESSION['user_id']."')";
		try{
			// calling mysql exe_query function
			if(!$result = $mysql->execute_query($query)){
				throw new Exception('Problem in getting employee details');
			}
			$obj = $mysql->display_result($result);
			$user_name = $obj['user_name'];
			// $user_email_id = $obj['email_id'];
			$user_email_id = 'noreply@managehiring.com';
					
			// free the memory
			$mysql->clear_result($result);
			// call the next result
			$mysql->next_query();
		}catch(Exception $e){
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
				
		// query to fetch BH/Director details 
		$query = "CALL get_admin_director_details('D','')";
		try{
			// calling mysql exe_query function
			if(!$result = $mysql->execute_query($query)){
				throw new Exception('Problem in getting approval user details');
			}
			while($account = $mysql->display_result($result)){
				// $row_account[] = $account;
				$row_account = $account;
			}
			// free the memory
			$mysql->clear_result($result);
			// call the next result
			$mysql->next_query();
		}catch(Exception $e){
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
			
		$modified_date = $fun->convert_date_to_display($date);
			
		// send mail to BH/Director
		// foreach($row_account as  $approval_user){ 					
			$sub = "Manage Hiring -  " .$user_name." edited Resume API!";
			$msg = $content->get_edit_resume_api_details($_POST,$user_name,$row_account['approval_name'],$modified_date);
			$mailer->send_mail($sub,$msg,$user_name,$user_email_id,$row_account['approval_name'],$row_account['email_id']);	
		// }die;
		if(!empty($affected_rows) && !empty($inserted_id)){
			// redirecting to view page
			header('Location: view_resume_api.php?status=updated');	
		}
	}
}

// closing mysql
$mysql->close_connection();
// assign page title
$smarty->assign('page_title' , 'Edit Reusme API - Manage Hiring');  
// assigning active class status to smarty menu.tpl
$smarty->assign('setting_active','active');
// display smarty file
$smarty->display('edit_resume_api.tpl');
?>