<?php
// @codingStandardsIgnoreFile

/**
 * @file
 * Load services definition file.
 */

$settings['container_yamls'][] = __DIR__ . '/services.yml';

/**
 * Include the Pantheon-specific settings file.
 *
 * N.b. The settings.pantheon.php file makes some changes
 *      that affect all environments that this site
 *      exists in.  Always include this file, even in
 *      a local development environment, to ensure that
 *      the site settings remain consistent.
 */
include __DIR__ . "/settings.pantheon.php";

/**
 * Place the config directory outside of the Drupal root.
 */
$settings['config_sync_directory'] = '../config/sync';

// Automatically enable the correct config_split based on the Pantheon environment.
$_env_is_live = isset($_ENV['PANTHEON_ENVIRONMENT']) && $_ENV['PANTHEON_ENVIRONMENT'] == 'live';
$config['config_split.config_split.dev']['status'] = !$_env_is_live;
$config['config_split.config_split.live']['status'] = $_env_is_live;

/**
 * If there is a local settings file, then include it.
 */
$local_settings = __DIR__ . "/settings.local.php";
if (file_exists($local_settings)) {
  include $local_settings;
}

// Always install the 'standard' profile to stop
// the installer from modifying settings.php.
$settings['install_profile'] = 'standard';

// Configure Redis.
if (isset($_ENV['PANTHEON_ENVIRONMENT'])) {

  // Switch the SAML auth_source based on the Pantheon environment.
  switch ($_ENV['PANTHEON_ENVIRONMENT']) {
    case 'dev':
      $config['simplesamlphp_auth.settings']['auth_source'] = 'alumni-dev-stanford';
      break;
    case 'test':
      $config['simplesamlphp_auth.settings']['auth_source'] = 'alumni-test-stanford';
      break;
  }

  // Include the Redis services.yml file.
  // Adjust the path if you installed to a contrib or other subdirectory.
  $settings['container_yamls'][] = 'modules/contrib/redis/example.services.yml';

  // Phpredis is built into the Pantheon application container.
  $settings['redis.connection']['interface'] = 'PhpRedis';
  // These are dynamic variables handled by Pantheon.
  $settings['redis.connection']['host']     = $_ENV['CACHE_HOST'];
  $settings['redis.connection']['port']     = $_ENV['CACHE_PORT'];
  $settings['redis.connection']['password'] = $_ENV['CACHE_PASSWORD'];

  $settings['redis_compress_length'] = 100;
  $settings['redis_compress_level'] = 1;

  // Use Redis as the default cache.
  $settings['cache']['default'] = 'cache.backend.redis';
  $settings['cache']['bins']['render'] = 'cache.backend.redis';
  $settings['cache']['bins']['default'] = 'cache.backend.redis';
  $settings['cache']['bins']['entity'] = 'cache.backend.redis';
  $settings['cache']['bins']['menu'] = 'cache.backend.redis';
  $settings['cache']['bins']['data'] = 'cache.backend.redis';
  $settings['cache']['bins']['page'] = 'cache.backend.redis';
  $settings['cache']['bins']['advagg'] = 'cache.backend.redis';
  $settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.redis';
  $settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.redis';


  $settings['cache_prefix']['default'] = 'pantheon-redis';

  // Set Redis to not get the cache_form (no performance difference).
  $settings['cache']['bins']['form'] = 'cache.backend.database';

  // Use redis for container cache.
  // The container cache is used to load the container definition itself, and
  // thus any configuration stored in the container itself is not available
  // yet. These lines force the container cache to use Redis rather than the
  // default SQL cache.
  $settings['bootstrap_container_definition'] = [
    'parameters' => [],
    'services' => [
      'redis.factory' => [
        'class' => 'Drupal\redis\ClientFactory',
      ],
      'cache.backend.redis' => [
        'class' => 'Drupal\redis\Cache\CacheBackendFactory',
        'arguments' => ['@redis.factory', '@cache_tags_provider.container', '@serialization.phpserialize'],
      ],
      'cache.container' => [
        'class' => '\Drupal\redis\Cache\PhpRedis',
        'factory' => ['@cache.backend.redis', 'get'],
        'arguments' => ['container'],
      ],
      'cache_tags_provider.container' => [
        'class' => 'Drupal\redis\Cache\RedisCacheTagsChecksum',
        'arguments' => ['@redis.factory'],
      ],
      'serialization.phpserialize' => [
        'class' => 'Drupal\Component\Serialization\PhpSerialize',
      ],
    ],
  ];

  // Enable Pantheon config split on Pantheon environment only.
  $config['config_split.config_split.pantheon']['status'] = TRUE;

  // Disable local config split on pantheon environment.
  $config['config_split.config_split.local']['status'] = FALSE;

  // Override Content Search SOLR server to use Pantheon Connector.
  $config['search_api.server.content_search']['backend_config'] = [
    'connector' => 'pantheon',
    'connector_config' => [
      'scheme' => 'https',
      'schema' => 'modules/contrib/search_api_solr/solr-conf/4.x/schema.xml',
      'host' => $_ENV['PANTHEON_INDEX_HOST'],
      'port' => $_ENV['PANTHEON_INDEX_PORT'],
      'path' => '/sites/self/environments/' . $_ENV['PANTHEON_ENVIRONMENT'] . '/index',
      'core' => '',
    ],
  ];

  // Migration settings
  // The Drupal 7 database connection details.
  $databases['drupal7']['default'] = [
    'database' => 'pantheon',
    'username' => 'pantheon',
    'password' => 'dc6cbf54a34140878e972c59c2ba4586',
    'prefix' => '',
    'host' => 'dbserver.migrate-d7.25bbc0fe-842b-4365-bfea-4331f5a801f5.drush.in',
    'port' => '15079',
    'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
    'driver' => 'mysql',
  ];
  $databases['default']['stanford_d7'] = [
    'database' => 'pantheon',
    'username' => 'pantheon',
    'password' => 'dc6cbf54a34140878e972c59c2ba4586',
    'prefix' => '',
    'host' => 'dbserver.migrate-d7.25bbc0fe-842b-4365-bfea-4331f5a801f5.drush.in',
    'port' => '15079',
    'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
    'driver' => 'mysql',
  ];
}

