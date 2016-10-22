<?php 
/*
Plugin Name: Wordpress plugin to include twitterfeed
Plugin URI:  https://github.com/error500/mwf_twitterfeed
Description: Plugin that includes a twitter feed as shortcode
Version:     0.1
Author:      Error500
Author URI:  https://github.com/error500
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

*/

require __DIR__ . '/vendor/autoload.php';


require __DIR__ . '/class/MwfTwitterFeed.php';

add_shortcode( 'mwf_twitter',array( 'MwfTwitterFeed',  'mwf_shortcode_twitter' ));