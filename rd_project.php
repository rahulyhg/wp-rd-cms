<?php
class Rd_Project extends Rd_Cms{

	protected $_post_type_id = 'rd_project';
	protected $_label_singular = 'Project';
	protected $_label_plural = 'Projects';

	public function __construct(){

		parent::__construct();

		$this->_taxonomies = array('rd_project_types');		

		$this->_fields[] = array(
			'name' => 'authors',
			'title' => 'Authors',
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
				'name' => 'credits_' . $lang,
				'title' => 'Credits ' . $lang,
				'type' => 'wysiwyg',
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

		$this->_fields[] = array(
			'name' => 'image',
			'title' => 'Thumbnail (min width: 340px, for homepage)',
			'type' => 'image',
			'context' => 'side',
			'description' => ''
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