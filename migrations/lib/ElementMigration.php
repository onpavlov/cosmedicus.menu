<?php

namespace Custom\Migrations;

/**
 * Миграция добавления/обновления элемента инфоблока
 * Class ElementMigration
 * @package Alcodream\Migrations
 */
class ElementMigration extends Migration
{
    /**
     * @return bool|int
     * @throws \Exception
     */
    public function isExist()
    {
        if ($this->id) {
            return $this->id;
        }

        if (empty($this->fields["CODE"]) && empty($this->fields["ID"])) {
            throw new \Exception("Невозможно определить, существует ли элемент, так как не задан его символьный код или ID");
        }

        if (!empty($this->fields['ID'])) {
            $filter = ['ID' => $this->fields['ID']];
        }

        if (!empty($this->fields['CODE'])) {
            $filter = ['CODE' => $this->fields['CODE']];
        }

        if (empty($this->fields["IBLOCK_ID"])) {
            throw new \Exception('Не указан ID инфоблока');
        }
        $filter['IBLOCK_ID'] = $this->fields['IBLOCK_ID'];

        $element = \CIBlockElement::GetList(['id' => 'asc'], $filter, false, false, ['ID', 'NAME'])->Fetch();

        if (!$element["ID"]) {
            return false;
        }

        $this->id = $element["ID"];
        $this->fields["NAME"] = $element["NAME"];
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
        if (empty($this->fields['NAME']) || empty($this->fields['IBLOCK_ID'])) {
            throw new \Exception("Невозможно добавить элемент, так как не заданы его символьный код и ID");
        }

        try {
            $element_id = $this->isExist();

            if ($element_id) {
                $this->writeLine("Элемент " . $this->fields["NAME"] . "(" . $element_id ."): " . " уже существует", $exit_if_exists);
                $this->id = $element_id;
                return $this->id;
            }
        } catch(\Exception $e) {
            $this->writeLine($e->getMessage(), $exit_on_fail);
        }

        $el = new \CIBlockElement();

        $this->id = $el->Add($this->fields);

        if (!$this->id) {
            $this->writeLine("Не удалось добавить элемент " . $this->fields["CODE"] . ": " . $el->LAST_ERROR, $exit_on_fail);
            return false;
        }

        $this->writeLine("Элемент " . $this->fields["CODE"] . " успешно добавлен, ID=" . $this->id, $exit_on_success);
        return $this->id;
    }

    /**
     * @param bool $exit_on_success
     * @param bool $exit_on_fail
     * @return bool
     */
    public function update($exit_on_success = false, $exit_on_fail = true)
    {
        if (empty($this->fields['ID']) || empty($this->fields['IBLOCK_ID']) || empty($this->fields['NAME'])) {
            throw new \Exception("Невозможно добавить элемент, так как не заданы его символьный код, имя или ID ");
        }

        try {
            $element_id = $this->isExist();
            if (!$element_id) {
                $this->writeLine("Элемент " . $this->fields["NAME"] . ": " . " не существует", $exit_on_fail);
                return false;
            }
        } catch(\Exception $e) {
            $this->writeLine($e->getMessage(), $exit_on_fail);
        }

        $iblock = new \CIBlockElement();
        $result = $iblock->Update($this->id, $this->fields);

        if (!$result) {
            $this->writeLine("Не удалось обновить элемент " . $this->fields["CODE"] . ": " . $iblock->LAST_ERROR, $exit_on_fail);
            return false;
        }

        $this->writeLine("Элемент " . $this->fields["CODE"] . " успешно обновлен", $exit_on_success);
        return true;
    }

    /**
     * @param bool $exit_on_success
     * @param bool $exit_on_fail
     * @return bool
     */
    public function updatePropertyValue($exit_on_success = false, $exit_on_fail = true)
    {
        if (empty($this->fields['ID']) || empty($this->fields['IBLOCK_ID']) || empty($this->fields['PROPERTY_VALUES'])) {
            throw new \Exception("Невозможно добавить значение для свойства, так как не заданы ID элемента, ID инфоблока или PROPERTY_VALUES");
        }

        try {
            $element_id = $this->isExist();
            if (!$element_id) {
                $this->writeLine("Элемент " . $this->fields["NAME"] . ": " . " не существует", $exit_on_fail);
                return false;
            }
        } catch(\Exception $e) {
            $this->writeLine($e->getMessage(), $exit_on_fail);
        }

        $property = new \CIBlockElement();
        $result = $property->SetPropertyValuesEx($this->id, $this->fields['IBLOCK_ID'], $this->fields['PROPERTY_VALUES']);
        $this->writeLine("Элемент " . $this->id . " успешно обновлен", $exit_on_success);

        return true;
    }
}