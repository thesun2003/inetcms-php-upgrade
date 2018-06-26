<?
using::add_class('fieldmanager');
using::add_class('dbpager');

class Entity{
    var $form;
    var $_table;

    /** 
     * @param  string DB table name.
     */
    function Entity($table){
        $this->form = new FieldManager();
        $this->_table   = $table;
    }

    function isValid($fields = false){
        return $this->form->isValid($fields);
    }

    /** @param  array */
    function setInfo($info){
        foreach ((array)$info as $k => $v) {
            $this->set($k, $v);
        }
    }

    function set($field, $value){
        if ($this->form->isExists($field)) {
            $this->form->set($field, $value);
        }
    }

    /** @param  string */
    function get($field, $htmlencode=true){
        return $this->form->get($field, $htmlencode);
    }

    function save(){
        global $DB;
        if ($this->get('id')) {
            $DB->autoExecute($this->_table, $this->form->getAll(), DB_AUTOQUERY_UPDATE, "id=".$DB->quote($this->get('id')));
        } else {
            $data = $this->form->getAll();
            unset($data['id']);
            $DB->autoExecute($this->_table, $data, DB_AUTOQUERY_INSERT);
            $this->set('id', $DB->insertedId());
        }
    }

    /** Deletes entity record from DB. */
    function del(){
        if (!$this->get('id')) return;
        global $DB;
        $DB->query("DELETE FROM $this->_table WHERE id=".$DB->quote($this->get('id')));
        $this->set('id', '');
    }

    /** Note: you should quote $where parameter!
    * @param    array   DB fields values.
    * @return   object */
    function &find($params, $order = false, $limit = false, $where = false){
        global $DB;
        $query  = array();
        foreach ($params as $k => $v) {
            $query[]    = "$k=".$DB->quote($v);
        }
        if ($where) $query[]    = $where;
        /* Newest records will be the first. */
        if (empty($query)) {
            $query  = '';
        } else {
            $query  = "WHERE ".join(' AND ', $query);
        }
        $order  = mysql_real_escape_string($order);
        $limit  = mysql_real_escape_string($limit);
        $res = new DBPager(strtolower(get_class($this)), $this->_table, $query, $order, $limit);
        return $res;
    }

    function get_item_level($parent_id_field) {
      $class_name = get_class($this);
      $item = new $class_name;
      $item->setInfo($this->form->getAll());
      $level = 0;
      while ($item->get('id')) {
        $item = $this->find(array('id' => $item->get($parent_id_field)))->next();
        if (!$item) {
          $item = new $class_name;
        }
        $level++;
      }
      return $level;
    }

    function get_children_list_by_parent_id($parent_id_field, $parent_id, $level = 0, $is_open = false) {
      $result = array();
      $search = $this->find(array($parent_id_field => $parent_id), 'parent_id, position');
      while($item = $search->next()) {
        $result[] = array(
          'object' => $item,
          'level' => $level
        );
      }
      return $result;
    }

    function get_children($parent_id_field = 'parent_id', $is_open = false) {
      $fields = $this->form->getAll();
      $list = array();
      if(in_array($parent_id_field, array_keys($fields))) {
        if(!$this->get('id')) {
          $this->set('id', '0');
        }
        $parent_id = $this->get('id');
        $list = array('-' . $parent_id);
        $walk_done = false;

        while (!$walk_done) {
          $level = MenuTree::get_level_by_id($parent_id, $list) + 1;

          $children_list = $this->get_children_list_by_parent_id($parent_id_field, $parent_id, $level, $is_open);

          $parent_pos = array_search('-' . $parent_id, $list);
          array_splice($list, $parent_pos, 1, $children_list);

          $parent_id = MenuTree::get_next_parent_id($list);

          if (!$parent_id) {
            $walk_done = true;
          }
        }
      }
      return $list;
    }
}
?>