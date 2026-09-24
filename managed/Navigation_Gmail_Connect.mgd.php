<?php
declare(strict_types = 1);

use CRM_Gmailconnect_ExtensionUtil as E;

// add navigation menu item
return [
  [
    'name' => 'Navigation_Gmail_Connect_Settings',
    'entity' => 'Navigation',
    'cleanup' => 'always',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Gmail Connect'),
        'name' => 'gmailconnect_settings',
        'url' => 'civicrm/gmailconnect/settings?reset=1',
        'permission' => ['access Gmail Connect endpoints'],
        'parent_id.name' => 'Contacts',
        'is_active' => TRUE,
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
];
