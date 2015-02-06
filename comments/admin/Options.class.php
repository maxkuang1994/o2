<?php

Class Options {
	public $table = 'options';

	public function __construct()
	{	
		if (!class_exists('Database'))
			return false;
	}
	public function get($name = false)
	{
		$db = new Database();
		if ($name) {
			if ($db->select($this->table, 'value', "name='$name'", null, 1) ) {
				$data = $db->getResult();
				return $data[1]['value'];
			}
		} else if ($db->select($this->table, 'name,value') ) {
			$res = $db->getResult();
			foreach ($res as $option)
				$data[$option['name']] = $option['value'];
			return $data;
		}

		return null;
	}
	public function add($name, $value)
	{
		$db = new Database();
		return $db->insert($this->table, array( 'name' => $name, 'value' => $value));
	}
	public function update($name, $value)
	{
		$db = new Database();
		if (!$db->select($this->table, 'id', "name='$name'", null, 1))
			return $this->add($name, $value);
		else return $db->update($this->table, array('value' => $value), 'name="'.$name.'"', 1);
	}
	public function delete($name)
	{
		$db = new Database();
		return $db->delete($this->table, "name='$name'", 1);
	}
	public function installed()
	{
		return mysql_query("select 1 from $this->table");
	}
	public function install()
	{
		$sql = "CREATE TABLE IF NOT EXISTS $this->table (
					id int(5) NOT NULL AUTO_INCREMENT,
					name varchar(50) NOT NULL,
					value text NOT NULL,
					PRIMARY KEY (id)
				);";
		@mysql_query($sql);
		
		$sql = "INSERT INTO `$this->table` (`name`, `value`) VALUES
					('comment_status', '1'),
					('maxlength', '500'),
					('comments_per_page', '10'),
					('comment_reply', '1'),
					('max_depth', '2'),
					('comments_order', 'desc'),
					('comments_captcha', '1'),
					('comments_limit', '3'),
					('mark_comment_as', '2'),
					('default_avatar', 'wavatar'),
					('reply_notification', '1'),
					('admin_user', 'admin');";
		@mysql_query($sql);
	}
}

?>