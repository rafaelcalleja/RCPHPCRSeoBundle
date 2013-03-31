<?php 
namespace RC\PHPCRSeoBundle\EventListener;

use RC\PHPCRRouteEventsBundle\Events\RouteDataEvent;
use RC\PHPCRRouteEventsBundle\Events\RouteMoveEventsData;
use RC\PHPCRRouteEventsBundle\Events\RouteFlushDataEvent;
use Doctrine\ODM\PHPCR\Event\LifecycleEventArgs;

class RouteListener {
	protected $routebase, $seobase, $seoservice;
	protected $preedited = false;
	protected $pre_event;
	
	public function __construct($seoservice, $routebase, $seobase){
		$this->seoservice = $seoservice;
		$this->seobase = $seobase;
		$this->routebase = $routebase;	
	}
	
	
	
	protected function newSource($event){
		return str_replace( $this->routebase, $this->seobase, $event->getSource());
	}
	
	protected function newDest($event){
		return str_replace( $this->routebase, $this->seobase, $event->getDest());
	}
	
	protected function getId(RouteDataEvent $event){
		return str_replace( $this->routebase, $this->seobase, $event->getId());
	}
	
	protected function getName(RouteDataEvent $event){
		if( $event->getId() === $this->routebase ) return basename($this->seobase);
		return basename($event->getId());
	}
	
	protected function getParentId(RouteDataEvent $event){
		return dirname($this->getId($event));
	}
	
	public function onRouteAdded(RouteDataEvent $event){
		//TODO Auto detect routebase ¿PROVIDER?
		$basename = $this->getParentId($event);
		//FIX
		if( strpos($basename, $this->seobase) === FALSE )return false;
		
		$name = $this->getName($event);
		$label = $event->getLabel();
		$uri = $event->getPath();
		$this->seoservice->createSeo($basename, $name, $uri, $label);
		
	}

	
	public function onRouteMoved(RouteMoveEventsData $event){
		$this->seoservice->move($this->newSource($event), $this->newDest($event));
 		
	}
	
	public function onRouteRemoved(RouteFlushDataEvent $event){
		$documents = $event->getRemoved();
		foreach($documents as $d){
			$newEvent = new RouteDataEvent(new LifecycleEventArgs($d, $event->getDocumentManager()));
			$basename = $this->getParentId($newEvent);
			$name = $this->getName($newEvent);
			$this->seoservice->remove("$basename/$name");
				
		}
// 		$basename = $this->getParentId($event);
// 		$name = $this->getName($event);
// 		$this->seoservice->remove("$basename/$name");
	}
	
	public function onRoutePreEdited(RouteDataEvent $event){
		
	}
	
	public function onRouteEdited(RouteDataEvent $event){
				
	}
	
}