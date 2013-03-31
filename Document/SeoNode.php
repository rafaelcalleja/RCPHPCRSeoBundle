<?php

namespace RC\PHPCRSeoBundle\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;
use Symfony\Cmf\Bundle\BlockBundle\Document\BaseBlock;
use Sonata\BlockBundle\Model\BlockInterface;

/**
 * This class represents a seo node.
 *
 * @author Rafael Calleja<rafa.calleja@d-noise.net>
  *
 * @PHPCRODM\Document
 */
class SeoNode extends BaseBlock
{


    /** @PHPCRODM\String */
    protected $keywords = '';
    
    /** @PHPCRODM\String */
    protected $description = '';
    
    /** @PHPCRODM\String */
    protected $title = '';

    /** @PHPCRODM\Uri */
    protected $uri;

    /** @PHPCRODM\String */
    protected $route;

    /** @PHPCRODM\Children() */
    protected $children = array();

    /**
     * Hashmap for extra stuff associated to the node
     *
     * @PHPCRODM\String(assoc="")
     */
    protected $extras;

    /** @PHPCRODM\String(multivalue=true, assoc="") */
    protected $routeParameters = array();

    public function __construct($name = null)
    {
        $this->name = $name;
    }



    /**
     * Return the title assigned to this seo node
     *
     * @return string
     */
    public function getTitle()
    {
        return empty($this->title) ? $this->getParentValue('title') : $this->title;
    }

    /**
     * Set title for this seo node
     *
     * @param $title string
     *
     * @return SeoNode - this instance
     */
    public function setTitle($value)
    {
        $this->title = strip_tags($value);

        return $this;
    }
    
    /**
     * Return the keywords assigned to this seo node
     *
     * @return string
     */
    public function getKeywords()
    {
    	return ( empty($this->keywords) ) ? $this->getParentValue('keywords') : $this->keywords;
    }
    
    
    protected function getParentValue($field){
    	$field = 'get'.ucfirst($field);
    	if(!empty($this->{$field})) return $this->{$field}; 
    	$parent = $this->getParentDocument();
    	if( $parent instanceof SeoNode ){
    		return $parent->{$field}();
    	}
		return '';
    }
    /**
     * Set description for this seo node
     *
     * @param $description string
     *
     * @return SeoNode - this instance
     */
    public function setKeywords($value)
    {
    	$this->keywords = strip_tags($value);
    
    	return $this;
    }
    
    /**
     * Return the keywords assigned to this seo node
     *
     * @return string
     */
    public function getDescription(){
    	return empty($this->description) ? $this->getParentValue('description') : $this->description;
    }
    
    /**
     * Set keywords for this seo node
     *
     * @param $keywords string
     *
     * @return SeoNode - this instance
     */
    public function setDescription($value)
    {
    	$this->description = strip_tags($value);
    
    	return $this;
    }
    

    /**
     * Return the URI
     *
     * @return $uri string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Set the URI
     *
     * @param $uri string
     *
     * @return SeoNode - this instance
     */
    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * Return the route name
     *
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set the route name
     *
     * @param $route string - name of route
     *
     * @return SeoNode - this instance
     */
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

   

    /**
     * Get all child seo nodes of this seo node. This will filter out all
     * non-NodeInterface nodes.
     *
     * @return SeoNode[]
     */
    public function getChildren()
    {
        $children = array();
        foreach ($this->children as $child) {
            if (!$child instanceof BlockInterface) {
                continue;
            }
            $children[] = $child;
        }

        return $children;
    }

    /**
     * Gets the route parameters
     *
     * @return array
     */
    public function getRouteParameters()
    {
        return $this->routeParameters;
    }

    /**
     * Sets the route parameters
     *
     * @param array the parameters
     */
    public function setRouteParameters($routeParameters)
    {
        $this->routeParameters = $routeParameters;
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions()
    {
        return array(
            'uri' => $this->getUri(),
            'route' => $this->getRoute(),
            'label' => $this->getLabel(),
            'attributes' => $this->getAttributes(),
            'childrenAttributes' => $this->getChildrenAttributes(),
            'display' => true,
            'displayChildren' => true,
            'content' => $this->getContent(),
            'routeParameters' => $this->getRouteParameters(),
            // TODO provide the following information
            'routeAbsolute' => false,
            'linkAttributes' => array(),
            'labelAttributes' => array(),
        );
    }

    /**
     * Get extra attributes
     *
     * @return array
     */
    public function getExtras()
    {
        return $this->extras;
    }

    /**
     * Set the extra attributes
     *
     * @param $extras array
     *
     * @return SeoNode - this instance
     */
    public function setExtras(array $extras)
    {
        $this->extras = $extras;

        return $this;
    }

    /**
     * Add a child seo node, automatically setting the parent node.
     *
     * @param SeoNode - seo node to add
     *
     * @return SeoNode - The newly added child node.
     */
    public function addChild(SeoNode $child)
    {
        $child->setParentDocument($this);
        $this->children[] = $child;

        return $child;
    }

    public function __toString()
    {
        return $this->getTitle() ? : '(no label set)';
    }
    
    public function getType()
    {
    	return 'rc.phpcr.sonata.seo.block';
    }
}
