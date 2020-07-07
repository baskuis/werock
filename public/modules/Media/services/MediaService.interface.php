<?php

interface MediaServiceInterface {

    /**
     * @param MediaCreateObject $MediaObject
     * @return MediaObject
     */
    public function create(MediaCreateObject $MediaObject);
    public function captureUrl($url = null);
    public function getById($id = null);
    public function get(MediaRequestObject $MediaObject);
    public function update(MediaObject $MediaObject);
    public function delete(MediaObject $MediaObject);
    public function stream(MediaRequestObject $MediaRequestObject);

}