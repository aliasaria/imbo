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
 * @package Resources
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @copyright Copyright (c) 2011-2012, Christer Edvartsen <cogo@starzinger.net>
 * @license http://www.opensource.org/licenses/mit-license MIT License
 * @link https://github.com/imbo/imbo
 */

namespace Imbo\Resource;

use Imbo\Http\Request\RequestInterface,
    Imbo\EventManager\EventInterface,
    Imbo\EventListener\ListenerDefinition,
    Imbo\EventListener\ListenerInterface,
    Imbo\Container,
    Imbo\ContainerAware,
    DateTime;

/**
 * Status resource
 *
 * This resource can be used to monitor the imbo installation to see if it has access to the
 * current database and storage.
 *
 * @package Resources
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @copyright Copyright (c) 2011-2012, Christer Edvartsen <cogo@starzinger.net>
 * @license http://www.opensource.org/licenses/mit-license MIT License
 * @link https://github.com/imbo/imbo
 */
class Status implements ContainerAware, ResourceInterface, ListenerInterface {
    /**
     * Service container
     *
     * @var Container
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(Container $container) {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedMethods() {
        return array(
            RequestInterface::METHOD_GET,
            RequestInterface::METHOD_HEAD,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition() {
        return array(
            new ListenerDefinition('status.get', array($this, 'get')),
            new ListenerDefinition('status.head', array($this, 'head')),
        );
    }

    /**
     * Handle GET requests
     *
     * @param EventInterface $event The current event
     */
    public function get(EventInterface $event) {
        $response = $event->getResponse();
        $database = $event->getDatabase();
        $storage = $event->getStorage();

        $response->getHeaders()->set('Cache-Control', 'max-age=0');

        $databaseStatus = $database->getStatus();
        $storageStatus = $storage->getStatus();

        if (!$databaseStatus || !$storageStatus) {
            if (!$databaseStatus && !$storageStatus) {
                $message = 'Database and storage error';
            } else if (!$storageStatus) {
                $message = 'Storage error';
            } else {
                $message = 'Database error';
            }

            $response->setStatusCode(500)
                     ->setStatusMessage($message);
        }

        $response->setBody(array(
            'date'     => $this->container->get('dateFormatter')->formatDate(new DateTime()),
            'database' => $databaseStatus,
            'storage'  => $storageStatus,
        ));
    }

    /**
     * Handle HEAD requests
     *
     * @param EventInterface $event The current event
     */
    public function head(EventInterface $event) {
        $this->get($event);

        // Remove body from the response, but keep everything else
        $event->getResponse()->setBody(null);
    }
}
