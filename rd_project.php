<?php
class Rd_Project extends Rd_Cms{

	protected $_post_type_id = 'rd_project';
	protected $_label_singular = 'Project';
	protected $_label_plural = 'Projects';

	public function __construct(){

		parent::__construct();

		$this->_taxonomies = array($this->_post_type_id . '_types', 'post_tag');		

		$this->_fields[] = array(
			'name' => 'author',
			'title' => 'Author',
			'type' => 'plain_text',
			'context' => 'side',
		);

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

		foreach ($this->_langs as $lang) {
			$this->_fields[] = array(
				'name' => 'subtitle1_' . $lang,
				'title' => 'Subtitle 1 ' . $lang,
				'type' => 'plain_text',
				'context' => 'side',
			);
		}

		foreach ($this->_langs as $lang) {
			$this->_fields[] = array(
				'name' => 'subtitle2_' . $lang,
				'title' => 'Subtitle 2 ' . $lang,
				'type' => 'plain_text',
				'context' => 'side',
			);
		}

		$this->_fields[] = array(
			'name' => 'image',
			'title' => 'Image',
			'type' => 'image',
			'context' => 'side',
		);

		$this->_fields[] = array(
			'name' => 'vimeo_id',
			'title' => 'Vimeo ID',
			'type' => 'plain_text',
			'context' => 'side',
		);

	}
	
	public function init(){

		$this->init_taxonomy();

		parent::init();

	}

}