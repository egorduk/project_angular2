<?php

namespace Acme\ServerBundle\Handler;

use Acme\ServerBundle\Entity\Picture;
use Acme\ServerBundle\Model\RestInterface;

interface RestHandlerInterface
{
    /**
     * Get a Page given the identifier
     *
     * @api
     *
     * @param mixed $id
     *
     * @return RestInterface
     */
    public function get($id);

    /**
     * Get a list of Pages.
     *
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 5, $offset = 0);

    /**
     * Post Page, creates a new Page.
     *
     * @api
     *
     * @param array $parameters
     *
     * @return RestInterface
     */
    public function post(array $parameters);

    /**
     * Edit a Page.
     *
     * @api
     *
     * @param RestInterface   $page
     * @param array           $parameters
     *
     * @return RestInterface
     */
    public function put(Picture $picture, array $parameters);

    /**
     * Partially update a Page.
     *
     * @api
     *
     * @param RestInterface   $page
     * @param array           $parameters
     *
     * @return RestInterface
     */
    public function patch(Picture $picture, array $parameters);

    /**
     * Delete a picture by it's id
     *
     * @api
     *
     * @param mixed $id
     *
     * @return RestInterface
     */
    public function delete($id);
}