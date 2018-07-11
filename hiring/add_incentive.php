<?php
/* 
Purpose : To add incentive.
Created : Nikitasa
Date : 22-01-2017
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
$module_access = $fun->check_role_access('13',$modules);
$smarty->assign('module',$module_access);

$inc_id = $_GET['id'];
$emp_id = $_GET['emp_id'];

// regenerate incentive
if($_GET['action'] == 'regenerate'){	
	// query to fetch approval user id. 
	$query = "CALL view_incentive_details('".$_GET['id']."','".$_GET['emp_id']."')";
	try{
		// calling mysql exe_query function
		if(!$result = $mysql->execute_query($query)){
			throw new Exception('Problem in getting incentive details');
		}
		
		$incentive_details = $mysql->display_result($result);
		if($incentive_details['incentive_type'] == 'I'){
			$incent_period_display = date('M, Y', strtotime($incentive_details['period']));
		}else{
			$explode_year = explode('-', $incentive_details['period']); 
			$display = $explode_year[1] == '10' ? 'Oct - Mar, '.date('Y', strtotime($incentive_details['period'])) : 'Apr - Sep, '.date('Y', strtotime($incentive_details['period']));
			$incent_period_display = $display;
		}
		$smarty->assign('incent_period_display',$incent_period_display);
		$smarty->assign('incentive_type',$fun->check_incentive_type($incentive_details['incentive_type']));
		$smarty->assign('incentive_details',$incentive_details);
		// free the memory
		$mysql->clear_result($result);
		// call the next result
		$mysql->next_query();
	}catch(Exception $e){
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}	
	
	// fetching L1 details
	$bh_role_id = '39';
	// query to fetch approval user id. 
	$query = "CALL get_inc_approval_user('".$bh_role_id."')";
	try{
		// calling mysql exe_query function
		if(!$result = $mysql->execute_query($query)){
			throw new Exception('Problem in getting approval user details');
		}
		$row = $mysql->display_result($result);
		$approval_id = $row['approval_id'];
		$level1_email = $row['email_id'];
		$level1_name = $row['approval_name'];
		$smarty->assign('approval_id',$row['approval_id']);
		// free the memory
		$mysql->clear_result($result);
		// call the next result
		$mysql->next_query();
	}catch(Exception $e){
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
	
	// assigning the date
	$created_date =  $fun->current_date();
		
	if(!empty($_POST)){
		
		if($incentive_details['incentive_type'] == 'I'){
			$no_days = date('t', strtotime($incentive_details['period']));
			
			// get the user leaves
			$year_month = date('Y-m', strtotime($incentive_details['period']));
			$query = "CALL get_user_leaves('".$emp_id."','".$year_month."')";
			// Calling the function that makes the fetch
			try{
				// calling mysql exe_query function
				if(!$result = $mysql->execute_query($query)){
					throw new Exception('Problem in getting user leave details');
				}
				while($leave_row = $mysql->display_result($result)){
					$leave_from = $leave_row['leave_from'];
					$leave_to = $leave_row['leave_to'];					
					$leave_from_split = explode('-', $leave_from);
					$leave_to_split = explode('-', $leave_to);
					// find the diff b/w days
					$diff = $leave_to_split[2] - $leave_from_split[2];
					$leave_day = $leave_from_split[2];
					for($k = 0; $k <= $diff; $k++){
						$leave_data[] = $leave_from_split[0].'-'.$leave_from_split[1].'-'.$leave_day++;
					}
				}
				$unique_leave = array_unique($leave_data);
				// check leave
				$leave_days = count($unique_leave);
				// free the memory
				$mysql->clear_result($result);
				// next query execution
				$mysql->next_query();								
			}catch(Exception $e){
				echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
			
		
			// iterate the days
			for($i = 1; $i <= $no_days; $i++){					
				$j = $i < 10 ? '0'.$i : $i;
						
				$date = date('Y-m-d', strtotime($year_month.'-'.$j));		

				// query to fetch employee position details. 
				$query = "CALL get_inc_emp_position_ctc('".$emp_id."', '".$date."')";
				try{
					// calling mysql exe_query function
					if(!$result = $mysql->execute_query($query)){
						throw new Exception('Problem in getting employee position details');
					}
					$row = $mysql->display_result($result);
					$ctc = $row['candidate_ctc'];
					// free the memory
					$mysql->clear_result($result);
					// next query execution
					$mysql->next_query();						
					// get the no. of requirements to send for that position ctc
					$query = "CALL get_resume_send('".$ctc."')";
					// Calling the function that makes the insert
					try{
						// calling mysql exe_query function
						if(!$result = $mysql->execute_query($query)){
							throw new Exception('Problem in getting CTC for the Positions');
						}
						$row = $mysql->display_result($result);	
						$expected_cv = $row['no_resumes'];
						// free the memory
						$mysql->clear_result($result);
						// next query execution
						$mysql->next_query();
						// get the actual sent CVs
						$query = "CALL get_resume_actual_send('".$emp_id."','".$date."')";
						// Calling the function that makes the fetch
						try{
							// calling mysql exe_query function
							if(!$result = $mysql->execute_query($query)){
								throw new Exception('Problem in getting actual sent cvs details');
							}
							$row = $mysql->display_result($result);
							$actual_cv = $row['total_sent'];
							$work_percent = ($actual_cv/$expected_cv)*100;
							$work_percent = round($work_percent, 1);
							$work_percent_day[$emp_name][][$date] = $work_percent;
								
							$work_avg += $work_percent;
							// $work_percent_day[$emp_id][$date] = $work_percent;
							// free the memory
							$mysql->clear_result($result);
							// next query execution
							$mysql->next_query();
								
						}catch(Exception $e){
							echo 'Caught exception: ',  $e->getMessage(), "\n";
						}
						
					}catch(Exception $e){
						echo 'Caught exception: ',  $e->getMessage(), "\n";
					}
						
				}catch(Exception $e){
					echo 'Caught exception: ',  $e->getMessage(), "\n";
				}
									
			}
			$work_days = $no_days - $leave_days;
			$avg[$emp_id][] = round(($work_days/$no_days)*$work_avg, 1); 
			$work_avg = '';					
			

			// check if percentage >= 100 and calculate incentive
			foreach($avg as $id => $avg_rec){
				$avg_user = $avg_rec[0];
				// get the interview sent candidates Position CTC for the month
				$query = "CALL get_candidate_interview('".$id."','".$year_month."')";
				try{
					// calling mysql exe_query function
					if(!$result_candi = $mysql->execute_query($query)){
						throw new Exception('Problem in getting candidates interview details');
					}							
					$n = 0;
					while($int_candidates = $mysql->display_result($result_candi)){ 
						$ctc = $int_candidates['ctc'];
						if($avg_user >= 100){	
							if($n == 0){
								$mysql->clear_result($result);
								$mysql->next_query();
							}
							$n++;
							// get the incentive amount for the position CTC from eligibility table
							$query = "CALL get_incentive_amount_ctc('".$ctc."','R','M','PI')";	 								
							try{
								// calling mysql exe_query function
								if(!$result2 = $mysql->execute_query($query)){
									throw new Exception('Problem in getting incentive amount details');
								}
								$row = $mysql->display_result($result2);
								$incentive_amount += $row['amount'];
								// get the incentive amount for the position CTC
								// free the memory
								$mysql->clear_result($result2);
								// next query execution
								$mysql->next_query();
								
								// delete record details
								$query = "CALL delete_candidate_interview('".$int_candidates['id']."')";
								try{
									if(!$result = $mysql->execute_query($query)){
										throw new Exception('Problem in deleting candidate interview details');
									} 
									// free the memory
									$mysql->clear_result($result2);
									// next query execution
									$mysql->next_query();									
								}catch(Exception $e){
									echo 'Caught exception: ',  $e->getMessage(), "\n";
								}
					
								// save the candidate interview details
								$query = "CALL save_candidate_interview('".$year_month.'-01'."','".$int_candidates['id']."','".$created_date."')";							
								try{
									// calling mysql exe_query function
									if(!$result = $mysql->execute_query($query)){
										throw new Exception('Problem in saving the candidate interview details');
									}
									$row = $mysql->display_result($result2);
									// free the memory
									$mysql->clear_result($result);
									// next query execution
									$mysql->next_query();											
								}catch(Exception $e){
									echo 'Caught exception: ',  $e->getMessage(), "\n";
								}
							}catch(Exception $e){
								echo 'Caught exception: ',  $e->getMessage(), "\n";
							}
						}		
					}
							
							
					$ctc = '';
					$mysql->clear_result($result_candi);
							
					if(empty($n)){ 
						$mysql->clear_result($result);
						$mysql->next_query();
					}
							
					if($incentive_amount != '' and $incentive_amount != '0'){
						// save the incentive details of the candidates	
						$query = "CALL edit_candidate_incentive('".$inc_id."','N','".$emp_id."','".$incentive_amount."','".$_SESSION['user_id']."','".$created_date."','','','".$avg_user."','".$n."','')";
						try{
							// calling mysql exe_query function
							if(!$result = $mysql->execute_query($query)){
								throw new Exception('Problem in saving the incentive details');
							}
							$row = $mysql->display_result($result);
							$affected_rows = $row['affected_rows'];
							// free the memory
							$mysql->clear_result($result);
							// next query execution
							$mysql->next_query();
						}catch(Exception $e){
							echo 'Caught exception: ',  $e->getMessage(), "\n";
						}
						$incentive_amount = '';
						
						// delete record details
						$query = "CALL delete_inc_reward_status('".$inc_id."')";
						try{
							if(!$result = $mysql->execute_query($query)){
								throw new Exception('Problem in deleting inc status');
							}  
						}catch(Exception $e){
							echo 'Caught exception: ',  $e->getMessage(), "\n";
						}
						
						// delete record details
						$query = "CALL delete_inc_reward_users('".$inc_id."')";
						try{
							if(!$result = $mysql->execute_query($query)){
								throw new Exception('Problem in deleting inc users');
							}  
						}catch(Exception $e){
							echo 'Caught exception: ',  $e->getMessage(), "\n";
						}
						
						// query to insert reward user status details.
						$query = "CALL add_inc_reward_status('".$created_date."','".$inc_id."','".$approval_id."')";
						// Calling the function that makes the insert
						try{
							// calling mysql exe_query function
							if(!$result = $mysql->execute_query($query)){
								throw new Exception('Problem in adding reward user status');
							}
							$row = $mysql->display_result($result);			
							// free the memory
							$mysql->clear_result($result);
							// call the next result
							$mysql->next_query();
						}catch(Exception $e){
							echo 'Caught exception: ',  $e->getMessage(), "\n";
						}
						
						// query to insert reward user details.  
						$query = "CALL add_inc_reward_users('".$inc_id."', '".$approval_id."')";
						// Calling the function that makes the insert
						try{
							// calling mysql exe_query function
							if(!$result = $mysql->execute_query($query)){
								throw new Exception('Problem in adding reward user');
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
					}
				}catch(Exception $e){
						echo 'Caught exception: ',  $e->getMessage(), "\n";
				}
			}
			
				
			// get the incentive user details
			$query = "CALL get_incentive_user('".$inc_id."','".$emp_id."')";
			try{
				if(!$result = $mysql->execute_query($query)){
					throw new Exception('Problem in getting the incentive user details');
				}
				// calling mysql fetch_result function
				while($incentive_user_details[] = $mysql->display_result($result)){
				}
				// free the memory
				$mysql->clear_result($result);
				// call the next result
				$mysql->next_query();
			}catch(Exception $e){
				echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
			
			// get the admin user details
			$query = "CALL get_approval_user_by_id('".$_SESSION['user_id']."')";
			try{
				if(!$result = $mysql->execute_query($query)){
					throw new Exception('Problem in getting admin user details');
				}
				// calling mysql fetch_result function
				$obj = $mysql->display_result($result);
				$admin_email = $obj['approval_email'];						
				$admin_name = ucwords($obj['approval_user']);
				// free the memory
				$mysql->clear_result($result);
				// call the next result
				$mysql->next_query();
			}catch(Exception $e){
				echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
						
			// get approval user details
			$approval_user_name = ucwords($_POST['approval_user_name']);
			$approval_user_email = $_POST['approval_user_email'];

			// get incentive months and year
			$incent_period_display = date('M, Y', strtotime($incentive_details['period']));	
			
			if(!empty($affected_rows)){
				// send mail to L1 
				$sub = "Manage Hiring -  Incentive -  ".$fun->check_incentive_tp($incentive_details['incentive_type']).",  ".$incent_period_display." Created By ".$admin_name;	
				$msg = $content->get_level1_incentive_details($incentive_user_details,$_POST,$admin_name,$level1_name);
				$mailer->send_mail($sub,$msg,$admin_name,$admin_email,$level1_name,$level1_email);
				header("Location: approve_incentive.php?status=Created");
			}
		}else if($incentive_details['incentive_type'] == 'J'){
			
			$no_days = date('t', strtotime($incentive_details['period']).'-01');
			$year_month = date('Y-m', strtotime($incentive_details['period']));	
			// query to get sharing percentage 
			$query = 'CALL get_sharing()';
			// Calling the function that makes the insert
			try{
				// calling mysql exe_query function
				if(!$result = $mysql->execute_query($query)){
					throw new Exception('Problem in getting sharing percentage');
				}
				while($percent = $mysql->display_result($result)){
					$sharing_percent[] = $percent;
				}
				// free the memory
				$mysql->clear_result($result);
				// call the next result
				$mysql->next_query();
			}catch(Exception $e){
				echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
				
			$year_month = date('Y-m', strtotime($incentive_details['period']));	
			$year_month2 = date('Y-m', strtotime(date("Y-m", strtotime($incentive_details['period']) . " +5 month")));
				
			$employee_sal = '';
			// get the incentive amount for the position CTC from eligibility table
			$query = "CALL get_employee_salary('".$emp_id."','".$year_month."','".$year_month2."')";
			try{
				// calling mysql exe_query function
				if(!$result = $mysql->execute_query($query)){
					throw new Exception('Problem in getting employee salary details');
				}
				while($row_sal = $mysql->display_result($result)){
					$employee_sal += $row_sal['employee_salary'];
				}
				// free the memory
				$mysql->clear_result($result);
				// next query execution
				$mysql->next_query();
			}catch(Exception $e){
				echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
			
			// get employee billing details
			$query = "CALL get_inc_emp_billing_ctc('".$emp_id."','".$year_month."','".$year_month2."')";
						
			try{
				// calling mysql exe_query function
				if(!$result = $mysql->execute_query($query)){
					throw new Exception('Problem in getting employee billing details');
				}
				$indiv_ah_percent = '';
				$rec_billing = '';
				$ah_billing  = '';
				$billing_amt = '';
				$total_billing = '';
				$employee_salary = '';
				$bill_ctc = '';
				$req_ctc = '';
				$incentive_target = '';								
				$incentive_amount = '';
																
				while($ctc_row = $mysql->display_result($result)){							
					$bill_ctc[] = $ctc_row['bill_ctc'];
					$req_ctc[] = $ctc_row['req_ctc'];
					$ah_id = $ctc_row['account_holder_id'];
					$rec_id = $ctc_row['recruiter_id'];
					$req_resume = $ctc_row['req_resume'];
					// explode the account holder for account holder percentage calculation
									
					$ah_split_id = explode(',', $ah_id);
					$indiv_ah_percent = round($sharing_percent[1]['percent']/$count_ah, 1);
					foreach($ah_split_id as $ah_new){
						if($ah_new == $emp_id){
							$ah_billing = round($ctc_row['bill_ctc'] * ($indiv_ah_percent/100), 1);
							$bill_user_type[] = 'AH';
						}
					}
					// for recruiter percentage calculation
					if($rec_id == $emp_id){
						$rec_billing = round($ctc_row['bill_ctc'] * ($sharing_percent[0]['percent']/100), 1);
						$bill_user_type[] = 'R';
					}
					$total_billing += $ah_billing + $rec_billing;						
				}						
												
				// free the memory
				$mysql->clear_result($result);
				// next query execution
				$mysql->next_query();							
				// get the employee salary
				// calculate incentive if eligible
				$incentive_target = $employee_sal * 3;
				// $total_billing ; echo '<br>';
				if($total_billing >= $incentive_target && !empty($employee_salary)){
					// iterate all the values in the bill
					foreach($req_ctc as $key => $pos_ctc){
						// get the incentive amount for the position CTC from eligibility table
						$query = "CALL get_incentive_amount_ctc('".$pos_ctc."','".$bill_user_type[$key]."','H','PC')";						
						try{
								// calling mysql exe_query function
							if(!$result = $mysql->execute_query($query)){
								throw new Exception('Problem in getting CTC for the Positions');
							}
							$row_ctc = $mysql->display_result($result);
							$incentive_amount += $row_ctc['amount'];
							// free the memory
							$mysql->clear_result($result);
							// next query execution
							$mysql->next_query();
							
							// delete record details
							$query = "CALL delete_candidate_interview('".$int_candidates['id']."')";
							try{
								if(!$result = $mysql->execute_query($query)){
									throw new Exception('Problem in deleting candidate interview details');
								} 
								// free the memory
								$mysql->clear_result($result2);
								// next query execution
								$mysql->next_query();									
							}catch(Exception $e){
								echo 'Caught exception: ',  $e->getMessage(), "\n";
							}
								
							// save the candidate billing details
							$query = "CALL save_candidate_billing('".$year_month.'-01'."','".$req_resume."','".$created_date."','".$row_ctc['amount']."','".$bill_user_type[$key]."')";
							try{
								// calling mysql exe_query function
								if(!$result = $mysql->execute_query($query)){
									throw new Exception('Problem in saving the candidate interview details');
								}
								$row = $mysql->display_result($result2);
								// free the memory
								$mysql->clear_result($result);
								// next query execution
								$mysql->next_query();											
							}catch(Exception $e){
								echo 'Caught exception: ',  $e->getMessage(), "\n";
							}
						}catch(Exception $e){
							echo 'Caught exception: ',  $e->getMessage(), "\n";
						}
					}							
				}
						
			}catch(Exception $e){
				echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
			// free the memory
			$mysql->clear_result($result);
			// next query execution
			$mysql->next_query();

			// save the incentive details of the candidates
			$query = "CALL edit_candidate_incentive('".$inc_id."','N','".$emp_id."','".$incentive_amount."','".$_SESSION['user_id']."','".$created_date."','".$incentive_target."','".$total_billing."','','','".count($bill_ctc)."')";			
			try{
				// calling mysql exe_query function
				if(!$result = $mysql->execute_query($query)){
					throw new Exception('Problem in saving the incentive details');
				}
				$row = $mysql->display_result($result);
				$last_id = $row['inserted_id'];
				$incentive_id = $last_id;
				// free the memory
				$mysql->clear_result($result);
				// next query execution
				$mysql->next_query();
			}catch(Exception $e){
				echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
						
			// delete record details
			$query = "CALL delete_inc_reward_status('".$inc_id."')";
			try{
				if(!$result = $mysql->execute_query($query)){
					throw new Exception('Problem in deleting inc status');
				}  
			}catch(Exception $e){
				echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
					
			// delete record details
			$query = "CALL delete_inc_reward_users('".$inc_id."')";
			try{
				if(!$result = $mysql->execute_query($query)){
					throw new Exception('Problem in deleting inc users');
				}  
			}catch(Exception $e){
				echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
					
			// query to insert reward user status details.
			$query = "CALL add_inc_reward_status('".$created_date."','".$inc_id."','".$approval_id."')";
			// Calling the function that makes the insert
			try{
				// calling mysql exe_query function
				if(!$result = $mysql->execute_query($query)){
					throw new Exception('Problem in adding reward user status');
				}
				$row = $mysql->display_result($result);			
				// free the memory
				$mysql->clear_result($result);
				// call the next result
				$mysql->next_query();
			}catch(Exception $e){
				echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
						
			// query to insert reward user details.  
			$query = "CALL add_inc_reward_users('".$inc_id."', '".$approval_id."')";
			// Calling the function that makes the insert
			try{
				// calling mysql exe_query function
				if(!$result = $mysql->execute_query($query)){
					throw new Exception('Problem in adding reward user');
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
		
			$inc_month++;	
			$year_month = date('Y-m', strtotime($year_month . "+1 months"));					
				
			// get the incentive user details
			$query = "CALL get_incentive_user_details('".$inc_id."','".$emp_id."')";
			try{
				if(!$result = $mysql->execute_query($query)){
					throw new Exception('Problem in getting the incentive user details');
				}
				// calling mysql fetch_result function
				$incentive_user_details = $mysql->display_result($result);
				// free the memory
				$mysql->clear_result($result);
				// call the next result
				$mysql->next_query();
			}catch(Exception $e){
				echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
			
			// get the L1 user details
			$query = "CALL get_approval_user_by_id('".$_SESSION['user_id']."')";
			try{
				if(!$result = $mysql->execute_query($query)){
					throw new Exception('Problem in getting approval user details');
				}
				// calling mysql fetch_result function
				$obj = $mysql->display_result($result);
				$admin_email = $obj['approval_email'];						
				$admin_name = ucwords($obj['approval_user']);
				// free the memory
				$mysql->clear_result($result);
				// call the next result
				$mysql->next_query();
			}catch(Exception $e){
				echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
						
			// get approval user details
			$approval_user_name = ucwords($_POST['approval_user_name']);
			$approval_user_email = $_POST['approval_user_email'];

			// get incentive months and year
			$incent_period_display = date('M, Y', strtotime($incentive_details['period']));	
			
			if(!empty($affected_rows)){
				// send mail to L1 
				$sub = "Manage Hiring -  Incentive -  ".$fun->check_incentive_tp($incentive_details['incentive_type']).",  ".$incent_period_display." Created By ".$admin_name;
				$msg = $content->get_level1_incentive_details($incentive_user_details,$_POST,$admin_name,$level1_name);
				$mailer->send_mail($sub,$msg,$admin_name,$admin_email,$level1_name,$level1_email);
				
				header("Location: approve_incentive.php?status=Created");
			}
		}
	}
}

if(!empty($_POST)){		
	// error message validation
	if(!empty($_POST['type']) && $_POST['type'] == 'I'){
		if(empty($_POST['ps_month']) && empty($_POST['ps_year'])){
			$smarty->assign('ps_monthErr','Please select the incentive month');
			$smarty->assign('ps_yearErr','Please select the incentive year');
			$test = 'error';
		}else if(empty($_POST['ps_year'])){
			$smarty->assign('ps_yearErr','Please select the incentive year');
			$test = 'error';
		}else if(empty($_POST['ps_month'])){
			$smarty->assign('ps_monthErr','Please select the incentive month');
			$test = 'error';
		}
	}else if(!empty($_POST['type']) && $_POST['type'] == 'J'){
		if(empty($_POST['position_month']) && empty($_POST['position_month'])){
			$smarty->assign('position_monthErr','Please select the incentive month');
			$smarty->assign('yearErr','Please select the incentive year');
			$test = 'error';
		}else if(empty($_POST['year'])){
			$smarty->assign('yearErr','Please select the incentive year');
			$test = 'error';
		}else if(empty($_POST['position_month'])){
			$smarty->assign('position_monthErr','Please select the incentive month');
			$test = 'error';
		}
	}else{
		$smarty->assign('typeErr','Please select the incentive type');
		$test = 'error';
	}
	
	/*
	// array for printing correct field name in error message
	$fieldtype = array('1', '1','1', '1','1');
	$actualfield = array('incentive type ', 'incentive month','incentive year','incentive month','incentive year');
	$field = array('type' => 'typeErr', 'position_month' => 'position_monthErr','year' => 'yearErr','ps_month' => 'ps_monthErr', 'ps_year' => 'ps_yearErr');
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
	*/
	// assigning the date
	$created_date =  $fun->current_date(); 

	$incentive_month = $_POST['position_month'] ? $_POST['position_month'] : $_POST['ps_month'];
	$incentive_year = $_POST['year'] ? $_POST['year'] : $_POST['ps_year'];
	
	//$test = '';
	
	if(empty($test)){
		// fetching L1 details
		$bh_role_id = '39';
		// query to fetch approval user id. 
		$query = "CALL get_inc_approval_user('".$bh_role_id."')";
		try{
			// calling mysql exe_query function
			if(!$result = $mysql->execute_query($query)){
				throw new Exception('Problem in getting approval user details');
			}
			$row = $mysql->display_result($result);
			$approval_id = $row['approval_id'];
			$level1_email = $row['email_id'];
			$level1_name = $row['approval_name'];
			$smarty->assign('approval_id',$row['approval_id']);
			// free the memory
			$mysql->clear_result($result);
			// call the next result
			$mysql->next_query();
		}catch(Exception $e){
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
						
		if($_POST['type'] == 'I'){
			
			// query to fetch employee for incentive.		
			$query = "CALL get_employee()";
			// Calling the function that makes the fetch
			try{
				// calling mysql exe_query function
				if(!$result = $mysql->execute_query($query)){
					throw new Exception('Problem in getting employee details');
				}
				while($row_employee[] = $mysql->display_result($result)){
				}				
				// free the memory
				$mysql->clear_result($result);
				// next query execution
				$mysql->next_query();
			}catch(Exception $e){
				echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
			$no_days = date('t', strtotime($incentive_year.'-'.$incentive_month.'-01'));
			$count_emp = count($row);
			// iterate the employees
			foreach($row_employee as $record){ 
				$emp_id = $record['id'];
				$emp_name = $record['emp_name'];
				
				// get the user leaves
				$year_month = $incentive_year.'-'.$incentive_month;
				$query = "CALL get_user_leaves('".$emp_id."','".$year_month."')";
				// Calling the function that makes the fetch
				try{
					// calling mysql exe_query function
					if(!$result = $mysql->execute_query($query)){
						throw new Exception('Problem in getting user leave details');
					}
					while($leave_row = $mysql->display_result($result)){
						$leave_from = $leave_row['leave_from'];
						$leave_to = $leave_row['leave_to'];					
						$leave_from_split = explode('-', $leave_from);
						$leave_to_split = explode('-', $leave_to);
						// find the diff b/w days
						$diff = $leave_to_split[2] - $leave_from_split[2];
						$leave_day = $leave_from_split[2];
						for($k = 0; $k <= $diff; $k++){
							$leave_data[] = $leave_from_split[0].'-'.$leave_from_split[1].'-'.$leave_day++;
						}
					}
					$unique_leave = array_unique($leave_data);
					// check leave
					$leave_days = count($unique_leave);
					// free the memory
					$mysql->clear_result($result);
					// next query execution
					$mysql->next_query();								
				}catch(Exception $e){
					echo 'Caught exception: ',  $e->getMessage(), "\n";
				}
				
				
				// query to check whether it is approved or not. 
				$query = "CALL check_incentive_approved('".$emp_id."','".$mysql->real_escape_str($_POST['type'])."','".$year_month."')";
				// Calling the function that makes the insert
				try{
					// calling mysql exe_query function
					if(!$result = $mysql->execute_query($query)){
						throw new Exception('Problem in executing to check incentive approved');
					}
					$check_approved = $mysql->display_result($result);
					// free the memory
					$mysql->clear_result($result);
					// call the next result
					$mysql->next_query();
				}catch(Exception $e){
					echo 'Caught exception: ',  $e->getMessage(), "\n";
				}
						
				// query to check whether it is exist or not. 
				$query = "CALL check_incentive_exist('".$emp_id."','".$mysql->real_escape_str($_POST['type'])."','".$mysql->real_escape_str($year_month)."')";
				// Calling the function that makes the insert
				try{
					// calling mysql exe_query function
					if(!$result = $mysql->execute_query($query)){
						throw new Exception('Problem in executing to check incentive exist');
					}
					$check = $mysql->display_result($result);
					// free the memory
					$mysql->clear_result($result);
					// call the next result
					$mysql->next_query();
				}catch(Exception $e){
					echo 'Caught exception: ',  $e->getMessage(), "\n";
				}
				// if not incentive created
				if($check['id'] == ''){	
					// iterate the days
					for($i = 1; $i <= $no_days; $i++){					
						$j = $i < 10 ? '0'.$i : $i;
						
						$date = date('Y-m-d', strtotime($incentive_year.'-'.$incentive_month.'-'.$j));		

						// query to fetch employee position details. 
						$query = "CALL get_inc_emp_position_ctc('".$emp_id."', '".$date."')";
						try{
							// calling mysql exe_query function
							if(!$result = $mysql->execute_query($query)){
								throw new Exception('Problem in getting employee position details');
							}
							$row = $mysql->display_result($result);
							$ctc = $row['candidate_ctc'];
							// free the memory
							$mysql->clear_result($result);
							// next query execution
							$mysql->next_query();						
							// get the no. of requirements to send for that position ctc
							$query = "CALL get_resume_send('".$ctc."')";
							// Calling the function that makes the insert
							try{
								// calling mysql exe_query function
								if(!$result = $mysql->execute_query($query)){
									throw new Exception('Problem in getting CTC for the Positions');
								}
								$row = $mysql->display_result($result);	
								$expected_cv = $row['no_resumes'];
								// free the memory
								$mysql->clear_result($result);
								// next query execution
								$mysql->next_query();
								// get the actual sent CVs
								$query = "CALL get_resume_actual_send('".$emp_id."','".$date."')";
								// Calling the function that makes the fetch
								try{
									// calling mysql exe_query function
									if(!$result = $mysql->execute_query($query)){
										throw new Exception('Problem in getting actual sent cvs details');
									}
									$row = $mysql->display_result($result);
									$actual_cv = $row['total_sent'];
									$work_percent = ($actual_cv/$expected_cv)*100;
									$work_percent = round($work_percent, 1);
									$work_percent_day[$emp_name][][$date] = $work_percent;
								
									$work_avg += $work_percent;
									// free the memory
									$mysql->clear_result($result);
									// next query execution
									$mysql->next_query();
								
								}catch(Exception $e){
									echo 'Caught exception: ',  $e->getMessage(), "\n";
								}
						
							}catch(Exception $e){
								echo 'Caught exception: ',  $e->getMessage(), "\n";
							}
						
						}catch(Exception $e){
							echo 'Caught exception: ',  $e->getMessage(), "\n";
						}
									
					}
					$work_days = $no_days - $leave_days;
					$avg[$emp_id][] = round(($work_days/$no_days)*$work_avg, 1); 
					$work_avg = '';					
				}
			}
			
			if(($check_approved['is_approve'] != 'Y') && $check['id'] == ''){
				// check if percentage >= 100 and calculate incentive
				foreach($avg as $id => $avg_rec){
					// echo $avg_user = $avg_rec[0];echo '<br>';
					if($avg_user >= '100'){
						// get the interview sent candidates Position CTC for the month
						$query = "CALL get_candidate_interview('".$id."','".$year_month."')";
						try{
							// calling mysql exe_query function
							if(!$result_candi = $mysql->execute_query($query)){
								throw new Exception('Problem in getting candidates interview details');
							}							
							$n = 0;
							while($int_candidates = $mysql->display_result($result_candi)){ 
								 $ctc = $int_candidates['ctc'];
								if($avg_user >= 100){
									
									if($n == 0){
										$mysql->clear_result($result);
										$mysql->next_query();
									}
									$n++;
									// get the incentive amount for the position CTC from eligibility table
									$query = "CALL get_incentive_amount_ctc('".$ctc."','R','M','PI')";	 								
									try{
										// calling mysql exe_query function
										if(!$result2 = $mysql->execute_query($query)){
											throw new Exception('Problem in getting incentive amount details');
										}
										$row = $mysql->display_result($result2);
										$incentive_amount += $row['amount'];
										// get the incentive amount for the position CTC
										// free the memory
										$mysql->clear_result($result2);
										// next query execution
										$mysql->next_query();
										// save the candidate interview details
										$query = "CALL save_candidate_interview('".$year_month.'-01'."','".$int_candidates['id']."','".$created_date."')";	 								
										try{
											// calling mysql exe_query function
											if(!$result = $mysql->execute_query($query)){
												throw new Exception('Problem in saving the candidate interview details');
											}
											$row = $mysql->display_result($result2);
											// free the memory
											$mysql->clear_result($result);
											// next query execution
											$mysql->next_query();											
										}catch(Exception $e){
											echo 'Caught exception: ',  $e->getMessage(), "\n";
										}
									}catch(Exception $e){
										echo 'Caught exception: ',  $e->getMessage(), "\n";
									}
								}		
							}
							
							$ctc = '';
							$mysql->clear_result($result_candi);
							
							if(empty($n)){ 
								$mysql->clear_result($result);
								$mysql->next_query();
							}
							
							if($incentive_amount != '' and $incentive_amount != '0'){
							// save the incentive details of the candidates
							$incentive_period = date('Y-m-d', strtotime($incentive_year.'-'.$incentive_month.'-01'));	
							$query = "CALL save_candidate_incentive('".$id."','I','".$incentive_period."','".$incentive_amount."','".$_SESSION['user_id']."','".$created_date."','','',
							'".$avg_user."','".$n."','')";
								try{
									// calling mysql exe_query function
									if(!$result = $mysql->execute_query($query)){
										throw new Exception('Problem in saving the incentive details');
									}
									$row = $mysql->display_result($result);
									$last_id = $row['inserted_id'];
									$incentive_id = $last_id;
									// free the memory
									$mysql->clear_result($result);
									// next query execution
									$mysql->next_query();
								}catch(Exception $e){
									echo 'Caught exception: ',  $e->getMessage(), "\n";
								}
								$incentive_amount = '';
								
								// query to insert reward user status details.
								$query = "CALL add_inc_reward_status('".$created_date."','".$incentive_id."','".$approval_id."')";
								// Calling the function that makes the insert
								try{
									// calling mysql exe_query function
									if(!$result = $mysql->execute_query($query)){
										throw new Exception('Problem in adding reward user status');
									}
									$row = $mysql->display_result($result);			
									// free the memory
									$mysql->clear_result($result);
									// call the next result
									$mysql->next_query();
								}catch(Exception $e){
									echo 'Caught exception: ',  $e->getMessage(), "\n";
								}
								
								// query to insert reward user details.  
								$query = "CALL add_inc_reward_users('".$incentive_id."', '".$approval_id."')";
								// Calling the function that makes the insert
								try{
									// calling mysql exe_query function
									if(!$result = $mysql->execute_query($query)){
										throw new Exception('Problem in adding reward user');
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
							}
							// free the memory
							$mysql->clear_result($result);
							// next query execution
							$mysql->next_query();
						}catch(Exception $e){
							echo 'Caught exception: ',  $e->getMessage(), "\n";
						}
					}else{/* elseif(empty($last_inserted_id) && empty($incentive_id)){*/
						// redirect to incentive page
						header("Location: approve_incentive.php?status=no_incentive");
					}
				}
			}else{
				header("Location: approve_incentive.php?status=Exist");
			}
			
			// get the incentive user details
			$query = "CALL get_incentive_user_details('I')";
			try{
				if(!$result = $mysql->execute_query($query)){
					throw new Exception('Problem in getting the incentive user details');
				}
				// calling mysql fetch_result function
				while($incentive_user_details[] = $mysql->display_result($result)){
				}
				// free the memory
				$mysql->clear_result($result);
				// call the next result
				$mysql->next_query();
			}catch(Exception $e){
				echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
			
			// get the L1 user details
			$query = "CALL get_approval_user_by_id('".$_SESSION['user_id']."')";
			try{
				if(!$result = $mysql->execute_query($query)){
					throw new Exception('Problem in getting approval user details');
				}
				// calling mysql fetch_result function
				$obj = $mysql->display_result($result);
				$admin_email = $obj['approval_email'];						
				$admin_name = ucwords($obj['approval_user']);
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

			// get incentive months and year
			$ps_month = $fun->display_months($_POST['ps_month']);
			$position_month = $fun->display_pc_Months($_POST['position_month']);
			$month = $ps_month ? $ps_month : $position_month;
			$year = $_POST['year'] ? $_POST['year'] : $_POST['ps_year'];
			echo $last_inserted_id;die;	
			if(!empty($last_inserted_id)){
				// send mail to L1 
				$sub = "Manage Hiring -  Incentive -  ".$fun->check_incentive_tp($_POST['type']).",  ".$month.' '.$year." Created By ".$admin_name;
				$msg = $content->get_level1_incentive_details($incentive_user_details,$_POST,$admin_name,$level1_name);
				$mailer->send_mail($sub,$msg,$admin_name,$admin_email,$level1_name,$level1_email);
				
				header("Location: approve_incentive.php?status=Created");
			}
	}else if($_POST['type'] == 'J'){
		
			// query to fetch employee for incentive.		
			$query = "CALL get_employee()";
			// Calling the function that makes the fetch
			try{
				// calling mysql exe_query function
				if(!$result = $mysql->execute_query($query)){
					throw new Exception('Problem in getting employee details');
				}
				while($row_employee[] = $mysql->display_result($result)){
				}				
				// free the memory
				$mysql->clear_result($result);
				// next query execution
				$mysql->next_query();
			}catch(Exception $e){
				echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
			$no_days = date('t', strtotime($incentive_year.'-'.$incentive_month.'-01'));
			$count_emp = count($row);
			
			// query to get sharing percentage 
			$query = 'CALL get_sharing()';
			// Calling the function that makes the insert
			try{
				// calling mysql exe_query function
				if(!$result = $mysql->execute_query($query)){
					throw new Exception('Problem in getting sharing percentage');
				}
				while($percent = $mysql->display_result($result)){
					$sharing_percent[] = $percent;
				}
				// free the memory
				$mysql->clear_result($result);
				// call the next result
				$mysql->next_query();
			}catch(Exception $e){
				echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
			
			$year_month = $incentive_year.'-'.$incentive_month;
				
			$year_month2 = date('Y-m', strtotime(date("Y-m", strtotime($incentive_year.'-'.$incentive_month)) . " +5 month"));
			
			// iterate the employees
			foreach($row_employee as $record){ 
				$emp_id = $record['id'];
				$emp_name = $record['emp_name'];
				// $inc_month = 1;
				$employee_sal = '';
				$incentive_target = '';
				// for particular employee
				if($record['id'] == '98'){
					// query to check whether it is approved or not. 
					$query = "CALL check_incentive_approved('".$emp_id."','".$mysql->real_escape_str($_POST['type'])."','".$year_month."')";
					// Calling the function that makes the insert
					try{
						// calling mysql exe_query function
						if(!$result = $mysql->execute_query($query)){
							throw new Exception('Problem in executing to check incentive approved');
						}
						$check_approved = $mysql->display_result($result);
						// free the memory
						$mysql->clear_result($result);
						// call the next result
						$mysql->next_query();
					}catch(Exception $e){
						echo 'Caught exception: ',  $e->getMessage(), "\n";
					}
							
					// check incentive exists
					$query = "CALL check_incentive_exist('".$emp_id."','".$mysql->real_escape_str($_POST['type'])."','".$year_month."')";
					// Calling the function that makes the insert
					try{
						// calling mysql exe_query function
						if(!$result = $mysql->execute_query($query)){
							throw new Exception('Problem in executing to check incetive exist');
						}
						$check = $mysql->display_result($result);
						$total = count($check['id']);
						// free the memory
						$mysql->clear_result($result);
						// call the next result
						$mysql->next_query();
					}catch(Exception $e){
						echo 'Caught exception: ',  $e->getMessage(), "\n";
					}
					
						
					
					if(($total == 0) && ($check_approved['is_approve'] != 'Y')){
						// get the incentive amount for the position CTC from eligibility table
						$query = "CALL get_employee_salary('".$emp_id."','".$year_month."','".$year_month2."')";
					
						try{
							// calling mysql exe_query function
							if(!$result = $mysql->execute_query($query)){
								throw new Exception('Problem in getting employee salary details');
							}
							while($row_sal = $mysql->display_result($result)){
								// $employee_sal_month[$row_sal['users_id']][$row_sal['sal_month']] = $row_sal['employee_salary'];
								$employee_sal += $row_sal['employee_salary'];
							}
							$incentive_target = round($employee_sal * 3);
							// free the memory
							$mysql->clear_result($result);
							// next query execution
							$mysql->next_query();
						}catch(Exception $e){
							echo 'Caught exception: ',  $e->getMessage(), "\n";
						}
						
						
						// get monthly billing until 6 months
						//while($inc_month <= 6){
							// get employee billing details
							$query = "CALL get_inc_emp_billing_ctc('".$emp_id."','".$year_month."','".$year_month2."')";
							try{
								// calling mysql exe_query function
								if(!$result = $mysql->execute_query($query)){
									throw new Exception('Problem in getting employee billing details');
								}
									$indiv_ah_percent = '';
									$rec_billing = '';
									$ah_billing  = '';
									$billing_amt = '';
									$total_billing = '';
									$employee_salary = '';
									$bill_ctc = '';
									$req_ctc = '';
																
									$incentive_amount = '';
									$bill_user_type = '';
									$bill_date = '';	
																	
									while($ctc_row = $mysql->display_result($result)){							
										$bill_ctc[] = $ctc_row['bill_ctc'];
										$req_ctc[] = $ctc_row['req_ctc'];
										$bill_date[] = $ctc_row['bill_date'];
										
										// $role_name[] = $ctc_row['role_name'];
										$ah_id = $ctc_row['account_holder_id'];
										$rec_id = $ctc_row['recruiter_id'];
										$req_resume = $ctc_row['req_resume'];
										
										
										// explode the account holder for account holder percentage calculation
										
										$ah_split_id = explode(',', $ah_id);
										$count_ah = count($ah_split_id);
										$indiv_ah_percent = round($sharing_percent[1]['percent']/$count_ah, 1);
										foreach($ah_split_id as $ah_new){
											if($ah_new == $emp_id){
												$ah_billing = round($ctc_row['bill_ctc'] * ($indiv_ah_percent/100), 1);
												$bill_user_type[] = 'AH';
											}
										}
										
										// for recruiter percentage calculation
										if($rec_id == $emp_id){
											$rec_billing = round($ctc_row['bill_ctc'] * ($sharing_percent[0]['percent']/100), 1);
											$bill_user_type[] = 'R';
										}
										$total_billing += $ah_billing + $rec_billing;						
									}						
													
									// free the memory
									$mysql->clear_result($result);
									// next query execution
									$mysql->next_query();
									
									// get the employee salary
									// $employee_salary = $employee_sal_month[$emp_id][$year_month];
									
									// calculate incentive if eligible
									//$incentive_target = $employee_salary * 3;
									
									
									if($total_billing >= $incentive_target){
										// iterate all the values in the bill
										foreach($req_ctc as $key => $pos_ctc){
											// get the incentive amount for the position CTC from eligibility table
											$query = "CALL get_incentive_amount_ctc('".$pos_ctc."','".$bill_user_type[$key]."','H','PC')";		
											
											try{
												// calling mysql exe_query function
												if(!$result = $mysql->execute_query($query)){
														throw new Exception('Problem in getting CTC for the Positions');
												}
												$row_ctc = $mysql->display_result($result);
												$incentive_amount += $row_ctc['amount'];
												// free the memory
												$mysql->clear_result($result);
												// next query execution
												$mysql->next_query();
												// save the candidate billing details
												$query = "CALL save_candidate_billing('".$bill_date[$key]."','".$req_resume."','".$created_date."',
												'".$row_ctc['amount']."','".$bill_user_type[$key]."')";
												
												try{
													// calling mysql exe_query function
													if(!$result = $mysql->execute_query($query)){
														throw new Exception('Problem in saving the candidate interview details');
													}
													$row = $mysql->display_result($result2);
													// free the memory
													$mysql->clear_result($result);
													// next query execution
													$mysql->next_query();											
												}catch(Exception $e){
													echo 'Caught exception: ',  $e->getMessage(), "\n";
												}
											}catch(Exception $e){
												echo 'Caught exception: ',  $e->getMessage(), "\n";
											}
										}							
									}else{
										// redirect to incentive page
										header("Location: approve_incentive.php?status=no_incentive");
									}

								
								}catch(Exception $e){
									echo 'Caught exception: ',  $e->getMessage(), "\n";
								}
								// free the memory
								$mysql->clear_result($result);
								// next query execution
								$mysql->next_query();
							
							
							$date = date('Y-m-d', strtotime($year_month.'-01'));	
							if($total == '0'){						
								if($incentive_amount > 0){
									// save the incentive details of the candidates
									$query = "CALL save_candidate_incentive('".$emp_id."','J','".$date."','".$incentive_amount."','".$_SESSION['user_id']."','".$created_date."',
									'".$incentive_target."','".$total_billing."','','','".count($bill_ctc)."')";
									
									try{
										// calling mysql exe_query function
										if(!$result = $mysql->execute_query($query)){
											throw new Exception('Problem in saving the incentive details');
										}
										$row = $mysql->display_result($result);
										$last_id = $row['inserted_id'];
										$incentive_id = $last_id;
										// free the memory
										$mysql->clear_result($result);
										// next query execution
										$mysql->next_query();
									}catch(Exception $e){
										echo 'Caught exception: ',  $e->getMessage(), "\n";
									}
									
									// query to insert reward user status details.
									$query = "CALL add_inc_reward_status('".$created_date."','".$incentive_id."','".$approval_id."')";
									// Calling the function that makes the insert
									try{
										// calling mysql exe_query function
										if(!$result = $mysql->execute_query($query)){
											throw new Exception('Problem in adding reward user status');
										}
										$row = $mysql->display_result($result);			
										// free the memory
										$mysql->clear_result($result);
										// call the next result
										$mysql->next_query();
									}catch(Exception $e){
										echo 'Caught exception: ',  $e->getMessage(), "\n";
									}
									
									// query to insert reward user details.  
									$query = "CALL add_inc_reward_users('".$incentive_id."', '".$approval_id."')";
									// Calling the function that makes the insert
									try{
										// calling mysql exe_query function
										if(!$result = $mysql->execute_query($query)){
											throw new Exception('Problem in adding reward user');
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
								}
				
							}
							//$inc_month++;	
							//$year_month = date('Y-m', strtotime($year_month . "+1 months"));
						//}
							
					}else{
						// redirect to incentive page
						header("Location: approve_incentive.php?status=no_incentive");
					}
				}
			}
			// get the incentive user details
			$query = "CALL get_incentive_user_details('J')";
			try{
				if(!$result = $mysql->execute_query($query)){
					throw new Exception('Problem in getting the incentive user details');
				}
				// calling mysql fetch_result function
				while($incentive_user_details[] = $mysql->display_result($result)){
				}
				// free the memory
				$mysql->clear_result($result);
				// call the next result
				$mysql->next_query();
			}catch(Exception $e){
				echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
			
			// get the L1 user details
			$query = "CALL get_approval_user_by_id('".$_SESSION['user_id']."')";
			try{
				if(!$result = $mysql->execute_query($query)){
					throw new Exception('Problem in getting approval user details');
				}
				// calling mysql fetch_result function
				$obj = $mysql->display_result($result);
				$admin_email = $obj['approval_email'];						
				$admin_name = ucwords($obj['approval_user']);
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

			// get incentive months and year
			$ps_month = $fun->display_months($_POST['ps_month']);
			$position_month = $fun->display_pc_Months($_POST['position_month']);
			$month = $ps_month ? $ps_month : $position_month;
			$year = $_POST['year'] ? $_POST['year'] : $_POST['ps_year'];
			
			if(!empty($last_id)){
				// send mail to L1 
				$sub = "Manage Hiring -  Incentive -  ".$fun->check_incentive_tp($_POST['type']).",  ".$month.' '.$year." Created By ".$admin_name;
				$msg = $content->get_level1_incentive_details($incentive_user_details,$_POST,$admin_name,$level1_name);
				$mailer->send_mail($sub,$msg,$admin_name,$admin_email,$level1_name,$level1_email);
				header("Location: approve_incentive.php?status=Created");
			}else{
				header("Location: approve_incentive.php?status=no_incentive");
			}
			
		}

	}
}
	
// smarty drop down array for incentive type
$smarty->assign('types', array('' => 'Select', 'I' => 'Profile Short-listing & Interviewing', 'J' => 'Position Closure'));

// smarty drop down array for status
$smarty->assign('position_months', array('' => 'Month', '04' => 'Apr - Sep','10' => 'Oct - Mar'));

// smarty drop down array for status
$smarty->assign('ps_months', array('' => 'Month', '01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr', '05' => 'May', '06' => 'Jun', '07' => 'Jul'
, '08' => 'Aug', '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dec'));

// smarty drop down array for no of times
$years = array();
for($i = 2017; $i <= date('Y'); $i++){
	$years[$i] = $i;
}
$smarty->assign('years', $years);

// closing mysql
$mysql->close_connection();
// assign page title
$smarty->assign('page_title' , 'Add Incentive - Manage Hiring');  
// assigning active class status to smarty menu.tpl
$smarty->assign('billings_active','active');
// $smarty->assign('setting_active', $fun->set_menu_active('add_billing')); 	  
// display smarty file
$smarty->display('add_incentive.tpl');
?>