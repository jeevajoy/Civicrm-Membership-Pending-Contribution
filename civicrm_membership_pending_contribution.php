<?php
define( 'CIVICRM_CREATE_PENDING_CONTRIBUTIONS', 1436 );

function civicrm_membership_pending_contribution_civicrm_config( &$config ) {
    $template =& CRM_Core_Smarty::singleton( );
    $customRoot = dirname( __FILE__ );
    $customDir = $customRoot . DIRECTORY_SEPARATOR . 'templates';
    if ( is_array( $template->template_dir ) ) {
        array_unshift( $template->template_dir, $customDir );
    } else {
        $template->template_dir = array( $customDir, $template->template_dir );
    }
    // also fix php include path
    $include_path = $customRoot . PATH_SEPARATOR . get_include_path( );
    set_include_path( $include_path );
}

/**
 * Implement hook_civicrm_buildForm
 * To display the multi row custom fields as text box fields 
 */

function civicrm_membership_pending_contribution_civicrm_searchTasks( $objectType, &$tasks ) {

  if ( $objectType == 'membership' ) {
        $tasks[CIVICRM_CREATE_PENDING_CONTRIBUTIONS] = array( 'title'  => ts( 'Renew & Create Contributions' ),
                                                'class'  => 'CRM_Form_Task_CreatePendingContributions',
                                                'result' => false );
  }
}