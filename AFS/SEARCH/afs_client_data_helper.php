<?php
require_once "AFS/SEARCH/afs_text_visitor.php";
require_once "AFS/SEARCH/afs_client_data_exception.php";
require_once "COMMON/afs_helper_base.php";
require_once "COMMON/afs_tools.php";
require_once "COMMON/lib/JsonPath/JsonStore.php";
require_once "COMMON/lib/JsonPath/JsonPath.php";

use Peekmo\JsonPath\JsonStore;


/** @brief Manage client data.
 *
 * Instances of this class allow to manage one or more XML and JSON client
 * data.*/
class AfsClientDataManager
{
    private $client_data = array();

    /** @brief Construct new manager with all necessary client data helpers.
     *
     * One or more client data helper can be created and managed.
     * @param $client_datas [in] root of client data element.
     */
    public function __construct($client_datas)
    {
        foreach ($client_datas as $data) {
            $helper = AfsClientDataHelperFactory::create($data);
            $this->client_data[$helper->id] = $helper;
        }
    }

    /** @brief Retrieves client data helper.
     *
     * @param $id [in] Id of the client data to retrieve (default='main').
     * @return client data helper.
     *
     * @exception OutOfBoundsException when required client data is not found.
     */
    public function get_clientdata($id='main')
    {
        if (array_key_exists($id, $this->client_data)) {
            return $this->client_data[$id];
        } else {
            throw new OutOfBoundsException('No client data with id \'' . $id . '\' found.');
        }
    }

    /** @brief Retrieves value from the appropriate client data.
     *
     * @param $id [in] client data id.
     * @param $name [in] name or XPath of the required element for JSON
     *        respectively XML client data.
     * @param $context [in] context used to look for text with specified name.
     * @param $formatter [in] used for highlighted content (default=null,
     *        appropriate formatter is instanced for JSON and XML).
     *
     * @return client data as text.
     */
    public function get_value($id, $name=null, $context=array(), $formatter=null)
    {
        return $this->get_clientdata($id)->get_value($name, $context, $formatter);
    }

    /** @brief Retrieves value(s) from the appropriate client data.
     *
     * @param $id [in] client data id.
     * @param $name [in] name or XPath of the required element for JSON
     *        respectively XML client data.
     * @param $context [in] context used to look for text with specified name.
     * @param $formatter [in] used for highlighted content (default=null,
     *        appropriate formatter is instanced for JSON and XML).
     *
     * @return client data as text.
     */
    public function get_values($id, $name=null, $context=array(), $formatter=null)
    {
        return $this->get_clientdata($id)->get_values($name, $context, $formatter);
    }
}


/** @brief Client data interface. */
interface AfsClientDataHelperInterface
{
    /** @brief Retrieves client data as text.
     *
     * All client data or sub-tree can be retrieved depending on @a name
     * parameter.
     * @param $name [in] data name to be extracted (default=null, retrieve
     *        all client data).
     * @param $context [in] context used for looking for text with specified name.
     * @param $formatter [in] format output string. It is used when highlight in
     *        client data is activated. See implementation to provide
     *        appropriate formatter (default=null, default formatter is used).
     * @return first matching client data with specified name as text.
     */
    public function get_value($name=null, $context=array(), $formatter=null);

    /** @brief Retrieves client data as array of texts.
     *
     * All client data or sub-tree can be retrieved depending on @a name
     * parameter.
     * @param $name [in] data name to be extracted (default=null, retrieve
     *        all client data).
     * @param $context [in] context used for looking for text with specified name.
     * @param $formatter [in] format output string. It is used when highlight in
     *        client data is activated. See implementation to provide
     *        appropriate formatter (default=null, default formatter is used).
     * @return matching client data as array of texts.
     */
    public function get_values($name=null, $context=array(), $formatter=null);

    /**
     * @brief Retrieves full client data node (value and attributes)
     * @param $name [in] node name to be extracted
     * @param $context [in] context used for looking for node with specified name.
     * @return first occurrence of node as array
     */
    public function get_node($name=null, $context=array());

    /**
     * @brief Retrieves full client data nodes (value and attributes)
     * @param $name [in] node name to be extracted
     * @param $context [in] context used for looking for node with specified name.
     * @return all occurrences as array
     */
    public function get_nodes($name=null, $context=array());

