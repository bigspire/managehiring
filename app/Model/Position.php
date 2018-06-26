<?php
/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
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
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

class Position extends AppModel {
	
	public $name = 'Position';
	 
	public $useTable = 'requirements';	
	  
	
	public $belongsTo = array(		
		'Creator' => array(
            'className'  => 'User',
			'foreignKey' => 'created_by'			
        ),
		'Client' => array(
            'className'  => 'Client',
			'foreignKey' => 'clients_id'			
        ),
		'ReqStatus' => array(
            'className'  => 'ReqStatus',
			'foreignKey' => 'req_status_id'			
        ),
		'FunctionArea' => array(
            'className'  => 'FunctionArea',
			'foreignKey' => 'function_area_id'			
        )		
		
	);
	
	public $hasOne = array(		
		'ReqResume' => array(
            'className'  => 'ReqResume',
			'foreignKey' => 'requirements_id',
			'conditions' => array('stage_title !=' => 'Draft')

        ),
		/*
		'ReqTeam' => array(
            'className'  => 'ReqTeam',
			'foreignKey' => 'requirements_id'			
        )
		*/

	);
	
	public $validate = array(
		'clients_id' => array(		
            'empty' => array(
                'rule'     => 'notEmpty',
                'required' => true,
                'message'  => 'Please select the client name'
            )
        ),
		'client_contact_id' => array(		
            'empty' => array(
                'rule'     => 'notEmpty',
                'required' => true,
                'message'  => 'Please select the SPOC'
            )
        ),
		'job_title' => array(		
            'empty' => array(
                'rule'     => 'notEmpty',
                'required' => true,
                'message'  => 'Please enter the job title'
            )
        ),
		'education' => array(
            'empty' => array(
                'rule'     => 'notEmpty',
                'required' => true,
                'message'  => 'Please enter the qualification'
            )
        ),
		'location' => array(		
            'empty' => array(
                'rule'     => 'notEmpty',
                'required' => true,
                'message'  => 'Please enter the location'
            )
        ),
		'max_exp' => array(		
            'empty' => array(
                'rule'     => 'validate_exp',
                'required' => true,
                'message'  => 'Please select the min exp. and max exp.'
            )
        ),
		'ctc_to_type' => array(		
            'empty' => array(
                'rule'     => 'validate_ctc',
                'required' => true,
                'message'  => 'Please select all values'
            )
        ),
			
		'no_job' => array(		
            'empty' => array(
                'rule'     => 'notEmpty',
                'required' => true,
                'message'  => 'Please select no. of openings'
            )
        ),
		'total_opening' => array(		
            'empty' => array(
                'rule'     => 'notEmpty',
                'required' => true,
                'message'  => 'Please select no. of openings'
            ),
			  'valid_opening' => array(
                'rule'     => 'valid_opening',
                'required' => true,
                'message'  => 'Total openings not matched with the openings assigned'
            )
        ),
		
		'team_member_req' => array(		
            'empty' => array(
                'rule'     => 'validate_team',
                'required' => true,
                'message'  => 'Please select the recruiters'
            )
        ),
		'end_date' => array(		
            'empty' => array(
                'rule'     => 'notEmpty',
                'required' => true,
                'message'  => 'Please select the expected joining date'
            )
        ),			
		'function_area_id' => array(		
            'empty' => array(
                'rule'     => 'notEmpty',
                'required' => true,
                'message'  => 'Please select the functional area'
            )
        ),		
			
		'job_desc' => array(		
            'empty' => array(
                'rule'     => 'validate_jobDesc',
                'required' => true,
                'message'  => 'Please type job description here or attach job description file below'
            ),
			 'minlength' => array(
                'rule'     => 'check_length',
                'required' => true,
                'message'  => 'Job description must be min. of 100 chars.'
            )
        ),
		'desc_file' => array(		
            'empty' => array(
                'rule'     => 'validate_file',
                'required' => true,
                'message'  => 'Please upload only docx formats only'
            )
        ),
	
		'interview_level' => array(		
            'empty' => array(
                'rule'     => 'notEmpty',
                'required' => true,
                'message'  => 'Please select the interview level'
            )
        ),
		'subject' => array(		
            'empty' => array(
                'rule'     => 'notEmpty',
                'required' => true,
                'message'  => 'Please enter the mail subject'
            )
        ),
		'message' => array(		
            'empty' => array(
                'rule'     => 'notEmpty',
                'required' => true,
                'message'  => 'Please select the mail message'
            )
        ),
	'subject_candidate' => array(		
            'empty' => array(
                'rule'     => 'notEmpty',
                'required' => true,
                'message'  => 'Please enter the mail subject'
            )
        ),
		'message_candidate' => array(		
            'empty' => array(
                'rule'     => 'notEmpty',
                'required' => true,
                'message'  => 'Please select the mail message'
            )
        ),
		'interview_stage_id' => array(		
            'empty' => array(
                'rule'     => 'notEmpty',
                'required' => true,
                'message'  => 'Please select the interview mode'
            )
        ),
	
		'int_date' => array(		
            'empty' => array(
                'rule'     => 'notEmpty',
                'required' => true,
                'message'  => 'Please select the interview date'
            )
        ),
	
		/*
		'int_time' => array(		
            'empty' => array(
                'rule'     => 'notEmpty',
                'required' => true,
                'message'  => 'Please select the interview time'
            )
        ),
		*/
	
		'int_duration' => array(		
            'empty' => array(
                'rule'     => 'notEmpty',
                'required' => true,
                'message'  => 'Please select the interview duration'
            )
        ),
		'remarks' => array(		
            'empty' => array(
                'rule'     => 'notEmpty',
                'required' => true,
                'message'  => 'Please enter the reason to reject'
            )
        ),
		'contact_name' => array(		
            'empty' => array(
                'rule'     => 'notEmpty',
                'required' => true,
                'message'  => 'Please enter the contact person name'
            )
        ),
		'contact_no' => array(		
            'empty' => array(
                'rule'     => 'notEmpty',
                'required' => true,
                'message'  => 'Please enter the contact no.'
            )
        ),
			
		'venue' => array(		
            'empty' => array(
                'rule'     => 'notEmpty',
                'required' => true,
                'message'  => 'Please enter the interview venue'
            )
        ),
		'note' => array(		
            'empty' => array(
                'rule'     => 'notEmpty',
                'required' => true,
                'message'  => 'Please enter the reason'
            )
        ),
		'ctc_offer' => array(		
            'empty' => array(
                'rule'     => 'notEmpty',
                'required' => true,
                'message'  => 'Please enter the offered CTC'
            ),
			 'numeric' => array(
                'rule'     => 'numeric',
                'required' => true,
                'message'  => 'Please enter numeric values only'
            )
        ),
		'date_offer' => array(		
            'empty' => array(
                'rule'     => 'notEmpty',
                'required' => true,
                'message'  => 'Please select the offered date'
            )
        ),
		'joined_on' => array(		
            'empty' => array(
                'rule'     => 'notEmpty',
                'required' => true,
                'message'  => 'Please select the joining date'
            )
        ),
		'plan_join_date' => array(		
            'empty' => array(
                'rule'     => 'notEmpty',
                'required' => true,
                'message'  => 'Please select the new joining date'
            )
        ),
		'tech_skill' => array(		
            'empty' => array(
                'rule'     => 'notEmpty',
                'required' => true,
                'message'  => 'Please enter the technical skills'
            )
        )
		,
		'behav_skill' => array(		
            'empty' => array(
                'rule'     => 'notEmpty',
                'required' => true,
                'message'  => 'Please enter the behavioural skills'
            )
        )
		,
		'reason_id' => array(		
            'empty' => array(
                'rule'     => 'notEmpty',
                'required' => true,
                'message'  => 'Please select the reason'
            )
        )
		,
		'resume_type' => array(		
            'empty' => array(
                'rule'     => 'notEmpty',
                'required' => true,
                'message'  => 'Please select any one'
            )
        )
		,
		'hide_contact' => array(		
            'empty' => array(
                'rule'     => 'notEmpty',
                'required' => true,
                'message'  => 'Please select any one'
            )
        )
		,
		'req_status_id' => array(		
            'empty' => array(
                'rule'     => 'notEmpty',
                'required' => true,
                'message'  => 'Please select the new status'
            )
        ),
		'job_code' => array(		
            'empty' => array(
                'rule'     => 'notEmpty',
                'required' => true,
                'message'  => 'Please enter the job code'
            ),
			 'unique' => array(
                'rule'     => 'check_unique_code',
                'required' => true,
                'message'  => 'Job Code already exists'
            )
        ),
		'rev_remarks' => array(
            'empty' => array(
                'rule'     => 'notEmpty',
                'required' => true,
                'message'  => 'Please enter the remarks for the revision'
            )
        ),
		'is_rpo' => array(
            'empty' => array(
                'rule'     => 'notEmpty',
                'required' => true,
                'message'  => 'Please select the project type'
            )
        ),
		'next_interview' => array(
            'empty' => array(
                'rule'     => 'notEmpty',
                'required' => true,
                'message'  => 'Please select the next interview'
            )
        )
		,
		'status_remark' => array(
            'empty' => array(
                'rule'     => 'notEmpty',
                'required' => true,
                'message'  => 'Please enter the remarks'
            )
        )
				
	);
	
