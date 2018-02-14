<?php

namespace Alcodream\Migrations;

/**
 * Миграция пользовательских свойств
 * Class UserFieldMigration
 * @package Alcodream\Migrations
 */
class UserFieldMigration extends Migration
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

        if (empty($this->fields["FIELD_NAME"])) {
            throw new \Exception("Невозможно определить, существует ли пользовательское свойство, так как не задан его символьный код");
        }
        if (empty($this->fields["ENTITY_ID"])) {
            throw new \Exception("Невозможно определить, существует ли пользовательское свойство, так как не задана принадлежность сущности");
        }
        $filter = ['FIELD_NAME' => $this->fields['FIELD_NAME']];

        $userfield = \CUserTypeEntity::GetList(array(),$filter)->Fetch();

        if (!$userfield["ID"]) {
            return false;
        }

        $this->id = $userfield["ID"];
        return $this->id;
    }

    /**
     * @return bool|int
     */
    public function add()
    {
        try {
            $iblock_id = $this->isExist();
            if ($iblock_id) {
                $this->writeLine("Пользовательское свойство " . $this->fields["FIELD_NAME"] . " уже существует");
                return $this->id;
            }
        } catch(\Exception $e) {
            $this->writeLine($e->getMessage());
        }

        $newPropertyUser = new \CUserTypeEntity();
        $this->id = $newPropertyUser->Add($this->fields);

        if (!$this->id) {
            global $APPLICATION;
            $error = $APPLICATION->GetException()->GetString();
            $this->writeLine("Не удалось добавить пользовательское свойство " . $this->fields["FIELD_NAME"] . ": " . $error);
            return false;
        }

        $this->writeLine("Пользовательское свойство " . $this->fields["FIELD_NAME"] . " успешно добавлено, ID=" . $this->id);
        return $this->id;
    }
}