    /** @brief Retrieve client data's mime type.
     *
     * @return mime type of the client data.
     */
    public function get_mime_type();
}

/** @brief Base class  for client data helpers. */
abstract class AfsClientDataHelperBase extends AfsHelperBase
{
    private $id = null;

    /** @brief Construct base class instance.
     * @param $client_data [in] client data used to retrieve the right id.
     */
    public function __construct($client_data)
    {
        $this->id = $client_data->id;
    }

    /** @brief Retrieve client data id.
     * @return id associated to client data.
     */
    public function get_id()
    {
        return $this->id;
    }
}


/** @brief Factory for client data helper. */
class AfsClientDataHelperFactory
{
    /** @brief Create appropriate client data helper.
     * @param $client_data [in] client data entry point.
     * @return appropriate client data helper.
     * @exception Exception invalid @a client_data parameter provided
     */
    public static function create($client_data)
    {
        if (! property_exists($client_data, 'mimeType')) {
            throw new Exception('No mime-type available for provided client data.');
        } elseif (! property_exists($client_data, 'contents')) {
            throw new Exception('No content available for provided client data.');
        } elseif ($client_data->mimeType == 'text/xml'
            || $client_data->mimeType == 'application/xml') {
            return new AfsXmlClientDataHelper($client_data);
        } elseif ($client_data->mimeType == 'text/json'
            || $client_data->mimeType == 'application/json') {
            return new AfsJsonClientDataHelper($client_data);
        } else {
            throw new Exception('Unmanaged client data type: ' . $client_data->mimeType);
        }
    }
}


/** @brief XML client data helper. */
class AfsXmlClientDataHelper extends AfsClientDataHelperBase implements AfsClientDataHelperInterface
{
    private $contents = null;
    private $doc = null;
    private $callbacks = array();   // callbacks activated on specific node name
    public static $afs_ns = 'http://ref.antidot.net/v7/afs#';

    /** @brief Construct new instance of XML helper.
     * @param $client_data [in] input data used to initialize the instance.
     */
    public function __construct($client_data)
    {
        parent::__construct($client_data);

        // Client data content is not XML valid when highlight is activated for
        // client data and a match occurs: no afs namespace prefix is defined!
        // No namespace declared for truncated client data...
        // So check whether afs prefix namespace is used
        $has_trunc = false;
        if (strpos($client_data->contents, '<afs:match>') !== false
                || strpos($client_data->contents, '<afs:trunc/>') !== false) {
            $this->contents = str_replace_first('>',
                ' xmlns:afs="' . AfsXmlClientDataHelper::$afs_ns . '">',
                $client_data->contents);
            $this->init_callbacks();
        } else {
            $this->contents = $client_data->contents;
        }

        $this->doc = new DOMDocument();
        $this->doc->loadXML($this->contents, LIBXML_NOBLANKS);
    }

    /** @brief Retrieves text from XML node.
     *
     * @remark @c afs prefix should never be used in provided XPath.
     *
     * @param $path [in] XPath to apply (default=null, retrieve all content as
     *        text). Has client data is override by the clientData node, all XPaths
     *        should begin with '/clientData/...'
     * @param $nsmap [in] prefix/uri mapping to use along with provided XPath.
     * @param $callbacks [in] list of callbacks to emphase text when highlight
     *        of client data is activated or when client data text is truncated.
     *        It should be list of @a FilterNode type
     *        (default=null, default instances of @a FilterNode are used).
     *
     * @return text of first specific node(s) depending on parameters.
     *
     * @exception AfsInvalidQueryException when provided XPath is invalid.
     * @exception AfsNoResultException when provided XPath returns no value/node.
     */
    public function get_value($path=null, $nsmap=array(), $callbacks=null)
    {
        if (is_null($path)) {
            return $this->contents;
        } else {

            $items = $this->apply_xpath($path, $nsmap);
            $named_callbacks = $this->update_callbacks(is_null($callbacks) ? array() : $callbacks);
            return DOMNodeHelper::get_text($items->item(0), $named_callbacks);
        }
    }

