<?php

require_once 'prospect.civix.php';

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function prospect_civicrm_config(&$config) {
  _prospect_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function prospect_civicrm_xmlMenu(&$files) {
  _prospect_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function prospect_civicrm_install() {
  _prospect_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function prospect_civicrm_postInstall() {
  _prospect_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function prospect_civicrm_uninstall() {
  _prospect_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function prospect_civicrm_enable() {
  _prospect_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function prospect_civicrm_disable() {
  _prospect_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function prospect_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _prospect_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function prospect_civicrm_managed(&$entities) {
  _prospect_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function prospect_civicrm_caseTypes(&$caseTypes) {
  _prospect_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function prospect_civicrm_angularModules(&$angularModules) {
  _prospect_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function prospect_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _prospect_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function prospect_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function prospect_civicrm_navigationMenu(&$menu) {
  _prospect_civix_insert_navigation_menu($menu, NULL, array(
    'label' => ts('The Page', array('domain' => 'uk.co.compucorp.civicrm.prospect')),
    'name' => 'the_page',
    'url' => 'civicrm/the-page',
    'permission' => 'access CiviReport,access CiviContribute',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _prospect_civix_navigationMenu($menu);
} // */

/**
 * Implements hook_civicrm_entityTypes().
 */
function prospect_civicrm_entityTypes(&$entityTypes) {
  $entityTypes[] = [
    'name'  => 'ProspectConverted',
    'class' => 'CRM_Prospect_DAO_ProspectConverted',
    'table' => 'civicrm_prospect_converted',
  ];
}

/**
 * Implements hook_civicrm_custom().
 */
function prospect_civicrm_custom($op, $groupID, $entityID, &$params ) {//
  if ((int) $groupID === _prospect_civicrm_get_custom_group_id('Prospect_Financial_Information') && $op === 'edit') {
    $fields = new CRM_Prospect_prospectFinancialInformationFields($entityID);

    $fields->updateExpectation();
  }
}

/**
 * Returns 'Prospect_Financial_Information' Custom Group ID.
 *
 * @return int|NULL
 */
function _prospect_civicrm_get_custom_group_id($customGroupName) {
  $customGroupResponse = civicrm_api3('CustomGroup', 'get', [
    'return' => ['id'],
    'name' => $customGroupName,
    'options' => ['limit' => 1],
  ]);

  if (!empty($customGroupResponse['id'])) {
    return (int) $customGroupResponse['id'];
  }

  return NULL;
}

/**
 * Implements hook_civicrm_post().
 */
function prospect_civicrm_post($op, $objectName, $objectId, &$objectRef) {
  $postFunction = '_prospect_civicrm_post_' . strtolower($objectName);

  if(!function_exists($postFunction)) {
    return;
  }

  call_user_func_array($postFunction, [$op, $objectId, $objectRef]);
}

/**
 * Function which will be called when hook_civicrm_post is executed for the
 * Case entity.
 *
 * @param string $op
 * @param int $objectId
 * @param object $objectRef
 */
function _prospect_civicrm_post_case($op, $objectId, &$objectRef) {
  if (in_array($op, ['create', 'edit'])) {
    try {
      // Update Financial Information fields data
      $fields = new CRM_Prospect_prospectFinancialInformationFields($objectId);
      $fields->updateFieldsFromRequest([
        'Prospect_Amount',
        'Probability',
        'Expected_Date',
      ]);
      $fields->updateExpectation();

      // Update Substatus fields data
      $fields = new CRM_Prospect_ProspectCustomGroups('Prospect_Substatus', $objectId);
      $fields->updateFieldsFromRequest([
        'Substatus',
      ]);
    } catch (CiviCRM_API3_Exception $e) {
      CRM_Core_Session::setStatus(
        ts('Cannot find Case entry. The Case didn\'t get created properly or there is other issue with retrieving the Case.'),
        ts('Error updating Expectation value'),
        'error'
      );

      return;
    }
  }
}

/**
 * Function which will be called when hook_civicrm_post is executed for the
 * Contribution entity.
 *
 * @param string $op
 * @param int $objectId
 * @param object $objectRef
 */
function _prospect_civicrm_post_contribution($op, $objectId, &$objectRef) {
  _prospect_civicrm_convert($objectId, CRM_Prospect_BAO_ProspectConverted::PAYMENT_TYPE_CONTRIBUTION);
}

/**
 * Function which will be called when hook_civicrm_post is executed for the
 * Pledge entity.
 *
 * @param string $op
 * @param int $objectId
 * @param object $objectRef
 */
function _prospect_civicrm_post_pledge($op, $objectId, &$objectRef) {
  _prospect_civicrm_convert($objectId, CRM_Prospect_BAO_ProspectConverted::PAYMENT_TYPE_PLEDGE);
}

/**
 * If Case Id is passed through New Contribution / Pledge form then it means
 * that the entity is asked to be converted by Prospect form.
 *
 * Creates ProspectConverted entity with specified payment entity, payment type
 * and Case Id.
 *
 * @param int $paymentEntityId
 * @param int $paymentTypeId
 */
function _prospect_civicrm_convert($paymentEntityId, $paymentTypeId) {
  $caseId = CRM_Utils_Request::retrieve('caseId', 'Integer');

  if (!$caseId) {
    return;
  }

  $prospectConverted = CRM_Prospect_BAO_ProspectConverted::findByCaseID($caseId);
  if (!empty($prospectConverted)) {
    return;
  }

  $fields = new CRM_Prospect_prospectFinancialInformationFields($caseId);

  CRM_Prospect_BAO_ProspectConverted::create([
    'prospect_case_id' => $caseId,
    'payment_entity_id' => $paymentEntityId,
    'payment_type_id' => $paymentTypeId,
  ]);

  // Sets (Prospect Amount) value to 0.
  $fields->setValueOf('Prospect_Amount', 0);

  // Sets (Expectation) value to 0.
  $fields->setValueOf('Expectation', 0);
}

/**
 * Implements hook_civicrm_apiWrappers().
 */
function prospect_civicrm_apiWrappers(&$wrappers, $apiRequest) {
  if (!($apiRequest['entity'] === 'Case' && in_array($apiRequest['action'], ['create', 'edit']))) {
    return;
  }

  $wrappers[] = new CRM_Prospect_APIWrapper_prospectFinancialInformationCustomFields();
}

/**
 * Implements hook_civicrm_alterTemplateFile().
 */
function prospect_civicrm_alterTemplateFile($formName, &$form, $context, &$tplName) {
  $functionName = '_prospect_civicrm_alterTemplateFile_' . $formName;

  if(!function_exists($functionName)) {
    return;
  }

  call_user_func_array($functionName, [$formName, &$form, $context, $tplName]);
}

/**
 * Implements hook_civicrm_alterTemplateFile callback for 'CRM_Case_Page_Tab'
 * form name.
 *
 * @param string $formName
 * @param object $form
 * @param string $context
 * @param string $tplName
 */
function _prospect_civicrm_alterTemplateFile_CRM_Case_Page_Tab($formName, &$form, $context, &$tplName) {
  $caseId = CRM_Utils_Request::retrieve('id', 'Integer');

  if (empty($caseId)) {
    return;
  }

  $fields = new CRM_Prospect_prospectFinancialInformationFields($caseId);
  $prospectConverted = CRM_Prospect_BAO_ProspectConverted::findByCaseID($caseId);

  if (!empty($prospectConverted)) {
    $form->assign('paymentInfo', $prospectConverted->getPaymentInfo());
  }

  $form->assign('isCaseConverted', !empty($prospectConverted));
  $form->assign('prospectFinancialInformationFields', $fields);
  $form->assign('campaignLabel', _prospect_civicrm_get_campaign_label_by_id($fields->getValueOf('Campaign_Id')));
  $form->assign('currency', CRM_Core_BAO_Country::getDefaultCurrencySymbol());

  CRM_Core_Resources::singleton()->addScriptFile('civicrm', 'js/jquery/jquery.crmEditable.js', CRM_Core_Resources::DEFAULT_WEIGHT, 'html-header');
  CRM_Core_Resources::singleton()->addScriptFile('uk.co.compucorp.civicrm.prospect', 'js/Prospect.Page.CaseView.js', CRM_Core_Resources::DEFAULT_WEIGHT, 'html-header');
}

/**
 * Implements hook_civicrm_alterTemplateFile callback for 'CRM_Pledge_Page_Payment'
 * form name.
 *
 * @param string $formName
 * @param object $form
 * @param string $context
 * @param string $tplName
 */
function _prospect_civicrm_alterTemplateFile_CRM_Pledge_Page_Payment($formName, &$form, $context, &$tplName) {
  _prospect_civicrm_addMainCSSFile();

  $pledgeId = CRM_Utils_Request::retrieve('pledgeId', 'Integer');

  if (empty($pledgeId)) {
    return;
  }

  $prospectConverted = civicrm_api3('ProspectConverted', 'get', [
    'sequential' => 1,
    'payment_entity_id' => $pledgeId,
    'payment_type_id' => CRM_Prospect_BAO_ProspectConverted::PAYMENT_TYPE_PLEDGE,
    'options' => [ 'limit' => 1 ],
  ]);

  if (empty($prospectConverted['count'])) {
    return;
  }

  $form->assign('caseID', $prospectConverted['values'][0]['prospect_case_id']);
  $form->assign('prospectFinancialInformationFields', new CRM_Prospect_prospectFinancialInformationFields($prospectConverted['values'][0]['prospect_case_id']));
}

/**
 * Implements hook_civicrm_alterTemplateFile callback for 'CRM_Contribute_Page_Tab'
 * form name.
 *
 * @param string $formName
 * @param object $form
 * @param string $context
 * @param string $tplName
 */
function _prospect_civicrm_alterTemplateFile_CRM_Contribute_Page_Tab($formName, &$form, $context, &$tplName) {
  _prospect_civicrm_addMainCSSFile();
}

/**
 * Implements hook_civicrm_alterTemplateFile callback for 'CRM_Pledge_Page_Tab'
 * form name.
 *
 * @param string $formName
 * @param object $form
 * @param string $context
 * @param string $tplName
 */
function _prospect_civicrm_alterTemplateFile_CRM_Pledge_Page_Tab($formName, &$form, $context, &$tplName) {
  _prospect_civicrm_addMainCSSFile();
}

/**
 * Adds 'style.css' to resource files.
 */
function _prospect_civicrm_addMainCSSFile() {
  CRM_Core_Resources::singleton()->addStyleFile('uk.co.compucorp.civicrm.prospect', 'css/style.css');
}

/**
 * Gets Campaign label by Campaign ID.
 *
 * @param int $id
 *
 * @return string|NULL
 */
function _prospect_civicrm_get_campaign_label_by_id($id) {
  $campaign = _prospect_civicrm_get_campaign_options($id);

  return !empty($campaign[$id]) ? $campaign[$id] : NULL;
}

/**
 * Implements hook_civicrm_buildForm().
 */
function prospect_civicrm_buildForm($formName, &$form) {
  $handlers = [
    new CRM_Prospect_Form_Handler_PaymentEntityAdd(),
    new CRM_Prospect_Form_Handler_PaymentEntityUpdate(),
    new CRM_Prospect_Form_Handler_CaseStatusUpdate(),
  ];

  foreach($handlers as $handler) {
    $handler->handle($formName, $form);
  }
}

/**
 * Implements hook_civicrm_fieldOptions().
 */
function prospect_civicrm_fieldOptions($entity, $field, &$options, $params) {
  $campaignCustomFieldID = CRM_Core_BAO_CustomField::getCustomFieldID('Campaign', 'Prospect_Financial_Information');

  if ($entity === 'Case' && $field === 'custom_' . $campaignCustomFieldID) {
    $options = _prospect_civicrm_get_campaign_options();
  }
}

/**
 * Get list of active Campaigns or a single Campaign if ID is specified.
 *
 * @param int $id
 *
 * @return array
 */
function _prospect_civicrm_get_campaign_options($id = NULL) {
  $result = [];

  $campaigns = civicrm_api3('Campaign', 'get', [
    'sequential' => 1,
    'id' => $id,
    'is_active' => 1,
    'return' => ['title'],
    'options' => [
      'limit' => 0,
      'sort' => 'title ASC',
    ],
  ]);

  foreach ($campaigns['values'] as $campaign) {
    $result[$campaign['id']] = $campaign['title'];
  }

  return $result;
}
