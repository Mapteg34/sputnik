<?php
/**
 * Created by PhpStorm.
 * User: mapt
 * Date: 9/9/17
 * Time: 4:13 PM
 */

class Sputnik {

	const POST_TYPE = "publishing_house";
	const SLUG = "publishing_houses";
	const PRODUCT_POST_TYPE = "product";
	const HOUSE_CITY_FIELD = "city";
	const PRODUCT_HOUSE_FIELD = "house";
	const HOUSE_PRODUCTS_FIELD = "products";
	const CAPABILITY = "publishing_house";
	const TAXONOMY = "category_publishing_houses";

	public function __construct($file_path) {
		register_activation_hook($file_path, array(&$this, "hook_activation"));
		register_deactivation_hook($file_path, array(&$this, "hook_deactivation"));

		add_action("init", array(&$this, "action_init"));
		add_action("save_post", array(&$this, "action_save_post"), 1, 2);
		add_action("add_meta_boxes", array(&$this, "action_add_meta_boxes"));
		add_action("manage_posts_columns", array(&$this, "action_manage_posts_columns"), 10, 2);
		add_action("manage_posts_custom_column", array(&$this, "action_manage_posts_custom_column"), 10, 2);

		add_filter("template_include", array(&$this,"filter_template_include"), 1);
	}
	public function filter_template_include($template_path) {
		if (get_post_type()!=self::POST_TYPE) return $template_path;

		if (is_single()) {
			if ( $theme_file = locate_template( array( "single-" . self::POST_TYPE . ".php" ) ) ) {
				$template_path = $theme_file;
			} else {
				$template_path = plugin_dir_path( __FILE__ ) . "/templates/single.php";
			}
		} else if (is_archive()) {
			if ( $theme_file = locate_template( array( "archive-" . self::POST_TYPE . ".php" ) ) ) {
				$template_path = $theme_file;
			} else {
				$template_path = plugin_dir_path( __FILE__ ) . "/templates/archive.php";
			}
		}
		return $template_path;
	}

	private function checkRights() {
		return current_user_can(self::CAPABILITY);
	}

	public function hook_activation() {
		$role = get_role("administrator");
		$role->add_cap(self::CAPABILITY);
	}
	public function hook_deactivation() {
		$role = get_role("administrator");
		$role->remove_cap(self::CAPABILITY);
	}
	public function action_init() {
		register_post_type(self::POST_TYPE,array(
			"label"=>"Publishing houses",
			"labels"=>array(
				"singular_name"=>"Publishing house"
			),
			"public"=>$this->checkRights(),
			"rewrite"=>array("slug"=>self::SLUG),
			"publicly_queryable" => true,
			"query_var" => true,
			"hierarchical"=>false,
			"has_archive"=>true,
			//"taxonomies" => array("category","post_tag")
		));
	}
	public function action_add_meta_boxes() {
		add_meta_box(
			self::POST_TYPE."_meta",
			"Details",
			array(&$this,"add_meta_box"),
			self::POST_TYPE,
			"normal"
		);
		add_meta_box(
			self::PRODUCT_POST_TYPE."_meta",
			"Publishind house",
			array(&$this,"product_add_meta_box"),
			self::PRODUCT_POST_TYPE,
			"side"
		);
	}
	public function add_meta_box() {
		include("views/meta_box.php");
	}
	public function product_add_meta_box() {
		include("views/product_meta_box.php");
	}
	public function action_save_post($post_id, $post) {
		if (empty($post_id) || empty($post) || empty($_POST)) return;
		if (defined("DOING_AUTOSAVE") && DOING_AUTOSAVE) return;
		if (is_int(wp_is_post_revision($post))) return;
		if (is_int(wp_is_post_autosave($post))) return;
		if (!current_user_can("edit_post", $post_id)) return;

		if ($post->post_type == self::POST_TYPE) return $this->save_house_post($post_id);
		if ($post->post_type == self::PRODUCT_POST_TYPE) return $this->save_product_post($post_id);
	}
	public function action_manage_posts_columns($posts_columns, $post_type) {
		if ($post_type==self::POST_TYPE) {
			$posts_columns[self::HOUSE_CITY_FIELD] = "City";
			$posts_columns[self::HOUSE_PRODUCTS_FIELD] = "Products";
		} elseif ($post_type==self::PRODUCT_POST_TYPE) {
			$posts_columns[self::PRODUCT_HOUSE_FIELD] = "Publish house";
		}
		return $posts_columns;
	}
	public function action_manage_posts_custom_column($column_name,$post_id) {
		$post = get_post($post_id);
		if ($post->post_type==self::POST_TYPE && $column_name==self::HOUSE_CITY_FIELD) {
			echo get_post_meta($post_id,self::HOUSE_CITY_FIELD,true);
		} elseif ($post->post_type==self::POST_TYPE && $column_name==self::HOUSE_PRODUCTS_FIELD) {
			$q = new WP_Query(array(
				"post_type"=>self::PRODUCT_POST_TYPE,
				"meta_key"=>self::PRODUCT_HOUSE_FIELD,
				"meta_value"=>$post_id
			));
			echo $q->found_posts;
		} elseif ($post->post_type==self::PRODUCT_POST_TYPE && $column_name==self::PRODUCT_HOUSE_FIELD) {
			$house_id = get_post_meta($post_id,self::PRODUCT_HOUSE_FIELD,true);
			if ($house_id) {
				$house = get_post($house_id);
				if ($house) {
					echo htmlspecialchars($house->post_title);
				}
			}
		}
	}

	private function save_house_post($post_id) {
		update_post_meta($post_id,self::HOUSE_CITY_FIELD,@$_POST[self::HOUSE_CITY_FIELD]);
		update_post_meta($post_id,"_image_id", $_POST["upload_image_id"]);
	}
	private function save_product_post($post_id) {
		update_post_meta($post_id,self::PRODUCT_HOUSE_FIELD,@$_POST[self::PRODUCT_HOUSE_FIELD]);
	}
}