    /** @brief Retrieves array of texts from XML node.
     *
     * @remark @c afs prefix should never be used in provided XPath.
     *
     * @param $path [in] XPath to apply (default=null, retrieve all content as
     *        text).
     * @param $nsmap [in] prefix/uri mapping to use along with provided XPath.
     * @param $callbacks [in] list of callbacks to emphase text when highlight
     *        of client data is activated or when client data text is truncated.
     *        It should be list of @a FilterNode type
     *        (default=null, default instances of @a FilterNode are used).
     *
     * @return text of first specific node(s) depending on parameters.
     *
     * @exception AfsInvalidQueryException when provided XPath is invalid.
     * @exception AfsNoResultException when provided XPath returns no value/node.
     */
    public function get_values($path=null, $nsmap=array(), $callbacks=null)
    {
        if (is_null($path)) {
            return array($this->contents);
        } else {

            $items = $this->apply_xpath($path, $nsmap);
            $named_callbacks = $this->update_callbacks(is_null($callbacks) ? array() : $callbacks);
            $result = array();
            foreach ($items as $item) {
                $result[] = DOMNodeHelper::get_text($item, $named_callbacks);
            }
            return $result;
        }
    }

    /**
     * @brief Retrieves full client data node (value and attributes)
     * @param $xpath [in] node name to be extracted
     * @param $nsmap [in] namespace used for looking for node with specified name.
     * @return first occurrence of node as array
     * @throws AfsNoResultException
     *
     * @par Usage example:
     * Input XML client data:
     * @verbatim
     *  <clientData>
     *      <data1 attr='foo'>value1</data1>
     *      <data2><k>v</k></data2>
     *   </clientData>
     * @endverbatim
     *
     * Call to get_node() will return
     * @verbatim
        array   (
                    [clientData] => Array
                        (
                            [data1] => Array
                                (
                                    [attributes] => Array
                                        (
                                            [attr] => foo
                                        )

                                    [0] => value1
                                )

                            [data2] => Array
                                (
                                    [data1] => value2
                                )

                        )

                )
      @endverbatim
     *
     * Call to get_node('/clientData/data1') will return
     * @verbatim
        array (
            [data1] => Array
                (
                    [attributes] => Array
                        (
                            [attr] => foo
                        )

                    [0] => value1
                )
            )
      @endverbatim
     *
     * Call to get_node('//data1/\@attr') will return
     * @verbatim
        Array
            (
                [attr] => foo
            )
      @endverbatim
     */
    public function get_node($xpath=null, $context=array()) {
        $nodes =  $this->get_nodes($xpath, $context);

        if (is_array($nodes) && ! empty($nodes)){
            return $nodes[0];
        } else {
            throw new AfsNoResultException($xpath . ' not found in current client data');
        }
    }

    /**
     * @brief Retrieves full client data nodes (value and attributes)
     * @param $name [in] node name to be extracted
     * @param $nsmap [in] namespace used for looking for node with specified name.
     * @return all occurrences as array
     * @throws AfsNoResultException
     *
     * @par Usage example:
     * Input XML client data:
     * @verbatim
     *  <clientData>
     *      <data1 attr='foo'>value1</data1>
     *      <data2><data1>value2</data1></data2>
     *   </clientData>
     * @endverbatim
     *
     * Call to get_nodes() will return
     * @verbatim
        array
    (
        [0] => Array
            (
                [clientData] => Array
                    (
                        [data1] => Array
                            (
                                [attributes] => Array
                                    (
                                        [attr] => foo
                                    )

                                [0] => value1
                            )

                        [data2] => Array
                            (
                                [data1] => value2
                            )

                    )

            )

    )
      @endverbatim
     *
     * Call to get_nodes('//data1') will return
     * @verbatim
    array (
            array (
                [data1] => Array
                    (
                        [attributes] => Array
                            (
                                [attr] => foo
                            )
                        [0] => value1
                    )
                )
            array (
                [data1] => value2
            )
        )

      @endverbatim
     *
     * Call to get_nodes('//data1/@attr') will return
     * @verbatim
         Array
            (
                [attr] => foo
            )
      @endverbatim
     */
    public function get_nodes($xpath=null, $nsmap=array())
    {
        if (is_null($xpath) || $xpath === "") {
            $xpath = "/";
        }

        $items = $this->apply_xpath($xpath, $nsmap);
        if (empty($items)) {
            throw new AfsNoResultException($xpath . ' not found in current client data');
        } else {
            $result = array();
            foreach ($items as $item) {
                if ($item instanceof DOMDocument) {
                    $item = $item->documentElement;
                }
                $result[] = $this->nodeToArray($this->doc, $item);
            }
            return $result;
        }
    }