$config['system.performance']['fast_404']['exclude_paths'] = '/\/(?:styles)|(?:system\/files)\//';
$config['system.performance']['fast_404']['paths'] = '/\.(?:txt|png|gif|jpe?g|css|js|ico|swf|flv|cgi|bat|pl|dll|exe|asp)$/i';
$config['system.performance']['fast_404']['html'] = '<!DOCTYPE html><html><head><title>Page not found | Stanford Graduate School of Business</title><meta content="width=device-width, initial-scale=1" name="viewport" /><link rel="stylesheet" media="all" href="/themes/custom/gsb_theme/css/styles.css?qb8l56"></head><body><body class="page-_04 path-_04 svg"><div class="dialog-off-canvas-main-canvas" data-off-canvas-main-canvas=""><header role="banner" id="header-wrapper"> <div id="header"> <div class="section clearfix"> <a href="/" title="Home" rel="home" id="logo"> <img src="/themes/custom/gsb_theme/images/logo-print.jpg" alt="Home"> </a> <div id="navigation"> <div class="section"> </div> </div> </div></div></header> <div class="region region-highlighted"><div data-drupal-messages-fallback="" class="hidden"></div> </div> <main role="main" id="content-wrapper"> <div id="hero"> <div class="section"> <div class="hero-content"> </div> </div> </div> <div id="main" class="column"> <div class="content-layout container page-404"> <div class="error-wrapper"> <h1>Sorry! The page you are looking for could not be found.</h1> <h2>Try our <a href="/search" title="Search">site search</a> or explore our site using the links below.</h2> <div class="menu-wrapper" width="33%"> <h3>Programs &amp; Research</h3> <ul class="menu"> <li><a href="/programs" title="Our Programs">The Programs</a></li> <li><a href="/exec-ed" title="Executive Education">Executive Education</a></li> <li><a href="/faculty-research" title="Faculty and Research">Faculty &amp; Research</a></li> <li><a href="/seed" title="Stanford Seed">Stanford Seed</a></li> <li><a href="/insights" title="Insights by Stanford Business">Insights by <em>Stanford Business</em></a></li> </ul> </div> <div class="menu-wrapper" width="33%"> <h3>About Stanford GSB</h3> <ul class="menu"> <li><a href="/stanford-gsb-experience/news-history" title="School News &amp; History">School News &amp; History</a></li> <li><a href="/stanford-gsb-experience" title="The Experience">The Experience</a></li> <li><a href="/visit" title="Visit Us">Visit Us</a></li> <li><a href="/contact" title="Contact Us">Contact Us</a></li> <li><a href="/jobs" title="Jobs">Jobs</a></li> </ul> </div> <div class="menu-wrapper" width="34%"> <h3>Connect with Stanford GSB</h3> <ul class="menu"> <li><a href="/events" title="Events">Events</a></li> <li><a href="/alumni" title="Alumni">Alumni</a></li> <li><a href="/alumni/giving" title="Giving">Giving</a></li> <li><a href="/organizations" title="Companies, Organizations and Recruiters">Companies, Organizations &amp; Recruiters</a></li> <li><a href="/stanford-university-community" title="Stanford University Community">Stanford University Community</a></li> <li><a href="/newsroom" title="Newsroom">Newsroom</a></li> <li><a href="/library" title="Business Library">Business Library</a></li> </ul> </div> <p><a href="http://mygsb.stanford.edu" title="MyGSB" target="_blank">Log In to MyGSB</a></p> </div> </div> </div></main> </div> <script type="application/json" data-drupal-selector="drupal-settings-json">{"path":{"baseUrl":"\/","scriptPath":null,"pathPrefix":"","currentPath":"","currentPathIsAdmin":false,"isFront":false,"currentLanguage":"en"},"pluralDelimiter":"\u0003","suppressDeprecationErrors":true,"user":{"uid":0,"permissionsHash":"d59cf9c8cb875d627e9b535da68dbd6ce8022a179de5e6a728b87ccbee954549"}}</script><script src="/core/assets/vendor/jquery/jquery.min.js?v=3.5.1"></script><script src="/core/assets/vendor/jquery-once/jquery.once.min.js?v=2.2.3"></script><script src="/core/misc/drupalSettingsLoader.js?v=8.9.0-beta3"></script><script src="/core/misc/drupal.js?v=8.9.0-beta3"></script><script src="/core/misc/drupal.init.js?v=8.9.0-beta3"></script><script src="/themes/custom/gsb_theme/js/script.js?v=8.9.0-beta3"></script></body></body></html>';

