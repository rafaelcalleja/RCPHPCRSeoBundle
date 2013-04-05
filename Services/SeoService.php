<?php
namespace RC\PHPCRSeoBundle\Services;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

use RC\PHPCRSeoBundle\Document\SeoNode;

use Doctrine\ODM\PHPCR\Translation\LocaleChooser\LocaleChooser;
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
	
	/**
	 * @var null|LoggerInterface
	 */
	protected $logger;

	public function __construct(ContainerInterface $container, $objectManagerName, LocaleChooser $localeChooser, LoggerInterface $logger = null ) {
		$this->container = $container;
        $dm = $this->container->get('doctrine_phpcr')->getManager($objectManagerName);
        $this->dm = $dm->create($dm->getPhpcrSession(),  $dm->getConfiguration(), $dm->getEventManager());
        $this->dm->setLocaleChooserStrategy($localeChooser);
        $this->logger = $logger;
	}
	
	public function createSeo($basename, $seoid, $uri, $title, $keywords = false, $description = false){
		try {
			
			$parent = $this->dm->find(null, $basename);
			$seoitem = $this->dm->find(null, "$basename/$seoid");
			
			if (!$parent) {
				NodeHelper::createPath($this->dm->getPhpcrSession(), $basename);
				$parent = $this->dm->find(null, $basename);
			}
			
			$seoitem = ($seoitem instanceof SeoNode ) ? $seoitem : new SeoNode();
			
			
			$seoitem->setParentDocument($parent);
			$seoitem->setName($seoid);
			$this->dm->persist($seoitem);
			
			$this->fixUriException();
			$seoitem->setUri($uri);
			$seoitem->setTitle($title);
					
			if($keywords)$seoitem->setKeywords($keywords);
			if($description)$seoitem->setDescription($description);
			
			$this->dm->persist($seoitem);
			$this->dm->flush($seoitem);
			
			if( $this->logger ){
				$message = sprintf('Seo Node was created for %s/%s uri:%s, title:%s, keywords:%s, description:%s', $basename, $seoid, $uri, $title, $keywords, $description);
				$this->logger->info($message);
			}
			
			return $seoitem;
			
		}catch(\Exception $e){
				
			if( $this->logger ){
				$message = sprintf("The class %s, The method %s, Exception %s", __CLASS__, __FUNCTION__, $e->getMessage() );
				$this->logger->error($message);
			}
				
		}
		
	}
	
	public function move($source, $dest){
		try {
			
			$block = $this->dm->find(null, $source);
			$this->dm->move($block, $dest);
			$this->dm->flush($block);
			
			if( $this->logger ){
				$message = sprintf('Seo Node was moved From: %s TO: %s', $source, $dest);
				$this->logger->info($message);
			}
			
		}catch(\Exception $e){
		
			if( $this->logger ){
				$message = sprintf("The class %s, The method %s, Exception %s", __CLASS__, __FUNCTION__, $e->getMessage() );
				$this->logger->error($message);
			}
		
		}
	}
	
	public function remove($source){
		try{
			
			$block = $this->dm->find(null, $source);
			
			if( !$block && $this->logger ){
				$message = sprintf("Trying to remove non-existent Seo Node source %s, class %s, method %s ", $source, __CLASS__, __FUNCTION__);
				$this->logger->error($message);
			}
			
			if($block instanceof SeoNode){
				$this->dm->remove($block);
				$this->dm->flush();
				
				if( $this->logger ){
					$message = sprintf('Seo Node was removed %s', $source);
					$this->logger->info($message);
				}
				
			}
			
		}catch(\Exception $e){
		
			if( $this->logger ){
				$message = sprintf("The class %s, The method %s, Exception %s", __CLASS__, __FUNCTION__, $e->getMessage() );
				$this->logger->error($message);
			}
		
		}
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
