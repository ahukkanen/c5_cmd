<?php
	if (!isset($argv) || $argv[0] == "base.php") {
		echo "Access denied.";
		exit;
	}
	
	## We define a special definition to indicate that the script is being run through the command line.
	define('C5_EXECUTE_CMD', true);
	
	if (!defined('DIR_BASE')) {
		define('DIR_BASE', dirname(dirname(__FILE__)));
	}
	# Config path
	if (!defined('DIR_CONFIG_SITE')) {
		define('DIR_CONFIG_SITE', DIR_BASE . '/config');
	}
	$site_startup_exists = false;
	if (!@include(DIR_CONFIG_SITE.'/startup.php')) {
		// no startup script for the site
		$site_startup_exists = true;
	}
	if (!defined('DIR_BASE_CORE')) {
		// Please note that this will end up in endless loop
		// if you have not set this yet. This is just here to
		// demonstrate what could be done in index.php
		// Of course in index.php you'd had to add '/concrete'
		// in the end.
		define('DIR_BASE_CORE', dirname(dirname(__FILE__)) . '/concrete');
	}
	$cdir = DIR_BASE_CORE;
	
	## This constant ensures that we're operating inside dispatcher.php. There is a LATER check to ensure that dispatcher.php is being called correctly. ##
	if (!defined("C5_EXECUTE")) {
		define('C5_EXECUTE', true);
	}

	if(defined("E_DEPRECATED")) {
		error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED); // E_DEPRECATED required for php 5.3.0 because of depreciated function calls in 3rd party libs (adodb).
	} else {
		error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
	}

	## Startup check ##
	require($cdir . '/config/base_pre.php');

	## Startup check ##
	require($cdir . '/startup/config_check.php');

	## Check to see if, based on a config variable, we need to point to an alternate core ##
	require($cdir . '/startup/updated_core_check.php');

	## Load the base config file ##
	require($cdir . '/config/base.php');

	## Required Loading
	require($cdir . '/startup/required.php');

	## Autoload core classes
	spl_autoload_register(array('Loader', 'autoloadCore'), true);

	## Autoload settings
	require($cdir . '/startup/autoload.php');

	if (file_exists(DIR_CONFIG_SITE . '/site_post_autoload.php')) {
		require(DIR_CONFIG_SITE . '/site_post_autoload.php');
	}

	## Exception handler
	require($cdir . '/startup/exceptions.php');

	## Set default permissions for new files and directories ##
	require($cdir . '/startup/file_permission_config.php');

	## Startup check, install ##
	require($cdir . '/startup/magic_quotes_gpc_check.php');

	## Default routes for various content items ##
	require($cdir . '/config/theme_paths.php');
	
	## Setup timezone support
	require($cdir . '/startup/timezone.php'); // must be included before any date related functions are called (php 5.3 +)

	## First we ensure that dispatcher is not being called directly
	require($cdir . '/startup/file_access_check.php');

	require($cdir . '/startup/localization.php');

	## Load the database ##
	Loader::database();

	## User level config ##
	if (!$config_check_failed) {
		require($cdir . '/config/app.php');
	}

	## Startup check ##
	require($cdir . '/startup/encoding_check.php');

	# Startup check, install ##
	require($cdir . '/startup/config_check_complete.php');

	# Must come before packages 
	require($cdir . '/startup/tools_upgrade_check.php');

	## Determines whether we can use the more efficient permission local caching
	require($cdir . '/startup/permission_cache_check.php');

	## Localization ##
	## This MUST be run before packages start - since they check ACTIVE_LOCALE which is defined here ##
	require($cdir . '/config/localization.php');

	## Security helpers
	require($cdir . '/startup/security.php');

	## Package events
	require($cdir . '/startup/packages.php');

	## Load permissions and attributes
	PermissionKey::loadAll();

	## File types ##
	## Note: these have to come after config/localization.php ##
	require($cdir . '/config/file_types.php');

	## Check host for redirection ##
	require($cdir . '/startup/url_check.php');

	## Set debug-related and logging activities
	require($cdir . '/startup/debug_logging.php');

	## Site-level config POST user/app config ##
	if (file_exists(DIR_CONFIG_SITE . '/site_post.php')) {
		require(DIR_CONFIG_SITE . '/site_post.php');
	}

	## Site-level config POST user/app config - managed by c5, do NOT add your own stuff here ##
	if (file_exists(DIR_CONFIG_SITE . '/site_post_restricted.php')) {
		require(DIR_CONFIG_SITE . '/site_post_restricted.php');
	}

	## Specific site routes for various content items (if they exist) ##
	if (file_exists(DIR_CONFIG_SITE . '/site_theme_paths.php')) {
		@include(DIR_CONFIG_SITE . '/site_theme_paths.php');
	}

	## Specific site routes for various content items (if they exist) ##
	if (file_exists(DIR_CONFIG_SITE . '/site_file_types.php')) {
		@include(DIR_CONFIG_SITE . '/site_file_types.php');
	}
	
	# site events - we have to include before tools
	if (defined('ENABLE_APPLICATION_EVENTS') && ENABLE_APPLICATION_EVENTS == true &&  file_exists(DIR_CONFIG_SITE . '/site_events.php')) {
		@include(DIR_CONFIG_SITE . '/site_events.php');
	}
	
	// Login the admin user
	$u = User::getByUserID(USER_SUPER_ID, true);
