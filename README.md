What this is
============

WordPress plugin. Adds CMS-like features to WP. Supports multilingual content.

How to use the plugin
=====================

* Create a subclass of Rd_Cms, save in same directory. See for example rd_page.php.
* At the very least, these properties should be set: $_post_type_id , $_label_singular,
$_label_plural;
* Overwrite __construct. Call the parent constructor, and add fields to $this->_fields. Again, see rd_page.php.
* Theming: create single-rd_page.php for a single page, and archive-rd_page.php for listings.
* To get the values of custom fields: see rd_gmd() and rd_gmdi() in functions.php.

TODO
====

* Only handles 1 image upload now. Name is hard coded.
* Handle validation.
* General code clean up. Perhaps rename base class to Rd_Cms_Base_Module.
* Test URL rewriting.