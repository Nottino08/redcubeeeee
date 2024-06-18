<?php 
/*
 *	Ghost module made by Coldfire
 *	https://coldfiredzn.com
 *
 */
 
 /*  
 *  ASUKA WAS HERE <3
 * https://blackspigot.com/members/asuka.430046/
 */

// Ghost class
require_once(ROOT_PATH . '/modules/Ghost/classes/Ghost.php');
$ghost = new Ghost();

class Ghost_Module extends Module {
    private $_language;
    private $_ghost_language;
    private $_cache;
	
	public function __construct($language, $ghost_language, $pages, $user, $navigation, $cache, $endpoints){
        $this->_language = $language;
        $this->_ghost_language = $ghost_language;
        $this->_cache = $cache;
		
		$name = 'Ghost';
		$author = '<a href="https://coldfiredzn.com" target="_blank" rel="nofollow noopener">Coldfire</a>';
		$module_version = '1.1.3';
		$nameless_version = '2.0.1';
		
		parent::__construct($this, $name, $author, $module_version, $nameless_version);
		
		$pages->add('Ghost', '/post', 'pages/posts.php', 'ghost', true);
		$pages->add('Ghost', '/panel/ghost', 'pages/panel/ghost.php');
	}
	
	public function onInstall(){

		// Timezone
		Ghost::setTimezone();

		try {
			$data = DB::getInstance()->createTable("ghost_posts", " `id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(96) NOT NULL, `date` varchar(96) NOT NULL, `author` varchar(96) NOT NULL, `readtime` varchar(96) NOT NULL, `image` varchar(200) NOT NULL, `content` longtext NOT NULL, `views` varchar(96) NOT NULL, PRIMARY KEY (`id`)", "ENGINE=$engine DEFAULT CHARSET=$charset");

			// Example Post
			$example_post_author = new User();

			DB::getInstance()->insert('ghost_posts', [
				'name' => 'Example Post',
				'date' => time(),
				'author' => $example_post_author->data()->id,
				'readtime' => 1,
				'image' => 'https://i.imgur.com/tB6IbOR.jpg',
				'content' => 'This is an example news post with the ghost module! Head over to StaffCP -> Ghost to delete this and create your first news post!',
				'views' => 0
			]);

			// Permissions
			$group = DB::getInstance()->get('groups', ['id', '=', 2])->results();
            $group = $group[0];
            
            $group_permissions = json_decode($group->permissions, TRUE);
            $group_permissions['admincp.ghost'] = 1;
            
            $group_permissions = json_encode($group_permissions);
            DB::getInstance()->update('groups', 2, ['permissions' => $group_permissions]);

		} catch(Exception $e){

		}

	}

	public function onUninstall(){
		// No actions necessary
	}

	public function onEnable(){
		// No actions necessary
	}

	public function onDisable(){
		// No actions necessary
	}

	public function onPageLoad($user, $pages, $cache, $smarty, $navs, $widgets, $template){

		// Widgets
		require_once(__DIR__ . '/widgets/latestNews.php');
		$widgets->add(new latestNewsWidget($user, $this->_language, $this->_ghost_language, $smarty, $cache));

		// Permissions
		PermissionHandler::registerPermissions('Ghost', [
			'admincp.ghost' => $this->_ghost_language->get('ghost', 'ghost')
		]);

		if(defined('PANEL_PAGE') && PANEL_PAGE == 'ghost'){
			$template->assets()->include([
                AssetTree::TINYMCE
            ]);

  		    $template->addJSScript(Input::createTinyEditor($this->_ghost_language, 'InputPostContent'));
		}

		if(defined('PAGE') && PAGE == 'ghost'){
			$template->assets()->include([
                AssetTree::TINYMCE_SPOILER
            ]);
		}

		if(defined('PAGE') && PAGE == 'index'){

  			// Timezone
			Ghost::setTimezone();
  			
  			// Page Query String Checker
  			if ($_SERVER['QUERY_STRING']) {
                $page_id = preg_replace("/[^0-9]/", "", $_SERVER['QUERY_STRING']);
				if (!$page_id) {
					$page_id = 1;
				}
  			} else {
  			    $page_id = 1;
  			}

  			$offset = ($page_id * 6) - 6;
  			
			// Fetch posts
			$posts = DB::getInstance()->query("SELECT * FROM `nl2_ghost_posts` WHERE date < " . time() . " ORDER BY date DESC LIMIT 6 OFFSET " . $offset)->results();
            $posts_array = [];

			// Redirect to home if no posts
			if (empty($posts) && $page_id != 1) {
				Redirect::to(URL::build('/'));
			}
			
			// Pagination
			$paginator = new Paginator(($template_pagination ?? []));
			$paginator_posts = DB::getInstance()->query("SELECT id FROM `nl2_ghost_posts` WHERE date < " . time() . " ORDER BY date DESC")->results();
            $results = $paginator->getLimited($paginator_posts, 6, $page_id, count($paginator_posts));
            $pagination = $paginator->generate(5);
            $smarty->assign('PAGINATION', $pagination);
			
			$post_multiplier = 0;
            foreach($posts as $post){

				$post_multiplier += 1;
				if ($post_multiplier == "1" && $page_id == 1) {
					$post_size = "post-card-large";
				} else if (($post_multiplier == "2" || $post_multiplier == "3") && $page_id == 1) {
					$post_size = "post-card-medium";
				} else {
					$post_size = "post-card-small";
				}
				
				$post_author = new User($post->author);
   			   
                $posts_array[] = [
					'name' => Output::getClean($post->name),
					'date' => date("M jS\, Y", Output::getClean($post->date)),
					'author' => $post_author->getDisplayname(),
					'readtime' => str_replace("{x}", $post->readtime, $this->_ghost_language->get('ghost', 'minute_read')),
					'image' => Output::getClean($post->image),
					'content' => Ghost::purifyPostContent($post->content),
					'avatar' => $post_author->getAvatar('40'),
					'author_styles' => $post_author->getGroupClass(),
					'author_profile' => $post_author->getProfileURL(),
					'author_groups' => $post_author->getMainGroup()->group_html,
                    'link' => URL::build('/post/' . $post->id . '-' . Ghost::purifyPostName($post->name)),
					'card_size' => $post_size
				];
            }
	
            $smarty->assign([
                'GHOST' => 'yes',
	            'GHOST_POSTS' => $posts_array
            ]);
  			
		}

		if(defined('BACK_END')){
			if($user->hasPermission('admincp.ghost')){
				$cache->setCache('panel_sidebar');
				if(!$cache->isCached('ghost_new_order')){
					$order = 12;
					$cache->store('ghost_new_order', $order);
				} else {
					$order = $cache->retrieve('ghost_new_order');
				}

				if(!$cache->isCached('ghost_icon')){
					$icon = '<i class="fas fa-ghost"></i>';
					$cache->store('ghost_icon', $icon);
				} else {
					$icon = $cache->retrieve('ghost_icon');
				}
				
				$navs[2]->add('ghost_divider', mb_strtoupper($this->_ghost_language->get('ghost', 'ghost')), 'divider', 'top', null, $order, '');
				$navs[2]->add('ghost', $this->_ghost_language->get('ghost', 'ghost'), URL::build('/panel/ghost'), 'top', null, ($order + 0.1), $icon);
			}
		}
	}

	public function getDebugInfo(): array {
        return [];
    }
}