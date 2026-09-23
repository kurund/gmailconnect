# gmailconnect

Gmail integration for CiviCRM

This is an [extension for CiviCRM](https://docs.civicrm.org/sysadmin/en/latest/customize/extensions/), licensed under [AGPL-3.0](LICENSE.txt).

## Getting Started

Installing the extension sets up:

- the permission `access Gmail Connect endpoints`
- a WordPress role `Gmail Connect` with only the capabilities `read`,
  `access_gmail_connect_endpoints` and `authenticate_with_api_key`
- a WordPress user `gmailconnect+<hash>@example.com` with that role, its
  CiviCRM contact, and an API key for the contact
- the activity type `External Email` with a `MessageID` custom field

The API key is displayed after install and can be accessed at
**Administer >> System Settings >> Gmail Connect Settings**
(`civicrm/admin/gmailconnect`).

## Known Issues

- Works currently only in WordPress install
