<?php
class Ghost {
   
    // Set correct timezone
    public static function setTimezone() {
  		$timezone = DB::getInstance()->get('settings', ['name', '=', 'timezone'])->results();
  		$timezone = $timezone[0]->value;
  		date_default_timezone_set($timezone);
    }

    // Purify post name (for post URL)
    public static function purifyPostName($name) {
        $name = preg_replace('/\s/u', '-', $name);
        $name = strtolower($name);
        return Output::getClean($name);
    }

    // Trim post content to 250 characters cleanly
    public static function purifyPostContent($content) {
        $content = Output::getPurified(Output::getDecoded($content));
        $content = strip_tags($content, '<br>');
        $content = preg_replace('/^(<br\s*\/?>)*|(<br\s*\/?>)*$/i', '', $content);
        $content = preg_replace("/<br\W*?\/>/", "➍", $content);
        $content = Util::truncate($content, '250', array('exact' => false, 'html' => true));
        $content = preg_replace("/➍/", "<br/>", $content);
        return $content;
    }

}
?>