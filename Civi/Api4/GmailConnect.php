<?php
declare(strict_types = 1);

namespace Civi\Api4;

use Civi\Api4\Action\GmailConnect\AddContact;
use Civi\Api4\Action\GmailConnect\GetActivity;
use Civi\Api4\Action\GmailConnect\GetContact;
use Civi\Api4\Action\GmailConnect\RecordActivity;

/**
 * GmailConnect
 *
 * Endpoints used by the CiviCRM Connect Gmail add-on
 * Provided by the Gmail Connect extension
 *
 * @searchable none
 * @package Civi\Api4
 */
class GmailConnect extends Generic\AbstractEntity {

  /**
   * @param bool $checkPermissions
   * @return \Civi\Api4\Action\GmailConnect\RecordActivity
   */
  public static function recordActivity($checkPermissions = TRUE) {
    return (new RecordActivity(__CLASS__, __FUNCTION__))
      ->setCheckPermissions($checkPermissions);
  }

  /**
   * @param bool $checkPermissions
   * @return \Civi\Api4\Action\GmailConnect\GetActivity
   */
  public static function getActivity($checkPermissions = TRUE) {
    return (new GetActivity(__CLASS__, __FUNCTION__))
      ->setCheckPermissions($checkPermissions);
  }

  /**
   * @param bool $checkPermissions
   * @return \Civi\Api4\Action\GmailConnect\AddContact
   */
  public static function addContact($checkPermissions = TRUE) {
    return (new AddContact(__CLASS__, __FUNCTION__))
      ->setCheckPermissions($checkPermissions);
  }

  /**
   * @param bool $checkPermissions
   * @return \Civi\Api4\Action\GmailConnect\GetContact
   */
  public static function getContact($checkPermissions = TRUE) {
    return (new GetContact(__CLASS__, __FUNCTION__))
      ->setCheckPermissions($checkPermissions);
  }

  /**
   * @param bool $checkPermissions
   * @return \Civi\Api4\Generic\BasicGetFieldsAction
   */
  public static function getFields($checkPermissions = TRUE) {
    return (new Generic\BasicGetFieldsAction(__CLASS__, __FUNCTION__, function() {
      return [];
    }))->setCheckPermissions($checkPermissions);
  }

  public static function permissions(): array {
    return [
      'meta' => ['access Gmail Connect endpoints'],
      'default' => ['access Gmail Connect endpoints'],
    ];
  }

}
