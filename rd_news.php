<?php
class Rd_News extends Rd_Cms{

	protected $_post_type_id = 'rd_news';
	protected $_label_singular = 'News';
	protected $_label_plural = 'News';
	protected $_register_post_type_options = array(
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
				'name' => 'home_intro_' . $lang,
				'title' => 'Intro for home ' . $lang,
				'type' => 'wysiwyg',
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