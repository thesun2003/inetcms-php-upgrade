<?php
using::add_class('simpletemplate');

class SimplePage {
  private $_content = '';
  private $_metadata = array('title' => '',
                             'keywords' => '',
                             'description' => '');
  private $_include_js_files = array();
  private $_include_css_files = array();  
  private $_template_values = array();
  private $_page_html = '';

  function SimplePage($default_metadata = array()) {
    $this->setMetadata($default_metadata);
  }

  function setJSHeaders($js_headers = '') {
    $this->_include_js_files = $js_headers;
  }
  
  function setCSSHeaders($css_headers = '') {
    $this->_include_css_files = $css_headers;
  }
  function setContent($content) {
    $this->_content = $content;
  }

  function getContent() {
    return $this->_content;
  }

  function getPageHTML() {
    return $this->_page_html;
  }
  
  function processPageHTML($template_file = 'main/main') {
    $this->_template_values['include_css_files'] = $this->_include_css_files;
    $this->_template_values['include_js_files'] = $this->_include_js_files;
    $this->_template_values['include_headers'] = $this->process_template_file(
      MODULES . '/core',
      'main/head',
      $this->_template_values
    );    
    $this->_template_values['page_content'] = $this->getContent();
    $this->_template_values['metadata_title'] = $this->_metadata['title'];
    $this->_template_values['metadata_keywords'] = $this->_metadata['keywords'];
    $this->_template_values['metadata_description'] = $this->_metadata['description'];

    $this->_page_html = $this->process_template_file(
      MODULES . '/core',
      $template_file,
      $this->_template_values
    );
  }

  function display() {
    echo $this->getPageHTML();
    debug_print();
  }

  function setMetadata($metadata = array()) {
    foreach (array_keys($this->_metadata) as $key) {
      if (!empty($metadata[$key])) {
        $this->_metadata[$key] = $metadata[$key];
      }
    }
  }

  function getMetadata() {
    return $this->_metadata;
  }

  public static function process_template_file($path = '', $template_name = 'template', $values = array()) {
    $filename = $path . '/templates/' . $template_name . '.html';
    return SimpleTemplate::process_file($filename, $values);
  }

  public static function get_template($path, $template_name = 'template') {
    $filename = $path . '/templates/' . $template_name . '.html';
    return SimpleTemplate::get_file($filename);
  }
}
