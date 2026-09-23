<?php
declare(strict_types = 1);

namespace Civi\Api4;

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
