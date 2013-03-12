<?php
namespace RC\PHPCRSeoBundle\Services;

use Symfony\Component\Yaml\Parser;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\BlockBundle\Block\BaseBlockService;

class SeoBlockInterfaceService extends BaseBlockService{
	
	protected $container;
	public function __construct($name, $templating, ContainerInterface $container){
		parent::__construct($name, $templating);
		$this->container = $container;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function execute(BlockInterface $block, Response $response = null)	{
		return $this->renderResponse('RCPHPCRSeoBundle:Default:index.html.twig', array('block' => $block ) );
	}
	
	public function getSettings($block){
// 		if(is_array($block->getSettings())){
// 			return array_merge($this->getDefaultSettings(), $block->getSettings());
// 		}
// 		return $this->getDefaultSettings();
	
	}
	
	
	/**
	 * {@inheritdoc}
	 */
	public function validateBlock(ErrorElement $errorElement, BlockInterface $block)
	{
		// TODO: Implement validateBlock() method.
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
	{
// 		$formMapper->add('settings', 'sonata_type_immutable_array', array(
// 				'keys' => array(
// 						array('content', 'textarea', array()),
// 				)
// 		));
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return 'Name';
	}
	
	/**
	 * {@inheritdoc}
	 */
	function getDefaultSettings()
	{
		return array(
				'content' => 'CustomContent',
		);
	}
	public function getCacheKeys(BlockInterface $block)
	{
		return array(
				'name'       => $this->getName(),
				'block_id'   => $block->getId()
		);
	}
}
