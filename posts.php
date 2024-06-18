<?php
/*
 *	Ghost module made by Coldfire
 *	https://coldfiredzn.com
 *
 */
 
define('PAGE', 'ghost');

// Ghost class
require_once(ROOT_PATH . '/modules/Ghost/classes/Ghost.php');
$ghost = new Ghost();

// Page query string checker
$filtered_url = end(explode('/post/', $_SERVER['REQUEST_URI']));
if (str_contains($filtered_url, '-')) {
    $filtered_url = substr($filtered_url, 0, strpos($filtered_url, '-'));
}

// Get post
$post = DB::getInstance()->get("ghost_posts", ["id", "=", $filtered_url])->results();
$post = $post[0];

$page_title = Output::getClean($post->name);
require_once(ROOT_PATH . '/core/templates/frontend_init.php');

// Timezone
Ghost::setTimezone();

// Purify post content
$post_content_filtered = strip_tags(Output::getDecoded($post->content));
$post_content_filtered = Util::truncate($post_content_filtered, '250', ['exact' => true, 'html' => true]);

$post_author = new User($post->author);
if ($post->date < time()) {
    $smarty->assign([
        'post_name' => Output::getClean($post->name),
        'post_date' => date("M jS\, Y", Output::getClean($post->date)),
        'post_author' => $post_author->getDisplayname(),
        'post_readtime' => str_replace("{x}", $post->readtime, $ghost_language->get('ghost', 'minute_read')),
        'post_image' => Output::getClean($post->image),
        'post_content' => Output::getDecoded($post->content),
        'post_content_filtered' => $post_content_filtered,
        'post_avatar' => $post_author->getAvatar('40'),
        'post_author_styles' => $post_author->getGroupClass(),
        'post_author_profile' => $post_author->getProfileURL(),
        'post_author_groups' => $post_author->getMainGroup()->group_html
    ]);
} else {
    require_once(ROOT_PATH . '/404.php');
    die();
}

// View Counter
if ($user->isLoggedIn() || (defined('COOKIE_CHECK') && COOKIES_ALLOWED)) {
    if (!Cookie::exists('post-' . $post->id)) {
        DB::getInstance()->increment('ghost_posts', $post->id, 'views');
        Cookie::put('post-' . $post->id, 'true', 3600);
    }
} else {
    if (!Session::exists('post-' . $post->id)) {
        DB::getInstance()->increment('ghost_posts', $post->id, 'views');
        Session::put('post-' . $post->id, 'true');
    }
}

// Extra posts
$posts = DB::getInstance()->query("SELECT * FROM `nl2_ghost_posts` WHERE id NOT IN ('" . $post->id . "') AND date < " . time() . " ORDER BY date DESC LIMIT 3")->results();
$posts_array = [];

foreach($posts as $post){
    $post_author = new User($post->author);

    $posts_array[] = [
        'name' => Output::getClean($post->name),
		'date' => date("M jS\, Y", Output::getClean($post->date)),
		'author' => $post_author->getDisplayname(),
		'readtime' => str_replace("{x}", $post->readtime, $ghost_language->get('ghost', 'minute_read')),
		'image' => Output::getClean($post->image),
		'content' => Ghost::purifyPostContent($post->content),
		'avatar' => $post_author->getAvatar('40'),
		'author_styles' => $post_author->getGroupClass(),
	    'author_profile' => $post_author->getProfileURL(),
		'author_groups' => $post_author->getMainGroup()->group_html,
        'link' => URL::build('/post/' . $post->id . '-' . Ghost::purifyPostName($post->name)),
		'card_size' => 'post-card-small'
    ];
}

$smarty->assign([
    'GHOST_POSTS' => $posts_array,
    'MORE_POSTS' => $ghost_language->get('ghost', 'more_posts')
]);

Module::loadPage($user, $pages, $cache, $smarty, [$navigation, $cc_nav, $staffcp_nav], $widgets, $template);

$template->onPageLoad();

require(ROOT_PATH . '/core/templates/navbar.php');
require(ROOT_PATH . '/core/templates/footer.php');
	
$template->displayTemplate('ghost/post.tpl', $smarty);

/*  
 *  R4M_
 *  22772
 *  88077
 *  1678395978
 *  e23439d1871d08c05b7921e8e1269f1c
 *  57902da51912cfc645989d4b29401db6
 */