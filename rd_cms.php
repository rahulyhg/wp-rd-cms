<?php
/*
Plugin Name: Rekall Design CMS
Plugin URI: http://www.rekalldesign.com
Description: Rekall WordPress CMS extensions
Author: Bert Balcaen. Image upload code based on http://www.foxrunsoftware.net/articles/wordpress/custom-post-type-with-image-uploads/. WYSWIG code based on http://chromeorange.co.uk/change-excerpt-to-product-summary-and-add-the-editor/.
Author URI: http://www.rekalldesign.com
Version: 1.0
Copyright: © 2012 Bert Balcaen (email : bert@rekalldesign.com)
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

/*
TODO:
- Only handles 1 image upload now. Name is hard coded.
- Handle validation.
- General code clean up. Perhaps rename base class to Rd_Cms_Base_Module.
- Check if custom URL's work.
*/

/*
HOW TO USE THIS:

- Create a subclass of Rd_Cms, save in same directory. See for example rd_page.php.
- At the very least, these properties should be set: $_post_type_id , $_label_singular,
$_label_plural;
- Overwrite __construct. Call the parent constructor, and add fields to $this->_fields. Again, see rd_page.php.
- Theming: create single-rd_page.php for a single page, and archive-rd_page.php for listings.
- To get the values of custom fields: see rd_gmd() and rd_gmdi() in functions.php.
*/

require_once('functions.php');
require_once('multi_lang.php');

$rd_modules = array(
	array(
		'name' => 'rd_project',
		'class_name' => 'Rd_Project',
	),
	array(
		'name' => 'rd_event',
		'class_name' => 'Rd_Event',
	),
	array(
		'name' => 'rd_news',
		'class_name' => 'Rd_News',
	),
	array(
		'name' => 'rd_page',
		'class_name' => 'Rd_Page',
	),
);
foreach ($rd_modules as $props) {
	require_once($props['name'] . '.php');
	$GLOBALS[$props['name']] = new $props['class_name']();
}

class Rd_Cms {

	protected $_post_type_id;
	protected $_label_singular;
	protected $_label_plural;
	protected $_langs = array(
		'en',
		'nl',
	);
	protected $_fields = array();
	protected $_taxonomies = array();
	protected $_register_post_type_options = array();

	protected $_dateTimeLibsIncluded = false;

	public function __construct() {
		
		$this->_label_plural = __($this->_label_plural);

		add_action( 'init', array( &$this, 'init' ) );
		
		if ( is_admin() ) {
			add_action( 'admin_init', array( &$this, 'admin_init' ) );
		}
	}
	
	
	/** Frontend methods ******************************************************/
	
	
	/**
	 * Register the custom post type
	 */
	public function init() {

		$defaults = array( 
			'public' => true, 
			'label' => $this->_label_plural,
			'labels' => array(
				'add_new_item' => 'Add New ' . $this->_label_singular,
				'edit_item' => 'Edit ' . $this->_label_singular,
				'new_item' => 'New ' . $this->_label_singular,
				'view_item' => 'View ' . $this->_label_singular,
				'search_items' => 'Search ' . $this->_label_plural,
				'not_found' => 'No ' . $this->_label_plural . ' found.',
				'not_found_in_trash' => 'No ' . $this->_label_plural . 'found in trash.',
			),
			'menu_position' => 10,
			'has_archive' => true,
			'taxonomies' => $this->_taxonomies,
		);
		$register_post_type_options = array_merge(
			$defaults,
			$this->_register_post_type_options
		);

		register_post_type( 
			$this->_post_type_id,
			$register_post_type_options 
		);

	}
	
	/** Admin methods ******************************************************/
	
	
	/**
	 * Initialize the admin, adding actions to properly display and handle 
	 * the custom post type add/edit page
	 */
	public function admin_init() {
		global $pagenow;
		
		if ( $pagenow == 'post-new.php' || $pagenow == 'post.php' || $pagenow == 'edit.php' ) {
			
			add_action( 'add_meta_boxes', array( &$this, 'meta_boxes' ) );
			add_filter( 'enter_title_here', array( &$this, 'enter_title_here' ), 1, 2 );

			add_action( 'save_post', array( &$this, 'meta_boxes_save' ), 1, 2 );
			add_action( 'save_post', array( &$this, 'set_post_title_and_content' ));

			add_action( 'admin_print_scripts', array( &$this, 'init_styles' ) );

		}
	}
	
