<?php
/* 
Purpose : To import salary.
Created : Nikitasa
Date : 13-11-2017
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

// to download template file
if($_GET['action'] == 'download'){
	$path = 'uploads/import/template/Salary Template.xls';
	$fun->download_file($path);
}

$attachmentuploadErr = '';
if($_SERVER["REQUEST_METHOD"] == "POST"){

	// validating the required fields
	if(!isset($_POST['salary']) && empty($_FILES['salary']['name'])){
		$smarty->assign('salaryErr', 'Please upload the excel');	
		$test = 'error';			
	}
	
	$req_size =  5242880;

	// upload the file if attached
	if(!empty($_FILES['salary']['name'])){
		// upload directory
		$uploaddir = 'uploads/import/'; 
		$attachmentsize = $_FILES['salary']['size'];
		$attachmenttype = pathinfo($_FILES['salary']['name']);
		$extension = $attachmenttype['extension'];	
		// file extensions
		$extensions = array('xls'); 
		
		// checking the file extension is doc,docx
		if($fun->extension_validation($extension,$extensions) == true){		
			$attachmentuploadErr = 'Attachment must be .xls';
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
	
	// assigning the date
	$date =  $fun->current_date();
	
	if(empty($test)){ 
		//update the attached file
		if(!empty($_FILES['salary']['name'])){			
			// upload the file
			$prefix = substr(time(), 2,5).rand(1000,10000000).'_';
			$new_file = $prefix.$_FILES['salary']['name'];
			$path = $uploaddir.$new_file;
			move_uploaded_file($_FILES['salary']['tmp_name'], $path);
						
			
			// fetch the attached data 
			// add excel class
			include('classes/class.excel.php');
			$excelObj = new libExcel();
			$salary_data = $excelObj->read_data($uploaddir.$new_file);
			// assigning the date
			$created_date =  $fun->current_date();

			// iterate the holidays data
			foreach($salary_data as  $key => $salary){ 
				if($key > 1 && $salary['A'] != ''){
					$employee = $mysql->real_escape_str($salary['A']);
					$ctc = $mysql->real_escape_str($salary['D']);
					$emplyee_list .= $employee."<br>";
					
				
					// echo $sal_diff_co;
					$query = "CALL get_emp_id_byname('".$employee."')";
					try{
						if(!$result = $mysql->execute_query($query)){
							throw new Exception('Problem in getting emp details');
						}
						$row = $mysql->display_result($result);
						$emp_id = $row['id'];
						// free the memory
						$mysql->clear_result($result);
						// call the next result
						$mysql->next_query();
					}catch(Exception $e){
						echo 'Caught exception: ',  $e->getMessage(), "\n";
					}
						
					if($emp_id != ''){
						
						// split the date with explode
						$from_orderdate = explode('-', $salary['B']);
						$from_month = $from_orderdate[0];
						$from_year   = $from_orderdate[1];
						
						$to_orderdate = explode('-', $salary['C']);
						$to_month = $to_orderdate[0];
						$to_year   = $to_orderdate[1];					
					
						// using function get the numeric value
						$salary_from_date = date("Ym", strtotime($from_year.'-'.$from_month.'-01'));
						$salary_to_date = date("Ym", strtotime($to_year.'-'.$to_month.'-01'));

						
						// get salary date diff
						$sal_diff_co = $mysql->diff_date_salary($salary_from_date,$salary_to_date);
						$sal_diff_co += 1; 
						
						// get excel salary from date
						$salary_format_date = date("Y-m-d", strtotime($from_year.'-'.$from_month.'-01'));
						
						for($i = 0; $i < $sal_diff_co; $i++){
							
							$salary_date = date('Y-m-01',strtotime($salary_format_date. ", +$i month"));

							// query to check whether it is exist or not. 
							$query = "CALL check_salary_exist('$emp_id','$salary_date')";
							// Calling the function that makes the insert
							try{
								// calling mysql exe_query function
								if(!$result = $mysql->execute_query($query)){
									throw new Exception('Problem in executing to check emp salary exist');
								}
								$check = $mysql->display_result($result);
								// free the memory
								$mysql->clear_result($result);
								// call the next result
								$mysql->next_query();
							}catch(Exception $e){
								echo 'Caught exception: ',  $e->getMessage(), "\n";
							}
								
						
							if($check['id'] == ''){	
								// query to import the file
								$query = "CALL add_salary('".$emp_id."','".$salary_date."','".$ctc."','".$created_date."','".$_SESSION['user_id']."')";
								try{
									if(!$result = $mysql->execute_query($query)){
										throw new Exception('Problem in adding salary');
									}
									$row = $mysql->display_result($result);
									$last_id = $row['inserted_id'];
						
									// call the next result
									$mysql->next_query();
								}catch(Exception $e){
									echo 'Caught exception: ',  $e->getMessage(), "\n";die;
								}
							}else{
								// query to import the file
								$query = "CALL edit_salary('".$emp_id."','".$salary_date."','".$ctc."','".$created_date."','".$_SESSION['user_id']."')";
								try{
									if(!$result = $mysql->execute_query($query)){
										throw new Exception('Problem in editing salary');
									}
									$row = $mysql->display_result($result);
									$affected_rows = $row['affected_rows'];
						
									// call the next result
									$mysql->next_query();
								}catch(Exception $e){
									echo 'Caught exception: ',  $e->getMessage(), "\n";die;
								}
								
							}
						}
						
					}
							
				}
			}
			
			$success = '0';
			if($affected_rows != ''){
				// query to fetch admin details. 
				$query = "CALL get_admin_director_details('A','".$_SESSION['user_id']."')";
				try{
					// calling mysql exe_query function
					if(!$result = $mysql->execute_query($query)){
						throw new Exception('Problem in getting employee details');
					}
					$obj_admin = $mysql->display_result($result);
					$user_name = $obj_admin['user_name'];
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
				
				$modified_date = $fun->convert_date_time_display($created_date);
				// send mail to Director
				if(!empty($row_account)){
					// send mail to Director				
					$sub = "Manage Hiring - Salary details updated by " .$user_name;
					$msg = $content->get_edit_salary_details($_POST,$user_name,$row_account['approval_name'],$emplyee_list,$modified_date);
					$mailer->send_mail($sub,$msg,$user_name,$user_email_id,$row_account['approval_name'],$row_account['email_id']);	
					$success = '1';
				}
				
			}

			if(!empty($last_id) || (!empty($affected_rows) && $success == '1' )){
				$smarty->assign('form_sent' , 1);
			}
		}
	}
}

// closing mysql
$mysql->close_connection();
$url = 'salary.php?status=updated';	
$smarty->assign('page_redirect', $url);  

// assign page title
$smarty->assign('page_title' , 'Import Salary- Manage Hiring');  
// display smarty file
$smarty->display('add_salary.tpl');
?>
