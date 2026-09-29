# gmailconnect

Gmail integration for CiviCRM

This is an [extension for CiviCRM](https://docs.civicrm.org/sysadmin/en/latest/customize/extensions/), licensed under [AGPL-3.0](LICENSE.txt).

It provides the endpoint used by the CiviCRM Connect Gmail add-on to look up
and add contacts and record emails as activities.

## Getting Started

Installing the extension sets up:

- the permission `access Gmail Connect endpoints`
- the activity type `External Email` with a `MessageID` custom field
- the table `civicrm_gmailconnect_token`, holding one token per contact

To use the Gmail add-on:

1. Give staff the `access Gmail Connect endpoints` permission. They need a
   CMS user account linked to their contact.
2. Each person goes to **Contacts >> Gmail Connect**
   (`civicrm/gmailconnect/settings`) and copies their personal URL into the
   add-on.

## Known Issues

- Tested on WordPress only.