	/**
	 * Save meta boxes
	 * 
	 * Runs when a post is saved and does an action which the write panel save scripts can hook into.
	 */
	public function meta_boxes_save( $post_id, $post ) {
		if ( empty( $post_id ) || empty( $post ) || empty( $_POST ) ) return;
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if ( is_int( wp_is_post_revision( $post ) ) ) return;
		if ( is_int( wp_is_post_autosave( $post ) ) ) return;
		if ( ! current_user_can( 'edit_post', $post_id ) ) return;
		if ( $post->post_type != $this->_post_type_id ) return;
			
		$this->process_post_type_meta( $post_id, $post );
	}
	
	
	/**
	 * Function for processing and storing all post data.
	 */
	private function process_post_type_meta( $post_id, $post ) {

		update_post_meta( $post_id, '_image_id', $_POST['upload_image_id'] );

		foreach($this->_fields as $props){
			$field_name = $props['name'];
			if ($props['type'] == 'wysiwyg') {
				$post_key = $field_name . '_editor';
			} else {
				$post_key = $field_name;
			}
			update_post_meta($post_id, $field_name, $_POST[$post_key]);
		}

	}

	/**
	 * Set a more appropriate placeholder text for the New item title field
	 */
	public function enter_title_here( $text, $post ) {
		if ( $post->post_type == $this->_post_type_id ) return __( $this->label->singular . ' Title' );
		return $text;
	}
	
	
	/**
	 * Add and remove meta boxes from the edit page
	 */
	public function meta_boxes() {

		foreach ($this->_fields as $field) {

			// print '<pre>'; var_dump($field);

			add_meta_box(
				$field['name'], // Unique ID
				esc_html__($field['title']), // Title
				array( &$this, $field['type'] . '_meta_box' ), // Callback function
				$this->_post_type_id, // Admin page (or post type)
				$field['context'], // Context
				'default' // Priority
			);

		}

	}
	
