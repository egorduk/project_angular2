<?php

/**
 * View that renders the result in json format
 */
class JsonView extends ApiView
{
    /**
     * Render content in json format
     *
     * @param  string $content - requested data
     * @return Json - Json representation of requested data
     */
    public function render($content)
    {
        header('Content-Type: application/json; charset=utf8');

        // JSON_PRETTY_PRINT is available from PHP 5.4.0
        //if (PHP_VERSION >= "5.4.0")
        //    echo json_encode($content, JSON_PRETTY_PRINT);
        //else
            echo json_encode($content);

        return true;
    }
}