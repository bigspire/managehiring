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
 
class ResumeController extends AppController {  
	
	public $name = 'Resume';	
	
	public $helpers = array('Html', 'Form', 'Session');
	
	public $components = array('Session', 'Functions', 'Excel');

	public function index(){ 
		// when the form is submitted for search
		if($this->request->is('post')){
			$url_vars = $this->Functions->create_url(array('keyword','from','to','min_exp','max_exp',
			'int_from','int_to','emp_id','loc','status','report_status','spec'),'Resume'); 			
			$this->redirect('/resume/?srch_status=1&'.$url_vars);				
		}
		// set the page title
		$this->set('title_for_layout', 'Resumes - Manage Hiring');	
		$this->set('empList', $this->Resume->get_employee_details());	
		$this->set('locList', $this->get_loc_details());
		$this->set('stList', $this->get_status_details());
		
		if($this->request->query['from'] != '' || $this->request->query['to'] != ''){
			$start = $this->request->query['from'] ? $this->request->query['from'] : ''; //date('d/m/Y', strtotime('-15 months'));
			$end = $this->request->query['to'] ? $this->request->query['to'] : date('d/m/Y');
			$end_search = $this->request->query['to'] ? $this->request->query['to'] : date('d/m/Y', strtotime('+1 day'));
			// set date condition
			$date_cond = array('or' => array("DATE_FORMAT(Resume.created_date, '%Y-%m-%d') between ? and ?" => 
					array($this->Functions->format_date_save($start), $this->Functions->format_date_save($end_search))));
		}			
		
		$exp_list = $this->Functions->get_experience();
		$this->set('expList', $exp_list);	
		// set keyword condition
		if($this->params->query['keyword'] != '' && $this->params->query['report_status'] == ''){
			$keyCond = array("MATCH (ResLocation.location,Resume.first_name,Resume.last_name,Resume.present_employer,
			present_location,native_location,Resume.email_id,Resume.mobile,Resume.mobile2,nationality,
			Resume.skills,ResExp.skills,Resume.code,Client.client_name) AGAINST ('".$this->Functions->format_search_keyword($this->params->query['keyword'])."' IN BOOLEAN MODE)");
		}else if($this->params->query['keyword'] != '' && $this->params->query['report_status'] != ''){
			$keyCond = array('Client.client_name'  => $this->params->query['keyword']);

		}
		
		// for director and BDH
		if($this->Session->read('USER.Login.roles_id') == '33' || $this->Session->read('USER.Login.roles_id') == '35'){
			$show = 'all';
			$team_cond = false;
		}else{
			$show = '1';
			$team_cond = true;
		}
		
				
		// get the team members
		$result = $this->Resume->get_team($this->Session->read('USER.Login.id'),$show);
		$data[] =  $this->Session->read('USER.Login.id');

		if(!empty($result)){
			$this->set('approveUser', '1');
			// for drop down listing
			$format_list = $this->Functions->format_dropdown($result, 'u','id','first_name', 'last_name');
			$this->set('empList', $format_list);
			foreach($result as $rec){
				$data[] =  $rec['u']['id'];
			}			
		}
		
		if($team_cond){
				$teamCond = array('OR' => array(
					'ReqResume.created_by' =>  $data,
					'AH.users_id' => $data					
					)
			);
		}
		
		
		if($this->Session->read('USER.Login.roles_id') == '38'){ // branch admin
			// get the branch users
			$user_data = $this->Resume->Creator->find('all', array('conditions' => array('Creator.location_id' => $this->Session->read('USER.Login.location_id'),
			'Creator.is_deleted' => 'N'),
			'fields' => array('Creator.id')));
			foreach($user_data as $rec){
				$branch_user[] =  $rec['Creator']['id'];
			}
			$teamCond = array('OR' => array(
					'ReqResume.created_by' =>  $branch_user,
					'AH.users_id' => $branch_user						
				)
			);
		}
	
		// min. exp. condition
		if($this->request->query['min_exp'] != ''){
			$minCond = array('total_exp >=' => $this->request->query['min_exp']);
		}
		// max. exp. condition
		if($this->request->query['max_exp'] != ''){
			$maxCond = array('total_exp <=' => $this->request->query['max_exp']);
		}
		// for current status condition
		if($this->request->query['status'] != ''){ 
			$statusCond = $this->get_status_cond($this->request->query['status']);
		}
		// for position status condition
		if($this->request->query['spec'] != ''){ 
			$specCond = array('Position.id' => $this->request->query['spec']);
			$statusCond = $this->get_status_cond($this->request->query['status']);
			$teamCond = '';
		}
		// for report status condition
		if($this->request->query['report_status'] != ''){ 
			$repStatusCond = $this->get_report_status_cond($this->request->query['report_status']);
			
			// set date condition
			$date_cond = array('or' => array("DATE_FORMAT(ReqResumeStatus.created_date, '%Y-%m-%d') between ? and ?" => 
					array($this->Functions->format_date_save($start), $this->Functions->format_date_save($end_search))));
			
		}
		
		/*
			// check role based access
		if($this->Session->read('USER.Login.roles_id') == '34' && !$team_cond){ // account holder
			// $empCond = array('AH.users_id' => $this->Session->read('USER.Login.id'));
			$empCond = array('OR' => array(
					'AH.users_id' =>  $this->Session->read('USER.Login.id'),
					'ReqResume.created_by' =>  $this->Session->read('USER.Login.id')
					)
				);
		}else if($this->Session->read('USER.Login.roles_id') == '30'  && !$team_cond){ // recruiter
					$empCond = array('OR' => array(
						'ReqResume.created_by' =>  $this->Session->read('USER.Login.id')
				)
		);		
			//$empCond = array('ReqResumeStatus.created_by' => $this->Session->read('USER.Login.id'),
			//'ReqResumeStatus.stage_title' => 'Validation - Account Holder', 'ReqResumeStatus.status_title' => 'Validated');		
		}else if($this->Session->read('USER.Login.roles_id') == '33' || $this->Session->read('USER.Login.roles_id') == '35'){ // director & BD
			$empCond = '';
			$teamCond = '';
		}
		*/
		
		if($this->Session->read('USER.Login.roles_id') == '33' || $this->Session->read('USER.Login.roles_id') == '35'){ // director & BD
			$empCond = '';
			$team_cond = '';
		}
		
		$options = array(			
				array('table' => 'req_resume',
						'alias' => 'ReqResume',					
						'type' => 'LEFT',
						'conditions' => array('`ReqResume`.`resume_id` = `Resume`.`id`')
				),
				array('table' => 'res_employer',
						'alias' => 'ResExp',					
						'type' => 'LEFT',
						'conditions' => array('`ResExp`.`resume_id` = `Resume`.`id`')
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
				array(
						'table' => 'req_resume_status',
						'alias' => 'ReqResumeStatus',					
						'type' => 'LEFT',
						'conditions' => array('`ReqResumeStatus`.`req_resume_id` = `ReqResume`.`id`')
				),
				array('table' => 'client_account_holder',
						'alias' => 'AH',					
						'type' => 'LEFT',
						'conditions' => array('`AH`.`clients_id` = `Client`.`id`')
				),
				array(
						'table' => 'resume_doc',
						'alias' => 'ResDoc',					
						'type' => 'LEFT',
						'conditions' => array('`ResDoc`.`id` = `Resume`.`resume_doc_id`')
						)
				,
				array('table' => 'req_resume_interview',
						'alias' => 'ResInterview',					
						'type' => 'LEFT',
						'conditions' => array('`ReqResume`.`id` = `ResInterview`.`req_resume_id`')
				)
		);
		// for employee condition
		if($this->request->query['emp_id'] != ''){			
			$empCond = array('Resume.created_by' => $this->request->query['emp_id']);
		}
		
		// for branch condition
		if($this->request->query['loc'] != ''){ 
			$branchCond = array('Creator.location_id' => $this->request->query['loc']);
		}
		
		// for iframe in report
		if($this->request->query['iframe'] == '1'){
			$this->set('noHead', '1');
			$date_cond = '';
			$keyCond = '';
			$minCond = '';			
			$maxCond = '';
			$int_cond = '';
			// employee condition
			if($this->request->query['emp'] != ''){
				$empCond = array('Resume.created_by' => $this->request->query['emp']);
			}
			if($this->request->query['client'] != ''){
				$clientCond = array('Position.clients_id' => $this->request->query['client'], 'Position.is_approve' => 'A');
			}
			$branchCond = '';
			$statusCond = '';
			$repStatusCond = '';
			$teamCond = '';
			$specCond = '';	
			$this->set('sticky', '');
			switch($this->request->params['pass'][0]){
				case 'cvs':
				$reportCond = array('ReqResumeStatus.stage_title' => 'Shortlist','ReqResumeStatus.status_title' => 'CV-Sent');
				break;
				case 'short':
				$reportCond = array('ReqResumeStatus.stage_title' => 'Shortlist','ReqResumeStatus.status_title' => 'Shortlisted');
				break;
				case 'cvr':
				$reportCond = array('ReqResumeStatus.stage_title' => 'Shortlist','ReqResumeStatus.status_title' => 'Rejected');
				break;
				case 'fba':
				$reportCond = array('ReqResume.stage_title' => 'Shortlist','ReqResume.status_title' => 'CV-Sent');
				break;
				case 'ia':
				$reportCond = array('ReqResume.stage_title' => 'Shortlist','ReqResume.status_title' => 'Shortlisted');
				break;
				case 'pia':
				$reportCond = array('ReqResumeStatus.stage_title like' => '%Interview%');
				break;
				case 'fi':
				$reportCond = array('ReqResumeStatus.stage_title' => 'Final Interview');
				break;
				case 'op':
				$reportCond = array('ReqResumeStatus.stage_title' => 'Offer','ReqResumeStatus.status_title' => 'Offer Pending');
				break;
				case 'oa':
				$reportCond = array('ReqResumeStatus.stage_title' => 'Offer','ReqResumeStatus.status_title' => 'Offer Accepted');
				break;
				case 'or':
				$reportCond = array('ReqResumeStatus.stage_title' => 'Offer','ReqResumeStatus.status_title' => 'Declined');
				break;
				case 'jp':
				$reportCond = array('ReqResumeStatus.stage_title' => 'Joining','ReqResumeStatus.status_title' => 'Declined');
				break;
				case 'j':
				$reportCond = array('ReqResume.stage_title' => 'Offer','ReqResume.status_title' => 'Offer Accepted');
				break;
				case 'nj':
				$reportCond = array('ReqResumeStatus.stage_title' => 'Joining','ReqResumeStatus.status_title' => 'Not Joined');
				break;
				case 'jd':
				$reportCond = array('ReqResumeStatus.stage_title' => 'Joining','ReqResumeStatus.status_title' => 'Deferred');
				break;
				case 'bp':
				$reportCond = array('ReqResume.status_title' => 'Joined', 'ReqResume.bill_ctc' => '');
				break;
				case 'b':
				$reportCond = array('ReqResume.bill_ctc !=' => '');
				break;
			}
			//$empCond = array('ReqResumeStatus.created_by' => $this->request->query['emp_id']);
		}else{	
			$this->set('sticky', 'stickyTable');
		}
		
		
		// interview condition
		if($this->request->query['int_from'] != '' || $this->request->query['int_to'] != ''){
			$int_start = $this->request->query['int_from'] ? $this->request->query['int_from'] : date('d/m/Y', strtotime('-6 months'));
			$int_end = $this->request->query['int_to'] ? $this->request->query['int_to'] : date('d/m/Y');
			// set date condition
			$int_cond = array('or' => array("DATE_FORMAT(ResInterview.int_date, '%Y-%m-%d') between ? and ?" => 
						array($this->Functions->format_date_save($int_start), $this->Functions->format_date_save($int_end))));
			/*
			$options = array(			
				array('table' => 'req_resume',
						'alias' => 'ReqResume',					
						'type' => 'LEFT',
						'conditions' => array('`ReqResume`.`resume_id` = `Resume`.`id`')
				),
				array('table' => 'req_resume_interview',
						'alias' => 'ResInterview',					
						'type' => 'LEFT',
						'conditions' => array('`ReqResume`.`id` = `ResInterview`.`req_resume_id`')
				)
			);*/
		}
		$fields = array('id', 'Resume.code',"concat(Resume.first_name,' ',Resume.last_name) full_name",'email_id','mobile','mobile2','total_exp','education','present_employer',
		'ResLocation.location','present_ctc','expected_ctc', 'Creator.first_name','Resume.created_date',
		'Resume.modified_date','ReqResume.stage_title','ReqResume.status_title','ResDoc.resume','present_location','autoresume_modified','Position.resume_type',
		'Resume.created_by');	
		// for export
		if($this->request->query['action'] == 'export'){ 
			$data = $this->Resume->find('all', array('fields' => $fields,'conditions' => 
			array('Resume.is_deleted' => 'N', $date_cond,$keyCond,$minCond,	$maxCond,$int_cond,$empCond,$branchCond,$statusCond,$repStatusCond,$teamCond,$specCond,$clientCond,$reportCond), 
			'order' => array('Resume.created_date' => 'desc'), 'group' => array('Resume.id'), 'joins' => $options));
			$this->Excel->generate('resumes', $data, $data, 'Report', 'ResumeDetails'.date('dmy'),'',$this->webroot);
		}
		$this->paginate = array('fields' => $fields,'limit' => '25','conditions' => array('Resume.is_deleted' => 'N', $date_cond,$keyCond,$minCond,
		$maxCond,$int_cond,$empCond,$branchCond,$statusCond,$repStatusCond,$teamCond,$specCond,$clientCond,$reportCond),
		'order' => array('Resume.created_date' => 'desc'),'group' => array('Resume.id'), 'joins' => $options);
		$data = $this->paginate('Resume');
		$this->request->query['keyword'] = str_replace('||', '&', $this->params->query['keyword']);		
		$this->request->query['from'] = $start;
		$this->request->query['to'] = $end;
		$this->set('data', $data);
		if(empty($data)){
			$this->Session->setFlash('<button type="button" class="close" data-dismiss="alert">&times;</button>Oops! No Resumes Found!', 'default', array('class' => 'alert alert-info'));
		}
		// downlod the pdf 
		if(!empty($this->request->query['download']) && !isset($this->request->params['named']['page'])){
			$this->set('file_download',$this->request->query['download']);
		}else{
			$this->set('file_download','');
		}
		
		
	}
	
	
	
	/* function to get the status condition */
	public function get_status_cond($st){
		switch($st){
			case '1':
			$cond = array('ReqResume.stage_title' => 'Shortlist','ReqResume.status_title' => 'CV-Sent');
			break;
			case '2':
			$cond = array('ReqResume.status_title' => 'Shortlisted');
			break;
			case '3':
			$cond = array('ReqResume.status_title' => 'Rejected','ReqResume.stage_title' => 'Shortlist');
			break;
			case '4':
			$cond = array('ReqResume.status_title' => 'YRF');
			break;
			case '5':
			$cond = array('ReqResume.stage_title like' => '%Interview','ReqResume.status_title' => 'Scheduled');
			break;
			case '6':
			$cond = array('ReqResume.stage_title like' => '%Interview','ReqResume.status_title' => 'No Show' );
			break;
			case '7':
			$cond = array('ReqResume.status_title like' => '%Interview','ReqResume.status_title' => 'Rejected');
			break;
			case '8':
			$cond = array('ReqResume.stage_title' => 'Offer', 'ReqResume.status_title !=' => array('Offer Pending'));
			break;
			case '9':
			$cond = array('ReqResume.stage_title' => 'Offer','ReqResume.status_title' => array('Rejected', 'Not Interested','Quit'));
			break;
			case '10':
			$cond = array('ReqResume.stage_title' => 'Joining','ReqResume.status_title' => 'Joined');
			break;
			case '11':
			$cond = array('ReqResume.bill_ctc >' => '0',  'ReqResume.status_title' => 'Joined');
			break;
			case '12':
			$cond = array('ReqResume.stage_title' => 'Validation - Account Holder', 'ReqResume.status_title' => 'Pending');
			break;
			case '13':
			$cond = array('ReqResume.stage_title' => 'Validation - Account Holder', 'ReqResume.status_title' => 'Validated');
			break;
			case '14':
			$cond = array('ReqResume.status_title like' => '%Interview','ReqResume.status_title' => 'Selected');
			break;
			case '15':
			$cond = array('ReqResume.stage_title' => 'Joining','ReqResume.status_title' => 'Not Joined');
			break;
			case '16':
			$cond = array('ReqResume.stage_title' => 'Joining','ReqResume.status_title' => 'Deferred');
			break;
			case '17':
			$cond = array('ReqResume.stage_title like' => '%Interview','ReqResume.status_title' => 'Re-Scheduled');
			break;
			case '18':
			$cond = array('ReqResume.stage_title like' => 'In-Active','ReqResume.status_title' => 'Not Billable');
			break;
		}
		return $cond;
	}
	
	/* function to get the report status condition */
	public function get_report_status_cond($st){
		switch($st){
			case '1': 
			$cond = array('ReqResumeStatus.stage_title' => 'Validation - Account Holder','ReqResumeStatus.status_title' => 'Validated');
			break;
			case '2':
			$cond = array('ReqResumeStatus.status_title' => 'Shortlisted');
			break;
			case '3':
			$cond = array('ReqResumeStatus.status_title' => 'Rejected','ReqResumeStatus.stage_title' => 'Shortlist');
			break;
			case '4':
			$cond = array('ReqResumeStatus.status_title' => 'YRF');
			break;
			case '5':
			$cond = array('ReqResumeStatus.stage_title like' => '%Interview');
			break;
			case '6':
			$cond = array('ReqResumeStatus.stage_title like' => '%Interview','ReqResumeStatus.status_title' => 'No Show' );
			break;
			case '7':
			$cond = array('ReqResumeStatus.status_title' => 'Rejected',	'ReqResumeStatus.stage_title like' => '%Interview');
			//$cond = array('ReqResumeStatus.status_title like' => '%Interview','ReqResumeStatus.status_title' => 'Rejected');
			break;
			case '8':
			$cond = array('ReqResumeStatus.stage_title' => 'Offer', 'ReqResumeStatus.status_title !=' => array('Offer Pending','Rejected','Not Interested','Quit'));
			break;
			case '9':
			$cond = array('ReqResumeStatus.stage_title' => 'Offer','ReqResumeStatus.status_title' => array('Rejected', 'Not Interested','Quit'));
			break;
			case '10':
			$cond = array('ReqResumeStatus.stage_title' => 'Joining','ReqResumeStatus.status_title' => 'Joined');
			break;
			case '11':
			$cond = array('ReqResume.bill_ctc >' => '0' , 'ReqResumeStatus.status_title' => 'Joined');
			break;
			case '12':
			$cond = array('ReqResume.bill_ctc >' => '0' , 'ReqResumeStatus.status_title' => 'Joined');
			break;
			case '13':
			$cond = array('ReqResumeStatus.stage_title' => 'Validation - Account Holder', 'ReqResumeStatus.status_title' => 'Validated');
			break;
			case '14':
			$cond = array('ReqResumeStatus.status_title like' => '%Interview','ReqResumeStatus.status_title' => 'Selected');
			break;
			case '15':
			$cond = array('ReqResumeStatus.stage_title' => 'Joining','ReqResumeStatus.status_title' => 'Not Joined');
			break;
			case '16':
			$cond = array('ReqResumeStatus.stage_title' => 'Joining','ReqResumeStatus.status_title' => 'Deferred');
			break;
			case '17':
			$cond = array('ReqResumeStatus.stage_title like' => '%Interview','ReqResumeStatus.status_title' => 'Re-Scheduled');
			break;
			case '18':
			$cond = array('ReqResumeStatus.stage_title like' => 'In-Active','ReqResumeStatus.status_title' => 'Not Billable');
			break;
			
		}
		return $cond;
	}
	
	
	/* function to view the Resume */
	public function view($id){	
		// set the page title
		$this->set('title_for_layout', 'View Resume - Manage Hiring');	
		$options = array(			
			array('table' => 'req_resume',
					'alias' => 'ReqResume',					
					'type' => 'LEFT',
					'conditions' => array('`Resume`.`id` = `ReqResume`.`resume_id`')
			),
			array(
				'table' => 'resume_doc',
				'alias' => 'ResDoc',					
				'type' => 'LEFT',
				'conditions' => array('`ResDoc`.`id` = `Resume`.`resume_doc_id`')
			),
			array(
				'table' => 'requirements',
				'alias' => 'Position',					
				'type' => 'LEFT',
				'conditions' => array('`Position`.`id` = `ReqResume`.`requirements_id`')
			),			
			array(
				'table' => 'res_language',
				'alias' => 'ResLanguage',					
				'type' => 'LEFT',
				'conditions' => array('`ResLanguage`.`resume_id` = `Resume`.`id`')
			),
			
			array(
				'table' => 'language',
				'alias' => 'Language',					
				'type' => 'LEFT',
				'conditions' => array('`Language`.`id` = `ResLanguage`.`language_id`')
			)
			,
			array(
				'table' => 'reason',
				'alias' => 'ResReason',					
				'type' => 'LEFT',
				'conditions' => array('`ResReason`.`id` = `Resume`.`reason_not_billable`')
			),
		);
		$fields = array('id','ReqResume.id','first_name','last_name','email_id','mobile','mobile2','total_exp','education','present_employer',
		'ResLocation.location', 'present_ctc','expected_ctc', 'Creator.first_name','created_date','notice_period',
		'Resume.modified_date','ReqResume.stage_title','ReqResume.status_title','Designation.designation','present_ctc_type','expected_ctc_type',
		'gender','marital_status','family','present_location','native_location', 'dob','consultant_assess','interview_avail','ResDoc.resume',
		'Position.job_title','Resume.skills','Resume.created_by','Resume.tech_skill_rate','Resume.behav_skill_rate','Position.id','Position.resume_type','certification',
		'personality','achievement','about_company','candidate_brief','credential_shortlisting','vital_info_interview','relevant_exposure','nationality','ResReason.reason','other_input',
		"group_concat(Language.language SEPARATOR ', ') language",'hobby','tech_expert','address1');
		$data2 = $this->Resume->find('all', array('fields' => $fields,'conditions' => array('Resume.id' => $id),
		'order' => array('ReqResume.id' => 'desc'),'joins' => $options));
		$this->set('resume_data', $data2[0]);		
		// get resume education details
		$this->loadModel('ResEdu');
		$data = $this->ResEdu->find('all', array('conditions' => array('resume_id' => $id), 'fields' => array('percent_mark','year_passing','college',
		'course_type','university','location','ResDegree.degree','ResSpec.spec'), 'order' => array('ResEdu.id' => 'desc')));
		$this->set('edu_data', $data);	
		// get resume experience details
		$this->loadModel('ResExp');
		$data = $this->ResExp->find('all', array('conditions' => array('resume_id' => $id), 'fields' => array('experience','work_location','skills',
		'company','other_info','Designation.designation','from_month','from_year','to_month','to_year','company_profile',
		'key_achieve','key_resp','reporting'), 'order' => array('ResExp.id' => 'desc')));
		$this->set('exp_data', $data);
		// get resume experience details
		$this->loadModel('ResTraining');
		$data = $this->ResTraining->find('all', array('conditions' => array('resume_id' => $id), 'fields' => array('train_desc','train_year','prog_title',
		'location'), 'order' => array('train_year' => 'desc')));
		$this->set('train_data', $data);
		// get interview details
		$this->loadModel('ResInterview');		
		$int_data = $this->ResInterview->find('all', array('fields' => array('int_date','stage_title','status_title',
		'ReqResume.billing_date','ReqResume.bill_ctc','ReqResume.ctc_offer','ReqResume.joined_on','outcome','ReqResume.date_offer'),
		'conditions' => array('req_resume_id' => $data2[0]['ReqResume']['id']), 'order' => array('ResInterview.id' => 'desc')));
		$this->set('int_data', $int_data);
		// get requirement details
		$this->loadModel('ReqResume');
		$options = array(			
			array('table' => 'clients',
					'alias' => 'Client',					
					'type' => 'LEFT',
					'conditions' => array('`Client`.`id` = `Position`.`clients_id`')
			),
			array('table' => 'client_contact',
					'alias' => 'ClientContact',					
					'type' => 'LEFT',
					'conditions' => array('`ClientContact.clients_id` = `Client`.`id`',
					'`Position.client_contact_id` = `ClientContact`.`contact_id`')
			),
			array('table' => 'contact',
					'alias' => 'Contact',					
					'type' => 'LEFT',
					'conditions' => array('`Contact.id` = `ClientContact`.`contact_id`')
			)
		);	
		
		
		$data = $this->ReqResume->find('all', array('fields' => array('Position.job_title','Client.client_name','Contact.first_name',
		'Contact.email','Contact.mobile','Contact.phone','Position.id'), 'conditions' => array('ReqResume.resume_id' => $id),
		'order' => array('ReqResume.created_Date' => 'desc'), 'group' => array('ReqResume.id'),	'joins' => $options));
		$this->set('position_data', $data);
		// get the position skills
		$data = $this->ReqResume->Position->findById($data2[0]['Position']['id'], array('fields' => 'tech_skill','behav_skill'));
		$this->set('skill_data', $data);
		
		
	}
	
	/* function to get the location details */
	public function get_loc_details(){
		$this->loadModel('Location');
		return $this->Location->find('list',  array('fields' => array('id','location'), 'order' => array('location ASC'),'conditions' => array('status' => 1)));

	}
	
	/* function to view the resume */
	public function view_doc($file){
		$this->layout = false;
		// fetch the resume data
		$uploaddir = '../../hiring/uploads/resume/';
		$resume_data = $this->Functions->read_document($uploaddir.$file);
		$this->set('RESUME_DATA', $resume_data);
		// extract the mobile
	}
	
	/* auto complete search */	
	public function search(){
		$this->layout = false;		
		$q = trim(Sanitize::escape($_GET['q']));	
			if(!empty($q)){
				$options = array(	
					array('table' => 'req_resume',
						'alias' => 'ReqResume',					
						'type' => 'LEFT',
						'conditions' => array('`ReqResume`.`resume_id` = `Resume`.`id`')
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
				);
			// execute only when the search keywork has value		
			$this->set('keyword', $q);
			/*
			$start = date('Y-m-d H:i:s', strtotime('-6 month'));
			$end = date('Y-m-d', strtotime('+1 day'));
			// last year condition
			$date_cond = array('or' => array("Resume.created_date between ? and ?" => 
					array($start, $end)));
			*/
			$this->Resume->unBindModel(array('belongsTo' => array('Creator')));
			$data = $this->Resume->find('all', array('fields' => array('ResLocation.location',"concat(first_name, ' ', last_name) as first_name",'present_employer','Resume.code',
			'Client.client_name','present_location','native_location'),'group' => array('ResLocation.location','first_name','present_employer','Client.client_name','present_location','native_location'), 'conditions' => 	array("OR" => array ('ResLocation.location like' => '%'.$q.'%',
			'first_name like' => '%'.$q.'%', 'Client.client_name like' => '%'.$q.'%', 'present_employer like' => '%'.$q.'%','code like' => '%'.$q.'%',
			'present_location like' => '%'.$q.'%','native_location like' => '%'.$q.'%'), 'AND' => array('Resume.is_deleted' => 'N')),
			'joins' => $options));		
			$this->set('results', $data);
		}
    }
	
	/* function to view the resume pdf */
	public function view_resume_pdf($file){
		$this->layout = false;
		$this->set('filePath', '../../hiring/uploads/snapshotwatermarked/'.$file);
	}
	
	
	
	/* function to export the profile snap shot */
	public function profile_snapshot($snap_file, $updated, $action){
		 $snap_exp = substr($snap_file, 0, strlen($snap_file) - 5);
		 $pdf_date = date('d-m-Y', $updated);
		 if($action == 'view'){
			$this->redirect('/resume/view_resume_pdf/'.$this->Functions->filter_file($snap_exp).'_'.$pdf_date.'.pdf');		
		 }else{ 
			$this->download_file('../../hiring/uploads/snapshotwatermarked/'.$this->Functions->filter_file($snap_exp).'_'.$pdf_date.'.pdf');
		 }
		 die;		
			// $id = '144515';
			
			/*
			// create the pdf
			if(!empty($id)){
											
				// convert to PDF
				$img_path ='http://localhost/ctsvn/cthiring/img/career-tree3.png';
				require_once(WWW_ROOT.'/vendor/html2pdf/vendor/autoload.php');												
				try{
					$options = array(			
				array('table' => 'req_resume',
						'alias' => 'ReqResume',					
						'type' => 'LEFT',
						'conditions' => array('`ReqResume`.`resume_id` = `Resume`.`id`')
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
				array(
						'table' => 'req_resume_status',
						'alias' => 'ReqResumeStatus',					
						'type' => 'LEFT',
						'conditions' => array('`ReqResumeStatus`.`req_resume_id` = `ReqResume`.`id`')
					),
				array(
						'table' => 'resume_doc',
						'alias' => 'ResDoc',					
						'type' => 'LEFT',
						'conditions' => array('`ResDoc`.`id` = `Resume`.`resume_doc_id`')
					)
				
				);
					// get candidate details
					$user_data2 = $this->Resume->find('all', array('fields' => array('first_name', 'last_name','Designation.designation','education',
					'total_exp','present_employer','exp_skills','ResLocation.location','present_ctc', 'present_ctc_type',
					'expected_ctc_type','expected_ctc','notice_period','dob','gender','present_location','skills',
					'family','consultant_assess','Position.job_title','ResDoc.resume'),
					'conditions' => array('Resume.id' => $id), 'joins' => $options));
					$user_data = $user_data2[0];
					// get resume education details
					$this->loadModel('ResEdu');
					$data = $this->ResEdu->find('all', array('conditions' => array('resume_id' => $id), 'fields' => array('percent_mark','year_passing','college',
					'course_type','university','location','ResDegree.degree','ResSpec.spec'), 'order' => array('ResEdu.id' => 'desc')));
					$edu_data = $data;	
					// get resume experience details
					$this->loadModel('ResExp');
					$data = $this->ResExp->find('all', array('conditions' => array('resume_id' => $id), 'fields' => array('experience','work_location','skills',
					'company','other_info','Designation.designation'), 'order' => array('ResExp.id' => 'desc')));
					$exp_data = $data;
					
					// get the HTML
					ob_start();
					// generate resume jpg
					// $file = '../../hiring/uploads/resume/'.$user_data['ResDoc']['resume'];
					// $convert = $this->Functions->read_document($file);
					// generate pdf file
					include(WWW_ROOT.'/vendor/html2pdf/examples/res/snapshot_template.php');					
					$content = ob_get_clean();
					$html2pdf = new HTML2PDF('P', 'A4', 'fr');
					$html2pdf->pdf->SetDisplayMode('fullpage');
					$html2pdf->writeHTML($content, isset($_GET['vuehtml']));
					$html2pdf->Output(ucfirst(strtolower($user_data['Resume']['first_name'])).' '.ucfirst(strtolower($user_data['Resume']['last_name'])).'Snapshot.pdf', 'D');
					// $root = WWW_ROOT.'home';
					// echo "<script>location.href=$root></script>";								
				}catch(HTML2PDF_exception $e){
					echo $e;
					exit;
				}
			}
			*/
	}
	
	/* function to download the file */
	public function download_snap($file){
		 $this->download_file('../../hiring/uploads/snapshotwatermarked/'.$file);
		 die;
	}
	
	
	/* function to download the file */
	public function download_doc($file){
		 $this->download_file('../../hiring/uploads/resume/'.$file);
		 die;
	}
	
	
	/* function to view the auto pdf */
	public function view_auto_pdf($file){
		$this->layout = false;
		$this->set('filePath', '../../hiring/uploads/autoresumepdf/'.$file);
	}
	
	/* function to download the autoresume */
	public function autoresume($file, $updated, $action){ 
		 $snap_exp = substr($file, 0, strlen($file) - 5);
		 $pdf_date = date('d-m-Y', $updated);
		 if($action == 'view'){
			$this->redirect('/resume/view_auto_pdf/'.$this->Functions->filter_file($snap_exp).'_'.$pdf_date.'.pdf');		
		 }else{ 
			$this->download_file('../../hiring/uploads/autoresumepdf/'.$this->Functions->filter_file($snap_exp).'_'.$pdf_date.'.pdf');
		 }
		 die;	
	/*
	
			// create the pdf
			if(!empty($id)){											
				// convert to PDF
				require_once(WWW_ROOT.'/vendor/html2pdf/vendor/autoload.php');												
				try{
					$options = array(			
				array('table' => 'req_resume',
						'alias' => 'ReqResume',					
						'type' => 'LEFT',
						'conditions' => array('`ReqResume`.`resume_id` = `Resume`.`id`')
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
				array(
						'table' => 'req_resume_status',
						'alias' => 'ReqResumeStatus',					
						'type' => 'LEFT',
						'conditions' => array('`ReqResumeStatus`.`req_resume_id` = `ReqResume`.`id`')
						),
				array(
						'table' => 'resume_doc',
						'alias' => 'ResDoc',					
						'type' => 'LEFT',
						'conditions' => array('`ResDoc`.`id` = `Resume`.`resume_doc_id`')
					),
					
					
				array('table' => 'res_location',
				  'alias' => 'ResLocation2',					
				  'type' => 'LEFT',
				  'conditions' => array('`ResLocation2`.`id` = `Client`.`res_location_id`')
				)
				,	
				array('table' => 'state',
				  'alias' => 'State2',					
				  'type' => 'LEFT',
				  'conditions' => array('`State2`.`id` = `ResLocation2`.`state_id`')
				),
				array('table' => 'res_location',
				  'alias' => 'ResLocation3',					
				  'type' => 'LEFT',
				  'conditions' => array('`ResLocation3`.`id` = `Creator`.`location_id`')
				)
			);
					// get candidate details
					// get candidate details
					$user_data2 = $this->Resume->find('all', array('fields' => array('first_name', 'last_name','Designation.designation','education','total_exp','present_employer','exp_skills','ResLocation.location','present_ctc', 'present_ctc_type','Client.client_name','ResLocation2.location','State2.state',
					'expected_ctc_type','expected_ctc','notice_period','dob','gender','present_location','skills',
					'family','consultant_assess','Position.job_title','ResDoc.resume','address1', 'address2','mobile','mobile2','tech_expert','achievement','personality','about_company','candidate_brief',
					'email_id','Creator.first_name','Creator.last_name','Resume.created_date'),
					'conditions' => array('Resume.id' => $id), 'joins' => $options));
					$user_data = $user_data2[0];
					// get resume education details
					$this->loadModel('ResEdu');
					$data = $this->ResEdu->find('all', array('conditions' => array('resume_id' => $id), 'fields' => array('percent_mark','year_passing','college',
					'course_type','university','location','ResDegree.degree','ResSpec.spec'), 'order' => array('ResEdu.id' => 'desc')));
					$edu_data = $data;	
					// get resume experience details
					$this->loadModel('ResExp');
					$data = $this->ResExp->find('all', array('conditions' => array('resume_id' => $id), 'fields' => array('experience','work_location','skills','company','other_info','Designation.designation','key_resp', 'key_achieve', 'reporting',
					'company_profile','from_month','to_month','from_year','to_year'), 'order' => array('ResExp.id' => 'desc')));
					$exp_data = $data;
					// get resume training details
					$this->loadModel('ResTraining');
					$data = $this->ResTraining->find('all', array('conditions' => array('resume_id' => $id, 'ResTraining.is_deleted' => 'N'), 'fields' => array('train_desc','train_year','prog_title','location'), 'order' => array('ResTraining.id' => 'desc')));
					$training_data = $data;
					// get the HTML
					ob_start();
					include(WWW_ROOT.'/vendor/html2pdf/examples/res/autoresume_template.php');
					
					$content = ob_get_clean();
					$html2pdf = new HTML2PDF('P', 'A4', 'fr');
					$html2pdf->pdf->SetDisplayMode('fullpage');
					$html2pdf->writeHTML($content, isset($_GET['vuehtml']));
					$html2pdf->addFont('OpenSans', 'normal', 'OpenSans.php');
					$html2pdf->setDefaultFont('OpenSans');
					$html2pdf->Output(ucfirst(strtolower($user_data['Resume']['first_name'])).' '.ucfirst(strtolower($user_data['Resume']['last_name'])).'_Auto Resume.pdf', 'D');
					// $root = WWW_ROOT.'home';
					// echo "<script>location.href=$root></script>";								
				}catch(HTML2PDF_exception $e){
					echo $e;
					exit;
				}
			}
			
			*/
	}
	
	// check the role permissions
	public function beforeFilter(){ 
		$this->check_session();
		$this->check_role_access(8);
	}

}