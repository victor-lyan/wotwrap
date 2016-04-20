<?php
namespace WotWrap;

class Response
{
    /**
     * The content of the response.
     *
     * @var string
     */
    protected $content;

    /**
     * @var
     */
    protected $decodedContent;

    /**
     * The HTTP code of the response
     *
     * @var integer
     */
    protected $code;

    /**
     * The status of the response
     * 
     * @var string
     */
    protected $status;

    /**
     * The error array of the response 
     * Can contain following keys: code, message, field, value
     * 
     * @var null
     */
    protected $errorArray = [];

    /**
     * The primary content of the response.
     *
     * @param string $content
     * @param int $code
     */
    public function __construct($content, $code)
    {
        $this->content = trim($content);
        $this->code    = intval($code);
        $this->decodedContent = json_decode($content, true);
        if (array_key_exists('error', $this->decodedContent) && $this->decodedContent['error'] !== null) {
            $this->errorArray = $this->decodedContent['error'];
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->content;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return array
     */
    public function getErrorArray()
    {
        return $this->errorArray;
    }

    /**
     * @return mixed
     */
    public function getDecodedContent()
    {
        return $this->decodedContent;
    }
}
