<?php

class RedirectRulesRuleObject {

    /** @var string $pattern */
    public $pattern;

    /** @var callable $matcher */
    public $matcher;

    /** @var string $match */
    public $matchUrl;

    /** @var string $target */
    public $target;

    /** @var callable $handler */
    public $handler;

    /**
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * @param string $pattern
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * @return callable
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @param callable $handler
     */
    public function setHandler($handler)
    {
        $this->handler = $handler;
    }

    /**
     * @return callable
     */
    public function getMatcher()
    {
        return $this->matcher;
    }

    /**
     * @param callable $matcher
     */
    public function setMatcher($matcher)
    {
        $this->matcher = $matcher;
    }

    /**
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param string $target
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    /**
     * @return string
     */
    public function getMatchUrl()
    {
        return $this->matchUrl;
    }

    /**
     * @param string $matchUrl
     */
    public function setMatchUrl($matchUrl)
    {
        $this->matchUrl = $matchUrl;
    }

    /**
     * Does this rule match this url?
     *
     * @param null $url
     * @return bool|int
     */
    public function match($url = null){

        /**
         * A custom handler
         */
        $matcher = $this->getMatcher();
        if(is_callable($matcher)){
            return $matcher($url);
        }

        /**
         * A pattern
         */
        if(!empty($this->pattern)){
            return preg_match($this->pattern, $url);
        }

        /**
         * An exact match
         */
        return ($url == $this->match);

    }

    /**
     * Handle this rule
     * First try to use the handler
     * otherwise return the target
     *
     * @param null $url
     * @return mixed
     */
    public function handle($url = null){

        /**
         * See if we have a custom handler
         */
        $handler = $this->getHandler();
        if(is_callable($handler)) {
            $handler($url);
            return;
        }

        /**
         * Or direct to target
         */
        CoreHeaders::setRedirect($this->target);

    }

}