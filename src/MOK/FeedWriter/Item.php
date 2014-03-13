<?php namespace MOK\FeedWriter;

/**
 * Author:  Mark O'Keeffe
 * Date:    13/01/14
 */

class Item extends Tag {

  /**
   * @var string
   */
  public $id;

  /**
   * @var string
   */
  public $title;

  /**
   * @var string
   */
  public $description;

  /**
   * @var string
   */
  public $content;

  /**
   * @var string
   */
  public $link;

  /**
   * @var string
   */
  public $author;

  /**
   * @var string
   */
  public $datePublished;

  /**
   * @var string
   */
  private $_name = 'item';

  /**
   * @var array
   */
  protected $_requiredTags = array(
    'id' => 'guid',
    'title' => 'title',
    'description' => 'description',
    'link' => 'link',
    'author' => 'author',
  );

  /**
   * @var array
   */
  protected $_optionalTags = array(
    'content' => 'content:encoded',
    'datePublished' => 'pubDate',
  );

  /**
   * Set attributes of the feed from an associative array e.g.
   *
    $data = array(
      'title' => 'Test Feed',
      'description' => 'My feed for testing',
      'datePublished' => date(DATE_RSS),
      'dateUpdated' => date(DATE_RSS),
      'link' => 'http://feed.example.com',
      'author' => 'test@example.com (tester)',
    );

    $feed = new \MOK\FeedWriter\Feed($data);
   *
   * @param array $data
   */
  public function __construct($data = array())
  {
    foreach ($data as $key => $val) {
      if (property_exists($this, $key)) {
        $this->{$key} = $val;
      }
    }
    // Create Tag instances from this object's attributes
    $this->createChildren();
  }

  /**
   * Get the tag name
   *
   * @return string
   */
  public function getName()
  {
    return $this->_name;
  }

} 