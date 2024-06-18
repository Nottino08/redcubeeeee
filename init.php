<?php 
/*
 *	Ghost module made by Coldfire
 *	https://coldfiredzn.com
 *
 */

$ghost_language = new Language(ROOT_PATH . '/modules/Ghost/language', LANGUAGE);

require_once(ROOT_PATH . '/modules/Ghost/module.php');
$module = new Ghost_Module($language, $ghost_language, $pages, $user, $navigation, $cache, $endpoints);