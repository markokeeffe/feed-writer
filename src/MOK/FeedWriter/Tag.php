<?php namespace MOK\FeedWriter;
/**
 * Author:  Mark O'Keeffe
 * Date:    13/01/14
 */

class Tag {

  /**
   * @var string
   */
  protected $name;

  /**
   * @var string
   */
  protected $value;

  /**
   * @var array
   */
  protected $attrs = array();

  /**
   * @var Tag
   */
  private $_parent;

  /**
   * @var Tag[]
   */
  private $_children = array();

  /**
   * @var array
   */
  protected $_requiredTags = array();

  /**
   * @var array
   */
  protected $_optionalTags = array();

  /**
   * Create a Tag instance with a name, value and optional attributes
   *
   * @param       $name
   * @param null  $value
   * @param array $attrs
   */
  public function __construct($name, $value=null, $attrs = array())
  {
    $this->name = $name;
    $this->value = $value;
    $this->attrs = $attrs;
  }

  /**
   * Get the tag name
   *
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * Get the tag value
   *
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }

  /**
   * Get all attributes
   *
   * @return array
   */
  public function getAttrs()
  {
    return $this->attrs;
  }

  /**
   * Get the parent tag
   *
   * @return Tag
   */
  public function getParent()
  {
    return $this->_parent;
  }

  /**
   * Get child tags
   *
   * @return Tag[]
   */
  public function getChildren()
  {
    return $this->_children;
  }

  /**
   * Add a parent tag
   *
   * @param Tag $tag
   */
  public function addParent($tag)
  {
    $this->_parent = $tag;
  }

  /**
   * Add a child tag
   *
   * @param       $name
   * @param null  $value
   * @param array $attrs
   *
   * @internal param \MOK\FeedWriter\Tag $tag
   */
  public function addChild($name, $value=null, $attrs=array())
  {
    $tag = new Tag($name, $value, $attrs);
    $this->_children[] = $tag;
    $tag->addParent($this);
  }

  /**
   * Create child tags for values set on this object
   *
   * @throws \Exception
   */
  public function createChildren()
  {
    foreach ($this->_requiredTags as $key => $name) {
      if (!isset($this->{$key}) || $this->{$key} === null) {
        throw new \Exception('Required attribute not set: '.$key);
      }
      $this->addChild($name, $this->{$key});
    }

    foreach ($this->_optionalTags as $key => $name) {
      if ($this->{$key} !== null) {
        $this->addChild($name, $this->{$key});
      }
    }
  }

  /**
   * HTML encode
   *
   * @param $text
   *
   * @return string
   */
  public function encode($text)
  {
    return htmlspecialchars($text,ENT_QUOTES,'UTF-8');
  }

  /**
   * Encloses the given string within a CDATA tag.
   * @param string $text the string to be enclosed
   * @return string the CDATA tag with the enclosed content.
   */
  public static function cdata($text)
  {
    return '<![CDATA[' . $text . ']]>';
  }

} 