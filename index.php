<?php
/*
Plugin Name: Subpage Index
Description: Defines a shortcode that allows you to create an index on a page of its sub-pages.
Author: Loud Dog
Version: 1.0
Author URI: http://www.louddog.com/
*/

new LoudDog_Subpage_Index();
class LoudDog_Subpage_Index {
	protected $defaults = array(
		'page_id' => false,
	);
	
	function __construct() {
		include dirname(__FILE__).'/functions.php';
		add_action('init', array(&$this, 'styles_and_scripts'));
		add_action('admin_menu', array(&$this, 'admin_menu'));
		add_shortcode('subpage-index', array(&$this, 'subpage_index'));
		add_post_type_support('page', 'excerpt');
	}
	
	function styles_and_scripts() {
		$docroot = plugin_dir_url(__FILE__);
		
		wp_register_style(
			'subpage-index',
			$docroot.'/screen.css',
			array('jquery-ui'),
			'1.0'
		);

		wp_register_script(
			'subpage-index',
			$docroot.'/site.js',
			array('jquery'),
			'1.0',
			true
		);
		
		if (!is_admin()) {
			wp_enqueue_style('subpage-index');
			wp_enqueue_script('subpage-index');
		}
	}
	
	function admin_menu() {
		add_submenu_page(
			'options-general.php',
			'Subpage Index Settings',
			'Subpage Index',
			'manage_options',
			'subpage_index',
			array(&$this, 'settings_page')
		);
	}
	
	function settings_page() {
		echo "Hi.";
	}
	
	function subpage_index($options) {
		extract($this->options = shortcode_atts($this->defaults, $options));

		$page = $page_id ? get_post($page_id) : $GLOBALS['post'];
		
		if (!$page) {
			if ($page_id) return "[Unknown page ID: $page_id]";
			else return "[subpage-index must be used within a page or a page ID must be specified (page_id)]";
		}
		
		$children = get_children(array(
			'post_parent' => $page->ID,
			'post_type' => 'page',
		));
		
		ob_start();

		if (count($children)) { ?>
			
			<dl class="subpage-index">
				<?php foreach ($children as $subpage) {
					if (function_exists('subpage_index_page_output')) {
						echo subpage_index_page_output($subpage);
					} else {
						$options = csv_to_array(get_post_meta($subpage->ID, 'options', true));
						$default = get_post_meta($subpage->ID, 'default', true);
						
						?>
					
						<dt>
							<a href="<?php echo get_permalink($subpage->ID); ?>">
								<?php echo $subpage->post_title; ?>
							</a>
							
							<?php if ($options) echo "<span class='option'>[".implode(',', $options)."]</span>"; ?>
							<?php if ($default) echo "<span class='default'>default: $default</span>"; ?>
						</dt>
					
						<dd><?php echo generate_excerpt($subpage); ?></dd>
						
					<?php } ?>
				<?php } ?>
			</dl>
			
		<?php }
		
		return ob_get_clean();
	}
}