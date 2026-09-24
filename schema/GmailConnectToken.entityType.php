<?php
use CRM_Gmailconnect_ExtensionUtil as E;

return [
  'name' => 'GmailConnectToken',
  'table' => 'civicrm_gmailconnect_token',
  'class' => 'CRM_Gmailconnect_DAO_GmailConnectToken',
  'getInfo' => fn() => [
    'title' => E::ts('Gmail Connect Token'),
    'title_plural' => E::ts('Gmail Connect Tokens'),
    'description' => E::ts('Token a contact uses to call the Gmail Connect endpoint from the Gmail add-on'),
    'log' => FALSE,
  ],
  'getFields' => fn() => [
    'id' => [
      'title' => E::ts('ID'),
      'sql_type' => 'int unsigned',
      'input_type' => 'Number',
      'required' => TRUE,
      'description' => E::ts('Unique GmailConnectToken ID'),
      'primary_key' => TRUE,
      'auto_increment' => TRUE,
    ],
    'contact_id' => [
      'title' => E::ts('Contact ID'),
      'sql_type' => 'int unsigned',
      'input_type' => 'EntityRef',
      'required' => TRUE,
      'description' => E::ts('FK to Contact'),
      'entity_reference' => [
        'entity' => 'Contact',
        'key' => 'id',
        'on_delete' => 'CASCADE',
      ],
    ],
    'token' => [
      'title' => E::ts('Token'),
      'sql_type' => 'varchar(64)',
      'input_type' => 'Text',
      'required' => TRUE,
      'description' => E::ts('Secret token identifying the contact'),
    ],
  ],
  'getIndices' => fn() => [
    'UI_contact_id' => [
      'fields' => [
        'contact_id' => TRUE,
      ],
      'unique' => TRUE,
    ],
    'UI_token' => [
      'fields' => [
        'token' => TRUE,
      ],
      'unique' => TRUE,
    ],
  ],
  'getPaths' => fn() => [],
];
