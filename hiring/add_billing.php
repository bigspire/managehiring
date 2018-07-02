<?php
/* 
Purpose : To add billing.
Created : Nikitasa
Date : 31-01-2017
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
$module_access = $fun->check_role_access('36',$modules);
$smarty->assign('module',$module_access);
$smarty->assign('today' , date('Y-m-d'));
if(!empty($_GET['res_id']) && !empty($_GET['req_res_id'])){
	
	// query to fetch approval user id. 
	$query = "CALL get_billing('".$_GET['res_id']."','".$_GET['req_res_id']."')";
	try{
		// calling mysql exe_query function
		if(!$result = $mysql->execute_query($query)){
			throw new Exception('Problem in getting requirements details');
		}
		// check record exists
		if($result->num_rows){
			// calling mysql fetch_result function
			while($obj = $mysql->display_result($result)){
				$smarty->assign('resume_id', $obj['resume_id']);
				$smarty->assign('requirement_id', $obj['requirement_id']);
				$smarty->assign('client_id', $obj['client_id']);
				$smarty->assign('candidate_name', $obj['candidate_name']);
				$smarty->assign('position', $obj['position']);
				$smarty->assign('client_name', $obj['client_name']);
				$smarty->assign('ctc_offer', $obj['ctc_offer']);
				$smarty->assign('billing_amount', $obj['billing_amount']);
				$smarty->assign('bill_percent', $obj['bill_percent']);
				// $smarty->assign('billing_date' , date('d/m/Y',$obj['billing_date']));
				$smarty->assign('joined_date' , $fun->convert_date_to_display($obj['joined_date']));
				$smarty->assign('noformat_joined_date', date('d/m/Y', strtotime($obj['joined_date'])));
			}
		}else{
			header('Location: ../?access=invalid');
		}
		// free the memory
		$mysql->clear_result($result);
		// call the next result
		$mysql->next_query();
	}catch(Exception $e){
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
}else{
	header('Location: ../');
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){			
	
	if($_POST['ctc_offer'] == '' || $_POST['ctc_offer'] <= '0'){
		$smarty->assign('ctc_offerErr','Please enter the ctc offered ');
		$test = 'error';
	}else{
		$smarty->assign('ctc_offer',$_POST['ctc_offer']);
	}
	
	if($_POST['billing_amount'] == '' || $_POST['billing_amount'] <= '0'){
		$smarty->assign('billing_amountErr','Please enter the billing amount');
		$test = 'error';
	}else{
		$smarty->assign('billing_amount',$_POST['billing_amount']);
	}
	
	if($_POST['billing_amount'] != '' && ($_POST['billing_amount'] > $_POST['ctc_offer'])){
		$smarty->assign('billing_amountEr','Billing amount should be lesser than CTC offered');
		$test = 'error';
	}
	
	if($_POST['billing_date'] == '' || $_POST['billing_date'] == '0000-00-00'){
		$smarty->assign('billing_dateErr','Please enter the billing date');
		$test = 'error';
	}else{
		$smarty->assign('billing_date',$_POST['billing_date']);
	}
	
	if($_POST['bill_percent'] != '' && ($fun->isnumeric($_POST['bill_percent']))){
		$smarty->assign('bill_percentErr','Please enter the valid Billing %');
		$test = 'error';
	}else{
		$smarty->assign('bill_percent',$_POST['bill_percent']);
	}
	
	if(!isset($_POST['offer']) && empty($_FILES['offer']['name'])){
		$smarty->assign('offerErr','Please upload the proof of offer');	
		$test = 'error';			
	}else{
		$smarty->assign('offer',$_POST['offer']);
	}
	
	$req_size =  5242880;

	// upload the file if attached
	if(!empty($_FILES['offer']['name'])){
		// upload directory
		$uploaddir = 'uploads/offer/'; 
		$attachmentsize = $_FILES['offer']['size'];
		$attachmenttype = pathinfo($_FILES['offer']['name']);
		$extension = $attachmenttype['extension'];	
		// file extensions
		$extensions = array('docx','jpg','png','doc','pdf'); 
		
		// checking the file extension is doc,docx
		if($fun->extension_validation($extension,$extensions) == true){		
			$attachmentuploadErr = 'Attachment must be docx,png,jpg,doc,pdf';
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
	
	if(empty($test)){
		
		// assigning the date
		$date =  $fun->current_date();
		$billing_user_id = $_SESSION['user_id'];
	
		// query to fetch approval user id. 
		$query = "CALL get_approval_user_id('".$_SESSION['user_id']."')";
		try{
			// calling mysql exe_query function
			if(!$result = $mysql->execute_query($query)){
				throw new Exception('Problem in getting approval user details');
			}
			$row = $mysql->display_result($result);
			$smarty->assign('created_by',$row['created_by_id']);
			// free the memory
			$mysql->clear_result($result);
			// call the next result
			$mysql->next_query();
		}catch(Exception $e){
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}	
			
		// store approve users id in session
		$_SESSION["approval_user_id"] = $row['created_by_id'];
	
		if($row['created_by_id'] != ''){
	
			// query to insert into database. 
			$query = "CALL add_billing('".$mysql->real_escape_str($_POST['resume_id'])."',
			'".$mysql->real_escape_str($_POST['requirement_id'])."','".$mysql->real_escape_str($_POST['client_id'])."',
			'$billing_user_id', '".$date."')";

			// Calling the function that makes the insert
			try{
				// calling mysql exe_query function
				if(!$result = $mysql->execute_query($query)){
					throw new Exception('Problem in adding billing details');
				}
				$row = $mysql->display_result($result);
				$billing_id = $row['inserted_id'];				
				// free the memory
				$mysql->clear_result($result);
				// call the next result
				$mysql->next_query();
			}catch(Exception $e){
				echo 'Caught exception: ',  $e->getMessage(), "\n";
			}	

			
			$prefix = substr(time(), 2,5).rand(1000,10000000).'_';
			$new_file = $prefix.$_FILES['offer']['name'];
			$path = $uploaddir.$new_file;
			move_uploaded_file($_FILES['offer']['tmp_name'], $path);
			// query to update the file
			$query = "CALL upload_offer('".$new_file."','".$billing_id."')";
			try{
				if(!$result = $mysql->execute_query($query)){
					throw new Exception('Problem in uploading offer proof');
				}
				$row = $mysql->display_result($result);
				$offer_id = $row['affected_rows'];
				// free the memory
				$mysql->clear_result($result);
				// call the next result
				$mysql->next_query();
			}catch(Exception $e){
				echo 'Caught exception: ',  $e->getMessage(), "\n";
			}

			// query to insert into database. 
			$query = "CALL edit_billing_req_resume('".$fun->is_white_space($mysql->real_escape_str($_POST['ctc_offer']))."',
			'".$fun->is_white_space($mysql->real_escape_str($_POST['billing_amount']))."',
			'".$fun->is_white_space($fun->convert_date($_POST['billing_date']))."',
			'".$_GET['req_res_id']."','".$fun->is_white_space($mysql->real_escape_str($_POST['bill_percent']))."')";

			// Calling the function that makes the insert
			try{
				// calling mysql exe_query function
				if(!$result = $mysql->execute_query($query)){
					throw new Exception('Problem in adding req billing details');
				}
				$row = $mysql->display_result($result);
				$req_res_id = $row['affected_rows'];				
				// free the memory
				$mysql->clear_result($result);
				// call the next result
				$mysql->next_query();
			}catch(Exception $e){
				echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
	
			// query to insert into database. 
			$query = "CALL add_billing_status('".$date."','".$billing_id."','".$_SESSION["approval_user_id"]."')";
			// Calling the function that makes the insert
			try{
				// calling mysql exe_query function
				if(!$result = $mysql->execute_query($query)){
					throw new Exception('Problem in adding billing status');
				}
				$row = $mysql->display_result($result);			
				// free the memory
				$mysql->clear_result($result);
				// call the next result
				$mysql->next_query();
			}catch(Exception $e){
				echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
		
			// query to insert into database. 
			$query = "CALL add_billing_users('".$billing_id."', '".$_SESSION["approval_user_id"]."')";
			// Calling the function that makes the insert
			try{
				// calling mysql exe_query function
				if(!$result = $mysql->execute_query($query)){
					throw new Exception('Problem in adding billing users');
				}
				$row = $mysql->display_result($result);
				$last_inserted_id = $row['inserted_id'];
				// free the memory
				$mysql->clear_result($result);
				// call the next result
				$mysql->next_query();
			}catch(Exception $e){
				echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
			
			
			// query to fetch billing employee details. 
			$query = "CALL get_employee_by_id('".$billing_user_id."')";
			try{
				// calling mysql exe_query function
				if(!$result = $mysql->execute_query($query)){
					throw new Exception('Problem in getting employee details');
				}
				$obj = $mysql->display_result($result);
				$_POST['user_name'] = $obj['first_name'].' '.$obj['last_name'];
				$_POST['email_address'] = $obj['email_id'];
 					
				// free the memory
				$mysql->clear_result($result);
				// call the next result
				$mysql->next_query();
			}catch(Exception $e){
				echo 'Caught exception: ',  $e->getMessage(), "\n";
			}	
			
			// query to fetch approval employee details. 
			$query = "CALL get_approval_user_by_id('".$_SESSION["approval_user_id"]."')";
			try{
				// calling mysql exe_query function
				if(!$result = $mysql->execute_query($query)){
					throw new Exception('Problem in getting approval employee details');
				}
				$obj = $mysql->display_result($result);
				$_POST['approval_user_name'] = $obj['approval_user'];
				$_POST['approval_user_email'] = $obj['approval_email'];
 					
				// free the memory
				$mysql->clear_result($result);
				// call the next result
				$mysql->next_query();
			}catch(Exception $e){
				echo 'Caught exception: ',  $e->getMessage(), "\n";
			}

			// get billing user details
			$user_name = ucwords($_POST['user_name']);
			$user_email = $_POST['email_address'];
			
			// get approval user details
			$approval_user_name = ucwords($_POST['approval_user_name']);
			$approval_user_email = $_POST['approval_user_email'];
			
			// send mail to approval user
			$sub = "Manage Hiring -  " .$user_name." submitted billing details!";
			$msg = $content->get_create_billing_mail($_POST,$obj,$user_name,$approval_user_name,$candidate_name);
			$mailer->send_mail($sub,$msg,$user_name,$user_email,$approval_user_name,$approval_user_email);
	
			if(!empty($last_inserted_id) && empty($test) && !empty($req_res_id) && !empty($billing_id) && !empty($offer_id)){ 
				// redirecting to list page
				header("Location: billing.php?status=created");		
			}
		}else{
			$msg = "Sorry, you have no L1 to approve your request. Please contact your admin.";
			$smarty->assign('EXIST_MSG',$msg); 
		} 
	}
}

// closing mysql
$mysql->close_connection();
// assign page title
$smarty->assign('page_title' , 'Add Billing - Manage Hiring');  
// assigning active class status to smarty menu.tpl
$smarty->assign('billings_active','active');
// $smarty->assign('setting_active', $fun->set_menu_active('add_billing')); 	  
// display smarty file
$smarty->display('add_billing.tpl');
?>