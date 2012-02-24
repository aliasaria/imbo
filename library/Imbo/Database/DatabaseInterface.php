<?php
/**
 * Imbo
 *
 * Copyright (c) 2011-2012, Christer Edvartsen <cogo@starzinger.net>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * * The above copyright notice and this permission notice shall be included in
 *   all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 *
 * @package Interfaces
 * @subpackage Database
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @copyright Copyright (c) 2011-2012, Christer Edvartsen <cogo@starzinger.net>
 * @license http://www.opensource.org/licenses/mit-license MIT License
 * @link https://github.com/imbo/imbo
 */

namespace Imbo\Database;

use Imbo\Image\ImageInterface,
    Imbo\Resource\Images\QueryInterface;

/**
 * Database driver interface
 *
 * This is an interface for different database drivers.
 *
 * @package Interfaces
 * @subpackage Database
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @copyright Copyright (c) 2011-2012, Christer Edvartsen <cogo@starzinger.net>
 * @license http://www.opensource.org/licenses/mit-license MIT License
 * @link https://github.com/imbo/imbo
 */
interface DatabaseInterface {
    /**
     * Insert a new image
     *
     * This method will insert a new image into the database
     *
     * @param string $publicKey The public key of the user
     * @param string $imageIdentifier Image identifier
     * @param Imbo\Image\ImageInterface $image The image to insert
     * @return boolean Returns true on success or false on failure
     * @throws Imbo\Exception\DatabaseException
     */
    function insertImage($publicKey, $imageIdentifier, ImageInterface $image);

    /**
     * Delete an image from the database
     *
     * @param string $publicKey The public key of the user
     * @param string $imageIdentifier Image identifier
     * @return boolean Returns true on success or false on failure
     * @throws Imbo\Exception\DatabaseException
     */
    function deleteImage($publicKey, $imageIdentifier);

    /**
     * Edit metadata
     *
     * @param string $publicKey The public key of the user
     * @param string $imageIdentifier Image identifier
     * @param array $metadata An array with metadata
     * @return boolean Returns true on success or false on failure
     * @throws Imbo\Exception\DatabaseException
     */
    function updateMetadata($publicKey, $imageIdentifier, array $metadata);

    /**
     * Get all metadata associated with an image
     *
     * @param string $publicKey The public key of the user
     * @param string $imageIdentifier Image identifier
     * @return array Returns the metadata as an array
     * @throws Imbo\Exception\DatabaseException
     */
    function getMetadata($publicKey, $imageIdentifier);

    /**
     * Delete all metadata associated with an image
     *
     * @param string $publicKey The public key of the user
     * @param string $imageIdentifier Image identifier
     * @return boolean Returns true on success or false on failure
     * @throws Imbo\Exception\DatabaseException
     */
    function deleteMetadata($publicKey, $imageIdentifier);

    /**
     * Get images based on some query parameters
     *
     * @param string $publicKey The public key of the user
     * @param Imbo\Resource\Images\Query
     * @return array
     * @throws Imbo\Exception\DatabaseException
     */
    function getImages($publicKey, QueryInterface $query);

    /**
     * Load information from database into the image object
     *
     * @param string $publicKey The public key of the user
     * @param string $imageIdentifier The image identifier
     * @param Imbo\Image\ImageInterface $image The image object to populate
     * @return boolean
     * @throws Imbo\Exception\DatabaseException
     */
    function load($publicKey, $imageIdentifier, ImageInterface $image);

    /**
     * Get the last modified timestamp of a user
     *
     * If the $imageIdentifier parameter is set, return when that image was last updated. If not
     * set, return when the user last updated any image. If the use does not any images return the
     * current date.
     *
     * @param string $publicKey The public key of the user
     * @param string $imageIdentifier The image identifier
     * @param boolean $formatted Wether or not to format the date as an HTTP date
     * @return DateTime|string If $formatted is true this method should return the timestamp as a
     *                         valid HTTP date. If not, return the DateTime instance
     * @throws Imbo\Exception\DatabaseException
     */
    function getLastModified($publicKey, $imageIdentifier = null, $formatted = false);

    /**
     * Fetch the number of images owned by a given user
     *
     * @param string $publicKey The public key of the user
     * @return int Returns the number of images
     * @throws Imbo\Exception\DatabaseException
     */
    function getNumImages($publicKey);
}
