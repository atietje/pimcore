<?php
/**
 * Pimcore
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @category   Pimcore
 * @package    Property
 * @copyright  Copyright (c) 2009-2016 pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace Pimcore\Model\Property\Predefined;

use Pimcore\Model;

class Dao extends Model\Dao\PhpArrayTable
{

    /**
     *
     */
    public function configure()
    {
        parent::configure();
        $this->setFile("predefined-properties");
    }

    /**
     * @param null $id
     * @throws \Exception
     */
    public function getById($id = null)
    {
        if ($id != null) {
            $this->model->setId($id);
        }

        $data = $this->db->getById($this->model->getId());

        if (isset($data["id"])) {
            $this->assignVariablesToModel($data);
        } else {
            throw new \Exception("Predefined property with id: " . $this->model->getId() . " does not exist");
        }
    }


    /**
     * @param null $key
     * @throws \Exception
     */
    public function getByKey($key = null)
    {
        if ($key != null) {
            $this->model->setKey($key);
        }

        $key = $this->model->getKey();

        $data = $this->db->fetchAll(function ($row) use ($key) {
            if ($row["key"] == $key) {
                return true;
            }
            return false;
        });

        if (count($data) && $data[0]["id"]) {
            $this->assignVariablesToModel($data[0]);
        } else {
            throw new \Exception("Route with name: " . $this->model->getName() . " does not exist");
        }
    }

    /**
     * @throws \Exception
     */
    public function save()
    {
        $ts = time();
        if (!$this->model->getCreationDate()) {
            $this->model->setCreationDate($ts);
        }
        $this->model->setModificationDate($ts);

        try {
            $dataRaw = get_object_vars($this->model);
            $data = [];
            $allowedProperties = ["id","name","description","key","type","data",
                "config","ctype","inheritable","creationDate","modificationDate"];

            foreach ($dataRaw as $key => $value) {
                if (in_array($key, $allowedProperties)) {
                    $data[$key] = $value;
                }
            }
            $this->db->insertOrUpdate($data, $this->model->getId());
        } catch (\Exception $e) {
            throw $e;
        }

        if (!$this->model->getId()) {
            $this->model->setId($this->db->getLastInsertId());
        }
    }

    /**
     * Deletes object from database
     *
     * @return void
     */
    public function delete()
    {
        $this->db->delete($this->model->getId());
    }
}
