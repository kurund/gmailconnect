<?php
declare(strict_types = 1);

// phpcs:disable PSR1.Files.SideEffects
require_once 'gmailconnect.civix.php';
// phpcs:enable

use CRM_Gmailconnect_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function gmailconnect_civicrm_config(\CRM_Core_Config $config): void {
  _gmailconnect_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function gmailconnect_civicrm_install(): void {
  _gmailconnect_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function gmailconnect_civicrm_enable(): void {
  _gmailconnect_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_permission().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_permission
 */
function gmailconnect_civicrm_permission(array &$permissions): void {
  $permissions['access Gmail Connect endpoints'] = [
    'label' => E::ts('Gmail Connect: access Gmail Connect endpoints'),
    'description' => E::ts('Use the Gmail Connect API endpoints'),
  ];
}
