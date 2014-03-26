<?php
/**
 * Created by PhpStorm.
 * User: agaillard
 * Date: 3/26/14
 * Time: 12:15 PM
 */

class AfsLayer
{
    private $mime;
    private $content;

    public function __construct($content, $mime)
    {
        $this->content = $content;
        $this->mime = $mime;
    }

    public function __toString()
    {
        return $this->content;
    }

    public function get_mime_type() {
        return $this->mime;
    }

    public function get_content() {
        return $this->content;
    }
}

class AfsMultipartResponse {
    private $layers = array();

    public function __construct($multipart) {
        foreach($multipart as $part) {
            $matches = array();
            preg_match("/name=\"urn:afs:[0-9\-a-f]*#([A-Z\_0-9]+)/", $part, $matches);
            $layer_name = $matches[1];
            $matches = array();
            preg_match("/Content-Type: ([a-zA-Z0-9\-]+\/[a-zA-Z0-9\-]+)/", $part, $matches);
            $mime = $matches[1];
            $content = preg_replace("/^(.*\r\n){4}/", '', $part);
            $this->layers[$layer_name] = new AfsLayer($content, $mime);
        }
    }

    public function get_layer($layer_name) {
        if (! array_key_exists($layer_name, $this->layers)) {
            throw new BadMethodCallException("Layer $layer_name does not exist.");
        }
        return $this->layers[$layer_name];
    }

    public function get_layers() {
        return $this->layers;
    }
} 