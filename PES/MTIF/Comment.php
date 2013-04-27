<?php

namespace PES\MTIF;

/**
 * \PES\MTIF\Comment class.
 *
 * Represents a single comment for a Moveable Type blog post
 */
class Comment {

  const PATTERN_AUTHOR = '/(?:^|\n)AUTHOR: (.*)\n/';
  const PATTERN_EMAIL = '/(?:^|\n)EMAIL: (.*)\n/';
  const PATTERN_IP = '/(?:^|\n)IP: (.*)\n/';
  const PATTERN_URL = '/(?:^|\n)URL: (.*)\n/';
  const PATTERN_DATE = '/(?:^|\n)DATE: (.*)\n/';
  const PATTERN_BODY = '/(?:^|\n)DATE: (?:[^\n]+)\n(.*)/is';

  /**
   * A reference to the post that this comment was left on
   *
   * @var \PES\MTIF\Post|NULL
   * @access protected
   */
  protected $post;

  /**
   * A string of the name of the comment's author
   *
   * (default value: '')
   *
   * @var string
   * @access protected
   */
  protected $author = '';

  /**
   * The email for the comment's author
   *
   * (default value: '')
   *
   * @var string
   * @access protected
   */
  protected $email = '';

  /**
   * The URL left with the comment
   *
   * (default value: '')
   *
   * @var string
   * @access protected
   */
  protected $url = '';

  /**
   * The IP the comment was left from
   *
   * (default value: '')
   *
   * @var string
   * @access protected
   */
  protected $ip = '';

  /**
   * The date the comment was left on
   *
   * (default value: '')
   *
   * @var \DateTime
   * @access protected
   */
  protected $date;

  /**
   * The body of the comment itself
   *
   * (default value: '')
   *
   * @var string
   * @access protected
   */
  protected $body = '';

  public function __construct($string, $post) {
    if ( ! empty($string)) {
      $this->parseString($string);
    }

    if ( ! empty($post)) {
      $this->post = $post;
    }
  }

  public function parseString($string) {

    $matches = array();

    if (preg_match(self::PATTERN_AUTHOR, $string, $matches) === 1) {
      $this->author = $matches[1];
    }

    if (preg_match(self::PATTERN_EMAIL, $string, $matches) === 1) {
      $this->email = $matches[1];
    }

    if (preg_match(self::PATTERN_IP, $string, $matches) === 1) {
      $this->ip = $matches[1];
    }

    if (preg_match(self::PATTERN_URL, $string, $matches) === 1) {
      $this->url = $matches[1];
    }

    if (preg_match(self::PATTERN_BODY, $string, $matches) === 1) {
      $this->body = $matches[1];
    }

    if (preg_match(self::PATTERN_DATE, $string, $matches) === 1) {
      $this->date = new \DateTime($matches[1]);
    }
    else {
      var_dump($string);
    }
  }

  // ====================
  // ! Getter / Setters
  // ====================
  public function post() {
    return $this->post;
  }

  public function setPost(\PES\MTIF\Post $a_post) {
    $this->post = $a_post;
    return $this;
  }

  public function author() {
    return $this->author;
  }

  public function setAuthor($an_author) {
    $this->author = $an_author;
    return $this;
  }

  public function email() {
    return $this->email;
  }

  public function setEmail($an_email) {
    $this->email = $an_email;
    return $this;
  }

  public function url() {
    return $this->url;
  }

  public function setUrl($a_url) {
    $this->url = $a_url;
    return $this;
  }

  public function ip() {
    return $this->ip;
  }

  public function setIp($an_ip) {
    $this->ip = $an_ip;
    return $this;
  }

  public function date() {
    return $this->date;
  }

  public function setDate(\DateTime $a_date) {
    $this->date = $a_date;
    return $this;
  }

  public function body() {
    return $this->body;
  }

  public function setBody($body) {
    $this->body = $body;
    return $this;
  }
}
