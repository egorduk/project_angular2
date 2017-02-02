<?php

namespace Acme\ServerBundle\Helper;

use Acme\ServerBundle\Model\RestEntityInterface;

interface RestHelperInterface
{
    /**
     * Get the item
     *
     * @api
     *
     * @param int $id
     *
     * @return RestEntityInterface
     */
    public function get($id);

    /**
     * Get a list of items
     *
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 5, $offset = 0);

    /**
     * Create new item
     *
     * @api
     *
     * @param array $parameters
     *
     * @return RestEntityInterface
     */
    public function post(array $parameters);

    /**
     * Edit an item
     *
     * @api
     *
     * @param RestEntityInterface   $obj
     * @param array                 $parameters
     *
     * @return RestEntityInterface
     */
    public function put(RestEntityInterface $obj, array $parameters);

    /**
     * Partially update the item
     *
     * @api
     *
     * @param RestEntityInterface   $obj
     * @param array                 $parameters
     *
     * @return RestEntityInterface
     */
    public function patch(RestEntityInterface $obj, array $parameters);

    /**
     * Delete the item
     *
     * @api
     *
     * @param RestEntityInterface $obj
     *
     * @return bool
     */
    public function delete(RestEntityInterface $obj);
}