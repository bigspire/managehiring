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

class ResEdu extends AppModel {
	
	public $name = 'ResEdu';
	 
	public $useTable = 'resume_education';
	
	
	public $belongsTo = array(		
		'Resume' => array(
            'className'  => 'Resume',
			'foreignKey' => 'resume_id'			
        ),
		'ResDegree' => array(
            'className'  => 'ResDegree',
			'foreignKey' => 'resume_degree_id'			
        ),
		'ResSpec' => array(
            'className'  => 'ResSpec',
			'foreignKey' => 'resume_spec_id'			
        )
	);
	
	
		


	
}