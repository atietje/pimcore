<?php
/**
 * Pimcore
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @category   Pimcore
 * @package    Object
 * @copyright  Copyright (c) 2009-2016 pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace Pimcore\Model\Object\KeyValue\TranslatorConfig;

use Pimcore\Model;

class Dao extends Model\Dao\AbstractDao
{

    const TABLE_NAME_TRANSLATOR = "keyvalue_translator_configuration";

    /**
     * Get the data for the object from database for the given id, or from the ID which is set in the object
     *
     * @param integer $id
     * @return void
     */
    public function getById($id = null)
    {
        if ($id != null) {
            $this->model->setId($id);
        }

        $data = $this->db->fetchRow("SELECT * FROM " . self::TABLE_NAME_TRANSLATOR . " WHERE id = ?", $this->model->getId());

        $this->assignVariablesToModel($data);
    }

    /**
     * @param null $name
     * @throws \Exception
     */
    public function getByName($name = null)
    {
        if ($name != null) {
            $this->model->setName($name);
        }

        $name = $this->model->getName();

        $stmt = "SELECT * FROM " . self::TABLE_NAME_TRANSLATOR . " WHERE name = '" . $name . "'";
        $data = $this->db->fetchRow($stmt);

        if ($data["id"]) {
            $this->assignVariablesToModel($data);
        } else {
            throw new \Exception("Config with name: " . $this->model->getName() . " does not exist");
        }
    }


    /**
     * Save object to database
     *
     * @return void
     */
    public function save()
    {
        if ($this->model->getId()) {
            return $this->model->update();
        }
        return $this->create();
    }

    /**
     * Deletes object from database
     *
     * @return void
     */
    public function delete()
    {
        $this->db->delete(self::TABLE_NAME_TRANSLATOR, $this->db->quoteInto("id = ?", $this->model->getId()));
    }

    /**
     * @throws \Exception
     */
    public function update()
    {
        try {
            $type = get_object_vars($this->model);

            foreach ($type as $key => $value) {
                if (in_array($key, $this->getValidTableColumns(self::TABLE_NAME_TRANSLATOR))) {
                    if (is_bool($value)) {
                        $value = (int) $value;
                    }
                    if (is_array($value) || is_object($value)) {
                        $value = \Pimcore\Tool\Serialize::serialize($value);
                    }

                    $data[$key] = $value;
                }
            }

            $this->db->update(self::TABLE_NAME_TRANSLATOR, $data, $this->db->quoteInto("id = ?", $this->model->getId()));
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Create a new record for the object in database
     *
     * @return boolean
     */
    public function create()
    {
        $this->db->insert(self::TABLE_NAME_TRANSLATOR, array());

        $this->model->setId($this->db->lastInsertId());

        return $this->save();
    }
}
