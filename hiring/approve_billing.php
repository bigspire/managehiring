<?php
/* 
   Purpose : To list and search approve billing.
	Created : Nikitasa
	Date : 31-01-2017
*/

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

// role based validation
$module_access = $fun->check_role_access('37',$modules);
$smarty->assign('module',$module_access);

$keyword = $_POST['keyword'] ? $_POST['keyword'] : $_GET['keyword'];
$f_date = $_POST['f_date'] ? $_POST['f_date'] : $_GET['f_date'];  
$t_date = $_POST['t_date'] ? $_POST['t_date'] : $_GET['t_date']; 
$from_date = $fun->convert_date($f_date);
$to_date = $fun->convert_date($t_date);
$employee = $_POST['employee'] ? $_POST['employee'] : $_GET['employee'];

//post url for paging
if($_POST){
	$post_url .= '&keyword='.$keyword;
	$post_url .= '&f_date='.$f_date;
	$post_url .= '&t_date='.$t_date;
	$post_url .= '&employee='.$employee;
}

// count the total no. of records
$query = "CALL list_approve_billing('".$keyword."','".$_SESSION['user_id']."','".$employee."','".$from_date."','".$to_date."','0','0','','','".$_GET['action']."')";
try{
	if(!$result = $mysql->execute_query($query)){
		throw new Exception('Problem in executing list approve billing page');
	}

	// fetch result
	$data_num = $mysql->display_result($result);

	// count result
	$count = $data_num['total'];
	if($count == 0){
		$alert_msg = 'This details are not in our database';
	}
	$page = $_GET['page'] ?  $_GET['page'] : 1;
	$limit = 15;

   include('paging_call.php');	
	// free the memory
	$mysql->clear_result($result);
	// execute next query
	$mysql->next_query();
}catch(Exception $e){
	echo 'Caught exception: ',  $e->getMessage(), "\n";
}

// set the condition to check ascending or descending order		
$order = ($_GET['order'] == 'desc') ? 'asc' :  'desc';	
$sort_fields = array('1' => 'employee','recruiter','ac_holder','job_title','client','billing_amount','billing_date','candidate','created');
$org_fields = array('1' => 'employee','recruiter','ac_holder','job_title','client_name','billing_amount','billing_date','candidate_name','created_date');

// to set the sorting image
foreach($sort_fields as $key => $b_field){
	if($b_field != $_GET['field']){ 
		$smarty->assign('sort_field_'.$b_field,'sorting');
	}else{	
		$order_img = ($_GET['order'] == 'asc') ? 'sorting desc' :  'sorting asc';
		$smarty->assign('sort_field_'.$b_field,$order_img);
	}			
}
// if no fields are set, set default sort image
if(empty($_GET['field'])){		
	$order = 'desc';			
	$field = 'ib.created_date';			
	$smarty->assign('sort_field_created_date', 'sorting desc');
}	
$smarty->assign('order', $order);
// set the original field for the sql query
if($search_key = array_search($_GET['field'], $sort_fields)){
	$field =  $org_fields[$search_key];
}

// fetch all records
$query =  "CALL list_approve_billing('".$keyword."','".$_SESSION['user_id']."','".$employee."','".$from_date."','".$to_date."','$start','$limit','".$field."','".$order."','".$_GET['action']."')";
try{
	if(!$result = $mysql->execute_query($query)){
		throw new Exception('Problem in executing list approve billing page');
	}
	// calling mysql fetch_result function
	$i = '0'; 
	while($obj = $mysql->display_result($result))
	{
 		$data[] = $obj;
 		$data[$i]['billing_date'] = $fun->convert_date_to_display($obj['billing_date']);
 		$data[$i]['status_check'] = $obj['st_status'];
 		$data[$i]['status'] = $fun->format_status($obj['st_status'],$obj['st_created'],$obj['st_user'],$obj['st_modified']);
		$data[$i]['pending_status'] = $fun->billing_status($obj['st_status']);
 		$data[$i]['created_date'] = $fun->convert_date_to_display($obj['created_date']);
 		$data[$i]['pending'] = $fun->time_diff($obj['created_date'], $ago_str=0, 0);		
 		$i++;
 		$pno[]=$paging->print_no();
 		$smarty->assign('pno',$pno);
	}
	
	// get current date 
	$current_date = $fun->display_date();
	// call to export the excel data
	if($_GET['action'] == 'export'){ 
		include('classes/class.excel.php');
		$excelObj = new libExcel();
		// function to print the excel header
      $excelObj->printHeader($header = array('Candidate Name','Position','Client Name','Billing Amount','Billing Date','recruiter','ac_holder','Created Date','Status','Pending') ,$col = array('A','B','C','D','E','F','G','H','I','J'));  
		// function to print the excel data
		$excelObj->printCell($data, $count,$col = array('A','B','C','D','E','F','G','H','I','J'), $field = array('candidate_name','job_title','client_name','billing_amount','billing_date','recruiter','ac_holder','created_date','pending_status','pending'),'Approve Billing_'.$current_date);
	}	
	
	// check the status of the billing for approval
	if(!empty($data)){
		foreach($data as $record){
			$mysql->next_query();
			$show_st = $mysql->auth_billing_action($record['id'], $record['status_id']);
			$status_id[] = $show_st;				
		}
	}
	$smarty->assign('show_status', $status_id);

	// approve or reject validation
	if($_GET['status'] == 'Approved' || $_GET['status'] == 'Rejected'){
 		$success_msg = 'Billing ' . ucfirst($_GET['status']) . ' Successfully';
	}	

	// validating pagination
	$total_pages = ceil($count / $limit);

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
	$smarty->assign('emp_name',$emp_name);
	// free the memory
	$mysql->clear_result($result);
	// call the next result
	$mysql->next_query();
}catch(Exception $e){
	echo 'Caught exception: ',  $e->getMessage(), "\n";
} 

// calling mysql close db connection function
$c_c = $mysql->close_connection();
$paging->posturl($post_url);

// assign smarty variables here
$smarty->assign('page_links',$paging->print_link_frontend());

$smarty->assign('data', $data);
$smarty->assign('page' , $page); 
$smarty->assign('total_pages' , $total_pages); 	
$smarty->assign('keyword' , $keyword); 	
$smarty->assign('f_date', $f_date);
$smarty->assign('t_date', $t_date);
$smarty->assign('employee' , $employee); 
$smarty->assign('ALERT_MSG', $alert_msg);
$smarty->assign('SUCCESS_MSG', $success_msg);
// assign page title
$smarty->assign('page_title' , 'Approve Billing - Manage Hiring');  
// assigning active class status to smarty menu.tpl
$smarty->assign('billings_active','active');
 
// display smarty file
$smarty->display('approve_billing.tpl');
?>