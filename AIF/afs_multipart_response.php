<?php
/** @file afs_multipart_response.php */


/** @brief Class for AFS layers.
 *
 * This class provided basic methods to manage layers. */
class AfsLayer
{
    private $headers;
    private $content;

    /**@brief Constructs an object AfsLayer
     * @param $content Content of the layer
     * @param $headers Headers associated with the layer
     * @throws InvalidArgumentException invalid headers provided
     */
    public function __construct($content, $headers)
    {
        //Get rid of last \n in contents
        $this->content = strrev(preg_replace("#\n#", "", strrev($content), 1));
        if (is_null($headers) or empty($headers)) {
            throw new InvalidArgumentException("Headers should not be empty.");
        }
        $this->headers = $headers;
    }

    /**@brief Serialize content of layer
     * @return mixed content of layer as a string
     */
    public function __toString()
    {
        return $this->content;
    }

    /**@brief Get mime type of the layer
     * @return mixed Returns the mime type. Convenience method equivalent to get_header('Content-Type')
     */
    public function get_mime_type()
    {
        return $this->get_header('Content-Type');
    }

    /**@brief Get content of the layer
     * @return mixed Returns the content of the layer.
     */
    public function get_content()
    {
        return $this->content;
    }

    /**@brief Get header '$header' value
     * @param $header Requested header
     */
    public function get_header($header)
    {
        if (array_key_exists($header, $this->headers)) {
            return $this->headers[$header];
        }
        throw new InvalidArgumentException("Header $header doesn't exist.");
    }
}

/** @brief Class to handle multipart responses.
 *
 * This class provides methods to handle multipart responses. */
class AfsMultipartResponse {
    /**
     * @var array Field used to store received layers
     */
    private $layers = array();

    /**
     * @brief Parses header and seek to contents
     * @param $response string to parse
     * @return array headers in associative array
     * @throws AfsBOWSInvalidReplyException Malformed header was received
     */
    private function parse_headers(&$response)
    {
        $headers = array();
        $lines = explode("\n", $response);
        // Get rid of first empty line
        if (trim($lines[0]) === "")
            $lines = array_slice($lines, 1);
        $count = 0;
        foreach ($lines as &$line) {
            $count++;
            $line = trim($line);
            if (empty($line)) {
                break;
            }
            $header = explode(':', $line, 2);
            if (count($header) < 2) {
                throw new AfsBOWSInvalidReplyException("Couldn't parse headers : '$line'.");
            }
            $key = trim($header[0]);
            $value = trim($header[1]);
            if (array_key_exists($key, $headers)) {
                $headers[$key] .= ", $value";
            } else {
                $headers[$key] = $value;
            }
        }
        $response = implode("\n", array_slice($lines, $count));
        return $headers;
    }

    /** @brief Constructs an AfsMultipartResponse
     *  @param $res Server response to parse
     */
    public function __construct($res) {
        //Get multipart separator and split response
        $sep = strtok($res, "\r");
        $parts = explode($sep, $res);
        //Response is multipart
        if (count($parts) > 2) {
            /*Remove first and last element as they do not contain anything useful and make sure they're compliant with
              standard multipart response */
            if(trim(array_shift($parts)) !== "" || trim(array_pop($parts)) !== "--" ) {
                throw new AfsBOWSInvalidReplyException("Error parsing multipart response.");
            }
        //Response is singlepart
        } else {
            $parts = array($res);
        }
        //Parse all parts
        foreach($parts as $part) {
            $headers = $this->parse_headers($part);
            $matches = array();
            //Look for layer name
            preg_match("/name=\"urn:afs:[a-z0-9\-]*#([A-Z]+\_?[0-9]*)/", $headers['Content-Disposition'], $matches);
            if (! isset($matches[1])) {
                throw new AfsBOWSInvalidReplyException("Could not parse layer name.");
            }
            $layer_name = $matches[1];
            $this->layers[$layer_name] = new AfsLayer(preg_replace("/\r/m", "", $part), $headers);
        }
    }

    /**
     * @brief Get layer $layer_name
     * @param $layer_name The name of the layer
     * @return AfsLayer layer $layer_name
     * @throws InvalidArgumentException non-existant layer name provided
     */
    public function get_layer($layer_name) {
        if (! array_key_exists($layer_name, $this->layers)) {
            throw new InvalidArgumentException("Layer $layer_name does not exist.");
        }
        return $this->layers[$layer_name];
    }

    /**
     * @brief Get all layers
     * @return array of AfsLayer containing all layers
     */
    public function get_layers() {
        return $this->layers;
    }
} 