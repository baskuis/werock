<?php

class RedirectRulesService implements RedirectRulesServiceInterface {

    private $redirectRules = array();

    public function addRule(RedirectRulesRuleObject $redirectRulesRuleObject){
        array_push($this->redirectRules, $redirectRulesRuleObject);
    }

    public function getRules(){
        return $this->redirectRules;
    }

}