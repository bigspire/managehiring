<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
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



/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
App::uses('Sanitize', 'Utility');   
 
class HomeController  extends AppController {	
	
	public $name = 'Home';	
	
	public $helpers = array('Html', 'Form', 'Session');
	
	public $components = array('Session', 'Functions');
	
	public function index($dash_type){ 
	
	
		// set the page title
		$this->set('title_for_layout', 'Home - Manage Hiring');
		// set the validation
		$this->Home->set($this->request->data);
		if(!empty($this->request->data)){
			// validate the form
			//$validate = $this->Home->validates(array('fieldList' => array('to')));
			$validate = $this->Home->check_diff();
			if(!$validate){
				$this->Session->setFlash('<button type="button" class="close" data-dismiss="alert">&times;</button>Date diff. should not be more than 6 months (150 days)', 'default', array('class' => 'alert alert-error'));									
				$this->redirect('/home/');
			}
		}	
		
		
		// when the form is submitted for search
		if($this->request->is('post')){
			$url_vars = $this->Functions->create_url(array('from','to','loc','emp_id','srchSubmit','client','type'),'Home');		
			$this->redirect('/home/?'.$url_vars);				
		}
		// get the employee details
		$this->set('empList', $this->Home->get_employee_details());	
		// get the loc details
		$this->set('locList', $this->get_loc_details());
		// apply date conditions
		// for testing date changed
		$dateFrm = date('Y-m-d', strtotime('-30 days'));
		//$dateFrm = '2017-06-01';
		//$dateTo = '2017-06-10';
		$dateTo = date('Y-m-d');
		$start = $this->request->query['from'] ? $this->Functions->format_date_save($this->request->query['from']) : $dateFrm;
		$end = $this->request->query['to'] ? $this->Functions->format_date_save($this->request->query['to']) : $dateTo;
		
		$start_chart = date('Y-m-d', strtotime('-6 days'));
		$end_chart = date('Y-m-d');
		// set date condition				
		$this->request->query['from'] = $this->Functions->format_date_show($start);
		$this->request->query['to'] = $this->Functions->format_date_show($end);	
		
		$this->set('chartStart', $this->Functions->format_date_show($start_chart));
		$this->set('chartEnd', $this->Functions->format_date_show($end_chart));
		
		// for employee condition
		/*
		if($this->request->query['emp_id'] != ''){			
			//$pos_emp_cond = array('ReqTeam.users_id' => $this->request->query['emp_id']);
			$cv_emp_cond = array('ReqResumeStatus.created_by' => $this->request->query['emp_id']);
			//$int_emp_cond = array('ReqResumeStatus.modified_by' => $this->request->query['emp_id']);
		}else if($this->Session->read('USER.Login.rights') != '5'){			
			//$pos_emp_cond = array('ReqTeam.users_id' => $this->request->query['emp_id']);
			$cv_emp_cond = array('ReqResumeStatus.created_by' => $this->Session->read('USER.Login.id'));
			//$int_emp_cond = array('ReqResumeStatus.modified_by' => $this->Session->read('USER.Login.rights'));
		}
		*/
		
		/*
		if($this->request->query['emp_id'] != ''){			
			$cv_emp_cond = array('ReqResumeStatus.created_by' => $this->request->query['emp_id']);
			$int_emp_cond = array('ReqResumeStatus.created_by' => $this->request->query['emp_id']);
			$pos_emp_cond = array('ReqResume.created_by' => $this->request->query['emp_id']);
			$cv_sent_emp_cond = array('Resume.created_by' => $this->request->query['emp_id']);
			$client_emp_cond = array('Client.created_by' => $this->request->query['emp_id']);
		}else if($this->Session->read('USER.Login.rights') != '5'){			
			$cv_emp_cond = array('ReqResumeStatus.created_by' => $this->Session->read('USER.Login.id'));
			$int_emp_cond = array('ReqResumeStatus.created_by' => $this->Session->read('USER.Login.id'));
			$pos_emp_cond = array('ReqResume.created_by' => $this->Session->read('USER.Login.id'));
			$cv_sent_emp_cond = array('Resume.created_by' => $this->Session->read('USER.Login.id'));
			$client_emp_cond = array('Client.created_by' => $this->Session->read('USER.Login.id'));
		}
		*/
		
		/* for dashboard switching */
		if($dash_type == 'rec_view' || $this->Session->read('USER.Login.roles_id') == '30'){ 
			$cv_emp_cond = array('ReqResume.created_by' => $this->Session->read('USER.Login.id'));
			$int_emp_cond = array('ReqResume.created_by' => $this->Session->read('USER.Login.id'));
			$pos_emp_cond = array('ReqTeam.users_id' => $this->Session->read('USER.Login.id'), 'ReqTeam.is_approve' => 'A');
			$cv_sent_emp_cond = array('Resume.created_by' => $this->Session->read('USER.Login.id'));
			$resume_options = array(			
				array('table' => 'req_resume',
						'alias' => 'ReqResume',					
						'type' => 'LEFT',
						'conditions' => array('`ReqResume`.`resume_id` = `Resume`.`id`')
				)
			);
			$cli_options = array(						
				array('table' => 'requirements',
						'alias' => 'Position',					
						'type' => 'LEFT',
						'conditions' => array('`Position`.`clients_id` = `Client`.`id`',
						'Position.status' => 'A')
				),
				array('table' => 'req_team',
						'alias' => 'ReqTeam',					
						'type' => 'LEFT',
						'conditions' => array('`ReqTeam`.`requirements_id` = `Position`.`id`',
						'ReqTeam.is_approve' => 'A')
				),
				array('table' => 'req_resume',
						'alias' => 'ReqResume',					
						'type' => 'LEFT',
						'conditions' => array('`ReqResume`.`requirements_id` = `Position`.`id`')
				),
				array('table' => 'req_approval_status',
						'alias' => 'PositionStatus',					
						'type' => 'LEFT',
						'conditions' => array('`PositionStatus`.`requirements_id` = `Position`.`id`', 'member_approve' => 'A')
				)
			);	
			$pos_emp_cond2 = array('ReqTeam.users_id' => $this->Session->read('USER.Login.id'), 'ReqTeam.is_approve' => 'A');	
			
			// $client_emp_cond = array('ReqTeam.users_id' => $this->Session->read('USER.Login.id'));
			
			$client_emp_cond = array('OR' => array(
					'ReqResume.created_by' =>  $this->Session->read('USER.Login.id'),
					'ReqTeam.users_id' => $this->Session->read('USER.Login.id')
					)
			);
			$this->set('rec_dash', 'active');

			
		}else if($this->Session->read('USER.Login.roles_id') == '40' || $this->Session->read('USER.Login.roles_id') == '37'){ // for Vertical Lead
			$cli_options = array(						
				array('table' => 'requirements',
						'alias' => 'Position',					
						'type' => 'LEFT',
						'conditions' => array('`Position`.`clients_id` = `Client`.`id`',
						'Position.status' => 'A')
				),
				array('table' => 'client_account_holder',
						'alias' => 'ClientAH',					
						'type' => 'LEFT',
						'conditions' => array('`Client`.`id` = `ClientAH`.`clients_id`',
						'ClientAH.users_id' => $this->Session->read('USER.Login.id'))
				)
			);
			$resume_options = array(
				array('table' => 'req_resume',
						'alias' => 'ReqResume',					
						'type' => 'LEFT',
						'conditions' => array('`ReqResume`.`resume_id` = `Resume`.`id`')
				),
				array('table' => 'requirements',
						'alias' => 'Position',					
						'type' => 'LEFT',
						'conditions' => array('`Position`.`id` = `ReqResume`.`requirements_id``')
				),
				array('table' => 'clients',
						'alias' => 'Client',					
						'type' => 'LEFT',
						'conditions' => array('`Position`.`clients_id` = `Client`.`id`')
				),				
				array('table' => 'client_account_holder',
						'alias' => 'ClientAH',					
						'type' => 'LEFT',
						'conditions' => array('`Client`.`id` = `ClientAH`.`clients_id`')
				)
			);
			$count_options = array(				
				array('table' => 'clients',
						'alias' => 'Client',					
						'type' => 'LEFT',
						'conditions' => array('`Position`.`clients_id` = `Client`.`id`')
				),				
				array('table' => 'client_account_holder',
						'alias' => 'ClientAH',					
						'type' => 'LEFT',
						'conditions' => array('`Client`.`id` = `ClientAH`.`clients_id`',
						'ClientAH.users_id' => $this->Session->read('USER.Login.id'))
				)
			);
			$ac_join = array(							
				array('table' => 'client_account_holder',
						'alias' => 'ClientAH',					
						'type' => 'LEFT',
						'conditions' => array('`Client`.`id` = `ClientAH`.`clients_id`')
				)
			);
			$cv_emp_cond = array('ClientAH.users_id' => $this->Session->read('USER.Login.id'));
			$int_emp_cond =  array('ClientAH.users_id' => $this->Session->read('USER.Login.id'));
			$pos_emp_cond =  array('ClientAH.users_id' => $this->Session->read('USER.Login.id'), 'Home.status' => 'A');
			$cv_sent_emp_cond =  array('ClientAH.users_id' => $this->Session->read('USER.Login.id'));			
			$client_emp_cond = array('ClientAH.users_id' => $this->Session->read('USER.Login.id'));
			$pos_emp_cond2 =  array('ClientAH.users_id' => $this->Session->read('USER.Login.id'), 'Position.status' => 'A');
			$this->set('ac_dash', 'active');
		}else if($this->Session->read('USER.Login.roles_id') == '33'  || $this->Session->read('USER.Login.roles_id') == '35'  || $this->Session->read('USER.Login.roles_id') == '39'){ // director
			$resume_options = array(			
				array('table' => 'req_resume',
						'alias' => 'ReqResume',					
						'type' => 'LEFT',
						'conditions' => array('`ReqResume`.`resume_id` = `Resume`.`id`')
				)
			);
			$cv_emp_cond = '';
			$int_emp_cond = '';
			$pos_emp_cond = array('Home.status' => 'A');
			$cv_sent_emp_cond = '';
			$pos_emp_cond2 = array('Position.status' => 'A');
			// get recent clients
			$cli_options = array(						
				array('table' => 'requirements',
						'alias' => 'Position',					
						'type' => 'LEFT',
						'conditions' => array('`Position`.`clients_id` = `Client`.`id`',
						'Position.status' => 'A')
				)
			);
			// $client_emp_cond = array('Client.created_by' => $this->Session->read('USER.Login.id'));
			$this->set('bd_dash', 'active');
		}else if($this->Session->read('USER.Login.roles_id') == '38'){ // branch admin
			// get the branch users
			$user_data = $this->Home->Creator->find('all', array('conditions' => array('Creator.location_id' => $this->Session->read('USER.Login.location_id'),
			'Creator.is_deleted' => 'N'),
			'fields' => array('Creator.id')));
			foreach($user_data as $rec){
				$branch_user[] =  $rec['Creator']['id'];
			}
			$resume_options = array(			
				array('table' => 'req_resume',
						'alias' => 'ReqResume',					
						'type' => 'LEFT',
						'conditions' => array('`ReqResume`.`resume_id` = `Resume`.`id`')
				)
			);
			$cv_emp_cond = array('ReqResume.created_by' => $branch_user);
			$int_emp_cond =  array('ReqResume.created_by' => $branch_user);
			$pos_emp_cond =  array('ReqResume.created_by' => $branch_user, 'Home.status' => 'A');
			$cv_sent_emp_cond =  array('ReqResume.created_by' => $branch_user);			
			$client_emp_cond = array('ReqResume.created_by' => $branch_user);
			$pos_emp_cond2 =  array('ReqResume.created_by' => $branch_user, 'Position.status' => 'A');
			// get recent clients
			$cli_options = array(						
				array('table' => 'requirements',
						'alias' => 'Position',					
						'type' => 'LEFT',
						'conditions' => array('`Position`.`clients_id` = `Client`.`id`',
						'Position.status' => 'A')
				)
			);
			 $client_emp_cond = array('Client.created_by' => $branch_user);
		}else{ // for all roles, same as recruiter
			$cv_emp_cond = array('ReqResume.created_by' => $this->Session->read('USER.Login.id'));
			$int_emp_cond = array('ReqResume.created_by' => $this->Session->read('USER.Login.id'));
			$pos_emp_cond = array('ReqResume.created_by' => $this->Session->read('USER.Login.id'));
			$cv_sent_emp_cond = array('Resume.created_by' => $this->Session->read('USER.Login.id'));
			$resume_options = array(			
				array('table' => 'req_resume',
						'alias' => 'ReqResume',					
						'type' => 'LEFT',
						'conditions' => array('`ReqResume`.`resume_id` = `Resume`.`id`')
				)
			);
			$cli_options = array(						
				array('table' => 'requirements',
						'alias' => 'Position',					
						'type' => 'LEFT',
						'conditions' => array('`Position`.`clients_id` = `Client`.`id`')
				),
				array('table' => 'req_team',
						'alias' => 'ReqTeam',					
						'type' => 'LEFT',
						'conditions' => array('`ReqTeam`.`requirements_id` = `Position`.`id`',
						'ReqTeam.is_approve' => 'A')
				),
				array('table' => 'req_resume',
						'alias' => 'ReqResume',					
						'type' => 'LEFT',
						'conditions' => array('`ReqResume`.`requirements_id` = `Position`.`id`')
				),
				array('table' => 'req_approval_status',
						'alias' => 'PositionStatus',					
						'type' => 'LEFT',
						'conditions' => array('`PositionStatus`.`requirements_id` = `Position`.`id`', 'member_approve' => 'A')
				)
			);	
			$pos_emp_cond2 = array('ReqResume.created_by' => $this->Session->read('USER.Login.id'));			
			
			// $client_emp_cond = array('ReqTeam.users_id' => $this->Session->read('USER.Login.id'));
			
			$client_emp_cond = array('OR' => array(
					'ReqResume.created_by' =>  $this->Session->read('USER.Login.id'),
					'ReqTeam.users_id' => $this->Session->read('USER.Login.id')
					)
			);
		}
		
		// for branch condition
		if($this->request->query['loc'] != ''){					
			$branch_cond = array('Creator.location_id' => $this->request->query['loc']);
			$cv_branch_cond = array('User.location_id' => $this->request->query['loc']);
		}
		// for client condition
		if($this->request->query['client'] != ''){					
			$client_cond = array('Client.client_name' => $this->request->query['client']);			
		}		
		// generate graph		
		$chart_date = $this->generate_chart_date($start_chart, $end_chart);
		// check the graph type
		
		foreach($chart_date as $date){ 
			// get the requirements count
			$req_count_data = $this->get_position_count($date,$pos_emp_cond,$ac_join,$branch_cond,$client_cond);	
			$req_count[] = count($req_count_data);
			$req_data[] = $req_count_data;
			if($this->request->query['type'] == 'req'){
				// get the IN conditions
				foreach($req_count_data as $req_detail){
					$req_id[] = $req_detail['Home']['id'];
				}
				$reqCond = array('Position.id' => $req_id);
				unset($req_id);				
			}
			
			// get cv sent details
			$cv_sent_data = $this->get_cv_sent_count($date,$cv_sent_emp_cond,$cv_branch_cond,$client_cond,$reqCond);
			$cv_sent[] = count($cv_sent_data);
			$cv_sent_detail[] = $cv_sent_data;
			// get cv shortlist details
			$cv_shortlist_data = $this->get_cv_shortlist_count($date,$cv_emp_cond,$cv_branch_cond,$client_cond,$reqCond);
			$cv_shortlist[] = count($cv_shortlist_data);
			$cv_shortlist_detail[] = $cv_shortlist_data;
			// get cv reject details
			$cv_reject_data = $this->get_cv_reject_count($date,$cv_emp_cond,$cv_branch_cond,$client_cond,$reqCond);
			$cv_reject[] = count($cv_reject_data);
			$cv_reject_detail[] = $cv_reject_data;
			// get cv feedback awaiting
			$cv_waiting_data = $this->get_cv_waiting_count($date,$cv_emp_cond,$cv_branch_cond,$client_cond,$reqCond);
			$cv_waiting[] = count($cv_waiting_data);
			$cv_waiting_detail[] = $cv_waiting_data;
			// get cv on hold 
			/* $cv_hold_data = $this->get_cv_hold_count($date,$cv_emp_cond,$cv_branch_cond,$client_cond);
			$cv_hold[] = count($cv_hold_data);
			$cv_hold_detail[] = $cv_hold_data; */
			// get interview data 
			$cv_interview_data = $this->get_interview_count($date,$int_emp_cond,$cv_branch_cond,$client_cond,$reqCond);
			$cv_interview[] = count($cv_interview_data);
			$cv_interview_detail[] = $cv_interview_data;
			// get cand. offer data 
			$cv_offer_data = $this->get_offer_count($date,$int_emp_cond,$cv_branch_cond,$client_cond,$reqCond);
			$cv_offer[] = count($cv_offer_data);
			$cv_offer_detail[] = $cv_offer_data;
			// get cand. offer drop outs data 
			$cv_offer_reject_data = $this->get_offer_reject_count($date,$int_emp_cond,$cv_branch_cond,$client_cond,$reqCond);
			$cv_offer_reject[] = count($cv_offer_reject_data);
			$cv_offer_reject_detail[] = $cv_offer_reject_data;
			// get cand. interview drop outs data 
			$cv_int_drop_data = $this->get_int_drop_count($date,$int_emp_cond,$cv_branch_cond,$client_cond,$reqCond);
			$cv_int_drop[] = count($cv_int_drop_data);
			$cv_int_drop_detail[] = $cv_int_drop_data;
			// get cand. interview reject  data 
			$cv_int_reject_data = $this->get_int_reject_count($date,$int_emp_cond,$cv_branch_cond,$client_cond,$reqCond);
			$cv_int_reject[] = count($cv_int_reject_data);
			$cv_int_reject_detail[] = $cv_int_reject_data;
			// get cand. joined data 
			$cv_join_data = $this->get_can_join_count($date,$int_emp_cond,$cv_branch_cond,$client_cond,$reqCond);
			$cv_join[] = count($cv_join_data);
			$cv_join_detail[] = $cv_join_data;
			// get cand. billing data 
			$cv_bill_data = $this->get_can_billing_count($date,$int_emp_cond,$cv_branch_cond,$client_cond,$reqCond);
			$cv_bill[] = count($cv_bill_data);
			$cv_bill_detail[] = $cv_bill_data;
			// format the dates
			$chart_format[] = date('d-M', strtotime($date));				
		}		
		
		// assign all data
		$this->set('REQ_DATA', $req_data);
		$this->set('CV_SENT_DATA', $cv_sent_detail);
		$this->set('CV_SHORTLIST_DATA', $cv_shortlist_detail);
		$this->set('CV_WAITING_DATA', $cv_waiting_detail);
		$this->set('CV_INTERVIEW_DATA', $cv_interview_detail);
		$this->set('CV_OFFER_DATA', $cv_offer_detail);
		$this->set('CV_OFFER_REJECT_DATA', $cv_offer_reject_detail);
		$this->set('CV_INT_REJECT_DATA', $cv_int_reject_detail);
		$this->set('CV_JOIN_DATA', $cv_join_detail);
		$this->set('CV_INT_DROP_DATA', $cv_int_drop_detail);
		$this->set('CV_REJECT_DATA', $cv_reject_detail);
		$this->set('CV_BILL_DATA', $cv_bill_detail);
		/* assign all counts */
		$this->set('REQ_COUNT', $req_count);
		$this->set('CV_SENT', $cv_sent);
		$this->set('CV_SHORTLIST', $cv_shortlist);
		$this->set('CV_WAITING', $cv_waiting);
		$this->set('CV_INTERVIEW', $cv_interview);
		$this->set('CV_OFFER', $cv_offer);
		$this->set('CV_OFFER_REJECT', $cv_offer_reject);
		$this->set('CV_INT_REJECT', $cv_int_reject);
		$this->set('CV_JOIN', $cv_join);
		$this->set('CV_INT_DROP', $cv_int_drop);
		$this->set('CV_REJECT', $cv_reject);
		$this->set('CV_BILL', $cv_bill);			
		// assign date counts
		$this->set('DATE_COUNT', $chart_format);
		$this->set('START_DATE', date('d-M', strtotime($start)));
		$this->set('END_DATE', date('d-M', strtotime($end)));
		
		$this->set('START_CHART_DATE', date('d-M', strtotime($start_chart)));
		$this->set('END_CHART_DATE', date('d-M', strtotime($end_chart)));
		
		// for detailed graph
		if($this->request->query['action'] == 'view_graph'){
			$this->render('view_graph');
		}
		
		$date_cond = array('or' => array("DATE_FORMAT(Client.created_date, '%Y-%m-%d') between ? and ?" => array($start, $end)));
		$fields = array('id','client_name','ResLocation.location','created_date','Creator.first_name',
		"count(distinct Position.id) req_count");
		$conditions = array('fields' => $fields,'limit' => '10','conditions' => array($keyCond,$date_cond,$client_emp_cond, 
		'Client.is_approve' => 'A'),
		'order' => array('Client.created_date' => 'desc'),	'group' => array('Client.id'), 'joins' => $cli_options);
		$data = $this->Home->Client->find('all', $conditions);
		$this->set('client_data', $data);
		// get recent positions
		$this->loadModel('Position');
		$pos_options = array(						
			array('table' => 'req_team',
				'alias' => 'ReqTeam',					
				'type' => 'LEFT',
				'conditions' => array('`ReqTeam`.`requirements_id` = `Position`.`id`',
				'ReqTeam.is_approve' => 'A')
			),
			array('table' => 'client_account_holder',
				'alias' => 'ClientAH',					
				'type' => 'LEFT',
				'conditions' => array('`Client`.`id` = `ClientAH`.`clients_id`')
			)
		);
		
	


		$date_cond = array('or' => array("DATE_FORMAT(Position.created_date, '%Y-%m-%d') between ? and ?" => array($start, $end)));
		$fields = array('id','job_title','location','Client.client_name', 'Creator.first_name','created_date',
		 'group_concat((case when ReqResume.stage_title != \'Validation - Account Holder\' and ReqResume.stage_title != \'Draft\' then 1 else \'\' end)) AS cv_stage','group_concat(ReqResume.id) req_resume_id', 
		'ReqStatus.title', 'ReqTeam.no_req', 'ctc_from','ctc_to','ctc_from_type','ctc_to_type');	
		// $pos_emp_cond2,
		$conditions = array('fields' => $fields,'conditions' => array($date_cond,
		'Position.status' => 'A'),	'order' => array('Position.created_date' => 'desc'),	'group' => array('Position.id'), 'joins' => $pos_options);
		$this->Position->unBindModel(array('belongsTo' => array('FunctionArea')));
		$data = $this->Position->find('all', $conditions);
		$this->set('position_data', $data);
		$this->set('POS_TAB_COUNT', count($data));
		// only for MOP for recruiters and account holders
		if($this->Session->read('USER.Login.roles_id') == '30' || $dash_type == 'rec_view'){
			// count the vacancy
			foreach($data as $record){
				$no_job += $record['ReqTeam']['no_req'];
				$ctc_from = $record['Position']['ctc_from_type'] == 'T' ?  round($record['Position']['ctc_from']/100, 1) : $record['Position']['ctc_from'];
				$ctc_to = $record['Position']['ctc_to_type'] == 'T' ?  round($record['Position']['ctc_to']/100, 1) : $record['Position']['ctc_to'];
				$ctc += round(($ctc_from+$ctc_to)/2, 1) * $record['ReqTeam']['no_req'];
			}
			$this->set('VACANCY_MOP_COUNT', $no_job);
			$this->set('BUSINESS_VALUE_MOP_COUNT', $ctc);
		}
		$date_cond = array('or' => array("DATE_FORMAT(ReqResume.modified_date, '%Y-%m-%d') between ? and ?" => 	array($start, $end)));
		// get recent resumes sent
		$this->loadModel('Resume');		
		$fields = array('id',"concat(Resume.first_name,' ',Resume.last_name) full_name",'email_id','mobile', 'Creator.first_name',
		'ReqResume.stage_title','ReqResume.status_title','ReqResume.modified_date','ReqResume.cv_sent_date');	
		$conditions = array('fields' => $fields,'limit' => '50','conditions' => array($date_cond, $int_emp_cond,
		'ReqResume.stage_title' =>   array( 'Shortlist', 'Validation - Account Holder'),  'Resume.is_deleted' => 'N'),
		'order' => array('ReqResume.modified_date' => 'desc'),'group' => array('Resume.id'), 'joins' => $resume_options);
		$data = $this->Resume->find('all', $conditions);
		$this->set('resume_data', $data);
		// get recent interviews
		/*
		$interview_options = array(
				array('table' => 'req_resume',
						'alias' => 'ReqResume',					
						'type' => 'LEFT',
						'conditions' => array('`ReqResume`.`resume_id` = `Resume`.`id`')
				),
				array('table' => 'req_resume_interview',
						'alias' => 'ResInterview',					
						'type' => 'LEFT',
						'conditions' => array('`ReqResume`.`id` = `ResInterview`.`req_resume_id`')
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
				array('table' => 'client_account_holder',
						'alias' => 'ClientAH',					
						'type' => 'LEFT',
						'conditions' => array('`Client`.`id` = `ClientAH`.`clients_id`')
				)
		);
		*/
		$fields = array('id',"concat(Resume.first_name,' ',Resume.last_name) full_name",'email_id','mobile', 'Creator.first_name',
		'ReqResume.modified_date','Resume.modified_date','ReqResume.stage_title','ReqResume.status_title','ReqResume.int_date');			
		$conditions = array('fields' => $fields,'limit' => '50','conditions' => array($int_emp_cond,$date_cond,
		'ReqResume.stage_title like' => '%Interview', 'Resume.is_deleted' => 'N'),
		'order' => array('ReqResume.modified_date' => 'desc'), 'group' => array('Resume.id'), 'joins' => $resume_options);
		$data = $this->Resume->find('all', $conditions);
		$this->set('interview_data', $data);
		// get recent resumes with offers
		$fields = array('id',"concat(Resume.first_name,' ',Resume.last_name) full_name",'email_id','mobile', 'Creator.first_name',
		'ReqResume.modified_date','Resume.modified_date','ReqResume.stage_title','ReqResume.status_title', 'ReqResume.date_offer');		
		$conditions = array('fields' => $fields,'limit' => '50','conditions' => array('ReqResume.stage_title' => 'Offer', 
		$date_cond,$int_emp_cond, 'ReqResume.stage_title' => 'Offer', 'Resume.is_deleted' => 'N'),'order' => array('ReqResume.modified_date' => 'desc'),
		'group' => array('Resume.id'), 'joins' => $resume_options);
		$data = $this->Resume->find('all', $conditions);
		$this->set('offer_data', $data);
		// get resent resumes joinees
		$fields = array('id',"concat(Resume.first_name,' ',Resume.last_name) full_name",'email_id','mobile', 'Creator.first_name',
		'Resume.created_date','ReqResume.modified_date','ReqResume.stage_title','ReqResume.status_title', 'ReqResume.plan_join_date',
		'ReqResume.joined_on');		
		$conditions = array('fields' => $fields,'limit' => '50','conditions' => array('ReqResume.stage_title' => 'Joining', 
		$date_cond,$int_emp_cond, 'ReqResume.stage_title' => 'Joining', 'Resume.is_deleted' => 'N'),'order' => array('ReqResume.modified_date' => 'desc'),		'group' => array('Resume.id'), 'joins' => $resume_options);
		$data = $this->Resume->find('all', $conditions);
		$this->set('join_data', $data);
		
		// get the total counts of positions
		/*
		$date_cond = array('or' => array("DATE_FORMAT(Position.created_date, '%Y-%m-%d') between ? and ?" => array($start, $end)));
		$pos_count_tab = $this->Position->find('count', array('conditions' => array('Position.req_status_id' => array('1', '2'),
		$pos_emp_cond,$date_cond)));
		*/
		// get the total counts of cv sent
		$this->loadModel('ReqResume');
		$date_cond = array('or' => array("DATE_FORMAT(ReqResume.modified_date, '%Y-%m-%d') between ? and ?" => array($start, $end)));
		$cv_sent_count_tab = $this->ReqResume->find('count', array('conditions' => array('ReqResume.stage_title !=' => 
		array('Validation - Account Holder'), $cv_emp_cond,$date_cond), 'joins' => $count_options));
		$this->set('CV_SENT_TAB_COUNT', $cv_sent_count_tab);
		// get the total counts of cv shortlisted
		$cv_shortlist_count_tab = $this->ReqResume->find('count', array('conditions' => array('ReqResume.status_title' => 'Shortlisted',
		$cv_emp_cond,$date_cond), 'joins' => $count_options));
		$this->set('CV_SHORTLIST_TAB_COUNT', $cv_shortlist_count_tab);
		// get the total counts of cv rejected
		$cv_reject_count_tab = $this->ReqResume->find('count', array('conditions' => array('ReqResume.stage_title' => 'Shortlist', 'ReqResume.status_title' => 'Rejected',
		$cv_emp_cond,$date_cond), 'joins' => $count_options));
		$this->set('CV_REJECT_TAB_COUNT', $cv_reject_count_tab);
		// get the total counts of cv waiting or hold
		$validate_cond_waiting = array("OR" => array ('ReqResume.status_title' => 'YRF',
		'ReqResume.status_title' => 'CV-Sent',	'ReqResume.status_title' => 'OnHold'));
		$cv_waiting_count_tab = $this->ReqResume->find('count', array('conditions' => array($validate_cond_waiting,
		$cv_emp_cond,$date_cond), 'joins' => $count_options));
		$this->set('CV_WAITING_TAB_COUNT', $cv_waiting_count_tab);
		// get the total counts of candidates interviewed		
		// $date_cond = array('or' => array("DATE_FORMAT(ResInterview.int_date, '%Y-%m-%d') between ? and ?" => array($start, $end)));
		$cv_interview_count_tab = $this->ReqResume->find('count', array('conditions' => array('ReqResume.status_title like' => '%Scheduled',
		$cv_emp_cond,$date_cond), 'joins' => $count_options));
		$this->set('INTERVIEW_TAB_COUNT', $cv_interview_count_tab);
		
		if($this->Session->read('USER.Login.roles_id') == '30' || $dash_type == 'rec_view' ){
			// pecentage of final interview - MOP
			$mop_options = array(			
				array('table' => 'resume',
						'alias' => 'Resume',					
						'type' => 'LEFT',
						'conditions' => array('`Resume`.`id` = `ReqResume`.`resume_id`')
				),
				array('table' => 'req_resume_interview',
					'alias' => 'ResInterview',					
					'type' => 'LEFT',
					'conditions' => array('`ResInterview`.`req_resume_id` = `ReqResume`.`id`')
				),
				array('table' => 'requirements',
						'alias' => 'Position',					
						'type' => 'LEFT',
						'conditions' => array('`Position`.`id` = `ReqResume`.`requirements_id`')
				),
				array('table' => 'clients',
						'alias' => 'Client',					
						'type' => 'LEFT',
						'conditions' => array('`Position`.`clients_id` = `Client`.`id`')
				),
				array('table' => 'client_account_holder',
						'alias' => 'ClientAH',					
						'type' => 'LEFT',
						'conditions' => array('`Client`.`id` = `ClientAH`.`clients_id`')
				)
			);
			$mop_date_cond = array('or' => array("DATE_FORMAT(ResInterview.int_date, '%Y-%m-%d') between ? and ?" => array($start, $end)));		
			$this->loadModel('ReqResumeStatus');
			$count_interview = $this->ReqResumeStatus->find('all', array('fields' => array('ReqResumeStatus.stage_title'),'conditions' => 
			array($mop_date_cond,$int_emp_cond,'ReqResumeStatus.stage_title like' => '% Interview'), 'joins' => $mop_options,'group' => array('Resume.id')));
			foreach($count_interview as $int_data){
				$int_attend += 1;
				if($int_data['ReqResumeStatus']['stage_title'] == 'Final Interview'){
					$final_int += 1;
				}
			}
			$this->set('INTERVIEW_MOP_PERCENT_COUNT', round(($final_int/$int_attend)*100, 1).'%');
		}
		/*
		// get the total counts of candidates interview drop outs
		$cv_int_drop_count_tab = $this->ReqResume->find('count', array('conditions' => array('ReqResume.stage_title like' => '%Interview', 'ReqResume.status_title' => 'No Show',
		$cv_emp_cond,$date_cond), 'joins' => $options));
		$this->set('INTERVIEW_DROP_TAB_COUNT', $cv_int_drop_count_tab);
		*/
		// get the total counts of candidates interview drop outs and rejected
		$int_count_tab = $this->ReqResume->find('all', array('fields' => array('ReqResume.status_title', 'count(*) count'),'conditions' => array('ReqResume.stage_title like' => '%Interview',
		$cv_emp_cond,$date_cond), 'group' => array('ReqResume.status_title'), 'joins' => $count_options));
		foreach($int_count_tab as $int_data){
			switch($int_data['ReqResume']['status_title']){
				case 'No Show':
				$this->set('INTERVIEW_DROP_TAB_COUNT',  $int_data[0]['count']);
				break;
				case 'Rejected':
				$this->set('INTERVIEW_REJ_TAB_COUNT',  $int_data[0]['count']);
				break;

			}
		}
		// get the candidates offered
		$offer_cond = array('ReqResume.stage_title' => 'Offer','ReqResume.status_title !=' => array('Offer Pending','Rejected', 'Not Interested','Quit'));
		$offer_count_tab = $this->ReqResume->find('count', array('conditions' => array($offer_cond,$cv_emp_cond,$date_cond), 'joins' => $count_options));
		$this->set('OFFER_TAB_COUNT', $offer_count_tab);
		// get the total counts of candidates offer drop outs and joined
		// $validate_cond = 		
		$offer_reject_cond = array('ReqResume.stage_title' => 'Offer', 'ReqResume.status_title' => array('Rejected', 'Not Interested','Quit'));
		$offer_reject_count_tab = $this->ReqResume->find('count', array('conditions' => array($offer_reject_cond,$cv_emp_cond,$date_cond), 'joins' => $count_options));
		$this->set('OFFER_REJ_TAB_COUNT', $offer_reject_count_tab);
		$join_cond = array('ReqResume.stage_title' => 'Joining', 'ReqResume.status_title' => 'Joined');
		$join_count_tab = $this->ReqResume->find('count', array('conditions' => array($join_cond,$cv_emp_cond,$date_cond), 'joins' => $count_options));
		$this->set('JOINED_TAB_COUNT', $join_count_tab);
		// get the billing status count
		$billing_count_tab = $this->ReqResume->find('all', array('fields' => array('count(*) count','sum(bill_ctc) ctc'),'conditions' => array($join_cond,$cv_emp_cond,$date_cond,
		'ReqResume.bill_ctc >' => '0'), 'joins' => $count_options));
		$this->set('BILLED_TAB_COUNT', $billing_count_tab[0][0]['count']);
		$this->set('BILLED_AMT_TAB_COUNT', $billing_count_tab[0][0]['ctc']);
		$billing_lacs = round($billing_count_tab[0][0]['ctc']/100000, 2);
		$this->set('BILLED_AMT_TAB_AVG_COUNT', $billing_lacs);
		$this->set('BILLED_AMT_INDIVIDUAL', round(($billing_lacs*66)/100, 2));

		// MOP Table for recruiter and account holders
		if($this->Session->read('USER.Login.roles_id') == '30' || $dash_type == 'rec_view' ){
		// get the data for MOP table 
		$this->loadModel('TaskPlan');		
		$date_cond = array('or' => array("DATE_FORMAT(TaskPlan.task_date, '%Y-%m-%d') between ? and ?" => array($start, $end)));
		$task_plan_data = $this->TaskPlan->find('all', array('fields' => array('task_date','ctc','session','requirements_id'),
		'conditions' => array('users_id' => $this->Session->read('USER.Login.id'),$date_cond, 'TaskPlan.is_deleted' => 'N'), 
		'group' => array('TaskPlan.id'), 'order' => array('TaskPlan.task_date' => 'desc'), 'joins' => $options));
		$this->set('task_plan_data', $task_plan_data);
		// get the no. of resumes sent for that day  for that position ctc
		foreach($task_plan_data as $task_data){
			$ctc_count_ar[] = $task_data['TaskPlan']['ctc'];
			$no_resume = $this->Home->get_resumes_ctc($task_data['TaskPlan']['ctc']);
			$resume_count = $task_data['TaskPlan']['session'] == 'D' ? $no_resume  : round($no_resume/2, 0);
			// calculate no. of days worked
			$work_days += $task_data['TaskPlan']['session'] == 'D' ? 1 : 0.5;
			$resume_count_elig[] = $resume_count;
			// get the actual resume uploaded
			$task_data['TaskPlan']['task_date'];
			$resume_upload_count = $this->ReqResume->find('count', array('conditions' => array('ReqResume.requirements_id' => $task_data['TaskPlan']['requirements_id'],
			"date_format(ReqResume.created_date, '%Y-%m-%d')" => $task_data['TaskPlan']['task_date'],'Resume.is_deleted' => 'N'), 'group' => array('ReqResume.id')));
			$resume_upload[] = $resume_upload_count;
			// get the actual resume sent
			$options = array(						
				array('table' => 'req_resume_status',
					'alias' => 'ReqResumeStatus',					
					'type' => 'LEFT',
					'conditions' => array('`ReqResumeStatus`.`req_resume_id` = `ReqResume`.`id`')
				)
			);
			$resume_sent_count = $this->ReqResume->find('count', array('conditions' => array('ReqResume.requirements_id' => $task_data['TaskPlan']['requirements_id'],
			"date_format(Resume.created_date, '%Y-%m-%d')" => $task_data['TaskPlan']['task_date'], 
			'Resume.is_deleted' => 'N', 'ReqResumeStatus.stage_title' => 'Shortlist',
			'ReqResumeStatus.status_title' => 'CV-Sent'), 	'group' => array('ReqResume.id'), 'joins' => $options));
			$resume_sent[] = $resume_sent_count;
			$resume_sent_mop  += $resume_sent_count;
			$productivity = round(($resume_sent_count / $resume_count) * 100, 1);
			$prod_ar[] = $productivity;			
			}
			$total_days = $this->Home->diff_date($start, $end);
			$overall_prod = round(($work_days / $total_days) * 100, 1);
			// assign all the values for display
			$this->set('ctc_count_ar', $ctc_count_ar);
			$this->set('resume_count_elig', $resume_count_elig);
			$this->set('resume_upload', $resume_upload);
			$this->set('resume_sent', $resume_sent);
			$this->set('prod_ar', $prod_ar);			
			$this->set('overall_prod', $overall_prod);
			$this->set('RESUME_SENT_MOP_COUNT', $resume_sent_mop);
			// get the avg. lead billing
			$this->loadModel('ApproveBilling');
			$date_cond = array('or' => array("DATE_FORMAT(ApproveBilling.created_date, '%Y-%m-%d') between ? and ?" => array($start, $end)));
			$billing_data = $this->ApproveBilling->find('all', array('fields' => array('Position.created_date','ApproveBilling.created_date'),
			'conditions' => array('Resume.created_by' => $this->Session->read('USER.Login.id'),$date_cond, 'ApproveBilling.is_deleted' => 'N',
			'ApproveBilling.is_approved' => 'Y'), 'group' => array('ApproveBilling.id')));
			$count_bill = count($billing_data);
			foreach($billing_data as $bill){
				$billing_day += $this->Home->diff_date($bill['Position']['created_date'], $bill['ApproveBilling']['created_date']);
			}
			$avg_bill_day = round($billing_day/$count_bill, 0);
			$this->set('AVG_BILLING_DAY', $avg_bill_day);
			// get the avg. cv lead time
			$date_cond = array('or' => array("DATE_FORMAT(Resume.created_date, '%Y-%m-%d') between ? and ?" => array($start, $end)));
			$cv_lead_data = $this->ReqResume->find('all', array('fields' => array('Position.created_date','Resume.created_date'),
			'conditions' => array('Resume.created_by' => $this->Session->read('USER.Login.id'),$date_cond, 'Resume.is_deleted' => 'N',
			'ReqResume.status_title !=' => 'Pending'), 'group' => array('Resume.id')));
			$cv_lead_count = count($cv_lead_data);
			foreach($cv_lead_data as $cv_lead){			
				$cv_lead_day += $this->Home->diff_date($cv_lead['Position']['created_date'], $cv_lead['Resume']['created_date']);
			}
			$avg_cv_lead_day = round($cv_lead_day/$cv_lead_count, 0);
			$this->set('AVG_CV_LEAD_DAY', $avg_cv_lead_day);
			
		}
	}
	
	
	
