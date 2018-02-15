<?

namespace Custom\Migrations;

/**
 * Миграция добавления разделов инфоблока
 * Class SectionMigration
 * @package  Alcodream\Migrations
 */
class SectionMigration extends Migration
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
            throw new \Exception("Невозможно определить, существует ли раздел, так как не задан его символьный код");
        }
        $filter = ['CODE' => $this->fields['CODE']];

        if (empty($this->fields["IBLOCK_ID"]) && empty($this->fields["IBLOCK_CODE"])) {
            throw new \Exception('Не указан ID или код инфоблока');
        }

        if (!empty($this->fields['IBLOCK_ID'])) {
            $filter['IBLOCK_ID'] = $this->fields['IBLOCK_ID'];
        } elseif (!empty($this->fields['IBLOCK_ID'])) {
            $filter['IBLOCK_ID'] = $this->fields['IBLOCK_ID'];
        }

        $filter['CHECK_PERMISSIONS'] = 'N';

        $section = \CIBlockSection::GetList(['id' => 'asc'], $filter)->Fetch();

        if (!$section["ID"]) {
            return false;
        }

        $this->id = $section["ID"];
        $this->fields['NAME'] = $section['NAME'];
        $this->fields['CODE'] = $section['CODE'];

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
            $section_id = $this->isExist();

            if ($section_id) {
                $this->writeLine("Раздел " . $this->fields["NAME"] . "(" . $section_id ."): " . " уже существует", $exit_if_exists);
                $this->id = $section_id;
                return $this->id;
            }
        } catch(\Exception $e) {
            $this->writeLine($e->getMessage(), $exit_on_fail);
        }

        $section = new \CIBlockSection();

        $this->id = $section->Add($this->fields);

        if (!$this->id) {
            $this->writeLine("Не удалось добавить раздел " . $this->fields["CODE"] . ": " . $section->LAST_ERROR, $exit_on_fail);
            return false;
        }

        $this->writeLine("Раздел " . $this->fields["CODE"] . " успешно добавлен, ID=" . $this->id, $exit_on_success);
        return $this->id;
    }

    /**
     * @param bool $tree_resort
     * @param bool $exit_on_success
     * @param bool $exit_if_exists
     * @param bool $exit_on_fail
     * @return bool
     */
    public function update($tree_resort = false, $exit_on_success = false, $exit_if_exists = false, $exit_on_fail = true)
    {
        try {
            $section_id = $this->isExist();

            if (!$section_id) {
                $this->fail("Раздел " . $this->fields["NAME"] . "(" . $this->fields['ID'] ."): " . " не существует", $exit_on_fail);

                return false;
            }
        } catch(\Exception $e) {
            $this->writeLine($e->getMessage(), $exit_on_fail);
        }

        $section = new \CIBlockSection();
        $this->id = $this->fields['ID'];

        if (!$section->Update($this->id, $this->fields, $tree_resort, false)) {
            $this->writeLine("Не удалось обновить раздел " . $this->fields["NAME"] . ": " . $section->LAST_ERROR, $exit_on_fail);
            return false;
        }

        $this->writeLine("Раздел " . $this->fields["NAME"] . " успешно обновлен, ID=" . $this->id, $exit_on_success);
        return true;
    }
}