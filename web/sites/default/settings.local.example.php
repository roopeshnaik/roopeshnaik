<?php

//temporary files
$settings["file_temp_path"] = '/var/www/tmp';

$settings['rebuild_access'] = TRUE;
$settings['skip_permissions_hardening'] = TRUE;


// Set the base URL for the local Drupal site.
$options['uri'] = "https://stanford-business.lndo.site";
$base_url  = "https://stanford-business.lndo.site";
$cookie_domain = '.stanford-business.lndo.site';


//Error reporting
$config['system.logging']['error_level'] = 'verbose';

//Disable local caches
$settings['container_yamls'][] = DRUPAL_ROOT . '/sites/development.services.local.yml';
$settings['cache']['bins']['render'] = 'cache.backend.null';
$settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.null';
$settings['cache']['bins']['bootstrap'] = 'cache.backend.memory';
$settings['cache']['bins']['discovery'] = 'cache.backend.database';
$settings['cache']['bins']['config'] = 'cache.backend.database';
$settings['cache']['default'] = 'cache.backend.database';


// Theming debug
$config['system.performance']['css']['preprocess'] = FALSE;
$config['system.performance']['js']['preprocess'] = FALSE;
$settings['twig_debug'] = TRUE;


//Limit nesting of devel
#require_once DRUPAL_ROOT . '/modules/contrib/devel/kint/kint/Kint.class.php';
#Kint::$maxLevels = 6;
