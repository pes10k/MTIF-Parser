<?php

namespace PES\MTIF;

class Post {

  const PATTERN_AUTHOR = '/(?:^|\n)AUTHOR: (.*)\n/';
  const PATTERN_TITLE = '/(?:^|\n)TITLE: (.*)\n/';
  const PATTERN_DATE = '/(?:^|\n)DATE: (.*)\n/';
  const PATTERN_STATUS = '/(?:^|\n)STATUS: (.*)\n/';
  const PATTERN_URL = '/(?:^|\n)BASENAME: (.*)\n/';
  const PATTERN_ALLOW_COMMENTS = '/(?:^|\n)ALLOW COMMENTS: (.*)\n/';
  const PATTERN_ALLOW_PINGS = '/(?:^|\n)ALLOW PINGS: (.*)\n/';
  const PATTERN_CONVERT_BREAKS = '/(?:^|\n)CONVERT BREAKS: (.*)\n/';
  const PATTERN_CATEGORY_PRIMARY = '/(?:^|\n)PRIMARY CATEGORY: (.*)/';
  const PATTERN_CATEGORY_SECONDARY = '/(?:^|\n)CATEGORY: (.*)/';
  const PATTERN_KEYWORDS = '/^KEYWORDS:\n(.*?)$/m';
  const PATTERN_BODY = '/^BODY:\n(.*?)\n-----$/ism';
  const PATTERN_EXTENDED_BODY = '/^EXTENDED BODY:\n(.*?)\n-----$/ism';
  const PATTERN_COMMENT = '/^COMMENT:\n(.*?)\n(-----\n\nCOMMENT:|--------^|--------\nAUTHOR:)$/ism';
  const PATTERN_EXCERPT = '/^EXCERPT:\n(.*?)\n-----$/ism';

  const STATUS_DRAFT = 1;
  const STATUS_PUBLISH = 2;

  /**
   * A unique id for the post.  This is not needed in most cases, since most of
   * time posts are independent and don't need to know about each other, so
   * they can be assigned arbitrarily by the importer.
   */
  protected $id = 0;

  /**
   * Date the post was published on
   *
   * @var \DateTime
   * @access protected
   */
  protected $date;

  /**
   * An object representing the author who created this post
   *
   * @var \PES\MTIF\Author
   * @access protected
   */
  protected $author;

  /**
   * Title of the post
   *
   * @var string
   * @access protected
   */
  protected $title = '';

  /**
   * The main category for this post
   *
   * @var string
   * @access protected
   */
  protected $primary_category = '';

  /**
   * An array of strings of the categories this post belongs to
   *
   * (default value: array())
   *
   * @var array
   * @access protected
   */
  protected $seconday_categories = array();

  /**
   * The posts status, one of the \PES\MTIF\Post::STATUS_* constants.
   * Alternatly, this can be a string if a custom / other status is needed.
   *
   * @var mixed
   * @access protected
   */
  protected $status = 0;

  /**
   * URL alias for the post title.  This is the BASENAME value
   * in the Movable Type Import Format
   * http://www.sixapart.com/movabletype/docs/mtimport
   *
   * (default value: '')
   *
   * @var string
   * @access protected
   */
  protected $url_alias = '';

  /**
   * Whether the post allows comments (Defaults to FALSE)
   *
   * (default value: FALSE)
   *
   * @var bool
   * @access protected
   */
  protected $allows_comments = FALSE;

  /**
   * Whether the post allows pings (Defaults to FALSE)
   *
   * (default value: FALSE)
   *
   * @var bool
   * @access protected
   */
  protected $allows_pings = FALSE;

  /**
   * Whether or not to convert new lines to <br /> entities
   *
   * (default value: FALSE)
   *
   * @var bool
   * @access protected
   */
  protected $convert_breaks = FALSE;

  /**
   * Teaser body of the post.  This will either be in HTML.  If
   * there is no extneded body, this will be the full version of
   * the post
   *
   * (default value: '')
   *
   * @var string
   * @access protected
   */
  protected $body = '';