    /**
     * @internal
     * Returns an array representation of a DOMNode
     * Note, make sure to use the LIBXML_NOBLANKS flag when loading XML into the DOMDocument
     * @param DOMDocument $dom
     * @param DOMNode $node
     * @return array
     */
    private function nodeToArray( $dom, $node) {
        if(!is_a( $dom, 'DOMDocument' ) || !is_a( $node, 'DOMNode' )) {
            return false;
        }
        $array = false;
        $attributes = array();

        if( XML_TEXT_NODE == $node->nodeType ) {
            return $node->nodeValue;
        } elseif (XML_ATTRIBUTE_NODE == $node->nodeType) {
            $array[$node->localName] = $node->nodeValue;
            return $array;
        }


        if ($node->firstChild->nodeType == XML_TEXT_NODE) {
            foreach ($node->attributes as $attr) {
                $attributes[$attr->localName] = $attr->nodeValue;
            }
            if (! empty($attributes)) {
                $array[$node->localName]['attributes'] = $attributes;
                $array[$node->localName][] = $node->firstChild->nodeValue;
            } else {
                $array[$node->localName] = $node->firstChild->nodeValue;
            }
        } else {
            $array[$node->localName] = array();
            foreach ($node->childNodes as $childNode) {
                if (false !== ($a = self::nodeToArray($dom, $childNode))) {
                    $array[$node->localName] = $array[$node->localName] +  $a;
                }
            }
        }
        return $array;
    }

    /** @internal
     * @brief Retrieves values at given XPath or fails.
     *
     * @param $path [in] XPath to apply to the document.
     * @param $nsmap [in] Namespace mapping used along with XPath.
     *
     * @return List of matching results.
     *
     * @exception AfsInvalidQueryException provided XPath is invalid.
     * @exception AfsNoResultException provided XPath does not return any result.
     */
    private function apply_xpath($path, $nsmap)
    {
        $xpath = new DOMXPath($this->doc);
        if (! array_key_exists('afs', $nsmap)) {
            $nsmap['afs'] = AfsXmlClientDataHelper::$afs_ns;
        }
        foreach ($nsmap as $prefix => $namespace) {
            $xpath->registerNamespace($prefix, $namespace);
        }
        $result = $xpath->query($path);
        if (false === $result) {
            throw new AfsInvalidQueryException('Invalid XPath: ' . $path);
        } elseif ($result->length == 0) {
            throw new AfsNoResultException('No result available for: ' . $path);
        }

        return $result;
    }

    /** @brief Retrieve client data's mime type.
     *
     * @return application/xml mime type.
     */
    public function get_mime_type()
    {
        return 'application/xml';
    }

    private function init_callbacks()
    {
        $this->callbacks[] = new BoldFilterNode('match', AfsXmlClientDataHelper::$afs_ns);
        $this->callbacks[] = new TruncatedFilterNode('trunc', AfsXmlClientDataHelper::$afs_ns);
    }
    private function update_callbacks($callbacks)
    {
        if (empty($callbacks))
            $callbacks = $this->callbacks;
        return array(XML_ELEMENT_NODE => $callbacks);
    }
}


/** @brief Helper for client data in JSON format. */
class AfsJsonClientDataHelper extends AfsClientDataHelperBase implements AfsClientDataHelperInterface
{
    private $client_data;

    /** @brief Construct new instance of JSON helper.
     * @param $client_data  [in] input data used to initialize the instance.
     */
    public function __construct($client_data)
    {
        parent::__construct($client_data);

        $this->client_data = $client_data;
    }

