<?php
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
 
App::uses('Sanitize', 'Utility');   
  
class ReportController extends AppController {  
	
	public $name = 'Report';	
	
	public $helpers = array('Html', 'Form', 'Session');
	
	public $components = array('Session', 'Functions','Excel');

	public function index(){
		// set the page title
		$this->set('title_for_layout', 'Reports - Manage Hiring');	
		// when the form is submitted for search
		if($this->request->is('post')){
			$url_vars = $this->Functions->create_url(array('from','to','loc','emp_id','keyword'),'Report'); 			
			$this->redirect('/report/?'.$url_vars);				
		}
		$this->set('empList', $this->get_employee_details());	
		$this->set('locList', $this->get_loc_details());
		$start = $this->request->query['from'] ? $this->request->query['from'] : date('d/m/Y', strtotime('-1 month'));
		$end = $this->request->query['to'] ? $this->request->query['to'] : date('d/m/Y');
		// set date condition
		$date_cond = array('or' => array("DATE_FORMAT(ReqResume.created_date, '%Y-%m-%d') between ? and ?" => 
					array($this->Functions->format_date_save($start), $this->Functions->format_date_save($end))));		
		$this->request->query['from'] = $start;
		$this->request->query['to'] = $end;
		// for branch condition
		if($this->request->query['loc'] != ''){			
			$branch_cond = array('User.location_id' => $this->request->query['loc']);
		}
		// for employee condition
		if($this->request->query['emp_id'] != ''){ 
			//$req_emp_cond = array('ReqTeam.users_id' => $this->request->query['emp_id']);
			$cv_emp_cond = array('ReqResumeStatus.created_by' => $this->request->query['emp_id']);
			$empSrchCond = array('User.id' => $this->request->query['emp_id']);
		}
		
		
		
		
		// for client condition
		if($this->request->query['keyword'] != ''){			
			$client_cond = array('Client.client_name' => $this->request->query['keyword']);
			$client_options = array(			
				array('table' => 'requirements',
					'alias' => 'Position',					
					'type' => 'LEFT',
					'conditions' => array('`Position`.`id` = `ReqResume`.`requirements_id`')
				),
				array('table' => 'clients',
					'alias' => 'Client',					
					'type' => 'LEFT',
					'conditions' => array('`Client`.`id` = `Position`.`clients_id`')
				)
				
			);
		}
		
			$client_options2 = array(			
					array('table' => 'resume',
						'alias' => 'Resume',					
						'type' => 'LEFT',
						'conditions' => array('`Resume`.`id` = `ReqResume`.`resume_id`')
					)					
				);
		
		// for client condition and sent resume condition
		if($this->request->query['keyword'] != ''){			
			$client_cond = array('Client.client_name' => $this->request->query['keyword']);
			$client_options2 = array(			
				array('table' => 'requirements',
					'alias' => 'Position',					
					'type' => 'LEFT',
					'conditions' => array('`Position`.`id` = `ReqResume`.`requirements_id`')
				),
				array('table' => 'clients',
					'alias' => 'Client',					
					'type' => 'LEFT',
					'conditions' => array('`Client`.`id` = `Position`.`clients_id`')
				),
				array('table' => 'resume',
						'alias' => 'Resume',					
						'type' => 'LEFT',
						'conditions' => array('`Resume`.`id` = `ReqResume`.`resume_id`')
				)
				
			);
		}
		

			
		if($this->Session->read('USER.Login.rights') != '5'){			
			$empCond = array('User.id' => $this->Session->read('USER.Login.id'));
		}
		// get the users
		$emp_data = $this->User->find('all',  array('fields' => array('id','first_name'), 
		'order' => array('first_name ASC'),'conditions' => array('User.status' => 0, 'User.is_deleted' => 'N', $branch_cond,$empCond,$empSrchCond)));
		$emp_list = $this->Functions->format_list($emp_data, 'User', 'id','first_name');
		// iterate the users
		foreach($emp_list as $emp_id => $emp_name){			
			// get the total req. by user
			$rec_count = $this->Report->find('all', array('fields' => array('COUNT(distinct Report.id) as reqCount'), 'conditions' => array('ReqResume.created_by' => $emp_id,
			$date_cond,$req_emp_cond,$client_cond), 'joins' => $client_options));
			// process only requirement count is not empty
			$rec_total = $rec_count[0][0]['reqCount'];
			//if($rec_total > 0){
				$req_count[] = $rec_total;
				// get resume sent by user				
				$this->loadModel('ReqResumeStatus');
				$cv_date_cond = array('or' => array("DATE_FORMAT(ReqResumeStatus.created_date, '%Y-%m-%d') between ? and ?" => 
						array($this->Functions->format_date_save($start), $this->Functions->format_date_save($end))));
				// for sent resumes			
				$cv_sent_count[] = $this->ReqResumeStatus->find('count', array('conditions' => array('Resume.created_by' => $emp_id
				,$cv_date_cond,$client_cond,'ReqResumeStatus.stage_title' => 'Validation - Account Holder','ReqResumeStatus.status_title' => 'Validated'),
				'group' => array('Resume.id'), 'joins' => $client_options2));							
				$cv_shortlist_count[] = $this->ReqResumeStatus->find('count', array('conditions' => array('ReqResumeStatus.created_by' => $emp_id,
				'ReqResumeStatus.status_title' => 'Shortlisted', $cv_date_cond,$cv_emp_cond,$client_cond),
				'group' => array('ReqResumeStatus.req_resume_id'), 'joins' => $client_options));				
				// CV rejected count
				$cv_reject_count[] = $this->ReqResumeStatus->find('count', array('conditions' => array('ReqResumeStatus.created_by' => $emp_id,
				'ReqResumeStatus.status_title' => 'Rejected', 'ReqResumeStatus.stage_title' => 'Shortlist', $cv_date_cond,$cv_emp_cond,
				$client_cond),'group' => array('ReqResumeStatus.req_resume_id'),'joins' => $client_options));
				// get cv feedback awaited						
				$cv_waiting_count[] = $this->ReqResumeStatus->find('count', array('conditions' => array('ReqResumeStatus.created_by' => $emp_id,
				'ReqResumeStatus.status_title' => 'YRF', $cv_date_cond,$cv_emp_cond,$client_cond),
				'group' => array('ReqResumeStatus.req_resume_id'),'joins' => $client_options));
				// short list date condition
				$cv_shortlist_date_cond = array('or' => array("DATE_FORMAT(ReqResumeStatus.created_date, '%Y-%m-%d') between ? and ?" => 
						array($this->Functions->format_date_save($start), $this->Functions->format_date_save($end))));
						
				// get candidates interviewed						
				$options = array(			
								
					array('table' => 'requirements',
							'alias' => 'Position',					
							'type' => 'LEFT',
							'conditions' => array('`Position`.`id` = `ReqResume`.`requirements_id`')
					),
					array('table' => 'clients',
							'alias' => 'Client',					
							'type' => 'LEFT',
							'conditions' => array('`Client`.`id` = `Position`.`clients_id`')
					)
				);
				$candidate_interview_count[] = $this->ReqResumeStatus->find('count', array('conditions' => array('ReqResumeStatus.created_by' => $emp_id,
				'ReqResumeStatus.stage_title like' => '%Interview', $cv_shortlist_date_cond,$cv_emp_cond,$client_cond), 
				'group' => array('ReqResumeStatus.req_resume_id'),	'joins' => $options));
				// get candidates drop outs interview						
				$candidate_int_drop_count[] = $this->ReqResumeStatus->find('count', array('conditions' => array('ReqResumeStatus.created_by' => $emp_id,
				'ReqResumeStatus.stage_title like' => '%Interview', 'ReqResumeStatus.status_title' => 'No Show', $cv_shortlist_date_cond,$cv_emp_cond,$client_cond), 
				'group' => array('ReqResumeStatus.req_resume_id'), 'joins' => $options));
				// get candidates rejected interview						
				$candidate_int_reject_count[] = $this->ReqResumeStatus->find('count', array('conditions' => array('ReqResumeStatus.created_by' => $emp_id,
				'ReqResumeStatus.stage_title like' => '%Interview', 'ReqResumeStatus.status_title' => 'Rejected', $cv_shortlist_date_cond,$cv_emp_cond,$client_cond), 
				'group' => array('ReqResumeStatus.req_resume_id'), 'joins' => $options));
				// get candidates offered 						
				$candidate_offer[] = $this->ReqResumeStatus->find('count', array('conditions' => array('ReqResumeStatus.created_by' => $emp_id,
				'ReqResumeStatus.stage_title' => 'Offer', 'ReqResumeStatus.status_title !=' => array('Offer Pending','Rejected'), $cv_shortlist_date_cond,$cv_emp_cond,$client_cond),
				'group' => array('ReqResumeStatus.req_resume_id'), 'joins' => $client_options));
				// get candidates offered 						
				$candidate_offer_reject[] = $this->ReqResumeStatus->find('count', array('conditions' => array('ReqResumeStatus.created_by' => $emp_id,
				'ReqResumeStatus.stage_title' => 'Offer', 'ReqResumeStatus.status_title' => array('Rejected', 'Not Interested','Quit'), $cv_shortlist_date_cond,$cv_emp_cond,$client_cond),
				'group' => array('ReqResumeStatus.req_resume_id'), 'joins' => $client_options));
				// get candidates joined 						
				$candidate_join[] = $this->ReqResumeStatus->find('count', array('conditions' => array('ReqResumeStatus.created_by' => $emp_id,
				'ReqResumeStatus.stage_title' => 'Joining', 'ReqResumeStatus.status_title' => 'Joined', $cv_shortlist_date_cond,$cv_emp_cond,$client_cond),
				'group' => array('ReqResumeStatus.req_resume_id'), 'joins' => $client_options));
				// get billing amount 						
				$billing_data = $this->ReqResumeStatus->find('all', array('fields' => array('sum(ReqResume.bill_ctc) as bill_ctc'),'conditions' => array('ReqResumeStatus.created_by' => $emp_id,
				'ReqResumeStatus.stage_title' => 'Joining',  'ReqResumeStatus.status_title' => 'Joined', $cv_shortlist_date_cond,$cv_emp_cond,$client_cond),
				'joins' => $client_options));
				//echo '<pre>';print_r($billing_data);
				$billing_amt[] = $billing_data[0][0]['bill_ctc'];
				// get billing report 	
				$billing_report[] = $this->ReqResumeStatus->find('count', array('conditions' => array('ReqResumeStatus.created_by' => $emp_id,
				'ReqResume.bill_ctc >' => '0','ReqResumeStatus.status_title' => 'Joined', $cv_shortlist_date_cond,$client_cond,$cv_emp_cond),
				'group' => array('ReqResumeStatus.req_resume_id'), 'joins' => $client_options));
				// assign the employee
				$empData[] = $emp_name;
				$empId[] = $emp_id;
			//}

		}		
		// for export
		if($this->request->query['action'] == 'export'){			
			$this->Excel->generate_report('performance_report', $empData, $req_count,
			$cv_sent_count,$cv_shortlist_count,$cv_reject_count,$cv_waiting_count,$candidate_interview_count,
			$candidate_int_drop_count,$candidate_int_reject_count,$candidate_offer,$candidate_offer_reject,$candidate_join,
			$billing_amt,$billing_report,'Report', 'Performance');
		}
		// assign all the values
		$this->set('empData', $empData);
		$this->set('empId', $empId);
		$this->set('reqData', $req_count);
		$this->set('cvSentData', $cv_sent_count);
		$this->set('cvShortlistData', $cv_shortlist_count);
		$this->set('cvRejectData', $cv_reject_count);		
		$this->set('cvWaitingData', $cv_waiting_count);
		$this->set('interviewData', $candidate_interview_count);
		$this->set('intDropData', $candidate_int_drop_count);		
		$this->set('intRejectData', $candidate_int_reject_count);
		$this->set('candidateOffer', $candidate_offer);
		$this->set('offerReject', $candidate_offer_reject);		
		$this->set('candidateJoin', $candidate_join);
		$this->set('billingData', $billing_amt);
		$this->set('billingReport', $billing_report);	

		
		
		if(empty($empData)){
			$this->Session->setFlash('<button type="button" class="close" data-dismiss="alert">&times;</button>Oops! No Reports Found!', 'default', array('class' => 'alert alert-info'));
		}
		
	}
	
