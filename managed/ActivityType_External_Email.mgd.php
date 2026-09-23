<?php
declare(strict_types = 1);

use CRM_Gmailconnect_ExtensionUtil as E;

// Add new activity type and custom group specific to
// this activity type
return [
  [
    'name' => 'OptionValue_External_Email',
    'entity' => 'OptionValue',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'activity_type',
        'name' => 'External_Email',
        'label' => E::ts('External Email'),
        'description' => E::ts('Email logged from Gmail via Gmail Connect'),
        'icon' => 'fa-envelope',
        'is_reserved' => TRUE,
      ],
      'match' => ['option_group_id', 'name'],
    ],
  ],
  [
    'name' => 'CustomGroup_External_Email_Details',
    'entity' => 'CustomGroup',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'External_Email_Details',
        'title' => E::ts('External Email Details'),
        'extends' => 'Activity',
        'extends_entity_column_value:name' => ['External_Email'],
        'is_reserved' => TRUE,
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'CustomGroup_External_Email_Details_CustomField_MessageID',
    'entity' => 'CustomField',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'External_Email_Details',
        'name' => 'MessageID',
        'label' => E::ts('Message ID'),
        'help_post' => E::ts('RFC 822 Message-ID header of the email. Used to avoid logging the same email twice.'),
        'data_type' => 'String',
        'html_type' => 'Text',
        'text_length' => 255,
        'is_searchable' => TRUE,
        'is_view' => TRUE,
      ],
      'match' => ['name', 'custom_group_id'],
    ],
  ],
];