  /**
   * Full body of the post.  This will either be in HTML or plain text.
   * This may be empty
   *
   * (default value: '')
   *
   * @var string
   * @access protected
   */
  protected $extended_body = '';

  /**
   * Brief version of the post, used for teasers
   *
   * (default value: '')
   *
   * @var string
   * @access protected
   */
  protected $excerpt = '';

  /**
   * Description of post type.  By default all MT items will be type 'post'.
   *
   * (default value: 'post')
   *
   * @var string
   * @access protected
   */
  protected $post_type = 'post';

  /**
   * An array of \PES\MTIF\Comment objects representing zero or more comments
   *
   * (default value: array())
   *
   * @var array
   * @access protected
   */
  protected $comments = array();

  /**
   * The order that the given post should appear in listings in WP.  Note that
   * most data sources (such as MT data) won't have this information.
   *
   * (default value: 0)
   *
   * @var int
   * @access protected
   */
  protected $menu_order = 0;

  /**
   * A collection of zero or more keywords, describing the post.  Each keyword
   * is represented by a string
   *
   * @var array
   */
  protected $keywords = array();

  public function __construct($string = '') {

    $this->date = new \DateTime();
    $this->author = new Author('');

    if ( ! empty($string)) {
      $this->parseString($string);
    }
  }

  /**
   * Takes a string describing a single post in the MTIF format as described here:
   * http://www.sixapart.com/movabletype/docs/mtimport
   *
   * @access public
   * @param string $string
   * @return bool
   */
  public function parseString($string) {

    $matches = array();

    // Normalize our line endings...
    $string = str_replace("\r\n", "\n", $string);
    $string = str_replace("\r", "\n", $string);

    if (preg_match(self::PATTERN_AUTHOR, $string, $matches) === 1) {
      $this->author = new Author($matches[1]);
    }

    if (preg_match(self::PATTERN_TITLE, $string, $matches) === 1) {
      $this->title = $matches[1];
    }

    if (preg_match(self::PATTERN_DATE, $string, $matches) === 1) {
      $this->date = new \DateTime($matches[1]);
    }

    if (preg_match(self::PATTERN_STATUS, $string, $matches) === 1) {
      $this->status = strtolower($matches[1]) === 'draft'
        ? self::STATUS_DRAFT
        : self::STATUS_PUBLISH;
    }

    if (preg_match(self::PATTERN_URL, $string, $matches) === 1) {
      $this->url_alias = $matches[1];
    }

    if (preg_match(self::PATTERN_CATEGORY_PRIMARY, $string, $matches) === 1) {
      $this->primary_category = $matches[1];
    }

    if (preg_match_all(self::PATTERN_CATEGORY_SECONDARY, $string, $matches) > 0) {

      if ( ! empty($matches[1])) {

        foreach ($matches[1] as $item) {
          $this->seconday_categories[] = $item;
        }
      }
    }

    if (preg_match(self::PATTERN_KEYWORDS, $string, $matches)) {
      $keywords = explode(',', $matches[1]);
      if ( ! empty($keywords)) {
        foreach ($keywords as $a_keyword) {
          $a_keyword = trim($a_keyword);
          if (!empty($a_keyword)) {
            $this->keywords[] = $a_keyword;
          }
        }
      }
    }

    if (preg_match(self::PATTERN_BODY, $string, $matches) === 1) {
      $this->body = $matches[1];
    }

    if (preg_match(self::PATTERN_EXTENDED_BODY, $string, $matches) === 1) {
      $this->extended_body = $matches[1];
    }

    if (preg_match(self::PATTERN_EXCERPT, $string, $matches) === 1) {
      $this->excerpt = $matches[1];
    }

    if (preg_match(self::PATTERN_ALLOW_COMMENTS, $string, $matches) === 1) {
      $this->allows_comments = trim($matches[1]) === '1';
    }

    if (preg_match(self::PATTERN_ALLOW_PINGS, $string, $matches) === 1) {
      $this->allows_pings = trim($matches[1]) === '1';
    }

    if (preg_match(self::PATTERN_CONVERT_BREAKS, $string, $matches) === 1) {
      $convert_break_setting = $matches[1];
      $this->convert_breaks = ($convert_break_setting !== 'wysiwyg' && $convert_break_setting !== '__default__');
    }

    $offset = 0;

    while (preg_match(self::PATTERN_COMMENT, $string, $matches, PREG_OFFSET_CAPTURE, $offset)) {

      $comment_body = $matches[1][0];
      $offset = $matches[1][1] + mb_strlen($comment_body);
      $this->comments[] = new Comment($comment_body, $this);
    }
  }

