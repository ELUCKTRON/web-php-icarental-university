<?php
include_once("storage.php");

class UsersStorage extends Storage {
  public function __construct() {
    parent::__construct(new JsonIO('data/users.json'));
  }
  public function getContactsByEmail($email) {
    return $this->findAll(["email" => $email]);
  }
}
