<?php
/* 
Purpose : To edit user.
Created : Nikitasa
Date : 23-02-2017
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

// role based validation
$module_access = $fun->check_role_access('31',$modules);
$smarty->assign('module',$module_access);

$getid = $_GET['id'];
$smarty->assign('getid',$getid);
// validate url 
if(($fun->isnumeric($getid)) || ($fun->is_empty($getid)) || ($getid == 0)){
  header('Location: ../?access=invalid');
}

// if id is not in database then redirect to list page
if($getid !=''){
	$query = "CALL check_valid_users('".$getid."')";
	try{
		// calling mysql execute query function
		if(!$result = $mysql->execute_query($query)){ 
			throw new Exception('Problem in checking users details');
		}
		$row = $mysql->display_result($result);
		$total = $row['total'];
		if($total == 0){ 
			header("Location:users.php?current_status=msg");
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
	$query = "CALL get_user_byid('$getid')";
	try{
		// calling mysql exe_query function
		if(!$result = $mysql->execute_query($query)){ 
			throw new Exception('Problem in getting user details');
		}
		$row = $mysql->display_result($result);
		$smarty->assign('rows',$row);
		// assign the db values into session
		foreach($row as $key => $record){
			$smarty->assign($key,$record);		
		}   
		// free the memory
		$mysql->clear_result($result);
		// call the next result
		$mysql->next_query();
	}catch(Exception $e){
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}	
}

// get L1 & L2
$query = "CALL get_approve_user('$getid')";
try{
	// calling mysql exe_query function
	if(!$result = $mysql->execute_query($query)){ 
		throw new Exception('Problem in getting approve user details');
	}
	$row = $mysql->display_result($result);
	$smarty->assign('level1', $row['level1']);
	$smarty->assign('level2', $row['level2']);
	// free the memory
	$mysql->clear_result($result);
	// call the next result
	$mysql->next_query();
}catch(Exception $e){
	echo 'Caught exception: ',  $e->getMessage(), "\n";
}	

if(!empty($_POST)){
	// Validating the required fields  
	if($fun->is_phonenumber($_POST['mobile']) || $fun->size_of_phonenumber($_POST['mobile'])) {
		$mobileErr = 'Please enter the valid mobile';
    	$smarty->assign('mobileErr',$mobileErr);
    	$test = 'error';
	}
	
	if($fun->email_validation($_POST['email_id'])) {
		$emailErr = 'Please enter the valid email address';
    	$smarty->assign('emailErr',$emailErr);
    	$test = 'error';
	}
	
	if(!empty($_POST['level2'])){
    	$smarty->assign('level2',$_POST['level2']);
	}
	if(!empty($_POST['level1'])){
    	$smarty->assign('level1',$_POST['level1']);
	}
	
	// array for printing correct field name in error message
	$fieldtype = array('0', '0' ,'0','0','1','1','1','0');
	$actualfield = array('first name','last name', 'email address', 'mobile','role','status','location','email signature');
    $field = array('first_name' => 'first_nameErr','last_name' => 'last_nameErr', 'email_id' => 'emailErr' ,
	'mobile' => 'mobileErr','roles_id' => 'roleErr','status' => 'statusErr','location_id' => 'locationErr',
	'signature' => 'signatureErr');
	$j = 0;
	foreach ($field as $field => $er_var){ 
		if($_POST[$field] == ''){
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
	// query to check whether it is exist or not. 
	$query = "CALL check_user_exist('".$getid."', '".$_POST['email']."')";
	// Calling the function that makes the insert
	try{
		// calling mysql exe_query function
		if(!$result = $mysql->execute_query($query)){
			throw new Exception('Problem in executing to check mail exist');
		}
		$row = $mysql->display_result($result);
		// free the memory
		$mysql->clear_result($result);
		// call the next result
		$mysql->next_query();
	}catch(Exception $e){
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	} 
		
	
	if(empty($test)){
		if($row['total'] == '0'){
			// query to insert sharing percent. 
			$query = "CALL edit_user('$getid','".$mysql->real_escape_str($_POST['email_id'])."',
						'".$fun->is_white_space($mysql->real_escape_str($_POST['first_name']))."',
						'".$fun->is_white_space($mysql->real_escape_str($_POST['last_name']))."',
						'".$mysql->real_escape_str($_POST['mobile'])."',
						'".$fun->is_white_space($mysql->real_escape_str($_POST['position']))."',
						'".$mysql->real_escape_str($_POST['status'])."',
						'".$mysql->real_escape_str($_POST['roles_id'])."','".$mysql->real_escape_str($_SESSION['user_id'])."',
			 			'".$date."','".$mysql->real_escape_str($_POST['location_id'])."',
						'".$fun->is_white_space($mysql->real_escape_str($_POST['signature']))."')";
			// Calling the function that makes the insert
			try{
				// calling mysql exe_query function
				if(!$result = $mysql->execute_query($query)){
					throw new Exception('Problem in executing edit user');
				}
				$row = $mysql->display_result($result);				
				// free the memory
				$mysql->clear_result($result);
				$affected_rows = $row['affected_rows'];
				if(!empty($affected_rows)){
					// call the next result
					$mysql->next_query();
					// check L1 or L2 available
					// calling mysql exe_query function
					$query = "CALL check_approval('".$mysql->real_escape_str($_GET['id'])."')";
					if(!$result = $mysql->execute_query($query)){
						throw new Exception('Problem in getting check approval');
					}
					$row = $mysql->display_result($result);
					if($row['id'] != ''){
						// update L1 and L2 if required
						$query2 = "CALL edit_approval('".$mysql->real_escape_str($row['id'])."','".$mysql->real_escape_str($_GET['id'])."',
						'".$mysql->real_escape_str($_POST['level1'])."','".$mysql->real_escape_str($_POST['level2'])."',
						'".$date."')";
					}else if($_POST['level1'] != '' || $_POST['level2'] != ''){
						$query2 = "CALL add_approval('".$mysql->real_escape_str($_GET['id'])."',
						'".$mysql->real_escape_str($_POST['level1'])."','".$mysql->real_escape_str($_POST['level2'])."',
						'".$date."')";
					}
					// call the next result
					$mysql->next_query();
					// calling mysql exe_query function
					if($query2 != ''){
						if(!$result = $mysql->execute_query($query2)){
							throw new Exception('Problem in executing approval');
						}
					}
					// redirecting to list users page
					header('Location: users.php?status=updated');		
				}
			}catch(Exception $e){
				echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
		}else{
			$msg = "Email Address is already exists";
			$smarty->assign('EXIST_MSG',$msg); 
		} 
	}
}

// query to fetch all roles. 
$roles = array();
$query = 'CALL get_roles()';
try{
	// calling mysql exe_query function
	if(!$result = $mysql->execute_query($query)){
		throw new Exception('Problem in executing roles');
	}
	
	while($row = $mysql->display_result($result))
	{
 		$roles[$row['id']] = $row['role_name'];
	}
	$smarty->assign('roles',$roles);
	// free the memory
	$mysql->clear_result($result);
	// call the next result
	$mysql->next_query();
}catch(Exception $e){
	echo 'Caught exception: ',  $e->getMessage(), "\n";
} 

// query to fetch location list. 
$query = 'CALL get_branch()';
try{
	// calling mysql exe_query function
	if(!$result = $mysql->execute_query($query)){
		throw new Exception('Problem in getting location list');
	}
	while($row = $mysql->display_result($result))
	{
 		$locations[$row['id']] = ucwords($row['branch']);
	}
	$smarty->assign('locations',$locations);
	// free the memory
	$mysql->clear_result($result);
	// call the next result
	$mysql->next_query();
}catch(Exception $e){
	echo 'Caught exception: ',  $e->getMessage(), "\n";
}
// query to fetch all employee names. 
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
	$smarty->assign('users',$emp_name);
	// free the memory
	$mysql->clear_result($result);
	// call the next result
	$mysql->next_query();
}catch(Exception $e){
	echo 'Caught exception: ',  $e->getMessage(), "\n";
}

// smarty drop down array for status
$smarty->assign('user_status', array('' => 'Select', '0' => 'Active', '1' => 'Inactive'));
// closing mysql
$mysql->close_connection();

// assign page title
$smarty->assign('page_title' , 'Edit User - Manage Hiring');  
// assigning active class status to smarty menu.tpl
$smarty->assign('setting_active','active');
// $smarty->assign('setting_active', $fun->set_menu_active('edit_sharing_criteria'));	  
// display smarty file
$smarty->display('edit_user.tpl');
?>