    /** @brief Retrieves text from JSON content.
     *
     * @param $name [in] name of the first element to retrieve (default=null,
     *        all JSON content is returned as text). Empty string allows to
     *        retrieve text content correctly formatted when highlight is
     *        activated.
     * @param $unused Hum...
     * @param $visitor [in] instance of @a AfsTextVisitorInterface used to format
     *        appropriately text content when highlight has been activated
     *        (default=null, @a AfsTextVisitor is used).
     *
     * @return formatted text.
     *
     * @exception AfsNoResultException when required JSON element is not defined.
     *
     * @par Example with name=null:
     * Input JSON client data:
     * @verbatim
        {
            "clientData": [
                {
                    "contents": { "data": [ "afs:t": "KwicString", "text": "some text" ] },
                    "id": "data1",
                    "mimeType": "application/json"
                }
            ]
        }
    @endverbatim
     * Call to <tt>get_text(null)</tt> will return
     * @verbatim {"data":["afs:t":"KwicString","text":"some text"]}@endverbatim.
     *
     * @par Example with name='data':
     * Same input JSON as previous example:
     * @verbatim
        {
            "clientData": [
                {
                    "contents": { "data": [ "afs:t": "KwicString", "text": "some text" ] },
                    "id": "data1",
                    "mimeType": "application/json"
                }
            ]
        }
    @endverbatim
     * Call to <tt>get_text('data')</tt> will return
     * @verbatim some text @endverbatim.
     *
     * @par Example with name='':
     * Client data is a @em simple text:
     * @verbatim
     *  {
            "clientData": [
                {
                    "contents": [ { "afs:t": "KwicString", "text": "some text" } ],
                    "id": "data1",
                    "mimeType": "application/json"
                }
            ]
        }
        @endverbatim
     * Call to <tt>get_text('')</tt> will return
     * @verbatim some text @endverbatim.
     */
    public function get_value($path=null, $unused=array(), $visitor=null)
    {
        return $this->get_values($path, $unused, $visitor);
    }


    /** @brief Same result as @a get_value method
     *
     * @param $name [in] name of the first element to retrieve (default=null,
     *        all JSON content is returned as text). Empty string allows to
     *        retrieve text content correctly formatted when highlight is
     *        activated.
     * @param $unused Hum...
     * @param $visitor [in] instance of @a AfsTextVisitorInterface used to format
     *        appropriately text content when highlight has been activated
     *        (default=null, @a AfsTextVisitor is used).
     *
     * @return formatted text.
     *
     * @throws AfsNoResultException when required JSON element is not defined.
     */
    public function get_values($name=null, $unused=array(), $visitor=null)
    {
        if (is_null($visitor)) {
            $visitor = new AfsTextVisitor();
        }
        $contents = $this->client_data->contents;
        if (is_null($name)) {
            return json_encode($contents);
        } else {
            if (! is_array($contents)) {
                if (property_exists($contents, $name)) {
                    $text_mgr = new AfsTextManager($contents->$name);
                } else {
                    throw new AfsNoResultException('No client data content named: ' . $name);
                }
            } else {
                $text_mgr = new AfsTextManager($contents);
            }
            return $text_mgr->visit_text($visitor);
        }
    }

    /**
     * @brief Retrieves full client data node (not only text)
     * @param $jpath [in] node name to be extracted
     * @param $context [in] context used for looking for node with specified name.
     * @return all matching nodes as array (each node format like json_decode in array mode)
     * @throws AfsNoResultException
     */
    public function get_nodes($jpath=null, $unused=array()) {
        if (is_null($jpath) || $jpath === "") {
            // return json as array
            return array(json_decode(json_encode($this->client_data->contents), true));
        }

        $store = new JsonStore($this->client_data->contents);
        $result = $store->get($jpath, false);

        if (! empty($result)) {
            return $result;
        } else {
            throw new AfsNoResultException();
        }
    }

    /**
     * @brief Retrieves full client data node (not only text)
     * @param $jpath [in] node name to be extracted
     * @param $context [in] context used for looking for node with specified name.
     * @return first occurrence of node as array (node format like json_decode in array mode)
     * @throws AfsNoResultException
     */
    public function get_node($jpath=null, $unused=array())
    {
        $nodes = $this->get_nodes($jpath, $unused);

        if (is_array($nodes) && ! empty($nodes)) {
            return $nodes[0];
        } else {
            throw new AfsNoResultException('No result for: ' . $jpath);
        }
    }

    /** @brief Retrieve client data's mime type.
     *
     * @return application/json mime type.
     */
    public function get_mime_type()
    {
        return 'application/json';
    }
}


