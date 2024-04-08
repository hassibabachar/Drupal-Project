Page Notifications README.txt
=================


CONTENTS OF THIS FILE
---------------------

* Introduction
* Requirements
* Installation
* Configuration
* Related projects & alternatives
* Maintainers


INTRODUCTION
------------

Page Notifications is a simple, lightweight module for sending e-mail
notifications to subscribers about changes on node on a Drupal web
site.

* For a full description of the module, visit the project page:
  https://drupal.org/project/page_notifications

* For more documentation about its use, visit the documentation page:
  https://www.drupal.org/documentation/modules/page_notifications

* To submit bug reports and feature suggestions, or to track changes:
  https://drupal.org/project/issues/page_notifications


REQUIREMENTS
------------

This module requires a supported version of Drupal 8 or 9 to be running.
It is highly recommended to use recaptcha module https://www.drupal.org/project/recaptcha
For correct display of emails allow your mail settings to send HTML format email.


INSTALLATION
------------

1. Extract the Page Notifications module directory, including all its
   subdirectories, into directory where you keep contributed modules
   (e.g. /modules/).

2. Enable the Page Notifications module on the Modules list page. The database tables and default data
    will be created automatically for you at this point.

3. Create three fields on content type that will have notification functionality:
    3.1 Boolean - field that will enable function to send email to subscribers
    3.2 Text (plain, long) - field that will contain notes or short message about changes
    3.3 Timestamp - field will record when last notification emails were sent

4. Go to /admin/page-notifications/tabs » tab "Messages configuration" and enter those three machine name fields into corresponded
    configuration fields » Save configuration.

4. Place Page Notifications block into the region you would like block to be displayed » Configure » Save.


CONFIGURATION
-------------

All configuration for the module is located under /admin/page-notifications/tabs: Admin menu » Extend »
 Page Notifications » Configure.

Fill Out configuration fields under "Messages configuration" tab:
Page Notifications header text - will be displayed above block form as header of the block;
The "from" email - enter email if you don't want to use main site emails;
Checkbox field - the boolean type filed machine name of the field that created on one of the content types
to enable functionality;
Notes field - the Text (plain, long) type filed machine name of the field that created on one of the content types
that contain notes or short message about changes;
Timestamp - the timestamp type filed machine name of the field that created on one of the content types
that indicates when last notification emails were sent;

Note: all configuration fields for emails and web messages must be in full HTML format.
It will automagically save in full HTML format if chosen different format before saving configuration.

RELATED PROJECTS & ALTERNATIVES
-------------------------------

Notify: https://www.drupal.org/project/notify


MAINTAINERS
-----------

Lidiya Grushetska <grushetskl@chop.edu> is the original author https://www.drupal.org/u/lidia_ua.