  // ====================
  // ! Getter / Setters
  // ====================
  public function id () {
    return $this->id;
  }

  public function setId ($an_id) {
    $this->id = $an_id;
    return $this;
  }

  public function comments () {
    return $this->comments;
  }

  public function newComment () {
    $a_comment = new Comment();
    $a_comment->setPost($this);
    $this->comments[] = $a_comment;
    return $a_comment;
  }

  public function date () {
    return $this->date;
  }

  public function setDate ($a_date) {
    $this->date = $a_date;
    return $this;
  }

  public function author () {
    return $this->author;
  }

  public function setAuthor ($an_author) {

    if (is_string($an_author)) {

      $this->author = new Author($an_author);

    } else {

      $this->author = $an_author;
    }

    return $this;
  }

  public function title () {
    return $this->title;
  }

  public function setTitle ($a_title) {
    $this->title = $a_title;
    return $this;
  }

  public function primaryCategory () {
    return $this->primary_category;
  }

  public function setPrimaryCategory ($a_category) {
    $this->primary_category = $a_category;
    return $this;
  }

  public function secondayCategories () {
    return $this->seconday_categories;
  }

  public function addSecondaryCategory ($a_category) {
    $this->seconday_categories[] = $a_category;
    return $this;
  }

  public function status () {
    return $this->status;
  }

  public function setStatus ($a_status) {
    $this->status = $a_status;
    return $this;
  }

  public function urlAlias () {
    return $this->url_alias;
  }

  public function setUrlAlias ($an_alias) {
    $this->url_alias = $an_alias;
    return $this;
  }

  public function allowsComments () {
    return $this->allows_comments;
  }

  public function setAllowsComments ($allows_comments) {
    $this->allows_comments = $allows_comments;
    return $this;
  }

  public function allowsPings () {
    return $this->allows_pings;
  }

  public function setAllowsPings ($allows_pings) {
    $this->allows_pings = $allows_pings;
    return $this;
  }

  public function convertBreaks () {
    return $this->convert_breaks;
  }

  public function setConvertBreaks ($convert_breaks) {
    $this->convert_breaks = $convert_breaks;
    return $this;
  }

  public function body () {
    return empty($this->extended_body)
      ? $this->normalizeEndlines($this->body)
      : $this->normalizeEndlines($this->extended_body);
  }

  public function setBody ($body) {
    $this->body = $body;
    return $this;
  }

  public function excerpt () {
    return empty($this->extended_body)
      ? $this->normalizeEndlines($this->excerpt)
      : $this->normalizeEndlines($this->body);
  }

  public function setExcerpt ($excerpt) {
    $this->excerpt = $excerpt;
    return $this;
  }

  public function type () {
    return $this->post_type;
  }

  public function setType ($a_type) {
    $this->post_type = $a_type;
    return $this;
  }

  public function menuOrder () {
    return $this->menu_order;
  }

  public function setMenuOrder ($an_order) {
    $this->menu_order = $an_order;
    return $this;
  }

  public function postParent () {
    return FALSE;
  }

  public function keywords () {
    return $this->keywords;
  }

  public function tags () {
    return $this->keywords();
  }

  public function setKeywords ($keywords) {
    $this->keywords = $keywords;
    return $this;
  }

  private function normalizeEndlines ($text) {
    $text = preg_replace("/\r\n|\r/", "\n", $text);
    return $text;
  }
}
