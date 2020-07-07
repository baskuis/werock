<?php

class SearchSuggestionsSuggestionObject {

    public $original;
    public $text;
    public $count;
    public $found;
    public $search;
    public $slug;
    public $attributeText;
    public $color;

    /** @var bool $personalized */
    public $personalized;

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text)
    {
        $this->slug = CoreStringUtils::url($this->original = $this->text = $text);
        $this->attributeText = str_replace(array('\'', '"', '\\'), array('', '', ''), $text);
        $this->color = CoreColorUtils::getColor($text);
    }

    /**
     * @return mixed
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param mixed $count
     */
    public function setCount($count)
    {
        $this->count = $count;
    }

    /**
     * @return mixed
     */
    public function getFound()
    {
        return $this->found;
    }

    /**
     * @param mixed $found
     */
    public function setFound($found)
    {
        $this->found = $found;
    }

    /**
     * @return mixed
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * @param mixed $search
     */
    public function setSearch($search)
    {

        $this->search = $search;

        $pieces = explode(' ', $this->search);
        array_unique($pieces);

        //the tokens
        $tokens = array(
            '|/|' => '<strong>',
            '|\\|' => '</strong>'
        );

        //make sure tokens don't exist in string
        $this->text = str_replace(array_keys($tokens), null, $this->text);

        //mark replacements
        foreach($pieces as $s){
            if(strpos($s, '|') !== false) continue;
            if(strpos($s, '\\') !== false) continue;
            if(strpos($s, '/') !== false) continue;
            $this->text = str_ireplace($s, '|/|' . $s . '|\\|', $this->text);
        }

        //replacements
        $this->text = strtolower(trim(str_replace(array_keys($tokens), array_values($tokens), $this->text)));

    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * Allows comparing
     *
     * @return string
     */
    function __toString(){
        return $this->original;
    }

    /**
     * @return mixed
     */
    public function getOriginal()
    {
        return $this->original;
    }

    /**
     * @param mixed $original
     */
    public function setOriginal($original)
    {
        $this->original = $original;
    }

    /**
     * @return boolean
     */
    public function isPersonalized()
    {
        return $this->personalized;
    }

    /**
     * @param boolean $personalized
     */
    public function setPersonalized($personalized)
    {
        $this->personalized = $personalized;
    }

    /**
     * @return mixed
     */
    public function getAttributeText()
    {
        return $this->attributeText;
    }

    /**
     * @param mixed $attributeText
     */
    public function setAttributeText($attributeText)
    {
        $this->attributeText = $attributeText;
    }

}