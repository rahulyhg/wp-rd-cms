<?php
class Rd_Page extends Rd_Cms{

	protected $_post_type_id = 'rd_page';
	protected $_label_singular = 'Page';
	protected $_label_plural = 'Pages';
	protected $_register_post_type_options = array(
		'hierarchical' => true,
		'supports' => array(
			'page-attributes'
		),
	);
	
	public function __construct(){

		parent::__construct();

		foreach ($this->_langs as $lang) {
			$this->_fields[] = array(
				'name' => 'title_' . $lang,
				'title' => 'Title ' . $lang,
				'type' => 'plain_text',
				'context' => 'normal',
			);
		}
		foreach ($this->_langs as $lang) {
			$this->_fields[] = array(
				'name' => 'text_' . $lang,
				'title' => 'Text ' . $lang,
				'type' => 'wysiwyg',
				'context' => 'normal',
			);
		}

	}

}