	/* function to validate the team members */
	public function validate_team(){
		$team = str_replace(',','',$this->data['Position']['team_id']);
		if(trim($this->data['Position']['team_id']) != ''){
			return true;
		}else{
			return false;
		}
	}
	
		/* function to validate the no. of openings*/
	public function valid_opening(){
		$assign = 0;
		$no_job = explode(',', $this->data['Position']['team_id']);
		foreach($no_job as $job_sel){ 
			$assign_job = explode('-', $job_sel);
			$assign += $assign_job[1];
		}	
		if($assign ==  $this->data['Position']['total_opening']){
			return true;
		}else{
			return false;
		}
	}
	
	
	/* function to validate the job desc length */
	public function check_length(){
		if($this->data['Position']['job_desc'] != ''){
			if(strlen($this->data['Position']['job_desc']) < 100){				
				return false;
			}else{
				return true;
			}
		}else{
			return true;
		}
	}
	
	/* function to check job code already exists */
	public function check_unique_code(){
		// for edit page
		if($this->data['Position']['page'] == 'edit_position'){
			$cond = array('Position.id !=' => $this->data['Position']['id']);
		}
		$count = $this->find('count', array('conditions' => array('job_code' => $this->data['Position']['job_code'],
		'Position.is_deleted' => 'N', $cond)));
		if($count){
			return false;
		}else{
			return true;
		}
	}
	
	
	/* function to validate the file type */
	public function validate_file(){ 
		if($this->data['Position']['desc_file']['name'] != ''){
			if($this->data['Position']['desc_file']['type'] == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'){			
				return true;
			}else{
				return false;
			}
		}else{
			return true;
		}
	}
	
	
	/* function to validate the experience */
	public function validate_exp(){
		if($this->data['Position']['min_exp'] == '' || $this->data['Position']['max_exp'] == ''){
			return false;
		}else{
			return true;
		}
	}
	
