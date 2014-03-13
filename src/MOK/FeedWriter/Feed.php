<?php namespace MOK\FeedWriter;
/**
 * Author:  Mark O'Keeffe
 * Date:    13/01/14
 */

class Feed extends Tag {

  /**
   * @var string
   */
  public $title;

  /**
   * @var string
   */
  public $description;

  /**
   * @var array
   */
  public $link = array();

  /**
   * @var array
   */
  public $author = array();

  /**
   * @var string
   */
  public $datePublished;

  /**
   * @var string
   */
  public $dateUpdated;

  /**
   * @var Item[]
   */
  public $items = array();

  /**
   * @var Tag[]
   */
  public $tags = array();

  /**
   * @var \DOMDocument
   */
  private $_dom;

  /**
   * @var array
   */
  protected $_requiredTags = array(
    'title' => 'title',
    'description' => 'description',
    'link' => 'link',
    'author' => 'managingEditor',
    'datePublished' => 'pubDate',
    'dateUpdated' => 'lastBuildDate',
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
   * Add an item to the feed
   *
   * @param Item $item
   */
  public function addItem($item)
  {
    $this->items[] = $item;
  }


  /**
   * Generate XML output for a feed
   */
  public function output()
  {
    $this->_dom = new \DOMDocument('1.0', 'utf-8');
    $this->_dom->preserveWhiteSpace = true;
    $this->_dom->formatOutput = true;
    $rss = $this->_dom->createElement('rss');
    $rss->setAttribute('version', '2.0');
    $rss->setAttribute('xmlns:atom', 'http://www.w3.org/2005/Atom');
    $rss->setAttribute('xmlns:content', 'http://purl.org/rss/1.0/modules/content/');

    $channel = $this->_dom->createElement('channel');

    $this->tags = array_merge($this->tags, $this->getChildren());

    // Add any saved tags to the 'rss' element
    foreach ($this->tags as $tag) {
      $channel->appendChild($this->createTag($tag));
    }

    // Add items to the 'channel' element
    foreach ($this->items as $item) {
      $channel->appendChild($this->createTag($item));
    }

    $rss->appendChild($channel);
    $this->_dom->appendChild($rss);

    return $this->_dom->saveXML();
  }

  /**
   * Publish a feed to pubsubhubbub
   *
   * @param $hub
   * @param $url
   *
   * @return bool
   * @throws \Exception
   */
  public static function publish($hub, $url)
  {
    $p = new \Google\Pubsubhubbub\Publisher($hub);
    if ($p->publish_update($url)) {
      return true;
    }
    throw new \Exception($p->last_response());
  }

  /**
   * Create a new XML tag from the Tag object
   *
   * @param Tag $tag
   *
   * @return \DOMElement
   */
  private function createTag($tag)
  {
    $elem = $this->_dom->createElement($tag->getName());

    if (preg_match('/:encoded/', $tag->getName())) {
      $value = $this->_dom->createCDATASection($tag->getValue());
    } else {
      $value = $this->_dom->createTextNode($tag->getValue());
    }

    $elem->appendChild($value);
    foreach ($tag->getAttrs() as $key => $val) {
      $elem->setAttribute($key, $val);
    }
    // Add child tags like '<title>', '<description>' etc
    foreach ($tag->getChildren() as $childTag) {
      $elem->appendChild($this->createTag($childTag));
    }
    return $elem;
  }

} 