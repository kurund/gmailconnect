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
        'label' => E::ts('Gmail Connect Settings'),
        'name' => 'gmailconnect_settings',
        'url' => 'civicrm/admin/gmailconnect',
        'permission' => ['administer CiviCRM'],
        'parent_id.name' => 'System Settings',
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
];
