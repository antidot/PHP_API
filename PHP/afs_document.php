<?php

/** @brief Represent a document.
 *
 * This document can be sent to Antidot component such as PaF through Antidot
 * Back Office APIs.*/
class AfsDocument
{
    private $filename = null;
    private $data = null;
    private $mime_type = null;
    private $temp_file = null;

    /** @brief Construct new document instance.
     *
     * Newly created document can be empty and filled later on, or filled with
     * appropriate content.
     *
     * @remark It is hardly recommended to set appropriate @a mime_type value.
     * Autodetection does not perform well for JSON documents, XML documents
     * whithout header and probably other types of documents.
     *
     * @param $data [in] string corresponding to the content of the created
     *        document (default: null, document is not initialized).
     * @param $mime_type [in] mime-type of the document (default: null). If not
     *        set mime-type will be automatically detected.
     *
     * @exception InvalidArgumentException when data is not a string.
     */
    public function __construct($data=null, $mime_type=null)
    {
        if (! is_null($data)) {
            $this->set_content($data, $mime_type);
        }
    }

    /** @brief Define document content and mime-type.
     *
     * @param $data [in] new document content.
     * @param $mime_type [in] mime-type of the document (default: null). If not
     *        set mime-type will be automatically detected.
     *
     * @exception InvalidArgumentException when data is not a string.
     */
    public function set_content($data, $mime_type=null)
    {
        $this->clean_up();
        if (! is_string($data)) {
            throw new InvalidArgumentException('Provided data is not of string type: '
                . gettype($data));
        }
        $this->data = $data;
        if (is_null($mime_type)) {
            $magic = new finfo(FILEINFO_MIME_TYPE);
            $this->mime_type = $magic->buffer(substr($data, 0, 2048));
        } else {
            $this->mime_type = $mime_type;
        }
    }

    /** @brief Define document content for existing file and mime-type.
     *
     * @param $filename [in] Name of the file to use as document.
     * @param $mime_type [in] mime-type of the document (default: null). If not
     *        set mime-type will be automatically detected.
     *
     * @exception RuntimeException when provided @a filename does not exist.
     */
    public function set_content_from_file($filename, $mime_type=null)
    {
        $this->clean_up();
        if (! file_exists($filename)) {
            throw new RuntimeException('Cannot define document using '
                . 'unexisting file: ' . $filename);
        }
        if (is_null($mime_type)) {
            $magic = new finfo(FILEINFO_MIME_TYPE);
            $this->mime_type = $magic->file($filename);
        } else {
            $this->mime_type = $mime_type;
        }
        $this->filename = $filename;
    }

    /** @brief Check whether document is valid.
     * @return true when the document is valid, false otherwise.
     */
    public function is_valid()
    {
        return ! is_null($this->data) || ! is_null($this->filename);
    }

    /** @brief Retrieve document filename.
     *
     * When the document is initialized with string data, temporary file is
     * created. It is automatically removed at the end of the script.
     * @return document filename.
     */
    public function get_filename()
    {
        if (is_null($this->filename)) {
            $this->temp_file = tmpfile();
            if ($this->temp_file === false) {
                throw new Exception('Cannot create temporary file');
            }
            $meta = stream_get_meta_data($this->temp_file);
            $this->filename = $meta['uri'];
            if (fwrite($this->temp_file, $this->data) != strlen($this->data)) {
                throw new Exception('Cannot write in temporary file');
            }
            fflush($this->temp_file);
            fseek($this->temp_file, 0);
        }
        return $this->filename;
    }

    /** @brief Retrieve the mime-type of the document.
     * @return mime-type.
     */
    public function get_mime_type()
    {
        return $this->mime_type;
    }

    /** @internal
     * @brief Clean up internal data.
     */
    private function clean_up()
    {
        $this->data = null;
        $this->filename = null;
        if (! is_null($this->temp_file)) {
            fclose($this->temp_file);
            $this->temp_file = null;
        }
    }
}

?>
