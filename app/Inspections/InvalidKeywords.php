<?php


namespace App\Inspections;


use Exception;

class InvalidKeywords
{
    /**
     * All registered invalid keywords.
     *
     * @var array
     */
    protected $keywords = [
        'Yahoo Customer Support'
    ];

    /**
     * Detect spam.
     *
     * @param string $body
     * @throws Exception
     */
    public function detect(string $body)
    {
        foreach ($this->keywords as $keyword) {
            if (stripos($body, $keyword) !== false) {
                throw new Exception('Your reply contains spam.');
            }
        }
    }
}