	/**
	 * Display the image meta box
	 */
	public function image_meta_box( $object, $box ) {

		// see http://www.foxrunsoftware.net/articles/wordpress/custom-post-type-with-image-uploads/

		global $post;
		
		$image_src = '';
		
		$image_id = get_post_meta( $post->ID, '_image_id', true );
		$image_src = wp_get_attachment_url( $image_id );
		
		?>
		<img id="<?php print $this->_post_type_id; ?>_image" src="<?php echo $image_src ?>" style="max-width:100%;" />
		<input type="hidden" name="upload_image_id" id="upload_image_id" value="<?php echo $image_id; ?>" />
		<p>
			<a title="<?php esc_attr_e( 'Set image' ) ?>" href="#" id="set-<?php print $this->_post_type_id; ?>-image"><?php _e( 'Set image' ) ?></a>
			<a title="<?php esc_attr_e( 'Remove image' ) ?>" href="#" id="remove-<?php print $this->_post_type_id; ?>-image" style="<?php echo ( ! $image_id ? 'display:none;' : '' ); ?>"><?php _e( 'Remove image' ) ?></a>
		</p>
		
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			
			// save the send_to_editor handler function
			window.send_to_editor_default = window.send_to_editor;
	
			$('#set-<?php print $this->_post_type_id; ?>-image').click(function(){
				
				// replace the default send_to_editor handler function with our own
				window.send_to_editor = window.attach_image;
				tb_show('', 'media-upload.php?post_id=<?php echo $post->ID ?>&amp;type=image&amp;TB_iframe=true');
				
				return false;
			});
			
			$('#remove-<?php print $this->_post_type_id; ?>-image').click(function() {
				
				$('#upload_image_id').val('');
				$('img').attr('src', '');
				$(this).hide();
				
				return false;
			});
			
			// handler function which is invoked after the user selects an image from the gallery popup.
			// this function displays the image and sets the id so it can be persisted to the post meta
			window.attach_image = function(html) {
				
				// turn the returned image html into a hidden image element so we can easily pull the relevant attributes we need
				$('body').append('<div id="temp_image">' + html + '</div>');
					
				var img = $('#temp_image').find('img');
				
				imgurl   = img.attr('src');
				imgclass = img.attr('class');
				imgid    = parseInt(imgclass.replace(/\D/g, ''), 10);
	
				$('#upload_image_id').val(imgid);
				$('#remove-<?php print $this->_post_type_id; ?>-image').show();
	
				$('img#<?php print $this->_post_type_id; ?>_image').attr('src', imgurl);
				try{tb_remove();}catch(e){};
				$('#temp_image').remove();
				
				// restore the send_to_editor handler function
				window.send_to_editor = window.send_to_editor_default;
				
			}
	
		});
		</script>
		<?php
	}

	public function plain_text_meta_box( $object, $box ) {
		?>
		<?php //wp_nonce_field( plugin_basename( __FILE__ ), $box['id'] . '_nonce' ); ?>
		<p>
			<label for="<?php print $box['id'] ?>"><?php _e( $box['title'], 'example' ); ?></label>
			<br />
			<input class="widefat" type="text" name="<?php print $box['id'] ?>" id="<?php print $box['id'] ?>" value="<?php echo esc_attr( get_post_meta( $object->ID, $box['id'], true ) ); ?>" size="30" />
		</p>
		<?php 
	}

	public function wysiwyg_meta_box( $object, $box ) {

		// http://chromeorange.co.uk/change-excerpt-to-product-summary-and-add-the-editor/

		$content = get_post_meta($object->ID, $box['id'], true);
		$settings = array(
			'quicktags' => array('buttons' => 'em,strong,link',),
			'text_area_name' => $box['id'],
			'quicktags' => true,
			'tinymce' => true,
			'editor_css' => '<style>#wp-' . $box['id'] . '-editor-container .wp-editor-area{height:150px; width:100%;}</style>'
		);
 		wp_editor($content, $box['id'] . '_editor', $settings);		
	}

	public function date_time_meta_box( $object, $box ){
		?>
		<p>
			<label for="<?php print $box['id'] ?>"><?php _e( $box['title'], 'example' ); ?></label>
			<br />
			<input type="hidden" name="<?php print $box['id'] ?>" id="<?php print $box['id'] ?>_fld" value="<?php echo esc_attr( get_post_meta( $object->ID, $box['id'], true ) ); ?>" />
			<input type="text" name="<?php print $box['id'] ?>_picker" id="<?php print $box['id'] ?>_picker" value="<?php echo esc_attr( get_post_meta( $object->ID, $box['id'], true ) ); ?>" size="30" />
		</p>
		<?php if (!$this->_dateTimeLibsIncluded): ?>
			<script src="http://code.jquery.com/jquery-1.8.3.js"></script>
			<script src="http://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
			<script src="<?php print plugins_url('/js/caret.js', __FILE__); ?>"></script>
			<script src="<?php print plugins_url('/js/datetimepicker.js', __FILE__); ?>"></script>
			<script src="<?php print plugins_url('/js/js_date_to_mysql_datetime.js', __FILE__); ?>"></script>
			<link rel="stylesheet" href="<?php print plugins_url('/css/datetimepicker.css', __FILE__); ?>">
			<?php $this->_dateTimeLibsIncluded = true; ?>
		<?php endif ?>
		<script type="text/javascript">
			jQuery(window).load(function(){
				var options = {};
				options.change = function (e, dt) {
						jQuery("#<?php print $box['id'] ?>_fld").val(dt.toMysqlFormat());
				};
				var date = jQuery("#<?php print $box['id'] ?>_fld").val();
				if (date) {
					var t = date.split(/[- :]/);
					options.date = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
				}
				jQuery("#<?php print $box['id'] ?>_picker").datetimepicker(options);
			});
		</script>
		<?php
	}

	public function init_taxonomy(){

		register_taxonomy(
			$this->_post_type_id . '_types',
			'rd_' . $this->_post_type_id . '_types',
			array(
				'hierarchical' => true,
				'label' => $this->_label_singular . ' Types',
				// 'query_var' => true,
			)
		);

	}

	public function init_styles(){

		if (count($this->_langs) > 1) {
			?>
			<style type="text/css">
			#titlediv, #postdivrich{
				display: none;
			}
			</style>
			<?php
		}

	}

	public function set_post_title_and_content($id){

		// unhook this function so it doesn't loop infinitely
		remove_action('save_post', array( &$this, 'set_post_title_and_content' ));		
		wp_update_post(array(
			'ID' => $id, 
			'post_title' => $_POST['title_' . DEFAULT_LANG],
			'post_content' => $_POST['text_' . DEFAULT_LANG . '_editor'],
		));
		// re-hook this function
		add_action('save_post', array( &$this, 'set_post_title_and_content' ));
		
	}
	
}