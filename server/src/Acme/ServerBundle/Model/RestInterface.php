<?php

namespace Acme\ServerBundle\Model;

Interface RestInterface
{
    /**
     * Set title
     *
     * @param string $title
     * @return RestInterface
     */
    public function setTitle($title);

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle();

    /**
     * Set body
     *
     * @param string $body
     * @return RestInterface
     */
    public function setBody($body);

    /**
     * Get body
     *
     * @return string 
     */
    public function getBody();
}
