<?php
namespace RC\PHPCRSeoBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;

class SeoTwigBlock extends \Twig_Extension
{
	/**
	 * @var ContainerInterface
	 */
	protected $container;
	
	/**
	 * @var \Doctrine\ODM\PHPCR\DocumentManager
	 */
	protected $dm;
	
	
	protected $render, $seopath, $current;
	
    public function __construct(ContainerInterface $container, $objectManagerName, $seopath, $sonata_render){
    	$this->container = $container;
    	$this->dm = $this->container->get('doctrine_phpcr')->getManager($objectManagerName);
    	$this->seopath = $seopath;
    	$this->render = $sonata_render;
    	
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
        	'seo_block_render'=>  new \Twig_Function_Method($this, 'render',  array('is_safe' => array('html') )),
        	'seo_meta_keywords'=>  new \Twig_Function_Method($this, 'getKeywords'),
        	'seo_meta_description'=>  new \Twig_Function_Method($this, 'getDescription'),
        	'seo_meta_title'=>  new \Twig_Function_Method($this, 'getTitle'),
            'seo_meta_block_id'=>  new \Twig_Function_Method($this, 'getId'),

        );
    }
    
   
    protected function getCurrent($url){
    	try{
	    	return ($this->current) ? $this->current : $this->current = $this->dm->find(null, $this->seopath.$url );
    	}catch(\Exception $e){
    		return '';
    	}
    }

    public function getId($url){
        return $this->seopath.$url;
    }
    
    public function getTitle($url){
    	try{
    		return ($this->getCurrent($url)) ? strip_tags($this->getCurrent($url)->getTitle()): '';
    	}catch(\Exception $e){
    		return '';
    	}
    }
    
    public function getKeywords($url){
    	try{
    		return ($this->getCurrent($url)) ? strip_tags($this->getCurrent($url)->getKeywords()) : '';
    	}catch(\Exception $e){
    		return '';
    	}
    }
    
    public function getDescription($url){
    	try{
    		return ($this->getCurrent($url)) ?  strip_tags($this->getCurrent($url)->getDescription()): '';
    	}catch(\Exception $e){
    		return '';
    	}
    }
    
    public function render($url = false){
    	try{
    		return ($this->getCurrent($url)) ? $this->render->renderBlock($this->getCurrent($url)): '';
    	}catch(\Exception $e){
    		return '';
    	}
    }
    
    
    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'rc_phpcr_seo_block_twig_extension';
    }
}