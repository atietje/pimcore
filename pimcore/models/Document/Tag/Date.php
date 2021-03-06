<?php
/**
 * Pimcore
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @category   Pimcore
 * @package    Document
 * @copyright  Copyright (c) 2009-2016 pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace Pimcore\Model\Document\Tag;

use Pimcore\Model;
use Pimcore\Config;

class Date extends Model\Document\Tag
{

    /**
     * Contains the date
     *
     * @var \Zend_Date|\DateTime
     */
    public $date;


    /**
     * @see Document\Tag\TagInterface::getType
     * @return string
     */
    public function getType()
    {
        return "date";
    }

    /**
     * @see Document\Tag\TagInterface::getData
     * @return mixed
     */
    public function getData()
    {
        return $this->date;
    }

    /**
     * Converts the data so it's suitable for the editmode
     *
     * @return string
     */
    public function getDataEditmode()
    {
        if ($this->date) {
            return $this->date->getTimestamp();
        }

        return null;
    }

    /**
     * @see Document\Tag\TagInterface::frontend
     */
    public function frontend()
    {
        if (!isset($this->options["format"]) || !$this->options["format"]) {
            $this->options["format"] = \DateTime::ISO8601;
        }

        if ($this->date instanceof \Zend_Date) {
            return $this->date->toString($this->options["format"], "php");
        } elseif ($this->date instanceof \DateTime) {
            return $this->date->format($this->options["format"]);
        }
    }

    /**
     * @see Document\Tag::getDataForResource
     * @return int|null
     */
    public function getDataForResource()
    {
        $this->checkValidity();
        if ($this->date) {
            return $this->date->getTimestamp();
        }

        return null;
    }

    /**
     * @see Document\Tag\TagInterface::setDataFromResource
     * @param mixed $data
     * @return $this
     */
    public function setDataFromResource($data)
    {
        if ($data) {
            $this->setDateFromTimestamp($data);
        }

        return $this;
    }

    /**
     * @see Document\Tag\TagInterface::setDataFromEditmode
     * @param mixed $data
     * @return $this
     */
    public function setDataFromEditmode($data)
    {
        if (strlen($data) > 5) {
            $timestamp = strtotime($data);
            $this->setDateFromTimestamp($timestamp);
        }

        return $this;
    }

    /**
     * @return boolean
     */
    public function isEmpty()
    {
        if ($this->date) {
            return false;
        }
        return true;
    }

    /**
    * Receives a Webservice\Data\Document\Element from webservice import and fill the current tag's data
    *
    * @param  Model\Webservice\Data\Document\Element $wsElement
     * @param $idMapper
    * @throws \Exception
    */
    public function getFromWebserviceImport($wsElement, $idMapper = null)
    {
        if (!$wsElement or empty($wsElement->value)) {
            $this->date = null;
        } elseif (is_numeric($wsElement->value)) {
            $this->setDateFromTimestamp($wsElement->value);
        } else {
            throw new \Exception("cannot get document tag date from WS - invalid value [  ".$wsElement->value." ]");
        }
    }

    /**
     * Returns the current tag's data for web service export
     *
     * @abstract
     * @return array
     */
    public function getForWebserviceExport()
    {
        if ($this->date) {
            return $this->date->getTimestamp();
        } else {
            return null;
        }
    }

    /**
     * @param $timestamp
     */
    protected function setDateFromTimestamp($timestamp)
    {
        if (\Pimcore\Config::getFlag("useZendDate")) {
            $this->date = new \Pimcore\Date($timestamp);
        } else {
            $this->date = new \DateTime();
            $this->date->setTimestamp($timestamp);
        }
    }
}
