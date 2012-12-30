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
				'name' => 'venue_text_' . $lang,
				'title' => 'Venue text ' . $lang,
				'type' => 'plain_text',
				'context' => 'normal',
			);
		}

		foreach ($this->_langs as $lang) {
			$this->_fields[] = array(
				'name' => 'venue_url_' . $lang,
				'title' => 'Venue URL ' . $lang,
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
		add_filter('manage_edit_rd_event_sortable_columns', array(&$this, 'manage_edit_rd_event_sortable_columns'));

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
			'venue_text_en' => 'Venue',
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
		d($columns);
		return array(
			'start_date_time' => 'start_date_time',
			'venue_text' => 'venue_text',
			'rd_project_id' => 'rd_project_id',
		);
	}

}