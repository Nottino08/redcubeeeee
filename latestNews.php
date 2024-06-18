<?php
/*
 *	Ghost module made by Coldfire
 *	https://coldfiredzn.com
 *
 */
class latestNewsWidget extends WidgetBase {

    private $_language, 
            $_ghost_language,
            $_cache,
            $_user;

    public function __construct($user, $language, $ghost_language, $smarty, $cache) {

    	$this->_user = $user;
		$this->_language = $language;
		$this->_ghost_language = $ghost_language;
    	$this->_smarty = $smarty;
    	$this->_cache = $cache;
		
        $widget_query = self::getData('Latest News');
        parent::__construct(self::parsePages($widget_query));
        
        // Get widget
        $widget_query = DB::getInstance()->query('SELECT `location`, `order` FROM nl2_widgets WHERE `name` = ?', ['Latest News'])->first();

        // Set widget variables
        $this->_module = 'Ghost';
        $this->_name = 'Latest News';
        $this->_location = isset($widget_query->location) ? $widget_query->location : null;
        $this->_description = 'Display the latest ghost module posts';
        $this->_order = isset($widget_query->order) ? $widget_query->order : null;

    }

    public function initialise(): void {

        $this->_smarty->assign('GHOST_LATEST_NEWS', $this->_ghost_language->get('ghost', 'latest_news'));

        $posts = DB::getInstance()->query("SELECT * FROM `nl2_ghost_posts` WHERE date < " . time() . " ORDER BY date DESC LIMIT 3")->results();
        $latest_news_array = [];

        foreach($posts as $post){
            $post_author = new User($post->author);

            // Purify post name (for post URL)
            $filtered_post_name = preg_replace('/\s/u', '-', $post->name);
            $filtered_post_name = strtolower($filtered_post_name);

            $latest_news_array[] = [
                'name' => Output::getClean($post->name),
	        	'date' => date("M jS\, Y", Output::getClean($post->date)),
                'image' => Output::getClean($post->image),
	        	'readtime' => str_replace("{x}", $post->readtime, $this->_ghost_language->get('ghost', 'minute_read')),
                'link' => URL::build('/post/' . $post->id . '-' . Output::getClean($filtered_post_name))
            ];
        }

        $this->_smarty->assign('GHOST_LATEST_NEWS_ARRAY', $latest_news_array);

		$this->_content = $this->_smarty->fetch('ghost/widgets/latest-news.tpl');
    }
}