	/* function to validate the job desc */
	public function validate_jobDesc(){
		if($this->data['Position']['page'] != 'edit_position'){
			if($this->data['Position']['job_desc'] == '' && $this->data['Position']['desc_file']['name'] == ''){
				return false;
			}else{
				return true;
			}
		}
		return true;
	}
	
	
	/* function to validate the req. date */
	public function validate_req_date(){
		if($this->data['Position']['start_date'] == '' || $this->data['Position']['end_date'] == ''){
			return false;
		}else{
			return true;
		}
	}
	
	/* function to validate the ctc */
	public function validate_ctc(){
		if($this->data['Position']['ctc_from'] == '' || $this->data['Position']['ctc_to'] == ''
		 || $this->data['Position']['ctc_from_type'] == ''  || $this->data['Position']['ctc_to_type'] == ''){
			return false;
		}else{
			return true;
		}
	}
	
	
	
	/* function to get the team members */
	public function get_team($id, $show){
		return $this->get_team_mem($id, $show);
	}
	
	/* function get the billing count */
	public function get_approve_billing($id){		
		$sql = "CALL count_approve_billing('$id')";		
		$result = $this->query($sql);
		return $result[0][0]['count'];
	}
	
		
	/* function to calculate the billing count */
	public function get_billing_count($id){
		$count = $this->get_approve_billing($id);
		return $count;
	}
	
	
	
}