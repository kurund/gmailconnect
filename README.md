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

To use the CiviCRM connect add-on:

1. In your Gmail account, Go to **Get add-ons** and search for **CiviCRM Connect**.
   - Depending on your organization's Google Workspace settings, individuals may be able to install
     the add-on themselves, or they may need to ask their Google Workspace administrator to install it.
2. In your CiviCRM instance, make sure the role assigned to the staff user has the `access Gmail Connect endpoints` permission.
3. Each user needs to get their Gmail connect URL.
   - Go to **Contacts >> Gmail Connect**
   - Copy the Gmail connect URL and paste it into the CiviCRM Connect add-on in Gmail.

## Known Issues

- Tested on WordPress only.
