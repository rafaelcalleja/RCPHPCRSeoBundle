<?php
namespace RC\PHPCRSeoBundle\Services;
use Symfony\Component\DependencyInjection\ContainerInterface;
use RC\PHPCRSeoBundle\Document\SeoNode;

use Doctrine\ODM\PHPCR\DocumentManager;
use Doctrine\Common\EventManager;
use PHPCR\Util\NodeHelper;

class SeoService {
	
	/**
	 * @var ContainerInterface
	 */
	protected $container;
	
	/**
	 * @var \Doctrine\ODM\PHPCR\DocumentManager
	 */
	protected $dm;

	public function __construct(ContainerInterface $container, $objectManagerName) {
		$this->container = $container;
        $dm = $this->container->get('doctrine_phpcr')->getManager($objectManagerName);
        $this->dm = $dm->create($dm->getPhpcrSession(),  $dm->getConfiguration(), new EventManager());
	}
	
	public function createSeo($basename, $seoid, $uri, $title, $keywords = false, $description = false){
		
		$parent = $this->dm->find(null, $basename);
		
		if(!$parent instanceof \Sonata\BlockBundle\Model\BlockInterface){
			$class = $this->dm->getClassMetadata("RC\PHPCRSeoBundle\Document\SeoNode");
			$document = $class->newInstance();
			$document->setParentDocument($parent);
			$document->setName($seoid);
			$this->dm->persist($document);
			return true;			
		}
		if (!$parent) {
			NodeHelper::createPath($this->dm->getPhpcrSession(), $basename);
			$parent = $this->dm->find(null, $basename);
			
		}
		
		
		$seoitem = $this->dm->find(null, "$basename/$seoid");
		$seoitem = ($seoitem instanceof SeoNode ) ? $seoitem : new SeoNode();
		
		
		$seoitem->setParentDocument($parent);
		$seoitem->setParent($parent);
		$seoitem->setName($seoid);
		$this->dm->persist($seoitem);
		
		$this->fixUriException();
		$seoitem->setUri($uri);
		$seoitem->setTitle($title);
				
		if($keywords)$seoitem->setKeywords($keywords);
		if($description)$seoitem->setDescription($description);
		
		$this->dm->persist($seoitem);
		$this->dm->flush($seoitem);
		return $seoitem;
		
	}
	
	public function move($source, $dest){
		$block = $this->dm->find(null, $source);
		$this->dm->move($block, $dest);
		$this->dm->flush($block);
	}
	
	
	private function fixUriException(){
		$metadata = $this->dm->getClassMetadata("RC\PHPCRSeoBundle\Document\SeoNode");
	
		if ($metadata->hasField('uri')) {
			$maps = array("fieldName" => "uri",
					"type" => "String",
					"translated" => false,
					"name" => "uri",
					"multivalue" => false,
					"assoc" => null);
	
			$metadata->mapField($maps, $metadata);
	
		}
	}
	
	public function updateSeo(){
		
	}
	
}
