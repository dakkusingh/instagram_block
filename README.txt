
CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Requirements
 * Installation
 * Initial Configuration
 * Demonstration
 * Acknowledgments


INTRODUCTION
------------

This is a very simple module that integrates with Instagram and creates a block
containing your most recent Instagram posts.

The block's configuration page lets you choose how many posts and what size
they should appear in the block. The images are individually exposed to the
drupal theme layer, so developers have access to an all of the variables
provided by the Instagram API should they choose to extent the block. For more
informations see the Instagram developer pages:

http://instagram.com/developer/endpoints/users/#get_users_media_recent


REQUIREMENTS
------------

This module depends on php curl commands to parse the information from Instagram
and thus has a dependency on php5-curl.

It also has a dependency on the drupal core block module.


INSTALLATION
------------

This module is installed like any drupal module hand has no specific
installation instructions.


INITIAL CONFIGURATION
----------------------

You can configure the settings for your Instagram block by going to the
configuration page (admin/config/services/instagram_block). You will need to
authorise the application with your Instagram account.


ACKNOWLEDGMENTS
---------------

The heavy lifting for this module was done by Nick from Blueprint Interactive,
so kudos to him.

MAINTAINERS
-----------

Current maintainers:
* Yan Loetzer (yanniboi) - https://www.drupal.org/u/yanniboi
* Naveen Valecha (naveenvalecha) - https://www.drupal.org/u/naveenvalecha