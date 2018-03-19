<?php

/**
 * Class for data manipulation and retrieval on Prospect extension custom groups.
 */
class CRM_Prospect_ProspectCustomGroups {
  /**
   * target prospect custom group name
   *
   * @var string
   */
  private $customGroupName;

  /**
   * Case Id.
   *
   * @var int
   */
  private $caseId;

  /**
   *  Custom fields with their machine names and values as
   * {name} => [ 'machine_name' => {machine_name}, 'value' => {value} ].
   *
   * @var array
   */
  private $fields = [];

  /**
   * Custom fields list as
   * {name} => custom_{id}.
   *
   * @var array
   */
  private $fieldsList = [];

  /**
   * @param string $customGroupName
   * @param int $caseId
   */
  public function __construct($customGroupName, $caseId) {
    $this->customGroupName = $customGroupName;
    $this->caseId = $caseId;
  }

  /**
   * Updates values with specified params.
   *
   * @param array $params
   */
  public function updateFieldsFromParams($params) {
    $updateParams = [
      'entity_id' => $this->caseId,
    ];

    foreach ($params as $key => $value) {
      if (substr($key, 0, 7) === 'custom_') {
        $updateParams[$key] = $value;
      }
    }

    civicrm_api3('CustomValue', 'create', $updateParams);

    $this->getFields(TRUE);
  }

  /**
   * Updates field values with request data.
   *
   * @param array $fields
   */
  public function updateFieldsFromRequest($fields) {
    $updateParams = [];

    foreach ($fields as $field) {
      $value = $this->getRequestValueOf($field);

      if ($value !== NULL) {
        $updateParams[$this->getMachineNameOf($field)] = $value;
      }
    }

    if (!empty($updateParams)) {
      $this->updateFieldsFromParams($updateParams);
    }
  }

  /**
   * Gets the value of a custom field.
   *
   * @param string $field
   *
   * @return mixed
   */
  public function getValueOf($field) {
    $fields = $this->getFields();

    return $fields[$field]['value'];
  }

  /**
   * Sets the value of a custom field.
   *
   * @param string $field
   * @param mixed $value
   */
  public function setValueOf($field, $value) {
    $machineName = $this->getMachineNameOf($field);

    $this->updateFieldsFromParams([
      $machineName => $value,
    ]);
  }

  /**
   * Gets the data type of a custom field.
   *
   * @param string $field
   *
   * @return string|NULL
   */
  private function getDataTypeOf($field) {
    $fields = $this->getFields();

    return !empty($fields[$field]['data_type']) ? $fields[$field]['data_type'] : NULL;
  }

  /**
   * Gets the Option Group ID of a custom field.
   *
   * @param string $field
   *
   * @return int|NULL
   */
  private function getOptionGroupIdOf($field) {
    $fields = $this->getFields();

    return !empty($fields[$field]['option_group_id']) ? $fields[$field]['option_group_id'] : NULL;
  }

  /**
   * Gets the machine name of a custom field.
   *
   * @param string $field
   *
   * @return string
   */
  public function getMachineNameOf($field) {
    $fields = $this->getFields();

    return $fields[$field]['machine_name'];
  }

  /**
   * Gets the label of a custom field.
   *
   * @param string $field
   *
   * @return string
   */
  public function getLabelOf($field) {
    $fields = $this->getFields();

    return $fields[$field]['label'];
  }

  /**
   * Gets Option Value's label of a custom field.
   *
   * @param string $field
   *
   * @return string|NULL
   */
  public function getOptionLabelOf($field) {
    $optionGroupId = $this->getOptionGroupIdOf($field);

    try {
      $option = civicrm_api3('OptionValue', 'getsingle', array(
        'option_group_id' => $optionGroupId,
        'value' => $this->getValueOf($field),
      ));

      return $option['label'];
    } catch (CiviCRM_API3_Exception $e) {
      return NULL;
    }
  }

  /**
   * Gets a value of a custom field from request.
   * Used to retrieve field's value of Custom Field input generated by CiviCRM.
   *
   * @param string $field
   *
   * @return mixed
   */
  private function getRequestValueOf($field) {
    $fieldKey = $this->getMachineNameOf($field) . '_-1';
    $dataType = $this->getDataTypeOf($field);
    $value = CRM_Utils_Request::retrieve($fieldKey, $dataType);

    // CRM_Utils_Request::retrieve() method expects date value in YYYYMMDD format.
    // So if the field's type is Date then we need to pick its value
    // from Request array and then convert it into CiviCRM date format.
    if ($dataType === 'Date') {
      if(method_exists(CRM_Utils_Request, 'retrieveValue')) {
        $dateArray = [
          'value' => CRM_Utils_Request::retrieveValue($fieldKey, 'String', NULL, FALSE, CRM_Utils_Request::exportValues()),
        ];
      }
      else {
        $dateArray = [
          'value' => CRM_Utils_Request::getValue($fieldKey, CRM_Utils_Request::exportValues()),
        ];
      }

      CRM_Utils_Date::convertToDefaultDate($dateArray, 1, 'value');

      $value = $dateArray['value'];
    }

    return $value;
  }

  /**
   * Returns an array of a custom fields as
   * name => [
   *   data_type => 'data_type',
   *   label => 'label',
   *   machine_name => 'machine_name',
   *   option_group_id => 'option_group_id',
   *   value => 'value',
   * ]
   *
   * It caches the result within class private variable but it may be
   * updated setting $force parameter value to TRUE.
   *
   * @param bool $force
   *
   * @return array
   */
  private function getFields($force = FALSE) {
    if (!empty($this->fields) && !$force) {
      return $this->fields;
    }

    $customFieldData = $this->getCustomFieldData();

    $caseProspectCustomValues = civicrm_api3('Case', 'getsingle', [
      'id' => $this->caseId,
      'return' => $this->getCustomFieldMachineNameList(),
    ]);

    foreach ($customFieldData as $name => $value) {
      $this->fields[$name] = [
        'machine_name' => $value['key'],
        'label' => $value['label'],
        'data_type' => $value['data_type'],
        'option_group_id' => $value['option_group_id'],
        'value' => isset($caseProspectCustomValues[$value['key']]) ? $caseProspectCustomValues[$value['key']] : NULL,
      ];
    }

    return $this->fields;
  }

  /**
   * Gets an array of a custom fields as
   * name => [
   *   key => machine_name,
   *   data_type => data_type
   *   option_group_id => option_group_id
   * ]
   *
   * @return array
   */
  private function getCustomFieldData() {
    if (!empty($this->fieldsList)) {
      return $this->fieldsList;
    }

    $customFields = civicrm_api3('CustomField', 'get', [
      'custom_group_id' => $this->customGroupName,
      'return' => ['name', 'label', 'data_type', 'option_group_id'],
    ]);

    foreach ($customFields['values'] as $customField) {
      $this->fieldsList[$customField['name']] = [
        'key' => 'custom_' . $customField['id'],
        'label' => $customField['label'],
        'data_type' => $customField['data_type'],
        'option_group_id' => !empty($customField['option_group_id']) ? $customField['option_group_id'] : NULL,
      ];
    }

    return $this->fieldsList;
  }

  /**
   * Gets an array of a custom field machine names.
   *
   * @return array
   */
  private function getCustomFieldMachineNameList() {
    $fields = $this->getCustomFieldData();

    return CRM_Utils_Array::collect('key', $fields);
  }
}
