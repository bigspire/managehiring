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

class Client extends AppModel {
	
	public $name = 'Client';	
	
    public $useTable = 'clients';
	 
	public $belongsTo = array(		
		'Creator' => array(
            'className'  => 'User',
			'foreignKey' => 'created_by'			
        ),
		'ContactBranch' => array(
            'className'  => 'ContactBranch',
			'foreignKey' => 'res_location_id'			
        )		
	);	
	


	
	public $validate = array(
		'client_name' => array(		
            'empty' => array(
                'rule'     => 'notEmpty',
                'required' => true,
                'message'  => 'Please enter the client name'
            ),
			'client_exists' => array(
                'rule'     => 'client_exists',
                'required' => true,
                'message'  => 'Client name already exists'
            )
        ),
		'city' => array(		
            'empty' => array(
                'rule'     => 'notEmpty',
                'required' => true,
                'message'  => 'Please enter the city name'
            )
        ),
		'pincode' => array(		
            'empty' => array(
                'rule'     => 'notEmpty',
                'required' => true,
                'message'  => 'Please enter the pincode'
            ),
			'numeric' => array(
                'rule'     => 'numeric',
                'required' => true,
                'message'  => 'Please enter numeric values only'
            )
        ),
		'state' => array(		
            'empty' => array(
                'rule'     => 'notEmpty',
                'required' => true,
                'message'  => 'Please select the state'
            )
        ),
		'res_location_id' => array(		
            'empty' => array(
                'rule'     => 'notEmpty',
                'required' => true,
                'message'  => 'Please select the district'
            )
        ),
		'account_holder' => array(		
            'empty' => array(
                'rule'     => 'validate_account',
                'required' => true,
                'message'  => 'Please select the client relationship manager'
            )
        ),
				
		'status' => array(		
            'empty' => array(
                'rule'     => 'notEmpty',
                'required' => true,
                'message'  => 'Please select the status'
            )
        ),
		'remarks' => array(		
            'empty' => array(
                'rule'     => 'notEmpty',
                'required' => true,
                'message'  => 'Please enter the reason to reject'
            )
        ),
		'rev_remarks' => array(
            'empty' => array(
                'rule'     => 'notEmpty',
                'required' => true,
                'message'  => 'Please enter the remarks for the revision'
            )
        )
	);
	
	
	
	/* function to check company already exists */
	public function client_exists(){
		// for edit page
		if($this->data['Client']['page'] == 'edit_client'){
			$cond = array('Client.id !=' => $this->data['Client']['id']);
		}
		$count = $this->find('count', array('conditions' => array('client_name' => $this->data['Client']['client_name'],
		'res_location_id' => $this->data['Client']['res_location_id'],  'Client.status' => '0', 'Client.is_deleted' => 'N', $cond)));
		if($count){
			return false;
		}else{
			return true;
		}
	}
	
	
	

	/* function to validate the permissions */
	public function validate_account(){ 
		if($this->data['Client']['account_holder'][0] != ''){
			return true;
		}else{
			return false;
		}
	}
	
	/* function to load locations */
	public function load_district_post($id){
		$loc_list = $this->ContactBranch->find('list', array('fields' => array('id','branch'), 'order' => array('branch ASC'),
		'conditions' => array('status' => '1',	'state_id' => $id)));
		return $loc_list;
	}
	
	
	
	
	
	/* function to load the districts options */
	public function load_district($id){
		$loc_list = $this->ContactBranch->find('list', array('fields' => array('id','branch'),
		'order' => array('branch ASC'),'conditions' => array('status' => '1', 'is_deleted' => 'N',
		'state_id' => $id)));
		$options .= "<option value=''>Choose District</option>";
		foreach($loc_list as $key => $option){ 
			$options .= "<option value='".$key."'>".$option."</option>";
		}
		echo $options;
	}

	

	/* function to get the team members */
	public function get_team($id, $show){
		return $this->get_team_mem($id, $show);
	}
	

	
}