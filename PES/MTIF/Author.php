<?php

namespace PES\MTIF;

/**
 * @file
 * This class represents a single author from a data source that provides
 * more information about an author than a login name.  For example, while
 * a TP -> WP import will only have author names (since thats all that
 * is exposed in the TP export data), a WP -> WP export has more information,
 * such as a first / last name, login name, etc.
 *
 * Since different data sources provide different amounts of data about post
 * authors, none of the below fields besides "login" are guaranteed to exist.
 */

class Author {

  /**
   * The unique ID of the author record, if the represented source supports
   * it.
   */
  private $id;

  /**
   * The login name of the author, if the underlying system supports it.
   * This might be something like "example" if the user's name was "John
   * Exampleman".
   */
  private $login;

  /**
   * The email address for the author, if one exists.
   */
  private $email;

  /**
   * The display name for the author in WP, if one exists in the data source.
   */
  private $display_name;

  /**
   * The authors first name, if provided by the data source.
   */
  private $first_name;

  /**
   * The authors last name, if provided by the data source.
   */
  private $last_name;

  public function __construct ($login_name) {
    $this->setLogin($login_name);
  }

  public function __toString () {
    return $this->login;
  }

  public function id () {
    return $this->id;
  }

  public function setId ($an_id) {
    $this->id = $an_id;
    return $this;
  }

  public function login () {
    return $this->login;
  }

  public function setLogin ($a_login) {
    $this->login = $a_login;
    return $this;
  }

  public function email () {
    return $this->email;
  }

  public function setEmail ($an_email) {
    $this->email = $an_email;
    return $this;
  }

  public function displayName () {
    return $this->display_name;
  }

  public function setDisplayName ($a_display_name) {
    $this->display_name = $a_display_name;
    return $this;
  }

  public function firstName () {
    return $this->first_name;
  }

  public function setFirstName ($a_first_name) {
    $this->first_name = $a_first_name;
    return $this;
  }

  public function lastName () {
    return $this->last_name;
  }

  public function setLastName ($a_last_name) {
    $this->last_name = $a_last_name;
    return $this;
  }
}