	/* function to get the employee details */
	public function get_employee_details(){
		$this->loadModel('User');
		$role_id = $this->request->data['Report']['role_id'];
		$branch_id = $this->request->data['Report']['branch_id'];
		$roleCond = !empty($role_id) ? array('roles_id' => $role_id) : '';
		$branchCond = !empty($branch_id) ? array('location_id' => $branch_id) : '';
		return $this->User->find('list',  array('fields' => array('id','first_name'), 'order' => array('first_name ASC'),'conditions' => array('status' => 0, $roleCond, $branchCond)));
	}
	
	/* function to get the location details */
	public function get_loc_details(){
		$this->loadModel('Location');
		return $this->Location->find('list',  array('fields' => array('id','location'), 'order' => array('location ASC'),'conditions' => array('status' => 1)));

	}
	
	/* function to get the ctc wise client opening */
	public function ctc_wise_client_opening(){
		
	}
	
	/* function to get the employee details */
	public function get_employee($role_id, $loc_id){
		$this->layout = 'ajax';
		$this->loadModel('User');
		$locCond = $loc_id != 'null' ? array('location_id' => $loc_id) : '';
		$roleCond = $role_id != 'null' ? array('roles_id' => $role_id) : '';
		$data = $this->User->find('list',  array('fields' => array('id','first_name'), 'order' => array('first_name ASC'),'conditions' => array('status' => 0,$roleCond, $locCond)));
		$list = "<option value=''>Select</option>";
		foreach($data as $id => $emp){
			$list .= "<option value=".$id.">".$emp."</option>";
		}
		echo $list;
		die;
	}
	

	
	/* function to get the client wise CV status */
	public function client_wise_cv_status(){
		// set the page title
		$this->set('title_for_layout', 'Client Wise CV Status - Manage Hiring');	
		$this->set('empList', $this->get_employee_details());	
		$this->set('locList', $this->get_loc_details());
		$client_data = $this->get_client_details();
		$this->set('clientList', $client_data);
		$this->get_role_details();
				
		// search filters
		if(!empty($this->request->data['Report']['from'])){
			$this->set('fromDate', date('d-M-Y', strtotime($this->Functions->format_date_save($this->request->data['Report']['from']))));
		}
		if(!empty($this->request->data['Report']['to'])){
			$this->set('toDate', date('d-M-Y', strtotime($this->Functions->format_date_save($this->request->data['Report']['to']))));
		}
		// date filter
		if($this->request->data['Report']['from'] != '' || $this->request->data['Report']['to'] != ''){
			$start = $this->request->data['Report']['from'] ? $this->request->data['Report']['from'] : ''; // date('d/m/Y', strtotime('-15 month'));
			$end_search = $this->request->data['Report']['to'] ? $this->request->data['Report']['to'] :  date('d/m/Y', strtotime('+1 day'));
			// set date condition
			$date_cond = array('or' => array("DATE_FORMAT(Position.created_date, '%Y-%m-%d') between ? and ?" => 
						 array($this->Functions->format_date_save($start), $this->Functions->format_date_save($end_search))));
			$date_cond_ow = array('or' => array("DATE_FORMAT(Report.created_date, '%Y-%m-%d') between ? and ?" => 
						 array($this->Functions->format_date_save($start), $this->Functions->format_date_save($end_search))));			 
		}

		// get client name
		if(!empty($this->request->data['Report']['client_id'])){
			$data = $this->Client->findById($this->request->data['Report']['client_id'], array('fields' => 'client_name'));
			$this->set('clientName', $data['Client']['client_name']);
		}
		// get branch name
		if(!empty($this->request->data['Report']['branch_id'])){
			$this->loadModel('Location');
			$data = $this->Location->findById($this->request->data['Report']['branch_id'], array('fields' => 'location'));
			$this->set('locName', $data['Location']['location']);
		}
		// get role name
		if(!empty($this->request->data['Report']['role_id'])){
			$this->loadModel('Role');
			$data = $this->Role->findById($this->request->data['Report']['role_id'], array('fields' => 'role_name'));
			$this->set('roleName', $data['Role']['role_name']);
		}
		// get employee name
		if(!empty($this->request->data['Report']['emp_id'])){
			$this->loadModel('User');
			$data = $this->User->findById($this->request->data['Report']['emp_id'], array('fields' => 'first_name','last_name'));
			$this->set('empName', ucwords($data['User']['first_name'].' '.$data['User']['last_name']));
			$emp_cond = array('ReqTeam.users_id' => $this->request->data['Report']['emp_id']);
			$req_team_cond = array('ReqTeam.is_approve !=' => array('S','R'));
		}
		
		$client_detail = $this->get_client_details($this->request->data['Report']['client_id']);
		$this->set('clientDetail', $client_detail);

		// iterate the clients
		foreach($client_detail as $id => $client){
		
			// get all the openings of the client		
			
			/*
			$ow_options = array(				
				array('table' => 'req_team',
					'alias' => 'ReqTeam',					
					'type' => 'LEFT',
					'conditions' => array('`ReqTeam`.`requirements_id` = `Report`.`id`')
				),	
				array('table' => 'clients',
					'alias' => 'Client',				
					'type' => 'LEFT',
					'conditions' => array('`Client`.`id` = `Report`.`clients_id`')
				)			
			);	
			*/
			
			$ow_options = array(				
				array('table' => 'clients',
					'alias' => 'Client',				
					'type' => 'LEFT',
					'conditions' => array('`Client`.`id` = `Report`.`clients_id`')					
					),	
				array('table' => 'req_team',
					'alias' => 'ReqTeam',					
					'type' => 'LEFT',
					'conditions' => array('`ReqTeam`.`requirements_id` = `Report`.`id`')
				),					
			);	
			
			$opening_worked[] = $this->Report->find('all', array('fields' => array("sum(Report.no_job) no_job"), 'conditions' => array('Report.status' => 'A', 'Report.is_approve' => 'A', 'Client.id' => $id,	 $date_cond_ow,$emp_cond),
			'joins' => $ow_options));
			
			// get all the cv sent details of the client positions
			
			$this->loadModel('ReqResumeStatus');
			$options = array(
					array('table' => 'users',
						'alias' => 'User',					
						'type' => 'LEFT',
						'conditions' => array('`User`.`id` = `ReqResume`.`created_by`')
					),
					array('table' => 'location',
						'alias' => 'Location',					
						'type' => 'LEFT',
						'conditions' => array('`Location`.`id` = `User`.`location_id`')
					)
					,
					array('table' => 'resume',
						'alias' => 'Resume',					
						'type' => 'LEFT',
						'conditions' => array('`Resume`.`id` = `ReqResume`.`resume_id`')
					),			
					array('table' => 'requirements',
						'alias' => 'Position',					
						'type' => 'LEFT',
						'conditions' => array('`Position`.`id` = `ReqResume`.`requirements_id`')
					),
					array('table' => 'clients',
						'alias' => 'Client',					
						'type' => 'LEFT',
						'conditions' => array('`Client`.`id` = `Position`.`clients_id`')
					),
					array('table' => 'req_team',
						'alias' => 'ReqTeam',					
						'type' => 'LEFT',
						'conditions' => array('`ReqTeam`.`requirements_id` = `Position`.`id`')
					)
				);
			$sent_cond = array('ReqResumeStatus.stage_title' => 'Shortlist','ReqResumeStatus.status_title' => 'CV-Sent');
			$cv_sent_count[] = $this->ReqResumeStatus->find('count', array('conditions' => array($sent_cond,$date_cond,
			$req_team_cond, $emp_cond, 'Client.id' => $id), 'joins' => $options));
			
			// get all the cv short list details of the client positions
			
			$shortlist_cond = array('ReqResumeStatus.status_title' => 'Shortlisted');
			$cv_shortlist_count[] = $this->ReqResumeStatus->find('count', array('conditions' => array($shortlist_cond,$date_cond,$req_team_cond, $emp_cond, 'Client.id' => $id), 'group' => array('ReqResumeStatus.req_resume_id'), 'joins' => $options));
			
			// get all the cv feedback awaiting list details of the client positions
							
			$feedback_awaiting_cond = array('ReqResume.stage_title' => 'Shortlist', 'ReqResume.status_title !=' => array('Shortlisted','Rejected'));
			$feedback_await_count[] = $this->ReqResumeStatus->find('count', array('conditions' => array($feedback_awaiting_cond,$date_cond,$req_team_cond,$emp_cond, 'Client.id' => $id), 'group' => array('ReqResume.id'), 'joins' => $options));
			
			// get all the interview schedule awaiting list details of the client positions
							
			$interview_awaiting_cond = array('ReqResume.stage_title' => 'Shortlist', 'ReqResume.status_title' => array('Shortlisted'));
			$interview_await_count[] = $this->ReqResumeStatus->find('count', array('conditions' => array($interview_awaiting_cond,$date_cond, $req_team_cond,$emp_cond,'Client.id' => $id), 'group' => array('ReqResume.id'), 'joins' => $options));
			
			// get all the preliminary interview schedule list details of the client positions
							
			$prili_awaiting_cond = array('ReqResumeStatus.stage_title like' => '%Interview');
			$prili_interview_count[] = $this->ReqResumeStatus->find('count', array('conditions' => array($prili_awaiting_cond,$req_team_cond, $date_cond,$emp_cond,'Client.id' => $id), 'group' => array('ReqResumeStatus.req_resume_id'), 'joins' => $options));
			
			// get all the final interview schedule list details of the client position
							
			$final_interview_cond = array('ReqResumeStatus.stage_title' => 'Final Interview');
			$final_interview_count[] = $this->ReqResumeStatus->find('count', array('conditions' => array($final_interview_cond,$date_cond,$req_team_cond,$emp_cond, 'Client.id' => $id), 'group' => array('ReqResumeStatus.req_resume_id'), 'joins' => $options));
			
			// get all the offer pending list details of the client position
							
			$offer_pending_cond = array('ReqResume.stage_title' => 'Offer', 'ReqResume.status_title' => 'Offer Pending');
			$offer_pending_count[] = $this->ReqResumeStatus->find('count', array('conditions' => array($offer_pending_cond,$date_cond,$req_team_cond,$emp_cond, 'Client.id' => $id), 'group' => array('ReqResume.id'), 'joins' => $options));
			
			// get all the offer accepted list details of the client position
							
			$offer_accept_cond = array('ReqResumeStatus.stage_title' => 'Offer', 'ReqResumeStatus.status_title' => 'Offer Accepted');
			$offer_accept_count[] = $this->ReqResumeStatus->find('count', array('conditions' => array($offer_accept_cond,$date_cond,$req_team_cond, $emp_cond,'Client.id' => $id), 'group' => array('ReqResume.id'), 'joins' => $options));
			
			// get all the offer rejected list details of the client position
							
			$offer_reject_cond = array('ReqResumeStatus.stage_title' => 'Offer', 'ReqResumeStatus.status_title' => 'Declined');
			$offer_reject_count[] = $this->ReqResumeStatus->find('count', array('conditions' => array($offer_reject_cond,$date_cond,$req_team_cond, $emp_cond,'Client.id' => $id), 'group' => array('ReqResumeStatus.req_resume_id'), 'joins' => $options));
			
			// get all the joining pending list details of the client position
							
			$join_pending_cond = array('ReqResume.stage_title' => 'Offer', 'ReqResume.status_title' => 'Offer Accepted');
			$join_pending_count[] = $this->ReqResumeStatus->find('count', array('conditions' => array($join_pending_cond,$date_cond,$req_team_cond,$emp_cond, 'Client.id' => $id), 'group' => array('ReqResume.id'), 'joins' => $options));
			
			// get all the joining accepted list details of the client position
							
			$join_accept_cond = array('ReqResume.stage_title' => 'Joining', 'ReqResume.status_title' => 'Joined');
			$join_accept_count[] = $this->ReqResumeStatus->find('count', array('conditions' => array($join_accept_cond,$date_cond,$req_team_cond, $emp_cond,'Client.id' => $id), 'group' => array('ReqResume.id'), 'joins' => $options));
			
			// get all the joining rejected list details of the client position
							
			$join_reject_cond = array('ReqResume.stage_title' => 'Joining', 'ReqResume.status_title' => 'Not Joined');
			$join_reject_count[] = $this->ReqResumeStatus->find('count', array('conditions' => array($join_reject_cond,$date_cond,$req_team_cond,$emp_cond, 'Client.id' => $id), 'group' => array('ReqResume.id'), 'joins' => $options));
			
			// get all the joining deferred list details of the client position
							
			$join_defer_cond = array('ReqResume.stage_title' => 'Joining', 'ReqResume.status_title' => 'Deferred');
			$join_defer_count[] = $this->ReqResumeStatus->find('count', array('conditions' => array($join_defer_cond,$date_cond,$req_team_cond,$emp_cond, 'Client.id' => $id), 'group' => array('ReqResume.id'), 'joins' => $options));
			
			// get all the not billed details of the client position
							
			$not_billed_cond = array('ReqResume.stage_title' => 'Joining', 'ReqResume.status_title' => 'Joined', 'ReqResume.bill_ctc' => NULL);
			$not_billed_count[] = $this->ReqResumeStatus->find('count', array('conditions' => array($not_billed_cond,$date_cond,$req_team_cond,$emp_cond, 'Client.id' => $id), 'group' => array('ReqResume.id'), 'joins' => $options));
			
			// get all the billed details of the client position
							
			$billed_cond = array('ReqResume.stage_title' => 'Joining', 'ReqResume.status_title' => 'Joined', 'ReqResume.bill_ctc >' => '0');
			$billed_count[] = $this->ReqResumeStatus->find('count', array('conditions' => array($billed_cond,$date_cond,$req_team_cond,$emp_cond,	'Client.id' => $id), 'group' => array('ReqResume.id'), 'joins' => $options));
			
			// get the CV Sent to shortlisted percentage
			
		}
		
		// for export
		if($this->request->query['action'] == 'export'){			
			$this->Excel->generate_report('client_wise_cv_status_report', $client_detail, $opening_worked,
			$cv_sent_count,$cv_shortlist_count,$cv_reject_count,$feedback_await_count,$interview_await_count,
			$prili_interview_count,$final_interview_count,$offer_pending_count,$offer_accept_count,$offer_reject_count,
			$join_pending_count,$join_accept_count,$join_reject_count,$join_defer_count,
			$not_billed_count,$billed_count,'client wise cv status', 'client wise cv status');
		}
		
	
		// for pdf generation
		if($this->request->query['action'] == 'pdf'){
			$pdf_file = 'client_wise_cv_status_'.date('y-m-d');
			
			include('vendor/dompdf_0-8-2/pdf.php');
		}
					


		// die;

		// assign all the counts
		$this->set('OPENING_WORKED', $opening_worked);
		$this->set('CV_SENT', $cv_sent_count);
		$this->set('CV_SHORTLIST', $cv_shortlist_count);
		$this->set('CV_REJECT', $cv_reject_count);
		$this->set('FEEDBACK_AWAITING', $feedback_await_count);
		$this->set('INTERVIEW_AWAITING', $interview_await_count);
		$this->set('PRILIMINARY_INTERVIEW_ATTEND', $prili_interview_count);
		$this->set('FINAL_INTERVIEW_ATTEND', $final_interview_count);
		
		$this->set('OFFER_PENDING', $offer_pending_count);
		$this->set('OFFER_ACCEPT', $offer_accept_count);
		$this->set('OFFER_REJECT', $offer_reject_count);
		
		$this->set('JOIN_PENDING', $join_pending_count);
		$this->set('JOIN_ACCEPT', $join_accept_count);
		$this->set('NOT_JOIN', $join_reject_count);		
		$this->set('JOIN_DEFER', $join_defer_count);
		
		$this->set('NOT_BILLED', $not_billed_count);		
		$this->set('BILLED', $billed_count);
		
		$count_client = count($client_data);
		$this->set('chart_height', $count_client < 10 ? '500' :  $count_client*50);
		
		
		
	}
	
	
	/* function to get the month wise CV status */
	public function month_wise_cv_status(){
		// set the page title
		$this->set('title_for_layout', 'Month Wise CV Status - Manage Hiring');	
		$this->set('empList', $this->get_employee_details());	
		$this->set('locList', $this->get_loc_details());
		$client_data = $this->get_client_details();
		$this->set('clientList', $client_data);
		$this->get_role_details();
		
		// date filter when form submitted
		if($this->request->data['Report']['from'] != '' || $this->request->data['Report']['to'] != ''){
			$fin_start_year = $this->request->data['Report']['from'] ? $this->request->data['Report']['from'] : ''; // date('d/m/Y', strtotime('-15 month'));
			$fin_end_year = $this->request->data['Report']['to'] ? $this->request->data['Report']['to'] :  date('d/m/Y', strtotime('+1 day'));
			$fin_start_split = explode('/', $this->request->data['Report']['from']);
			$fin_start_year_cal = $fin_start_split[1].'-'.$fin_start_split[0].'-01';
			
			$fin_end_split = explode('/', $this->request->data['Report']['to']);
			$fin_end_year_cal = $fin_end_split[1].'-'.$fin_end_split[0].'-31';	

			$this->set('FINSTART',$fin_start_year);
			$this->set('FINEND',$fin_end_year);	
		}else{
			// get the financial year	
			if(date('m') >= 3){
				$fin_start_year = '04/'.date('Y');
				$fin_end_year = '03/'.(date('Y') + 1);
				// for calculation
				$fin_start_year_cal = date('Y').'-04-01';
				$fin_end_year_cal = (date('Y') + 1).'-03-31';
				
			}else{
				$fin_start_year = '04/'.(date('Y') - 1);
				$fin_end_year = '03/'.date('Y');
				// for calculation
				$fin_start_year_cal = (date('Y') - 1).'-04-01';
				$fin_end_year_cal = date('Y').'-03-31';
			}
			$this->set('FINSTART',$fin_start_year);
			$this->set('FINEND',$fin_end_year);
		}
		
		
		// search filters
		if(!empty($this->request->data['Report']['from'])){
			$this->set('fromDate', date('M, Y', strtotime($this->Functions->format_date_save_month($this->request->data['Report']['from']))));
		}
		if(!empty($this->request->data['Report']['to'])){
			$this->set('toDate', date('M, Y', strtotime($this->Functions->format_date_save_month($this->request->data['Report']['to']))));
		}
		

		// get client name
		if(!empty($this->request->data['Report']['client_id'])){
			$data = $this->Client->findById($this->request->data['Report']['client_id'], array('fields' => 'client_name'));
			$this->set('clientName', $data['Client']['client_name']);
			$client_cond = array('Client.id' => $this->request->data['Report']['client_id']);
		}
		
		// get branch name
		if(!empty($this->request->data['Report']['branch_id'])){
			$this->loadModel('Location');
			$data = $this->Location->findById($this->request->data['Report']['branch_id'], array('fields' => 'location'));
			$this->set('locName', $data['Location']['location']);
		}
		// get role name
		if(!empty($this->request->data['Report']['role_id'])){
			$this->loadModel('Role');
			$data = $this->Role->findById($this->request->data['Report']['role_id'], array('fields' => 'role_name'));
			$this->set('roleName', $data['Role']['role_name']);
		}
		// get employee name
		if(!empty($this->request->data['Report']['emp_id'])){
			$this->loadModel('User');
			$data = $this->User->findById($this->request->data['Report']['emp_id'], array('fields' => 'first_name','last_name'));
			$this->set('empName', ucwords($data['User']['first_name'].' '.$data['User']['last_name']));
			$emp_cond = array('ReqTeam.users_id' => $this->request->data['Report']['emp_id']);
			$req_team_cond = array('ReqTeam.is_approve !=' => array('S','R'));
		}
		
		
		//$client_detail = $this->get_client_details($this->request->data['Report']['client_id']);
		// $this->set('clientDetail', $client_detail);

		// iterate the clients
		$i = 0;
		while($i < 12){
			$month_detail[] = date('M,y', strtotime($fin_start_year_cal." +$i month"));
			// month condition
			$month_str = date('Y-m', strtotime($fin_start_year_cal." +$i month"));
			$month_cond = array('Report.created_date like' => "$month_str%");
			$month_cond2 = array('Position.created_date like' => "$month_str%");
			// get all the openings of the client		
		
			$ow_options = array(				
				array('table' => 'clients',
					'alias' => 'Client',				
					'type' => 'LEFT',
					'conditions' => array('`Client`.`id` = `Report`.`clients_id`')					
					),	
				array('table' => 'req_team',
					'alias' => 'ReqTeam',					
					'type' => 'LEFT',
					'conditions' => array('`ReqTeam`.`requirements_id` = `Report`.`id`')
				),					
			);	
			
			$opening_worked[] = $this->Report->find('all', array('fields' => array("sum(Report.no_job) no_job"), 'conditions' => array('Report.status' => 'A', 'Report.is_approve' => 'A', $month_cond, $emp_cond),
			'joins' => $ow_options));
			
			$position_worked = $this->Report->find('all', array('fields' => array("group_concat(Distinct Report.id) pos_work"), 'conditions' => array('Report.status' => 'A', 'Report.is_approve' => 'A', $month_cond, $emp_cond),'joins' => $ow_options));
			
			$pos_split = explode(',', $position_worked[0][0]['pos_work']);
			$pos_work_cond = array('Position.id' => $pos_split);
			// get all the cv sent details of the client positions
			
			$this->loadModel('ReqResumeStatus');
			$options = array(
					array('table' => 'users',
						'alias' => 'User',					
						'type' => 'LEFT',
						'conditions' => array('`User`.`id` = `ReqResume`.`created_by`')
					),
					array('table' => 'location',
						'alias' => 'Location',					
						'type' => 'LEFT',
						'conditions' => array('`Location`.`id` = `User`.`location_id`')
					)
					,
					array('table' => 'resume',
						'alias' => 'Resume',					
						'type' => 'LEFT',
						'conditions' => array('`Resume`.`id` = `ReqResume`.`resume_id`')
					),			
					array('table' => 'requirements',
						'alias' => 'Position',					
						'type' => 'LEFT',
						'conditions' => array('`Position`.`id` = `ReqResume`.`requirements_id`')
					),
					array('table' => 'clients',
						'alias' => 'Client',					
						'type' => 'LEFT',
						'conditions' => array('`Client`.`id` = `Position`.`clients_id`')
					),
					array('table' => 'req_team',
						'alias' => 'ReqTeam',					
						'type' => 'LEFT',
						'conditions' => array('`ReqTeam`.`requirements_id` = `Position`.`id`')
					)
				);
			$sent_cond = array('ReqResumeStatus.stage_title' => 'Shortlist','ReqResumeStatus.status_title' => 'CV-Sent');
			$cv_sent_count[] = $this->ReqResumeStatus->find('count', array('conditions' => array($sent_cond,$month_cond2,$req_team_cond, $emp_cond, $client_cond,$pos_work_cond), 'joins' => $options));
			
			// get all the cv short list details of the client positions
			
			$shortlist_cond = array('ReqResumeStatus.status_title' => 'Shortlisted');
			$cv_shortlist_count[] = $this->ReqResumeStatus->find('count', array('conditions' => array($shortlist_cond,$month_cond2,$req_team_cond, $emp_cond, $client_cond,$pos_work_cond), 'group' => array('ReqResumeStatus.req_resume_id'), 'joins' => $options));
			
			// get all the cv feedback awaiting list details of the client positions
							
			$feedback_awaiting_cond = array('ReqResume.stage_title' => 'Shortlist', 'ReqResume.status_title !=' => array('Shortlisted','Rejected'));
			$feedback_await_count[] = $this->ReqResumeStatus->find('count', array('conditions' => array($feedback_awaiting_cond,$month_cond2,$req_team_cond,$emp_cond, $client_cond,$pos_work_cond), 'group' => array('ReqResume.id'), 'joins' => $options));
			
			// get all the interview schedule awaiting list details of the client positions
							
			$interview_awaiting_cond = array('ReqResume.stage_title' => 'Shortlist', 'ReqResume.status_title' => array('Shortlisted'));
			$interview_await_count[] = $this->ReqResumeStatus->find('count', array('conditions' => array($interview_awaiting_cond,$month_cond2, $req_team_cond,$emp_cond,$client_cond,$pos_work_cond), 'group' => array('ReqResume.id'), 'joins' => $options));
			
			// get all the preliminary interview schedule list details of the client positions
							
			$prili_awaiting_cond = array('ReqResumeStatus.stage_title like' => '%Interview');
			$prili_interview_count[] = $this->ReqResumeStatus->find('count', array('conditions' => array($prili_awaiting_cond,$req_team_cond, $month_cond2,$emp_cond,$client_cond,$pos_work_cond), 'group' => array('ReqResumeStatus.req_resume_id'), 'joins' => $options));
			
			// get all the final interview schedule list details of the client position
							
			$final_interview_cond = array('ReqResumeStatus.stage_title' => 'Final Interview');
			$final_interview_count[] = $this->ReqResumeStatus->find('count', array('conditions' => array($final_interview_cond,$month_cond2,$req_team_cond,$emp_cond, $client_cond,$pos_work_cond), 'group' => array('ReqResumeStatus.req_resume_id'), 'joins' => $options));
			
			// get all the offer pending list details of the client position
							
			$offer_pending_cond = array('ReqResume.stage_title' => 'Offer', 'ReqResume.status_title' => 'Offer Pending');
			$offer_pending_count[] = $this->ReqResumeStatus->find('count', array('conditions' => array($offer_pending_cond,$month_cond2,$req_team_cond,$emp_cond, $client_cond,$pos_work_cond), 'group' => array('ReqResume.id'), 'joins' => $options));
			
			// get all the offer accepted list details of the client position
							
			$offer_accept_cond = array('ReqResumeStatus.stage_title' => 'Offer', 'ReqResumeStatus.status_title' => 'Offer Accepted');
			$offer_accept_count[] = $this->ReqResumeStatus->find('count', array('conditions' => array($offer_accept_cond,$month_cond2,$req_team_cond, $emp_cond,$client_cond,$pos_work_cond), 'group' => array('ReqResume.id'), 'joins' => $options));
			
			// get all the offer rejected list details of the client position
							
			$offer_reject_cond = array('ReqResumeStatus.stage_title' => 'Offer', 'ReqResumeStatus.status_title' => 'Declined');
			$offer_reject_count[] = $this->ReqResumeStatus->find('count', array('conditions' => array($offer_reject_cond,$month_cond2,$req_team_cond, $emp_cond,$client_cond,$pos_work_cond), 'group' => array('ReqResumeStatus.req_resume_id'), 'joins' => $options));
			
			// get all the joining pending list details of the client position
							
			$join_pending_cond = array('ReqResume.stage_title' => 'Offer', 'ReqResume.status_title' => 'Offer Accepted');
			$join_pending_count[] = $this->ReqResumeStatus->find('count', array('conditions' => array($join_pending_cond,$month_cond2,$req_team_cond,$emp_cond, $client_cond,$pos_work_cond), 'group' => array('ReqResume.id'), 'joins' => $options));
			
			// get all the joining accepted list details of the client position
							
			$join_accept_cond = array('ReqResume.stage_title' => 'Joining', 'ReqResume.status_title' => 'Joined');
			$join_accept_count[] = $this->ReqResumeStatus->find('count', array('conditions' => array($join_accept_cond,$month_cond2,$req_team_cond, $emp_cond,$client_cond,$pos_work_cond), 'group' => array('ReqResume.id'), 'joins' => $options));
			
			// get all the joining rejected list details of the client position
							
			$join_reject_cond = array('ReqResume.stage_title' => 'Joining', 'ReqResume.status_title' => 'Not Joined');
			$join_reject_count[] = $this->ReqResumeStatus->find('count', array('conditions' => array($join_reject_cond,$month_cond2,$req_team_cond,$emp_cond, $client_cond,$pos_work_cond), 'group' => array('ReqResume.id'), 'joins' => $options));
			
			// get all the joining deferred list details of the client position
							
			$join_defer_cond = array('ReqResume.stage_title' => 'Joining', 'ReqResume.status_title' => 'Deferred');
			$join_defer_count[] = $this->ReqResumeStatus->find('count', array('conditions' => array($join_defer_cond,$month_cond2,$req_team_cond,$emp_cond, $client_cond,$pos_work_cond), 'group' => array('ReqResume.id'), 'joins' => $options));
			
			// get all the not billed details of the client position
							
			$not_billed_cond = array('ReqResume.stage_title' => 'Joining', 'ReqResume.status_title' => 'Joined', 'ReqResume.bill_ctc' => NULL);
			$not_billed_count[] = $this->ReqResumeStatus->find('count', array('conditions' => array($not_billed_cond,$month_cond2,$req_team_cond,$emp_cond, $client_cond,$pos_work_cond), 'group' => array('ReqResume.id'), 'joins' => $options));
			
			// get all the billed details of the client position
							
			$billed_cond = array('ReqResume.stage_title' => 'Joining', 'ReqResume.status_title' => 'Joined', 'ReqResume.bill_ctc >' => '0');
			$billed_count[] = $this->ReqResumeStatus->find('count', array('conditions' => array($billed_cond,$month_cond2,$req_team_cond,$emp_cond,	$client_cond,$pos_work_cond), 'group' => array('ReqResume.id'), 'joins' => $options));
			
			// get the CV Sent to shortlisted percentage
			$i++;
		}
		
		$this->set('monthDetail', $month_detail);
		
		// for export
		if($this->request->query['action'] == 'export'){			
			$this->Excel->generate_report('month_wise_cv_status_report', $client_detail, $opening_worked,
			$cv_sent_count,$cv_shortlist_count,$cv_reject_count,$feedback_await_count,$interview_await_count,
			$prili_interview_count,$final_interview_count,$offer_pending_count,$offer_accept_count,$offer_reject_count,
			$join_pending_count,$join_accept_count,$join_reject_count,$join_defer_count,
			$not_billed_count,$billed_count,'client wise cv status', 'client wise cv status');
		}
		
	
		// for pdf generation
		if($this->request->query['action'] == 'pdf'){
			$pdf_file = 'month_wise_cv_status_'.date('y-m-d');
			
			include('vendor/dompdf_0-8-2/pdf.php');
		}
					


		// die;

		// assign all the counts
		$this->set('OPENING_WORKED', $opening_worked);
		$this->set('CV_SENT', $cv_sent_count);
		$this->set('CV_SHORTLIST', $cv_shortlist_count);
		$this->set('CV_REJECT', $cv_reject_count);
		$this->set('FEEDBACK_AWAITING', $feedback_await_count);
		$this->set('INTERVIEW_AWAITING', $interview_await_count);
		$this->set('PRILIMINARY_INTERVIEW_ATTEND', $prili_interview_count);
		$this->set('FINAL_INTERVIEW_ATTEND', $final_interview_count);
		
		$this->set('OFFER_PENDING', $offer_pending_count);
		$this->set('OFFER_ACCEPT', $offer_accept_count);
		$this->set('OFFER_REJECT', $offer_reject_count);
		
		$this->set('JOIN_PENDING', $join_pending_count);
		$this->set('JOIN_ACCEPT', $join_accept_count);
		$this->set('NOT_JOIN', $join_reject_count);		
		$this->set('JOIN_DEFER', $join_defer_count);
		
		$this->set('NOT_BILLED', $not_billed_count);		
		$this->set('BILLED', $billed_count);
		
		$count_client = count($client_data);
		$this->set('chart_height', $count_client < 10 ? '500' :  $count_client*50);
		
		
		
	}
	
	
	/* function to get the employee productivity status */
	public function employee_productivity(){
		// set the page title
		$this->set('title_for_layout', 'Employee Productivity - Manage Hiring');	
		$this->set('empList', $this->get_employee_details());	
		$this->set('locList', $this->get_loc_details());
		$client_data = $this->get_client_details();
		$this->set('clientList', $client_data);
		$this->get_role_details();
		
		// date filter when form submitted
		if($this->request->data['Report']['from'] != '' || $this->request->data['Report']['to'] != ''){
			$fin_start_year = $this->request->data['Report']['from'] ? $this->request->data['Report']['from'] : ''; // date('d/m/Y', strtotime('-15 month'));
			$fin_end_year = $this->request->data['Report']['to'] ? $this->request->data['Report']['to'] :  date('d/m/Y', strtotime('+1 day'));
			$fin_start_split = explode('/', $this->request->data['Report']['from']);
			$fin_start_year_cal = $fin_start_split[1].'-'.$fin_start_split[0].'-01';
			
			$fin_end_split = explode('/', $this->request->data['Report']['to']);
			$fin_end_year_cal = $fin_end_split[1].'-'.$fin_end_split[0].'-31';	

			$this->set('FINSTART',$fin_start_year);
			$this->set('FINEND',$fin_end_year);	
		}else{
			// get the financial year	
			if(date('m') >= 3){
				$fin_start_year = '04/'.date('Y');
				$fin_end_year = '03/'.(date('Y') + 1);
				// for calculation
				$fin_start_year_cal = date('Y').'-04-01';
				$fin_end_year_cal = (date('Y') + 1).'-03-31';
				
			}else{
				$fin_start_year = '04/'.(date('Y') - 1);
				$fin_end_year = '03/'.date('Y');
				// for calculation
				$fin_start_year_cal = (date('Y') - 1).'-04-01';
				$fin_end_year_cal = date('Y').'-03-31';
			}
			$this->set('FINSTART',$fin_start_year);
			$this->set('FINEND',$fin_end_year);
		}
		
		
		// search filters
		if(!empty($this->request->data['Report']['from'])){
			$this->set('fromDate', date('M, Y', strtotime($this->Functions->format_date_save_month($this->request->data['Report']['from']))));
		}
		if(!empty($this->request->data['Report']['to'])){
			$this->set('toDate', date('M, Y', strtotime($this->Functions->format_date_save_month($this->request->data['Report']['to']))));
		}
		

		// get client name
		if(!empty($this->request->data['Report']['client_id'])){
			$data = $this->Client->findById($this->request->data['Report']['client_id'], array('fields' => 'client_name'));
			$this->set('clientName', $data['Client']['client_name']);
			$client_cond = array('Client.id' => $this->request->data['Report']['client_id']);
		}
		
		// get branch name
		if(!empty($this->request->data['Report']['branch_id'])){
			$this->loadModel('Location');
			$data = $this->Location->findById($this->request->data['Report']['branch_id'], array('fields' => 'location'));
			$this->set('locName', $data['Location']['location']);
		}
		// get role name
		if(!empty($this->request->data['Report']['role_id'])){
			$this->loadModel('Role');
			$data = $this->Role->findById($this->request->data['Report']['role_id'], array('fields' => 'role_name'));
			$this->set('roleName', $data['Role']['role_name']);
		}
		// get employee name
		if(!empty($this->request->data['Report']['emp_id'])){
			$this->loadModel('User');
			$data = $this->User->findById($this->request->data['Report']['emp_id'], array('fields' => 'first_name','last_name'));
			$this->set('empName', ucwords($data['User']['first_name'].' '.$data['User']['last_name']));
			$emp_cond = array('ReqTeam.users_id' => $this->request->data['Report']['emp_id']);
			$req_team_cond = array('ReqTeam.is_approve !=' => array('S','R'));
		}
		
		
		$user_detail = $this->get_employee_details($this->request->data['Report']['emp_id']);
		$this->set('userDetail', $user_detail);
		
		// for month lists
		$j = 1;
		while($j <= 12){
			$month_detail[] = date('M,y', strtotime($fin_start_year_cal." +$j month"));
			$j++;
		}

		// iterate the clients
		$i = 0;
		foreach($user_detail as $user){
			
			// month condition
			$month_str = date('Y-m', strtotime($fin_start_year_cal." +$i month"));
			$month_cond = array('Report.created_date like' => "$month_str%");
			$month_cond2 = array('Position.created_date like' => "$month_str%");
			// get all the openings of the client		
		
			$ow_options = array(				
				array('table' => 'clients',
					'alias' => 'Client',				
					'type' => 'LEFT',
					'conditions' => array('`Client`.`id` = `Report`.`clients_id`')					
					),	
				array('table' => 'req_team',
					'alias' => 'ReqTeam',					
					'type' => 'LEFT',
					'conditions' => array('`ReqTeam`.`requirements_id` = `Report`.`id`')
				),					
			);	
			
			$opening_worked[] = $this->Report->find('all', array('fields' => array("sum(Report.no_job) no_job"), 'conditions' => array('Report.status' => 'A', 'Report.is_approve' => 'A', $month_cond, $emp_cond),
			'joins' => $ow_options));
			
			$position_worked = $this->Report->find('all', array('fields' => array("group_concat(Distinct Report.id) pos_work"), 'conditions' => array('Report.status' => 'A', 'Report.is_approve' => 'A', $month_cond, $emp_cond),'joins' => $ow_options));
			
			$pos_split = explode(',', $position_worked[0][0]['pos_work']);
			$pos_work_cond = array('Position.id' => $pos_split);
			// get all the cv sent details of the client positions
			
			$this->loadModel('ReqResumeStatus');
			$options = array(
					array('table' => 'users',
						'alias' => 'User',					
						'type' => 'LEFT',
						'conditions' => array('`User`.`id` = `ReqResume`.`created_by`')
					),
					array('table' => 'location',
						'alias' => 'Location',					
						'type' => 'LEFT',
						'conditions' => array('`Location`.`id` = `User`.`location_id`')
					)
					,
					array('table' => 'resume',
						'alias' => 'Resume',					
						'type' => 'LEFT',
						'conditions' => array('`Resume`.`id` = `ReqResume`.`resume_id`')
					),			
					array('table' => 'requirements',
						'alias' => 'Position',					
						'type' => 'LEFT',
						'conditions' => array('`Position`.`id` = `ReqResume`.`requirements_id`')
					),
					array('table' => 'clients',
						'alias' => 'Client',					
						'type' => 'LEFT',
						'conditions' => array('`Client`.`id` = `Position`.`clients_id`')
					),
					array('table' => 'req_team',
						'alias' => 'ReqTeam',					
						'type' => 'LEFT',
						'conditions' => array('`ReqTeam`.`requirements_id` = `Position`.`id`')
					)
				);
			$sent_cond = array('ReqResumeStatus.stage_title' => 'Shortlist','ReqResumeStatus.status_title' => 'CV-Sent');
			$cv_sent_count[] = $this->ReqResumeStatus->find('count', array('conditions' => array($sent_cond,$month_cond2,$req_team_cond, $emp_cond, $client_cond,$pos_work_cond), 'joins' => $options));			
			
			// get the CV Sent to shortlisted percentage
			$i++;
		}
		
		$this->set('monthDetail', $month_detail);
		
		// for export
		if($this->request->query['action'] == 'export'){			
			$this->Excel->generate_report('month_wise_cv_status_report', $client_detail, $opening_worked,
			$cv_sent_count,$cv_shortlist_count,$cv_reject_count,$feedback_await_count,$interview_await_count,
			$prili_interview_count,$final_interview_count,$offer_pending_count,$offer_accept_count,$offer_reject_count,
			$join_pending_count,$join_accept_count,$join_reject_count,$join_defer_count,
			$not_billed_count,$billed_count,'client wise cv status', 'client wise cv status');
		}
		
	
		// for pdf generation
		if($this->request->query['action'] == 'pdf'){
			$pdf_file = 'month_wise_cv_status_'.date('y-m-d');
			
			include('vendor/dompdf_0-8-2/pdf.php');
		}
					


		// die;

		// assign all the counts
		$this->set('OPENING_WORKED', $opening_worked);
		$this->set('CV_SENT', $cv_sent_count);
		$this->set('CV_SHORTLIST', $cv_shortlist_count);
		$this->set('CV_REJECT', $cv_reject_count);
		$this->set('FEEDBACK_AWAITING', $feedback_await_count);
		$this->set('INTERVIEW_AWAITING', $interview_await_count);
		$this->set('PRILIMINARY_INTERVIEW_ATTEND', $prili_interview_count);
		$this->set('FINAL_INTERVIEW_ATTEND', $final_interview_count);
		
		$this->set('OFFER_PENDING', $offer_pending_count);
		$this->set('OFFER_ACCEPT', $offer_accept_count);
		$this->set('OFFER_REJECT', $offer_reject_count);
		
		$this->set('JOIN_PENDING', $join_pending_count);
		$this->set('JOIN_ACCEPT', $join_accept_count);
		$this->set('NOT_JOIN', $join_reject_count);		
		$this->set('JOIN_DEFER', $join_defer_count);
		
		$this->set('NOT_BILLED', $not_billed_count);		
		$this->set('BILLED', $billed_count);
		
		$count_client = count($client_data);
		$this->set('chart_height', $count_client < 10 ? '500' :  $count_client*50);
		
		
		
	}

