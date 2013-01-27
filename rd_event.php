<?php
class Rd_Event extends Rd_Cms{

	protected $_post_type_id = 'rd_event';
	protected $_label_singular = 'Event';
	protected $_label_plural = 'Events';
	protected $_register_post_type_options = array(
	);
	
	public function __construct(){

		parent::__construct();

		$this->_fields[] = array(
			'name' => 'start_date_time',
			'title' => 'Date',
			'type' => 'date_time',
			'context' => 'normal',
		);

		foreach ($this->_langs as $lang) {
			$this->_fields[] = array(
				'name' => 'text_' . $lang,
				'title' => 'Text ' . $lang,
				'type' => 'plain_text',
				'context' => 'normal',
			);
		}

		foreach ($this->_langs as $lang) {
			$this->_fields[] = array(
				'name' => 'url_' . $lang,
				'title' => 'URL ' . $lang,
				'type' => 'plain_text',
				'context' => 'normal',
			);
		}

		$this->_fields[] = array(
			'name' => 'rd_project_id',
			'title' => 'Related project',
			'type' => 'rd_project_id',
			'context' => 'side',
		);

		add_filter('manage_rd_event_posts_columns', array(&$this, 'manage_rd_event_posts_columns'));
		add_action('manage_rd_event_posts_custom_column', array(&$this, 'manage_rd_event_posts_custom_column'), 10, 2);
		add_filter('manage_edit-rd_event_sortable_columns', array(&$this, 'manage_edit_rd_event_sortable_columns'));
		add_filter('request', array(&$this, 'column_orderby'));
		add_filter('request', array(&$this, 'default_column_orderby'));

	}

	public function rd_project_id_meta_box(){

		global $post;
		
		$rd_project_id = get_post_meta( $post->ID, 'rd_project_id', true );
		
		?>
		<select name="rd_project_id">
			<?php
			$args = array(
				'post_type' => 'rd_project', 
				'orderby' => 'date',
				'order' => 'DESC',
			);
			$query = new WP_Query($args);
			?>
			<option value="">Choose</option>
			<?php while ($query->have_posts()): $query->next_post(); ?>
				<?php
				$selected = '';
				if ($query->post->ID == $rd_project_id) {
					$selected = ' selected="selected" ';
				}
				?>
				<option value="<?php print $query->post->ID; ?>" <?php print $selected ?>>
					<?php print get_the_title($query->post->ID); ?>
				</option>
			<?php endwhile; ?>
			<?php 
			wp_reset_query();
			wp_reset_postdata();
			wp_reset_vars();
			?>
		</select>
		
		<?php

	}

	function manage_rd_event_posts_columns($cols){
		$cols = array(
			'cb' => '<input type="checkbox" />',
			'start_date_time' => 'Time',
			'text_en' => 'Text',
			'rd_project_id' => 'Project',
		);
		return $cols;
	}

	function manage_rd_event_posts_custom_column($column, $post_id){
		switch($column){
			case 'rd_project_id':
				$rd_project_id = get_post_meta($post_id, $column, true);
				$val = get_post_meta($rd_project_id, 'title_en', true);
				print '<a href="' . get_edit_post_link($post_id) . '">' . $val. '</a>';
				break;
			default:
				$val = get_post_meta($post_id, $column, true);
				print '<a href="' . get_edit_post_link($post_id) . '">' . $val. '</a>';
				break;
		}
	}

	function manage_edit_rd_event_sortable_columns($columns) {
		return array_merge(
			$columns,
			array(
				'start_date_time' => 'start_date_time',
				'text_en' => 'text_en',
				'rd_project_id' => 'rd_project_id',
			)
		);
	}

	function column_orderby($vars){
		if (isset($vars['orderby']) && in_array($vars['orderby'], array('start_date_time', 'text'))) {
			$vars = array_merge($vars, array(
				'meta_key' => $vars['orderby'],
				'orderby' => 'meta_value_num',
			));
		}
		return $vars;
	}

	function default_column_orderby($vars){
		$screen = get_current_screen();
		if ($screen->base == 'edit' && $screen->post_type == 'rd_event'){
			if (!isset( $vars['orderby'])){
				$vars['meta_key'] = 'start_date_time';
				$vars['orderby'] = 'meta_value_num';
			}
		}
		return $vars;
	}

}