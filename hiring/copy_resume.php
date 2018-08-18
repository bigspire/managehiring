<?php
/* 
Purpose : To copy resume.
Created : Nikitasa
Date : 17-08-2018
*/

// starting session
session_start();
unset($_SESSION['position_for']);
unset($_SESSION['resume_doc']);
unset($_SESSION['resume_docs']);
unset($_SESSION['resume_docs_id']);
unset($_SESSION['clients_id']);
unset($_SESSION['IGNORE_CV']);
unset($_SESSION['extraction']);
$_SESSION['extraction'] = '';
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
	if($_POST['client'] == ''){
		$smarty->assign('clientErr', 'Please select the client');	
		$test = 'error';
	}
	if(empty($_POST['position_for'])){
		$smarty->assign('position_forErr', 'Please select the position for');	
		$test = 'error';
	}
	
	$req_size =  5242880;

	// upload the file if attached
	if(!empty($_FILES['resume']['name'])){
		// upload directory
		$uploaddir = 'uploads/resume/'; 
		$attachmentsize = $_FILES['resume']['size'];
		$attachmenttype = pathinfo($_FILES['resume']['name']);
		$extension = $attachmenttype['extension'];	
		// file extensions
		$extensions = array('docx'); 
		
		// checking the file extension is doc,docx
		if($fun->extension_validation($extension,$extensions) == true){		
			$attachmentuploadErr = 'Attachment must be .docx';
			$test = 'error';
		}
		// checking the file size is less than 1 MB		
		else if($fun->size_validation($attachmentsize,$req_size)){
			$attachmentuploadErr = 'Attachment file size must be less than 5 MB';
			$test = 'error';
		}
		// checking the file size is less than 1 MB		
		else if(empty($attachmentsize)){
			$attachmentuploadErr = 'Attachment file size must be less than 5 MB';
			$test = 'error';
		}				
	}	
	$smarty->assign('attachmentuploadErr', $attachmentuploadErr);
		
		
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
		$type = 'D';
		
		// query to fetch all clients names. 
		$query = "CALL get_resumetype_is_exist('".$_SESSION['req_id']."')";
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
			
		if(empty($test) && !empty($check_task)){ 
			$check['resume_type'] = 4;
			if($check['resume_type'] != ''){
				if(!empty($_FILES['resume']['name'])){
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
						if($row['req_status_id'] != '2' && $row['req_status_id'] != '3' && $row['req_status_id'] != '4'){	
								// upload the file
								$prefix = substr(time(), 2,5).rand(1000,10000000).'_';
								$new_file = $prefix.$_FILES['resume']['name'];
								$path = $uploaddir.$new_file;
								move_uploaded_file($_FILES['resume']['tmp_name'], $path);
								// query to update the file
								$query = "CALL upload_resume('".$new_file."','".$type."','".$_SESSION['user_id']."','".$date."')";
								try{
									if(!$result = $mysql->execute_query($query)){
										throw new Exception('Problem in uploading resume');
									}
									$row = $mysql->display_result($result);
									$last_id = $row['inserted_id'];
									$_SESSION['resume_docs_id'] = $last_id;
									// write the session to server
									$_SESSION['resume_docs'] = $new_file;								
									$_SESSION['client'] = $_POST['client'];
									$_SESSION['position_for'] = $_POST['position_for'];
									// call the next result
									$mysql->next_query();
								}catch(Exception $e){
									echo 'Caught exception: ',  $e->getMessage(), "\n";
								}
						}else{
							$smarty->assign('ALERT_MSG', 'Position status is not In-Process. You cannot upload resume');
						}
					}else{
						$smarty->assign('ALERT_MSG1', 'Number of Openings are already closed. You cannot upload resume');
					}
				}
			}else{
				$smarty->assign('typeErr', 'You cannot upload the resume if  resume type has not set for the position. Pls contact your CRM');	
			}
			$smarty->assign('form_sent' , 1);	
		}
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
