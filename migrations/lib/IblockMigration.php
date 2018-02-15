<?php

namespace Custom\Migrations;

/**
 * Миграция добавления инфоблока
 * Class IblockMigration
 * @package Alcodream\Migrations
 */
class IblockMigration extends Migration
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

        if (empty($this->fields["CODE"])) {
            throw new \Exception("Невозможно определить, существует ли инфоблок, так как не задан его символьный код");
        }
        $filter = ['CODE' => $this->fields['CODE']];

        if (empty($this->fields["IBLOCK_TYPE_ID"])) {
            throw new \Exception('Не указан тип инфоблока');
        }
        $filter['TYPE'] = $this->fields['IBLOCK_TYPE_ID'];
        $filter['CHECK_PERMISSIONS'] = 'N';

        $iblock = \CIBlock::GetList(['id' => 'asc'], $filter)->Fetch();

        if (!$iblock["ID"]) {
            return false;
        }

        $this->id = $iblock["ID"];
        $this->fields["NAME"] = $iblock["NAME"];
        $this->fields["IBLOCK_ID"] = $iblock["ID"];
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
            $iblock_id = $this->isExist();

            if ($iblock_id) {
                $this->writeLine("Инфоблок " . $this->fields["NAME"] . "(" . $iblock_id ."): " . " уже существует", $exit_if_exists);
                $this->id = $iblock_id;
                return $this->id;
            }
        } catch(\Exception $e) {
            $this->writeLine($e->getMessage(), $exit_on_fail);
        }

        $iblock = new \CIBlock();

        $this->id = $iblock->Add($this->fields);

        if (!$this->id) {
            $this->writeLine("Не удалось добавить инфоблок " . $this->fields["CODE"] . ": " . $iblock->LAST_ERROR, $exit_on_fail);
            return false;
        }

        $this->writeLine("Инфоблок " . $this->fields["CODE"] . " успешно добавлен, ID=" . $this->id, $exit_on_success);
        return $this->id;
    }

    /**
     * @param bool $exit_on_success
     * @param bool $exit_on_fail
     * @return bool
     */
    public function update($exit_on_success = false, $exit_on_fail = true)
    {
        try {
            $iblock_id = $this->isExist();
            if (!$iblock_id) {
                $this->writeLine("Инфоблок " . $this->fields["NAME"] . "(" . $iblock_id ."): " . " не существует", $exit_on_fail);
                return false;
            }
        } catch(\Exception $e) {
            $this->writeLine($e->getMessage(), $exit_on_fail);
        }

        $iblock = new \CIBlock();
        $result = $iblock->Update($this->id, $this->fields);

        if (!$result) {
            $this->writeLine("Не удалось обновить инфоблок " . $this->fields["CODE"] . ": " . $iblock->LAST_ERROR, $exit_on_fail);
            return false;
        }

        $this->writeLine("Инфоблок " . $this->fields["CODE"] . " успешно обновлен", $exit_on_success);
        return true;
    }

    /**
     * @param bool $exit_on_success
     * @param bool $exit_on_fail
     * @return bool
     */
    public function delete($exit_on_success = false, $exit_on_fail = true)
    {
        try {
            $iblock_id = $this->isExist();
            if (!$iblock_id) {
                $this->writeLine("Инфоблок " . $this->fields["NAME"] . "(" . $iblock_id ."): " . " не существует", $exit_on_fail);
                return false;
            }
        } catch(\Exception $e) {
            $this->writeLine($e->getMessage(), $exit_on_fail);
        }

        $iblock = new \CIBlock();
        $result = $iblock->Delete($this->id);

        if (!$result) {
            $this->writeLine("Не удалось удалить инфоблок " . $this->fields["CODE"], $exit_on_fail);
            return false;
        }

        $this->writeLine("Инфоблок " . $this->fields["CODE"] . " успешно удален", $exit_on_success);
        return true;
    }
}