$config['library_contact_form']['tokens'] = [
  '@@formactionurl' => 'https://webto.salesforce.com/servlet/servlet.WebToCase',
  '@@orgid' => '00D500000007AMA',
  '@@recordtypevalue' => '01238000000EANE',
  '@@name' => '00N50000002L6KR',
  '@@select' => '00N50000002L6KS',
  '@@requesttype' => '00N50000002L6KP',
  '@@requesttext' => '00N50000002L6KQ',
];

// Default Content directory path.
$settings['default_content_deploy_content_directory'] = '../content';

// Include ddev.settings.php on non-patheon environments. Adding it here only
// for local use.
if ((!isset($_ENV['PANTHEON_ENVIRONMENT'])) &&
  file_exists($app_root . '/' . $site_path . '/settings.ddev.php') &&
  getenv('IS_DDEV_PROJECT') == 'true') {
  include $app_root . '/' . $site_path . '/settings.ddev.php';
}

// Default content deploy directory path.
$settings['default_content_deploy_content_directory'] = '../content';

// Media WYSIWYG migration settings.
$settings['media_migration_embed_token_transform_destination_filter_plugin'] = 'media_embed';
$settings['media_migration_embed_media_reference_method'] = 'uuid';

$conf['omit_vary_cookie'] = TRUE;
$settings['omit_vary_cookie'] = TRUE;
