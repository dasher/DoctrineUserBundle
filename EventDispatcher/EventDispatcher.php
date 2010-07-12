<?php

namespace Bundle\DoctrineUserBundle\EventDispatcher;

use Symfony\Foundation\EventDispatcher as BaseEventDispatcher;

class EventDispatcher extends BaseEventDispatcher {
	
	const EVENT_POS_LAST = 0;
	const EVENT_POS_FIRST = 1;
	
    /**
     * Connects a listener to a given event name.
     *
     * @param string  $name      An event name
     * @param mixed   $listener  A PHP callable
     */
    public function connect($name, $listener, $position = self::EVENT_POS_LAST)
    {    	
    	if ($position == self::EVENT_POS_LAST) {
    		parent::connect($name, $listener);	
    	} else {
	        if (!isset($this->listeners[$name])) {
	            $this->listeners[$name] = array();
	        }
	        
	        array_unshift($this->listeners[$name],$listener);
    	}    	
    }

}

?>