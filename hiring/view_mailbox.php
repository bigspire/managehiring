<?php
/* 
   Purpose : View mailboz.
	Created : Nikitasa
	Date : 13-07-2017
*/

// starting session
session_start();

//include smarty congig file
include 'configs/smartyconfig.php';
// include mysql class
include('classes/class.mysql.php');
// Connecting Database
$mysql->connect_database();
// include function validation class
include('classes/class.function.php');
// include paging class 
include('classes/class.paging.php');
// add menu count
include('menu_count.php');
// mailing class
include('classes/class.mailer.php');
// content class
include('classes/class.content.php');

// role based validation
$module_access = $fun->check_role_access('29',$modules);
$smarty->assign('module',$module_access);

// get record id   
$id = $_GET['id'];

if(($fun->isnumeric($id)) || ($fun->is_empty($id)) || ($id == 0)){
  	header('Location: ../?access=invalid');
}


// select and execute query and fetch the result
$query = "CALL view_mailbox('$id')";
try{
	if(!$result = $mysql->execute_query($query)){
		throw new Exception('Problem in executing view mail box page');
	}
	// check record exists
	if($result->num_rows){
		// calling mysql fetch_result function
		$obj = $mysql->display_result($result);
		$smarty->assign('mail_to_details', htmlentities($obj['mail_to_details']));
		$smarty->assign('created_date', $fun->convert_date_to_display($obj['created_date']));
		$smarty->assign('data', $obj);
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

// get the client email by splitting database filed
$mul_res_client_split = explode("<", $obj['mail_to_details']);
$mul_res_client_email = explode(">", $mul_res_client_split[2]);
$mul_res_client_name = explode(">", $mul_res_client_split[1]);
// splitting and assigning client name
$smarty->assign('client_name', $mul_res_client_split[0]);

// to download files
if($_GET['action'] == 'download'){
	$path = 'uploads/attachment/'.$_GET['file'];
	$fun->download_file($path);
}

if(!empty($obj['multi_resume_id'])){
	$multi_id = $obj['multi_resume_id'];
	 // $str = implode("','", $multi_id);
	// $multi_id = "\"'137','136'\"";
	// echo $query = "CALL view_mailbox_multi_resume($multi_id)";die;
	$query = "select group_concat(distinct rd.resume) as res_details,group_concat(distinct r.first_name) as candidate_name from resume_doc rd 
				left join resume r on (r.resume_doc_id = rd.id) where r.is_deleted = 'N' and r.id in ($multi_id)";
	$result = $mysql->execute_query($query);		
	$mult_res_details = $mysql->display_result($result);
	$mul_resume = explode(",", $mult_res_details['res_details']);
	$mul_candidate_name = explode(",", $mult_res_details['candidate_name']);
	for($i = 0; $i < count($mult_res_details); $i++){
		// echo $mul_resume[$i];
		// echo $mul_candidate_name[$i];
	}
	
	$output = substr($obj['resume'], 0, strlen($obj['resume'])-5);
	$file = str_replace('_', '', $output);
		
	if($obj['resume_type'] == 'S'){
		if($obj['modified_date'] != '0000-00-00 00:00:00' && $obj['modified_date'] != ''){
			   $resume_type =  "uploads/snapshotwatermarked/".$file.'_'.$fun->convert_date_type_display($obj['modified_date']).'.pdf';
		}else{
			   $resume_type =  "uploads/snapshotwatermarked/".$file.'_'.$fun->convert_date_type_display($obj['created_date']).'.pdf';
		}
	}else if($obj['resume_type'] == 'F'){
		if($obj['modified_date'] != '0000-00-00 00:00:00' && $obj['modified_date'] != ''){
			   $resume_type =  "uploads/autoresumepdf/".$file.'_'.$fun->convert_date_type_display($obj['modified_date']).'.pdf';
		}else{
			  $resume_type =  "uploads/autoresumepdf/".$file.'_'.$fun->convert_date_type_display($obj['created_date']).'.pdf';
		}
	}
}

			
if($_SERVER["REQUEST_METHOD"] == "POST"){
		
	// cc mail validation
	if($obj['cc'] != ''){
		$replace_str = str_replace(';', ',', $obj['cc']);
		$cc = explode(',', $replace_str);
		$cc_new = array_map('trim',$cc);
		$cc_new2 = array_map(array($fun, 'email_validation_cc'), $cc_new);	
		$cc_new3 = array_filter($cc_new2);
	}

	// query to fetch admin details. 
	$query = "CALL get_employee_by_id('".$_SESSION['user_id']."')";
	try{
		// calling mysql exe_query function
		if(!$result = $mysql->execute_query($query)){
			throw new Exception('Problem in getting employee details');
		}
		$row = $mysql->display_result($result);
		$emp_name = $row['first_name'].' '.$row['last_name'];
		$emp_email_id = $row['email_id'];
						
		// free the memory
		$mysql->clear_result($result);
		// call the next result
		$mysql->next_query();
	}catch(Exception $e){
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
	
	$success = '0';
	if($obj['email'] != '' || !empty($mul_res_client_email[0])){
		
		// get resume type
		$query = "select req.resume_type,req.clients_id from requirements req  where req.id = '".$_GET['req_id']."'";
		$result = $mysql->execute_query($query);	
		$res1 = $mysql->display_result($result);
		
		// get multi resume details
		$query = "select r.email_id,rd.resume,concat(r.first_name,' ',last_name) as candidate_name, r.created_date,r.modified_date from resume_doc rd 
					left join resume r on (r.resume_doc_id = rd.id) where r.is_deleted = 'N' and r.id in ($multi_id)";
		$result = $mysql->execute_query($query);	
		
		while($res2 = $mysql->display_result($result)){ 
			// $mul_resume[$i];
			$candidate_name[] = $res2['candidate_name'];
			$output = substr($res2['resume'], 0, strlen($res2['resume'])-5);
			$file = str_replace('_', '', $output);
		
			if($res1['resume_type'] == 'S'){
				if($res2['modified_date'] != '0000-00-00 00:00:00' && $res2['modified_date'] != ''){
					  $resume_file[$res2['candidate_name'].'.pdf'] =  "uploads/snapshotwatermarked/".$file.'_'.$fun->convert_date_type_display($res2['modified_date']).'.pdf';
				}else{
					$resume_file[$res2['candidate_name'].'.pdf'] =  "uploads/snapshotwatermarked/".$file.'_'.$fun->convert_date_type_display($res2['created_date']).'.pdf';
				}
			}else if($res1['resume_type'] == 'F'){
				if($res2['modified_date'] != '0000-00-00 00:00:00' && $res2['modified_date'] != ''){
					   $resume_file[$res2['candidate_name'].'.pdf'] =  "uploads/autoresumepdf/".$file.'_'.$fun->convert_date_type_display($res2['modified_date']).'.pdf';
				}else{
					  $resume_file[$res2['candidate_name'].'.pdf'] =  "uploads/autoresumepdf/".$file.'_'.$fun->convert_date_type_display($res2['created_date']).'.pdf';
				}
			}
			
		}

		$smarty->assign('mult_cand_name' , $candidate_name);
		
		// assigning the date
		$date =  $fun->current_date();
		if(!empty($obj['multi_resume_id'])){
			$req_resume_id = '0';
		}else{
			$req_resume_id = $obj['req_resume_id'];
		}

		// query to insert mailbox. 
		$query = "CALL add_mailbox('".$obj['subject']."','".$obj['cc']."','".$obj['message']."','".$date."','".$_SESSION['user_id']."','".$obj['mail_type']."',
		'".$req_resume_id."','".$obj['mail_templates_id']."','".$obj['multi_resume_id']."','".$obj['attachment']."','".$_POST['mail_to_details']."','".$_GET['req_id']."')";
		// Calling the function that makes the insert
		try{
			// calling mysql exe_query function
			if(!$result = $mysql->execute_query($query)){
				throw new Exception('Problem in executing add mailbox');
			}
			$row = $mysql->display_result($result);
			$last_id = $row['inserted_id'];
			// free the memory
			$mysql->clear_result($result);
			// call the next result
			$mysql->next_query();
		}catch(Exception $e){
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
	
		if(!empty($last_id)){
			if($obj['attachment'] != ''){
				/*
				$attach = 'uploads/attachment/'.$obj['attachment'];
				$attc =  explode('_',$obj['attachment']);
				$attc_file[$attach] = $attc[1];
				*/
				$attach[$obj['attachment']] = 'uploads/attachment/'.$obj['attachment'];
			} 
			
			
			if(!empty($_GET['multi_resume_id'])){				
				// send mail to client					
				$msg = $content->send_mail_to_client($obj,$emp_name);
				$mailer->send_mail_to_client($obj['subject'],$msg,$emp_name,$emp_email_id,$mul_res_client_name[0],$mul_res_client_email[0],$cc_new3,$resume_file,$candidate_name,$attach);
				$success = '1';
			}else{	
				// send mail to client					
				$msg = $content->send_mail_to_client($obj,$emp_name);
				$mailer->send_mail_to_client($obj['subject'],$msg,$emp_name,$emp_email_id,$obj['client_name'],$obj['email'],$cc_new3,$resume_file,$candidate_name,$attach);
				$success = '1';			
				
			}
		}
	} 
	if($success == '1'){
		$smarty->assign('EXIST_MSG' , 'Mail Sent Successfully.');
	}
}


// calling mysql close db connection function
$c_c = $mysql->close_connection();

// here assign smarty variables
$smarty->assign('id' , $_GET['id']); 
// assign page title
$smarty->assign('page_title' , 'Mailbox - Manage Hiring');  
// assigning active class status to smarty menu.tpl
$smarty->assign('mailbox_active','active');
 
// display smarty file
$smarty->display('view_mailbox.tpl');
?>