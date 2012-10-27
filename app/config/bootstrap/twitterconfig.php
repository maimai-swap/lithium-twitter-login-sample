<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ishimaru
 * Date: 12/10/24
 * Time: 17:46
 * To change this template use File | Settings | File Templates.
 */

use li3_twitteroauth\TwitterApps;

TwitterApps::config(
    array('default' => array(
	    'consumer_key'         => 'YOURS',
	    'consumer_secret'      => 'YOURS',
        'callback_url'      => 'YOURS',
)));

