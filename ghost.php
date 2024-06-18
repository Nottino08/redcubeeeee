<?php 
/*
 *	Ghost module made by Coldfire
 *	https://coldfiredzn.com
 *
 */
 
if(!$user->handlePanelPageLoad('admincp.ghost')) {
    require_once(ROOT_PATH . '/403.php');
    die();
}

define('PAGE', 'panel');
define('PARENT_PAGE', 'ghost');
define('PANEL_PAGE', 'ghost');
$page_title = $ghost_language->get('ghost', 'ghost');
require_once(ROOT_PATH . '/core/templates/backend_init.php');

// Readtime
function estimated_read_time($content) {
    $clean_content = strip_tags($content);
    $word_count = str_word_count($clean_content);
    $time = ceil($word_count / 250);
    return $time;
}

if(!isset($_GET['action'])){


 	// Page Query String Checker
	if(strpos($_SERVER['QUERY_STRING'], 'p=') !== false){
		$page_id = strstr($_SERVER['QUERY_STRING'], 'p=');
    	$page_id = preg_replace("/[^0-9]/", "", $_SERVER['QUERY_STRING']);
		if (!$page_id) {
			$page_id = 1;
		}
	} else {
		$page_id = 1;
	}
	
  	$offset = ($page_id * 6) - 6;

	// Get posts
	$ghost_posts = DB::getInstance()->query("SELECT * FROM `nl2_ghost_posts` ORDER BY date DESC LIMIT 6 OFFSET " . $offset)->results();
	$posts_array = [];

	// Redirect to home if no posts
	if (empty($ghost_posts) && $page_id != 1) {
		Redirect::to(URL::build('/panel/ghost'));
	}

	// Pagination
	$paginator = new Paginator(($template_pagination ?? []));
	$paginator_posts = DB::getInstance()->query("SELECT id FROM `nl2_ghost_posts` ORDER BY date DESC")->results();
	$results = $paginator->getLimited($paginator_posts, 6, $page_id, count($paginator_posts));
	$pagination = $paginator->generate(5);
	$smarty->assign('PAGINATION', $pagination);

	// Timezone
	Ghost::setTimezone();

	if(count($ghost_posts)){
		foreach($ghost_posts as $post){
		    
  			// Set views to 0 if null
  			if (!$post->views) {
		        $post->views = 0;
		        DB::getInstance()->update('ghost_posts', $post->id, ['views' => 0]);
		    }

            if ($post->date < time()) {
                $published = "yes";
            } else {
                $published = "no";
            }

			$posts_array[] = [
				'edit_link' => URL::build('/panel/ghost/', 'action=edit&id=' . Output::getClean($post->id)),
				'name' => Output::getClean($post->name),
				'date' => date("M jS\, Y \- h:i A T", Output::getClean($post->date)),
				'views' => $post->views,
				'published' => $published,
				'delete_link' => URL::build('/panel/ghost/', 'action=delete&id=' . Output::getClean($post->id)),
				'view_link' => URL::build('/post/' . $post->id . '-' . Ghost::purifyPostName($post->name)),
			];
		}
	}

	$smarty->assign([
	    'VIEWS' => $ghost_language->get('ghost', 'views'),
		'NEW_POST' => $ghost_language->get('ghost', 'new_post'),
		'GHOST_POST_NAME' => $ghost_language->get('ghost', 'ghost_post_name'),
		'POST_DATE' => $ghost_language->get('ghost', 'ghost_post_date'),
		'PUBLISHED' => $ghost_language->get('ghost', 'published'),
		'GHOST_ACTION' => $ghost_language->get('ghost', 'ghost_action'),
		'NEW_POST_LINK' => URL::build('/panel/ghost/', 'action=new'),
		'POST_LIST' => $posts_array,
		'NO_GHOST_POSTS' => $ghost_language->get('ghost', 'no_ghost_posts'),
		'ARE_YOU_SURE' => $language->get('general', 'are_you_sure'),
		'CONFIRM_DELETE_POST' => $ghost_language->get('ghost', 'delete_post'),
		'YES' => $language->get('general', 'yes'),
		'NO' => $language->get('general', 'no')
	]);
	
	$template_file = 'ghost/ghost.tpl';
} else {
	switch($_GET['action']){
		case 'new':
			if(Input::exists()){
				if(Token::check(Input::get('token'))){
					$validation = Validate::check($_POST, [
						'ghost_post_name' => [
							Validate::REQUIRED => true,
							Validate::MAX => 96
						],
						'ghost_post_date' => [
						    Validate::REQUIRED => true
						],
						'ghost_post_image' => [
						    Validate::REQUIRED => true
						],
						'ghost_post_content' => [
							Validate::REQUIRED => true
						]
					])->messages([
						'ghost_post_name' => [
							Validate::REQUIRED => $ghost_language->get('ghost', 'post_name_required'),
							Validate::MAX => $ghost_language->get('ghost', 'post_name_maximum')
						],
						'ghost_post_date' => $ghost_language->get('ghost', 'post_date_required'),
						'ghost_post_image' => $ghost_language->get('ghost', 'post_image_required'),
						'ghost_post_content' => $ghost_language->get('ghost', 'post_content_required')
					]);
							
					if($validation->passed()){
						try {
							DB::getInstance()->insert('ghost_posts', [
								'name' => htmlspecialchars(Input::get('ghost_post_name')),
								'date' => htmlspecialchars(strtotime(Input::get('ghost_post_date'))),
								'author' => $user->data()->id,
								'readtime' => htmlspecialchars(estimated_read_time(Input::get('ghost_post_content'))),
								'image' => htmlspecialchars(Input::get('ghost_post_image')),
								'content' => htmlspecialchars(Input::get('ghost_post_content')),
 								'views' => 0
							]);
							Session::flash('staff_ghost', $ghost_language->get('ghost', 'post_created_successfully'));
							Redirect::to(URL::build('/panel/ghost'));
							die();
						} catch(Exception $e){
							$errors[] = $e->getMessage();
						}
					}
 				
                    if (!empty($validation->errors())) {
					  $errors = $validation->errors();
 				    }

				} else {
					$errors[] = $language->get('general', 'invalid_token');
				}

				// Saves input if post validation fails
				$smarty->assign([
					'GHOST_POST_NAME_VALUE' => Input::get('ghost_post_name'),
					'GHOST_POST_DATE_VALUE' => Input::get('ghost_post_date'),
					'GHOST_POST_IMAGE_VALUE' => Input::get('ghost_post_image'),
					'GHOST_POST_CONTENT_VALUE' => Input::get('ghost_post_content')
				]);
			}
						
			$smarty->assign([
				'NEW_POST' => $ghost_language->get('ghost', 'new_post'),
				'BACK' => $language->get('general', 'back'),
				'BACK_LINK' => URL::build('/panel/ghost'),
				'GHOST_POST_NAME' => $ghost_language->get('ghost', 'ghost_post_name'),
				'GHOST_POST_DATE' => $ghost_language->get('ghost', 'ghost_post_date'),
				'GHOST_POST_IMAGE' => $ghost_language->get('ghost', 'ghost_post_image'),
				'GHOST_POST_CONTENT' => $ghost_language->get('ghost', 'ghost_post_content')
			]);
			
			$template_file = 'ghost/post_new.tpl';
		break;
		case 'edit':
			if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
				Redirect::to(URL::build('/panel/ghost'));
				die();
			}
			$post = DB::getInstance()->get('ghost_posts', ['id', '=', $_GET['id']])->results();
			if(!count($post)){
				Redirect::to(URL::build('/panel/ghost'));
				die();
			}
			$post = $post[0];
			
			if(Input::exists()){
				if(Token::check(Input::get('token'))){
					$validation = Validate::check($_POST, [
						'ghost_post_name' => [
							Validate::REQUIRED => true,
							Validate::MAX => 96
						],
						'ghost_post_date' => [
						    Validate::REQUIRED => true
						],
						'ghost_post_image' => [
						    Validate::REQUIRED => true
						],
						'ghost_post_content' => [
							Validate::REQUIRED => true
						]
					])->messages([
						'ghost_post_name' => [
							Validate::REQUIRED => $ghost_language->get('ghost', 'post_name_required'),
							Validate::MAX => $ghost_language->get('ghost', 'post_name_maximum')
						],
						'ghost_post_date' => $ghost_language->get('ghost', 'post_date_required'),
						'ghost_post_image' => $ghost_language->get('ghost', 'post_image_required'),
						'ghost_post_content' => $ghost_language->get('ghost', 'post_content_required')
					]);
								
					if($validation->passed()){
						try {
							DB::getInstance()->update('ghost_posts', $post->id, [
								'name' => htmlspecialchars(Input::get('ghost_post_name')),
								'date' => htmlspecialchars(strtotime(Input::get('ghost_post_date'))),
								'readtime' => htmlspecialchars(estimated_read_time(Input::get('ghost_post_content'))),
								'image' => htmlspecialchars(Input::get('ghost_post_image')),
								'content' => htmlspecialchars(Input::get('ghost_post_content')),
							]);
							Session::flash('staff_ghost', $ghost_language->get('ghost', 'post_updated_successfully'));
							Redirect::to(URL::build('/panel/ghost'));
							die();
						} catch(Exception $e){
							$errors[] = $e->getMessage();
						}
					}

					if (!empty($validation->errors())) {
					  $errors = $validation->errors();
 				    }

				} else {
					$errors[] = $language->get('general', 'invalid_token');
				}
			}
						
			$smarty->assign([
				'EDIT_POST' => $ghost_language->get('ghost', 'edit_post'),
				'BACK' => $language->get('general', 'back'),
				'BACK_LINK' => URL::build('/panel/ghost'),
				'GHOST_POST_NAME' => $ghost_language->get('ghost', 'ghost_post_name'),
				'GHOST_POST_DATE' => $ghost_language->get('ghost', 'ghost_post_date'),
				'GHOST_POST_IMAGE' => $ghost_language->get('ghost', 'ghost_post_image'),
				'GHOST_POST_CONTENT' => $ghost_language->get('ghost', 'ghost_post_content'),
				'GHOST_POST_NAME_VALUE' => Output::getClean($post->name),
				'GHOST_POST_DATE_VALUE' => date("Y-m-d\Th:i", Output::getClean($post->date)),
				'GHOST_POST_IMAGE_VALUE' => Output::getClean($post->image),
				'GHOST_POST_CONTENT_VALUE' => Output::getClean($post->content),
			]);
		
			$template_file = 'ghost/post_edit.tpl';
		break;
		case 'delete':
			if(isset($_GET['id']) && is_numeric($_GET['id'])){
				try {
					DB::getInstance()->delete('ghost_posts', ['id', '=', $_GET['id']]);
				} catch(Exception $e){
					die($e->getMessage());
				}

				Session::flash('staff_ghost', $ghost_language->get('ghost', 'post_deleted_successfully'));
				Redirect::to(URL::build('/panel/ghost'));
				die();
			}
		break;
		default:
			Redirect::to(URL::build('/panel/ghost'));
			die();
		break;
	}
}
			
Module::loadPage($user, $pages, $cache, $smarty, [$navigation, $cc_nav, $staffcp_nav], $widgets, $template);

if(Session::exists('staff_ghost'))
	$success = Session::flash('staff_ghost');

if(isset($success))
	$smarty->assign([
		'SUCCESS' => $success,
		'SUCCESS_TITLE' => $language->get('general', 'success')
	]);

if(isset($errors) && count($errors))
	$smarty->assign([
		'ERRORS' => $errors,
		'ERRORS_TITLE' => $language->get('general', 'error')
	]);

$smarty->assign([
	'PARENT_PAGE' => PARENT_PAGE,
	'PAGE' => PANEL_PAGE,
	'DASHBOARD' => $language->get('admin', 'dashboard'),
	'GHOST' => $ghost_language->get('ghost', 'ghost'),
	'TOKEN' => Token::get(),
	'SUBMIT' => $language->get('general', 'submit')
]);

$page_load = microtime(true) - $start;
define('PAGE_LOAD_TIME', str_replace('{x}', round($page_load, 3), $language->get('general', 'page_loaded_in')));

$template->onPageLoad();

require(ROOT_PATH . '/core/templates/panel_navbar.php');

$template->displayTemplate($template_file, $smarty);