	/* function to get position counts */
	public function get_position_count($date,$pos_emp_cond,$ac_join,$branch_cond,$client_cond,$reqCond){	
		// set date condition
		if($this->Session->read('USER.Login.roles_id') == '30' || $this->request->params['pass'][0] ==  'rec_view'){
			$ac_join = array(						
				array('table' => 'req_team',
					'alias' => 'ReqTeam',					
					'type' => 'LEFT',
					'conditions' => array('`ReqTeam`.`requirements_id` = `Home`.`id`',
					'ReqTeam.is_approve' => 'A')
				),
				array('table' => 'req_approval_status',
					'alias' => 'PositionStatus',					
					'type' => 'INNER',
					'conditions' => array('`PositionStatus`.`requirements_id` = `Home`.`id`',
					'PositionStatus.member_approve' => 'A', 'PositionStatus.member_id' => $this->Session->read('USER.Login.id'))
				)
			);
		}
		$this->Home->unBindModel(array('belongsTo' => array('Contact','ReqStatus')));
		$count_data = $this->Home->find('all', array('fields' => array('Home.id','Home.job_title','Client.client_name'),
		'conditions' => array("DATE_FORMAT(Home.created_date, '%Y-%m-%d')" => $date,$pos_emp_cond,$branch_cond,$client_cond,$reqCond),
		'order' => array('Client.client_name' => 'asc'), 'joins' => $ac_join, 'group' => array('Home.id')));
		return $count_data;		
	}
	
	
	/* function to get cv sent counts */
	public function get_cv_sent_count($date,$cv_sent_emp_cond,$cv_branch_cond,$client_cond,$reqCond){
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
			array('table' => 'client_account_holder',
					'alias' => 'ClientAH',					
					'type' => 'LEFT',
					'conditions' => array('`Client`.`id` = `ClientAH`.`clients_id`')
			)
		);
		$validate_cond = array('ReqResumeStatus.stage_title' => 'Shortlist','ReqResumeStatus.status_title' => 'CV-Sent');
		if(empty($reqCond)){
			$dateCond = array("DATE_FORMAT(ReqResumeStatus.created_date, '%Y-%m-%d')" => $date);
		}
		$count_data = $this->ReqResumeStatus->find('all', array('fields' => array('Resume.id','Resume.first_name','Resume.last_name','Client.client_name'),
		'conditions' => array($dateCond,$validate_cond,$cv_sent_emp_cond,$cv_branch_cond,$client_cond,$reqCond),
		'group' => array('Resume.id'),'order' => array('Client.client_name' => 'asc'), 'joins' => $options));
		return $count_data;	
	}
	
	/* function to get cv shortlisted counts */
	public function get_cv_shortlist_count($date,$cv_emp_cond,$cv_branch_cond,$client_cond,$reqCond){	
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
			array('table' => 'client_account_holder',
					'alias' => 'ClientAH',					
					'type' => 'LEFT',
					'conditions' => array('`Client`.`id` = `ClientAH`.`clients_id`')
			)
		);
		if(empty($reqCond)){
			$dateCond = array("DATE_FORMAT(ReqResumeStatus.created_date, '%Y-%m-%d')" => $date);
		}
		$validate_cond = array('ReqResumeStatus.status_title' => 'Shortlisted');
		//$this->ReqResumeStatus->unBindModel(array('belongsTo' => array('Reason')));
		$count_data = $this->ReqResumeStatus->find('all', array('fields' => array('Resume.id','Resume.first_name','Resume.last_name','Client.client_name'),
		'conditions' => array($dateCond, $validate_cond,$cv_emp_cond,$cv_branch_cond,$client_cond,$reqCond),
		'order' => array('Client.client_name' => 'asc'),'group' => array('ReqResumeStatus.req_resume_id'),'joins' => $options));		
		return $count_data;	
	}
	
	/* function to get cv shortlisted counts */
	public function get_cv_reject_count($date,$cv_emp_cond,$cv_branch_cond,$client_cond,$reqCond){	
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
			)			,
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
			array('table' => 'client_account_holder',
					'alias' => 'ClientAH',					
					'type' => 'LEFT',
					'conditions' => array('`Client`.`id` = `ClientAH`.`clients_id`')
			)
		);
		if(empty($reqCond)){
			$dateCond = array("DATE_FORMAT(ReqResumeStatus.created_date, '%Y-%m-%d')" => $date);
		}
		$validate_cond = array('ReqResumeStatus.stage_title' => 'Shortlist', 'ReqResumeStatus.status_title' => 'Rejected');
		//$this->ReqResume->unBindModel(array('belongsTo' => array('Reason')));
		$count_data = $this->ReqResumeStatus->find('all', array('fields' => array('Resume.id','Resume.first_name','Resume.last_name','Client.client_name'),
		'conditions' => array($dateCond, $validate_cond,$cv_emp_cond,$cv_branch_cond,$client_cond,$reqCond),
		'order' => array('Client.client_name' => 'asc'),'group' => array('ReqResumeStatus.req_resume_id'),'joins' => $options));		
		return $count_data;	
	}
	
	
	/* function to get cv waiting status counts */
	public function get_cv_waiting_count($date,$cv_emp_cond,$cv_branch_cond,$client_cond,$reqCond){	
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
			),
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
			array('table' => 'client_account_holder',
					'alias' => 'ClientAH',					
					'type' => 'LEFT',
					'conditions' => array('`Client`.`id` = `ClientAH`.`clients_id`')
			)
		);
		if(empty($reqCond)){
			$dateCond = array("DATE_FORMAT(ReqResumeStatus.created_date, '%Y-%m-%d')" => $date);
		}
		$validate_cond = array("OR" => array ('ReqResumeStatus.status_title' => 'YRF',	'ReqResumeStatus.status_title' => 'OnHold'));
		//$this->ReqResume->unBindModel(array('belongsTo' => array('Reason')));
		$count_data = $this->ReqResumeStatus->find('all', array('fields' => array('Resume.id','Resume.first_name','Resume.last_name','Client.client_name'),
		'conditions' => array($dateCond, $validate_cond,$cv_emp_cond,$cv_branch_cond,$client_cond,$reqCond),
		'order' => array('Client.client_name' => 'asc'),'group' => array('ReqResumeStatus.req_resume_id'),'joins' => $options));	
		return $count_data;	
	}
	
	/* function to get cv hold status counts 
	public function get_cv_hold_count($date,$cv_emp_cond,$cv_branch_cond,$client_cond){	
		$this->loadModel('ReqResume');
		$options = array(			
			array('table' => 'clients',
					'alias' => 'Client',					
					'type' => 'LEFT',
					'conditions' => array('`Client`.`id` = `Position`.`clients_id`')
			),
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
		);
		$validate_cond = array('ReqResume.status_title' => 'OnHold');
		$this->ReqResume->unBindModel(array('belongsTo' => array('Reason')));
		$count_data = $this->ReqResume->find('all', array('fields' => array('Resume.id','Resume.first_name','Resume.last_name','Client.client_name'),
		'conditions' => array("DATE_FORMAT(ReqResume.modified_date, '%Y-%m-%d')" => $date, $validate_cond,$cv_emp_cond,$cv_branch_cond,$client_cond),
		'order' => array('Client.client_name' => 'asc'),'joins' => $options));		
		return $count_data;	
	}
	*/
	/* function to get cv interview counts */
	public function get_interview_count($date,$int_emp_cond,$cv_branch_cond,$client_cond,$reqCond){	
		$options = array(			
			array('table' => 'users',
					'alias' => 'User',					
					'type' => 'LEFT',
					'conditions' => array('`User`.`id` = `ReqResume`.`modified_by`')
			),
			array('table' => 'location',
					'alias' => 'Location',					
					'type' => 'LEFT',
					'conditions' => array('`Location`.`id` = `User`.`location_id`')
			),
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
			array('table' => 'req_resume_interview',
					'alias' => 'ResInterview',					
					'type' => 'LEFT',
					'conditions' => array('`ResInterview`.`req_resume_id` = `ReqResume`.`id`')
			),
			array('table' => 'client_account_holder',
					'alias' => 'ClientAH',					
					'type' => 'LEFT',
					'conditions' => array('`Client`.`id` = `ClientAH`.`clients_id`')
			)
		);
		
		if(empty($reqCond)){
			$dateCond = array("DATE_FORMAT(ResInterview.int_date, '%Y-%m-%d')" => $date);
		}
		$this->loadModel('ReqResumeStatus');
		$count_data = $this->ReqResumeStatus->find('all', array('fields' => array('Resume.id','Resume.first_name','Resume.last_name','ResInterview.status_title','ResInterview.stage_title','Client.client_name'),
		'conditions' => array($dateCond,$int_emp_cond,$cv_branch_cond,$client_cond,$reqCond,'ReqResumeStatus.stage_title like' => '%Interview'), 'joins' => $options,
		'group' => array('Resume.id'),'order' => array('Client.client_name' => 'asc')));
		
		return $count_data;	
	}
	
	
	/* function to get cv interview drop outs counts */
	public function get_int_drop_count($date,$int_emp_cond,$cv_branch_cond,$client_cond,$reqCond){
		$options = array(			
			array('table' => 'users',
					'alias' => 'User',					
					'type' => 'LEFT',
					'conditions' => array('`User`.`id` = `ReqResume`.`modified_by`')
			),
			array('table' => 'location',
					'alias' => 'Location',					
					'type' => 'LEFT',
					'conditions' => array('`Location`.`id` = `User`.`location_id`')
			),
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
			array('table' => 'client_account_holder',
					'alias' => 'ClientAH',					
					'type' => 'LEFT',
					'conditions' => array('`Client`.`id` = `ClientAH`.`clients_id`')
			)
		);
		if(empty($reqCond)){
			$dateCond = array("DATE_FORMAT(ReqResumeStatus.created_date, '%Y-%m-%d')" => $date);
		}
		$this->loadModel('ReqResumeStatus');			
		//$this->ReqResumeStatus->unBindModel(array('belongsTo' => array('Reason')));		
		$count_data = $this->ReqResumeStatus->find('all', array('fields' => array('Resume.id','Resume.first_name','Resume.last_name','ReqResumeStatus.status_title','ReqResumeStatus.stage_title','Client.client_name'),
		'conditions' => array($dateCond,$int_emp_cond,
		'ReqResumeStatus.stage_title like' => '%Interview', 'ReqResumeStatus.status_title' => 'No Show',$cv_branch_cond,$client_cond,$reqCond),'joins' => $options,
		'order' => array('Client.client_name' => 'asc'),'group' => array('Resume.id'),));		
		return $count_data;	
	}
	
	
	
	/* function to get cv interview reject counts */
	public function get_int_reject_count($date,$int_emp_cond,$cv_branch_cond,$client_cond,$reqCond){
		$options = array(			
			array('table' => 'users',
					'alias' => 'User',					
					'type' => 'LEFT',
					'conditions' => array('`User`.`id` = `ReqResume`.`modified_by`')
			),
			array('table' => 'location',
					'alias' => 'Location',					
					'type' => 'LEFT',
					'conditions' => array('`Location`.`id` = `User`.`location_id`')
			),
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
			array('table' => 'client_account_holder',
					'alias' => 'ClientAH',					
					'type' => 'LEFT',
					'conditions' => array('`Client`.`id` = `ClientAH`.`clients_id`')
			)
		);
		if(empty($reqCond)){
			$dateCond = array("DATE_FORMAT(ReqResumeStatus.created_date, '%Y-%m-%d')" => $date);
		}
		$this->loadModel('ReqResumeStatus');			
		//$this->ReqResume->unBindModel(array('belongsTo' => array('Reason')));		
		$count_data = $this->ReqResumeStatus->find('all', array('fields' => array('Resume.id','Resume.first_name','Resume.last_name','ReqResumeStatus.status_title','ReqResumeStatus.stage_title','Client.client_name'),
		'conditions' => array($dateCond,$int_emp_cond,
		'ReqResumeStatus.stage_title like' => '%Interview', 'ReqResumeStatus.status_title' => 'Rejected',$cv_branch_cond,$client_cond,$reqCond),
		'order' => array('Client.client_name' => 'asc'), 'group' => array('Resume.id'),'joins' => $options));		
		return $count_data;	
	}
	
	
	
	/* function to get candidate offer status counts */
	public function get_offer_count($date,$int_emp_cond,$cv_branch_cond,$client_cond,$reqCond){
		$options = array(			
			array('table' => 'users',
					'alias' => 'User',					
					'type' => 'LEFT',
					'conditions' => array('`User`.`id` = `ReqResume`.`modified_by`')
			),
			array('table' => 'location',
					'alias' => 'Location',					
					'type' => 'LEFT',
					'conditions' => array('`Location`.`id` = `User`.`location_id`')
			),
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
			array('table' => 'client_account_holder',
					'alias' => 'ClientAH',					
					'type' => 'LEFT',
					'conditions' => array('`Client`.`id` = `ClientAH`.`clients_id`')
			)
		);
		if(empty($reqCond)){
			$dateCond = array("DATE_FORMAT(ReqResumeStatus.created_date, '%Y-%m-%d')" => $date);
		}
		$this->loadModel('ReqResumeStatus');		
		$validate_cond = array('ReqResumeStatus.stage_title' => 'Offer','ReqResumeStatus.status_title !=' => array('Offer Pending','Rejected', 'Not Interested','Quit'));
		//$this->ReqResume->unBindModel(array('belongsTo' => array('Reason')));
		$count_data = $this->ReqResumeStatus->find('all', array('fields' => array('Resume.id','Resume.first_name','Resume.last_name','ReqResumeStatus.status_title','ReqResumeStatus.stage_title','Client.client_name'),
		'conditions' => array($dateCond, $validate_cond,$int_emp_cond,$cv_branch_cond,$client_cond,$reqCond),
		'order' => array('Client.client_name' => 'asc'),'group' => array('Resume.id'),'joins' => $options));
		return $count_data;	
	}
	
	/* function to get candidate offer drop outs status counts */
	public function get_offer_reject_count($date,$int_emp_cond,$cv_branch_cond,$client_cond,$reqCond){
		$options = array(			
			array('table' => 'users',
					'alias' => 'User',					
					'type' => 'LEFT',
					'conditions' => array('`User`.`id` = `ReqResume`.`modified_by`')
			),
			array('table' => 'location',
					'alias' => 'Location',					
					'type' => 'LEFT',
					'conditions' => array('`Location`.`id` = `User`.`location_id`')
			),
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
			array('table' => 'client_account_holder',
					'alias' => 'ClientAH',					
					'type' => 'LEFT',
					'conditions' => array('`Client`.`id` = `ClientAH`.`clients_id`')
			)
		);
		$this->loadModel('ReqResumeStatus');	
		if(empty($reqCond)){
			$dateCond = array("DATE_FORMAT(ReqResumeStatus.created_date, '%Y-%m-%d')" => $date);
		}		
		$validate_cond = array('ReqResumeStatus.stage_title' => 'Offer', 'ReqResumeStatus.status_title' => array('Rejected', 'Not Interested','Quit'));
		//$this->ReqResumeStatus->unBindModel(array('belongsTo' => array('Reason')));
		$count_data = $this->ReqResumeStatus->find('all', array('fields' => array('Resume.id','Resume.first_name','Resume.last_name','ReqResumeStatus.status_title','ReqResumeStatus.stage_title','Client.client_name'),
		'conditions' => array($dateCond, $validate_cond,$int_emp_cond,$cv_branch_cond,$client_cond,$reqCond),
		'order' => array('Client.client_name' => 'asc'),'group' => array('Resume.id'),'joins' => $options));
		return $count_data;	
	}
	
	/* function to get candidate joined status counts */
	public function get_can_join_count($date,$int_emp_cond,$cv_branch_cond,$client_cond,$reqCond){
		$options = array(			
			array('table' => 'users',
					'alias' => 'User',					
					'type' => 'LEFT',
					'conditions' => array('`User`.`id` = `ReqResume`.`modified_by`')
			),
			array('table' => 'location',
					'alias' => 'Location',					
					'type' => 'LEFT',
					'conditions' => array('`Location`.`id` = `User`.`location_id`')
			),
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
			array('table' => 'client_account_holder',
					'alias' => 'ClientAH',					
					'type' => 'LEFT',
					'conditions' => array('`Client`.`id` = `ClientAH`.`clients_id`')
			)
		);
		$this->loadModel('ReqResumeStatus');
		if(empty($reqCond)){
			$dateCond = array("DATE_FORMAT(ReqResumeStatus.created_date, '%Y-%m-%d')" => $date);
		}		
		$validate_cond = array('ReqResumeStatus.stage_title' => 'Joining', 'ReqResumeStatus.status_title' => 'Joined');
		//$this->ReqResume->unBindModel(array('belongsTo' => array('Reason')));
		$count_data = $this->ReqResumeStatus->find('all', array('fields' => array('Resume.id','Resume.first_name','Resume.last_name','Client.client_name'),
		'conditions' => array($dateCond, $validate_cond,$int_emp_cond,$cv_branch_cond,$client_cond,$reqCond),
		'order' => array('Client.client_name' => 'asc'),'group' => array('Resume.id'),'joins' => $options));
		return $count_data;	
	}
	
	/* function to get candidate billed status counts */
	public function get_can_billing_count($date,$int_emp_cond,$cv_branch_cond,$client_cond,$reqCond){
		$options = array(			
			array('table' => 'users',
					'alias' => 'User',					
					'type' => 'LEFT',
					'conditions' => array('`User`.`id` = `ReqResume`.`modified_by`')
			),
			array('table' => 'location',
					'alias' => 'Location',					
					'type' => 'LEFT',
					'conditions' => array('`Location`.`id` = `User`.`location_id`')
			),
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
			)
			,
			array('table' => 'client_account_holder',
					'alias' => 'ClientAH',					
					'type' => 'LEFT',
					'conditions' => array('`Client`.`id` = `ClientAH`.`clients_id`')
			)
		);
		$this->loadModel('ReqResumeStatus');
		if(empty($reqCond)){
			$dateCond = array("DATE_FORMAT(ReqResumeStatus.created_date, '%Y-%m-%d')" => $date);
		}		
		$validate_cond = array('ReqResumeStatus.stage_title' => 'Joining', 'ReqResumeStatus.status_title' => 'Joined');
		//$this->ReqResume->unBindModel(array('belongsTo' => array('Reason')));
		$count_data = $this->ReqResumeStatus->find('all', array('fields' => array('Resume.id','Resume.first_name','Resume.last_name',
		'Client.client_name'),	'conditions' => array('ReqResume.bill_ctc >' => '0', $dateCond, $validate_cond,$int_emp_cond,$cv_branch_cond,$client_cond,$reqCond),
		'order' => array('Client.client_name' => 'asc'),'group' => array('Resume.id'),'joins' => $options));
		return $count_data;	
	}
	
	
	
	/* function to get chart dates */
	public function generate_chart_date($start, $end){
		$n = $this->Home->diff_date($start, $end);
		$i = 0;		
		while($n >= $i){
			$date = date('Y-m-d', strtotime($end. '-'.$i.' days'));
			$chart_date[] = $date;
			$i++;
		}		
		$this->set('chartDate', $chart_date);
		$this->set('noDays', $n);
		return $chart_date;
	}
	
	/* function to get the location details */
	public function get_loc_details(){
		$this->loadModel('Location');
		return $this->Location->find('list',  array('fields' => array('id','location'), 'order' => array('location ASC'),'conditions' => array('status' => 1)));

	}
	
	/* auto complete search */	
	public function search(){ 
		$this->layout = false;		
		$q = trim(Sanitize::escape($_GET['q']));	
		if(!empty($q)){
			// execute only when the search keywork has value		
			$this->set('keyword', $q);			
			$this->Home->unBindModel(array('belongsTo' => array('User','Contact','ReqStatus')));
			$data = $this->Home->Client->find('all', array('fields' => array('Client.client_name'),
			'group' => array('Client.client_name'), 'conditions' => 	array("OR" => array ('Client.client_name like' => '%'.$q.'%'), 
			'AND' => array('Client.is_deleted' => 'N'))));		
			$this->set('results', $data);
		}
    }
	
	public function beforeFilter(){ 
		$this->check_session();
		// check role access
		$this->check_role_access();
	}
	
	/* function to save feedback */
	public function add_feedback(){
		$this->set('noHead', '1');
		if ($this->request->is('post')){
			// validates the form
			$this->Home->set($this->request->data);
			// validate file		
			if($this->Home->validates(array('fieldList' => array('message')))){
				$sub = 'Manage Hiring - Feedback received from '.ucfirst($this->Session->read('USER.Login.first_name')).' '.ucfirst($this->Session->read('USER.Login.last_name'));
				$from = ucfirst($this->Session->read('USER.Login.first_name')).' '.ucfirst($this->Session->read('USER.Login.last_name'));
				$vars = array('from_name' => $from, 'message' => $this->request->data['Home']['message']);
				// get system admin email address
				$admin_data = $this->Home->Creator->find('all', array('conditions' => array('roles_id' => '26', 'Creator.id' => '91'), 'fields' => array('email_id')));
				// notify superiors						
				if(!$this->send_email($sub, 'feedback', 'noreply@managehiring.com', $admin_data[0]['Creator']['email_id'],$vars)){	
					// show the msg.								
					$this->Session->setFlash('<button type="button" class="close" data-dismiss="alert">&times;</button>Problem in sending the mail to admin...', 'default', array('class' => 'alert alert-error'));				
				}else{
					$this->Session->setFlash('<button type="button" class="close" data-dismiss="alert">&times;</button>Thanks for feedback. Feedback sent successfully!', 'default', array('class' => 'alert alert-success'));
					unset($this->request->data);
				}				
			}
		}
	}
	
	/* function to save a bug  */
	public function report_bug(){
		$this->set('noHead', '1');
		if ($this->request->is('post')){
			// validates the form
			$this->Home->set($this->request->data);
			// validate file		
			if($this->Home->validates(array('fieldList' => array('subject','message')))){
				$sub = 'Manage Hiring - Report a bug received from '.ucfirst($this->Session->read('USER.Login.first_name')).' '.ucfirst($this->Session->read('USER.Login.last_name'));
				$from = ucfirst($this->Session->read('USER.Login.first_name')).' '.ucfirst($this->Session->read('USER.Login.last_name'));
				$vars = array('from_name' => $from, 'message' => $this->request->data['Home']['message'],'subject' => $this->request->data['Home']['subject']);
				// save the attachment
				if(!empty($this->request->data['Home']['attachment']['tmp_name'])){
					$src = $this->request->data['Home']['attachment']['tmp_name'];
					$dest = 'uploads/message/'.time().'_'.$this->request->data['Home']['attachment']['name'];
					$this->upload_file($src, $dest);
				}
				// get system admin email address
				$admin_data = $this->Home->Creator->find('all', array('conditions' => array('roles_id' => '26', 'Creator.id' => '91'), 'fields' => array('email_id')));
				// notify superiors						
				if(!$this->send_email($sub, 'report_bug', 'noreply@managehiring.com', $admin_data[0]['Creator']['email_id'],$vars, $dest)){	
					// show the msg.								
					$this->Session->setFlash('<button type="button" class="close" data-dismiss="alert">&times;</button>Problem in sending the mail to admin...', 'default', array('class' => 'alert alert-error'));				
				}else{
					$this->Session->setFlash('<button type="button" class="close" data-dismiss="alert">&times;</button>Thanks for reporting a bug. Details sent to admin successfully!', 'default', array('class' => 'alert alert-success'));
					unset($this->request->data);
				}				
			}
		}
	}
	
}