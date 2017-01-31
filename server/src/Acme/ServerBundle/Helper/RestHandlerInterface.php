<?php

namespace Acme\ServerBundle\Helper;

use Acme\ServerBundle\Entity\Picture;
use Acme\ServerBundle\Model\RestInterface;

interface RestHandlerInterface
{
    /**
     * Get the item.
     *
     * @api
     *
     * @param mixed $id
     *
     * @return RestInterface
     */
    public function get($id);

    /**
     * Get a list of items.
     *
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 5, $offset = 0);

    /**
     * Create new item.
     *
     * @api
     *
     * @param array $parameters
     *
     * @return RestInterface
     */
    public function post(array $parameters);

    /**
     * Edit an item.
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
     * Partially update the item.
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
     * Delete the item by it's id
     *
     * @api
     *
     * @param mixed $id
     *
     * @return RestInterface
     */
    public function delete($id);
}