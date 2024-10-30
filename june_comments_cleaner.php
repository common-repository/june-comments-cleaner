<?php
/*
	Plugin Name: WTC Comment Cleaner
	Plugin URI: http://coderevision.com/blog/
	Description: This plugin will strip out all html tags(except the allowed ones), css attributes and javascript code from comments. Many thanks to Nasty Web for his help.
	Version: 1.4.2
	Author: <a href="http://irismvc.net/" target="_blank">kos</a> and <a href="http://3n9.org/" target="_blank">Nasty Web</a>
    License: GPLv2 or later
*/
?>
<?php
	/**
	* Strip attributes
	* @access private
	*/
	function june_internal_clean( $matched )
	{
		$attribs = "javascript:|onclick|ondblclick|onmousedown|onmouseup|onmouseover|onmousemove|";
		$attribs .= "onmouseout|onkeypress|onkeydown|onkeyup|onload|onunload|class|id|src|style|width|height";

		$quot = "\"|\'|\`";
		$stripAttrib = "#\s+($attribs)\s*=\s*($quot)(.*?)(\\2)#is";
		$clean = stripslashes($matched[0]);
		$clean = preg_replace($stripAttrib, '', $clean);

		return $clean;
	}
	/**
	* Escape the code added in the <code> tags so it will not be stripped out if this is a tech blog
	* and visitors/owner may want to add code samples in their comments.
	* @access private
	*/
	function june_internal_code_clean( $str ) {
		return "<code>".htmlspecialchars( $str[1] )."</code>"; // do whatever you want with code string
	}
	/**
	* The following function will not only strip out any unwanted tags from your HTML code, but will also
	* clean up whatever attributes are inside those tags, preventing people from messing up your page.
	* @access public
	*/
	function june_clean_text( $source )
	{
		$allowedTags = get_option('june_comments_allowed_tags');
		$clean = ($allowedTags) ? strip_tags($source, $allowedTags) : strip_tags($source);
		$clean = preg_replace_callback('#<(.*?)>#s', "june_internal_clean", $clean);
		$clean = preg_replace_callback('#<\s*code\s*>(.*?)<\s*/code\s*>#is', "june_internal_code_clean", $clean);
		return $clean;
	}

/* Save the option into the db when the plugin is activated */
function june_comment_cleaner_activation_check()
{
	if (get_option('june_comments_tags'))
	{
		$tags = get_option('june_comments_tags');
		delete_option('june_comments_tags');
		add_option('june_comments_allowed_tags', $tags);
	}
	else {
		add_option('june_comments_allowed_tags', '<strong><em><u><p><code>');
	}
}
register_activation_hook(__FILE__, 'june_comment_cleaner_activation_check');

add_filter('comment_text', 'june_clean_text', 10, 1);

add_action('admin_menu', 'june_comment_cleaner_plugin_menu');

function june_comment_cleaner_plugin_menu() {
  add_options_page('WTC Comment Cleaner', 'WTC Comment Cleaner', 'manage_options', __FILE__, 'june_comment_cleaner_plugin_options');
}

function june_comment_cleaner_plugin_options() {
  	include 'june_plugin_options.php';
}

/**
* Clean up
* Delete the option from the db when the plugin is deactivated;
* Remove the filter for comments.
*/
function june_comment_cleaner_deactivation_check()
{
	if (get_option('june_comments_tags'))
	{
		delete_option('june_comments_tags');
	}
	if (get_option('june_comments_allowed_tags'))
	{
		delete_option('june_comments_allowed_tags');
	}
	remove_filter('comment_text', 'june_clean_text', 10, 1);
}
register_deactivation_hook(__FILE__, 'june_comment_cleaner_deactivation_check');