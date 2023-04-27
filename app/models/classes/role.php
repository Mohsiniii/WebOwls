<?php
class Role extends custom_error
{
  private $_db = null;

  public function __construct()
  {
    $this->_db       = DB::getInstance();
  }

  public function find($roleID = null)
  {
    if (is_string($roleID) === true) {
      $find = $this->_db->get('`role_id` as `id`, `title`', 'user_roles', '`role_id` = ?', array($roleID));
      if ($find->errorStatus() === false and $find->dataCount() == 1) {
        return $find->getFirstResult();
      } else {
        return null;
      }
    } else {
      return null;
    }
  }
}
