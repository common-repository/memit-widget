<?php

/*
Plugin Name: memit.com Widget
Plugin URI:
Description: Integration of the memit.com streams into your blog
Version: 0.1.1
Author: Oleg Zhavoronkin
Author URI: http://memit.com/oleg
*/

add_option("skylark_memitwidget_embededcode", '', "Memit Widget Embed HTML", 'no'); // default value
add_option("skylark_memitwidget_page", '', "Page name where to embed widget code", 'no'); // default value

function skylark_memit_widget_options_page()
{
	if ($_POST) {
		if ($_POST['skylark_memitwidget_embededcode']) {
			update_option('skylark_memitwidget_embededcode', htmlentities(stripslashes($_REQUEST['skylark_memitwidget_embededcode'])));
		}
		if ($_POST['skylark_memitwidget_page']) {
			update_option('skylark_memitwidget_page', $_POST['skylark_memitwidget_page']);
		}
	}


	$skylark_memitwidget_embededcode = html_entity_decode(get_option('skylark_memitwidget_embededcode'));
	$skylark_memitwidget_page = get_option('skylark_memitwidget_page');

	$pages = get_pages();
	$optionsTmpl = '';
	foreach ($pages as $page) {
		$option = '<option value="' . $page->ID . '"' . ($skylark_memitwidget_page == $page->ID ? 'selected="selected"' : '') . '>';
		$option .= $page->post_title;
		$option .= '</option>';
		$optionsTmpl .= $option;
	}

	$settings = '
			<div class="wrap">
			<h2>memit.com Widget Integration Setup</h2>
			<form method="post" action="http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '">

			<div id="appid">
				<p>Memit Widget is a powerful tool for sharing your knowledge with others. You can share not only your own mems stream, but also streams of the collections you manage.</p>
				<p>In the example below I`ll show how would I embed my collection of the Arduino related materials. Please follow the same steps to integrate your own mem streams to your blog.</p>
				<p>As a step 1 - please locate widget settings:</p>
				<img src="/wp-content/plugins/memit-widget/img/screenshot-1.png" /><br />
				<p>As you can see from the image below, you are able to get the code fou your profile mems stream and for any collection you manage. Also, to secure your content, you can set domain name of your blog, where the widget will be embeded</p>
				<img src="/wp-content/plugins/memit-widget/img/screenshot-2.png" /><br />
				<p>Next step would be clicking on the "Get widget code" button, to generate your embed HTML</p>
				<img src="/wp-content/plugins/memit-widget/img/screenshot-3.png" /><br />
				<p>Now you can copy that HTML code and paste it in the field below, to enable it on your blog:</p>
				<input type="text" id="skylark_memitwidget_embededcode" name="skylark_memitwidget_embededcode" value="' . esc_html($skylark_memitwidget_embededcode) . '"><br/>
				<p>Select the page where the widget will be embeded:</p>
				<select name="skylark_memitwidget_page">' . $optionsTmpl . '</select><br />
				<p>Now, save this options and your widget should be available on the page you have selected.</p>
			</div>

			<p class="submit" style="width:420px;"><input type="submit" value="Submit &raquo;" /></p>
			</form>
			</div>
		';

	print $settings;
}

function skylark_memit_widget_admin_page()
{
	add_submenu_page('options-general.php', 'memit.com Widget', 'memit.com Widget', 9, 'skylark-memit-widget.php', 'skylark_memit_widget_options_page');
}


function skylark_memit_widget_content_filter($content)
{

	global $post;
	$pageId = get_option('skylark_memitwidget_page');
	if ($post->ID == $pageId) {
		$skylark_memitwidget_embededcode = html_entity_decode(get_option('skylark_memitwidget_embededcode'));

		$content = $skylark_memitwidget_embededcode;
	}
	return $content;
}

add_filter('the_content', 'skylark_memit_widget_content_filter', 20);
add_action('admin_menu', 'skylark_memit_widget_admin_page');