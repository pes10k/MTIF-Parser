<?php

namespace PES\MTIF;

class Parser {

  /**
   * File system pointer resource pointing to the MTIF export
   * data
   *
   * @var resource
   * @access protected
   */
  protected $mtif_handle;

  /**
   * Path to the MTIF export data on the file system
   *
   * @var string
   * @access protected
   */
  protected $mtif_path;

  /**
   * Stores the text encoding, returend in a format described by
   * mb_detect_encoding(), of the input MTIF file.
   *
   * @var string
   */
  protected $encoding;

  /**
   * Convenience constructor to allow setting the path to the MTIF file
   * on initilization.
   */
  public function __construct($path = FALSE) {

    if ($path) {
      $this->setMTIFPath($path);
    }
  }

  /**
   * Iterates over the the MTIF export data and returns either an object representing
   * the next Post, or false if the data is not formatted correctly or we've reached
   * the end of the data
   *
   * @access public
   * @return MT2WP_MTIF_Post|false
   */
  public function next() {

    $current_post_contents = '';

    while ($line = fgets($this->mtif_handle)) {

      // If this line is the end of a post section, time to parse the section and
      // see if it contains a valid post.  We need to check for unix, windows,
      // and mac style line endings here.
      if ($line === "--------\n" OR $line === "--------\r\n" OR $line === "--------\r") {
        return new Post($current_post_contents);
      }

      $current_post_contents .= $line;
    }

    return false;
  }

  public function rewind() {
    fclose($this->mtif_path);
    $this->mtif_handle = fopen($this->mtif_path, 'r');
  }

  // ====================
  // ! Getter / Setters
  // ====================

  /**
   * Set the path to the MTIF export data on the filesystem.
   * Returns a false if there was no file at this location.
   * Otherwise returns true
   *
   * @access public
   * @param string $path
   * @return bool
   */
  public function setMTIFPath($path) {
    $this->encoding = FALSE;
    $this->mtif_path = $path;
    $this->mtif_handle = fopen($this->mtif_path, 'r');

    return !! $this->mtif_handle;
  }

  /**
   * Attempts to detect the encoding of the input file by reading, at most,
   * the first 1MB of the file.  This is lazy loaded, so the detcting will
   * only be checked for once.
   */
  public function getEncoding() {

    if (!$this->encoding) {

      $file_contents = file_get_contents(
        $this->mtif_path,
        FALSE,
        NULL,
        -1,
        125000
      );

      $this->encoding = mb_detect_encoding($file_contents);
    }

    return $this->encoding;
  }
}