	/* function to show employee business conversion */
	public function employee_business_conversion(){
		
		
	}
	
	/* function to show recruiter wise billing */
	public function recruiter_wise_billing(){
		
		
	}							
														
												
														
	
	/* function to load the clients */
	public function get_client_details($client_id){
		$this->loadModel('Client');
		$client_cond = $client_id ? array('Client.id' => $client_id) : '';
		$client_list = $this->Client->find('list', array('fields' => array('id','client_name'), 
		'order' => array('client_name ASC'),'conditions' => array('Client.is_deleted' => 'N', 'Client.status' => '0', 'Client.is_approve' => 'A', 'Client.client_name !=' => '', 'Client.is_inactive' => 'N', $client_cond ),	'group' => array('Client.id')));
		return $client_list;
	}
	
	
	/* function to load the clients */
	public function get_role_details(){
		$this->loadModel('Role');
		$role_data = $this->Role->find('list',  array('fields' => array('id','role_name'), 'order' => array('role_name ASC'),'conditions' => array('status' => '1')));
		$this->set('roleList', $role_data);
	}
	
	
	/* auto complete search */	
	public function search(){ 
		$this->layout = false;		
		$q = trim(Sanitize::escape($_GET['q']));
		$this->loadModel('Client');
		if(!empty($q)){
			// execute only when the search keywork has value		
			$this->set('keyword', $q);			
			$this->Client->unBindModel(array('belongsTo' => array('User','ResLocation')));
			$data = $this->Client->find('all', array('fields' => array('Client.client_name'),
			'group' => array('Client.client_name'), 'conditions' => 	array("OR" => array ('Client.client_name like' => '%'.$q.'%'), 
			'AND' => array('Client.is_deleted' => 'N'))));		
			$this->set('results', $data);
		}
    }
	
		// check the role permissions
	public function beforeFilter(){
		$this->check_session();
		$this->set($this->request->params['action'].'_sidebar', 'in');
		$this->set($this->request->params['action'].'_sidebar_menu', 'active');
		$this->check_role_access(17);
	}
}