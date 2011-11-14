<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Olivier Chauvel <olivier@generation-multiple.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Genemu\Bundle\FormBundle\Form\EventListener;

use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Event\DataEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Adds a protocol to a URL if it doesn't already have one.
 *
 * @author Bernhard Schussek <bernhard.schussek@symfony-project.com>
 */
class JQueryFileListener implements EventSubscriberInterface
{
    protected $rootDir;
    protected $multiple;

    /**
     * Construct
     *
     * @param string  $rootDir
     * @param boolean $multiple
     */
    public function __construct($rootDir, $multiple = false)
    {
        $this->rootDir = $rootDir;
        $this->multiple = $multiple;
    }

    /**
     * {@inheritdoc}
     */
    public function onBindNormData(DataEvent $event)
    {
        $data = $event->getData();

        if (empty($data)) {
            return;
        }

        if ($this->multiple) {
            $files = explode(',', $data);

            $data = array();
            foreach ($files as $file) {
                $data[] = new File($this->rootDir . $this->stripQueryString($file));
            }

            $event->setData($data);
        } else {
            $event->setData(new File($this->rootDir . $this->stripQueryString($data)));
        }
    }

    /**
     * Delete info after `?`
     */
    protected function stripQueryString($file)
    {
        if (($pos = strpos($file, '?')) !== false) {
            $file = substr($file, 0, $pos);
        }

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    static public function getSubscribedEvents()
    {
        return array(FormEvents::BIND_NORM_DATA => 'onBindNormData');
    }
}
