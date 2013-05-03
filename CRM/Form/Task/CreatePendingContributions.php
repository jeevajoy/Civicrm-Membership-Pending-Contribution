<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.3                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2010                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2010
 * $Id$
 *
 */

require_once 'CRM/Member/Form/Task.php';
require_once 'CRM/Member/DAO/Membership.php';
require_once 'CRM/Member/DAO/MembershipType.php';
require_once 'CRM/Contribute/PseudoConstant.php';
require_once 'api/v2/Contact.php';
require_once 'api/v2/Contribution.php';
require_once 'api/v2/MembershipContributionLink.php';

/**
 * This class provides the functionality to delete a group of
 * contacts. This class provides functionality for the actual
 * addition of contacts to groups.
 */

class CRM_Form_Task_CreatePendingContributions extends CRM_Member_Form_Task {

    /**
     * build all the data structures needed to build the form
     *
     * @return void
     * @access public
     */
	function preProcess()
    {	
		parent::preProcess( );
		
		    $count = count($this->_memberIds); 
		
		    $contributionTypes = CRM_Contribute_PseudoConstant::contributionType( );
		    
		    foreach($this->_memberIds as $k => $v){
		        $membershipDAO =& new CRM_Member_DAO_Membership( );
        		$membershipDAO->id = $v;
        		if ($membershipDAO->find( true )) {
        		
        		    $membershipTypeDAO =& new CRM_Member_DAO_MembershipType( );
            		$membershipTypeDAO->id = $membershipDAO->membership_type_id;
            		$membershipTypeDAO->find( true );
            		
            	/*	if ($membershipDAO->membership_type_id ==  4) 
                        $generalType = 'FRIEND'; 
                    else
                        $generalType = 'MEMBER'; */
            		
            		$membership_fee = $membershipTypeDAO->minimum_fee;
            		
            		$contact_id = $membershipDAO->contact_id;		
           		    $params1 = array(
                    				'contact_id' => $contact_id
                    				);			
        		    $contactDetails = civicrm_contact_get($params1);
        		      
        		    $params[] = array(
                    'display_name'           => $contactDetails[$contact_id]['display_name'],
                    'receive_date'           => date('d/m/Y'),
                    'total_amount'           => $membershipTypeDAO->minimum_fee,
                    'source'                 => 'Offline Contribution : Renew Memberships',
                    'contribution_status_id' => 1,
                    'contribution_type'      => $contributionTypes[2],
                    'membership_type'        => $membershipTypeDAO->name,
                    'membership_start_date'  =>date("d/m/Y", strtotime($membershipDAO->start_date)),
                    'membership_end_date'  =>date("d/m/Y", strtotime($membershipDAO->end_date)),            
                    );
                    //$contribution =& civicrm_contribution_add($params);      
        		}
        }
               
    		$this->assign('totalAddedContributions', $params);        
    		$this->assign('selectedMembershipCount', $count);
    		$this->assign('contributionsCount', count($params));
            $this->addElement('checkbox', 'is_renew', ts('<b style="color:red;"> Do you want the memberships to be renewed ?</b>') );
	}
	
	
    /**
     * Build the form
     *
     * @access public
     * @return void
     */
    function buildQuickForm( ) {
		    $this->addDefaultButtons( ts('Submit') );
    }
	

   
    /**
     * process the form after the input has been submitted and validated
     *
     * @access public
     * @return None
     */
    public function postProcess() {

        $i = 0;
		    foreach($this->_memberIds as $k => $v){
            
		        $membershipDAO =& new CRM_Member_DAO_Membership( );
        		$membershipDAO->id = $v;
        		if ($membershipDAO->find( true )) {
        		
        		    $membershipTypeDAO =& new CRM_Member_DAO_MembershipType( );
            		$membershipTypeDAO->id = $membershipDAO->membership_type_id;
            		$membershipTypeDAO->find( true );
        		    
        		    
        		    
        		    $params = array(
                     'q'                     =>'civicrm/ajax/rest',
                    'version'                =>'3',
                    'contact_id'             => $membershipDAO->contact_id,
                    'receipt_date'           => date('YmdHis'),
                    'receive_date'           => date('YmdHis'),
                    'total_amount'           => $membershipTypeDAO->minimum_fee,
                    'source'                 => 'Offline Contribution : Renew Memberships',
                    'contribution_status_id' => 1,
                    'contribution_type_id'   => 2
                    
                    );
                    require_once 'api/api.php'; 
                    require_once 'api/v3/Contribution.php';
                    
                    //$contribution =& civicrm_contribution_add($params);
                    $contribution=civicrm_api("Contribution","create",$params);
                    
                    
                    $memberContribparams = array (
                             'contribution_id' => $contribution['id'] ,
                             'membership_id'   => $v
                             );
                    $membershipPayment = civicrm_membershipcontributionlink_create( $memberContribparams );
                    //Moving Membership Start Dates and End Dates
                    if($_POST['is_renew']){                   
                        $temp_date = strtotime($membershipDAO->end_date);                       
                        $duration_interval = $membershipTypeDAO->duration_interval;
                        $duration_unit = $membershipTypeDAO->duration_unit;
                        
                      //  $mem_start_date = strtotime ( "+1 day" , $temp_date ) ;
                      //  $mem_start_date = date ( 'YmdHis' , $mem_start_date );
                        
                        $mem_end_date = strtotime ( "+$duration_interval $duration_unit" , $temp_date ) ;
                        $mem_end_date = date ( 'YmdHis' , $mem_end_date );
                        //Updating Membership Fields
                        $membership_params = array ( 
                                         'version'                =>'3',
                                         'sequential'             =>'1',
                                         'id'                     =>$membershipDAO->id,               
                                         'status_id'              =>'1',
                                        // 'start_date'             =>$mem_start_date,
                                         'end_date'               =>$mem_end_date,        
                                         );                      
                        $membership_update = civicrm_api("Membership","update",$membership_params );                    
                    }                       
        		}
        		$i++;
        }
         if ( $i ) { 
            if($_POST['is_renew']){
               $status[] = ts('Total Memberships(s) with Contribution renewed: %1 <br />', array(1 => $i));  
            }else{
               $status[] = ts('Total Contributions(s) created: %1', array(1 => $i)); 
            }               
        }
    	$status = @ implode( '<br/>', $status );
        CRM_Core_Session::setStatus( $status );
	}//end of function
}
