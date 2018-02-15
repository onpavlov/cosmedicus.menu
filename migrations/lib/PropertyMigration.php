<?php

namespace Custom\Migrations;

/**
 * Миграция добавления свойств инфоблока
 * Class PropertyMigration
 * @package  Alcodream\Migrations
 */
class PropertyMigration extends Migration
{
    /**
     * @return bool|int
     * @throws \Exception
     */
    public function isExist()
    {
        $filter = [];
        if (empty($this->fields["CODE"])) {
            throw new \Exception("Невозможно определить, существует ли свойство, так как не задан его код");
        } else {
            $this->fields["CODE"] = strval($this->fields["CODE"]);
            $filter["CODE"] = $this->fields["CODE"];
        }

        if (empty($this->fields["IBLOCK_ID"])) {
            if (empty($this->fields["IBLOCK_CODE"]) && empty($this->fields["IBLOCK_TYPE"])) {
                throw new \Exception("Невозможно определить, существует ли свойство, так как не задан код инфоблока");
            } else {
                $filter["IBLOCK_CODE"] = $this->fields["IBLOCK_CODE"];
                $filter["IBLOCK_TYPE"] = $this->fields["IBLOCK_TYPE"];
            }
        } else {
            $filter["IBLOCK_ID"] = $this->fields["IBLOCK_ID"];
        }

        $property = \CIBlockProperty::GetList([], $filter)->Fetch();
        if (!$property) {
            return false;
        }

        $this->id = $property["ID"];
        return $this->id;
    }

    /**
     * @param bool $exit_on_success
     * @param bool $exit_if_exists
     * @param bool $exit_on_fail
     * @return bool|int
     */
    public function add($exit_on_success = false, $exit_if_exists = false, $exit_on_fail = true)
    {
        try {
            if ($this->isExist()) {
                $this->writeLine("Свойство " . $this->fields["CODE"] . " уже существует", $exit_if_exists);
                return $this->id;
            }
        } catch (\Exception $e) {
            $this->writeLine($e->getMessage());
        }

        $iblock_property = new \CIBlockProperty();
        $this->id = $iblock_property->Add($this->fields);

        if (!$this->id) {
            $this->writeLine("Не удалось добавить свойство " . $this->fields["CODE"] . ": " . $iblock_property->LAST_ERROR, $exit_on_fail);
            return false;
        }

        $this->writeLine("Свойство " . $this->fields["CODE"] . " успешно добавлено, ID=" . $this->id, $exit_on_success);
        return $this->id;
    }
}