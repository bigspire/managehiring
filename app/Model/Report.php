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

class Report extends AppModel {
	
	public $name = 'Report';
	 
	public $useTable = 'requirements';	
	  
	
	/*
	public $hasOne = array(		
		'ReqResume' => array(
            'className'  => 'ReqResume',
			'foreignKey' => 'requirements_id',
			// 'conditions' => array('stage_title not like' => 'Validation%', 'status_title not like' => 'Pending')
        )
	);
	
	  
	*/
	
	
	/* get no. resumes for the ctc */
	public function get_resumes_ctc($ctc){
		$sql = "CALL get_resume_send('$ctc')";
		$result = $this->query($sql);
		return $result[0]['inc_eligibility']['no_resumes'];

	}
	

	
}