<?php
declare(strict_types = 1);

use CRM_Gmailconnect_ExtensionUtil as E;

return [
  'gmailconnect_contact_id' => [
    'name' => 'gmailconnect_contact_id',
    'type' => 'Integer',
    'html_type' => 'text',
    'default' => NULL,
    'add' => '1.0',
    'title' => E::ts('Gmail Connect contact ID'),
    'description' => E::ts('Contact linked to the Gmail Connect user.'),
    'is_domain' => 1,
    'is_contact' => 0,
  ],
];
