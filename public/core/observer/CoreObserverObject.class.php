<?php

/**
 * This static object will keep track of event subscriptions
 * and will fire off events/listeners when the event is dispatched
 *
 * @author WeRock (bkuis)
 */
class CoreObserverObject {

    private $event;
    private $object;
    private $method;

    /**
     * Build core observer object
     *
     * @param $event
     * @param $object
     * @param $method
     * @return CoreObserverObject
     */
    public static function build($event, $object, $method){
        return new CoreObserverObject($event, $object, $method);
    }

    /**
     * Build an observer
     *
     * @param $event
     * @param $object
     * @param $method
     */
    function __construct($event, $object, $method){
        $this->event = $event;
        $this->object = $object;
        $this->method = $method;
    }

    /**
     * @return mixed
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @return mixed
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

}