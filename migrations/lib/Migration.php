<?

namespace Alcodream\Migrations;
use Bitrix\Main\Loader;

class Migration
{
    const DATE_FORMAT = '[Y-m-d H:i:s] ';

    public $description;
    public $startTime;
    public $fields;
    protected $id;

    /**
     * @param $migrationDescription
     * @param null $data
     */
    public function __construct($migrationDescription, $data = null)
    {
        $this->setDescription($migrationDescription);

        if (!empty($data)) {
            $this->setFields($data);
        }

        $this->startTime = time();
        $this->start_time = time();

        if (!empty($this->description)) {
            $this->writeLine('==== ' . date(self::DATE_FORMAT, $this->startTime) . ' ====' . PHP_EOL . $migrationDescription);
        }

        if (!Loader::includeModule('iblock')) {
            $this->fail("Can't include module iblock");
        }
    }

    /**
     * @param $description
     */
    public function setDescription($description)
    {
        $this->description = trim($description);
    }

    /**
     * @param $fields
     * @throws \Exception
     */
    public function setFields($fields)
    {
        if (empty($fields)) {
            throw new \Exception("Задан пустой набор полей");
        }

        $this->fields = $fields;
        $id = intval($fields["ID"]);

        if ($id) {
            $this->id = $id;
        }
    }

    /**
     * @param string $text
     * @param bool $exit
     */
    public function writeLine($text = "", $exit = false)
    {
        echo PHP_EOL . $text . PHP_EOL;

        if ($exit) {
            die();
        }
    }

    /**
     * Выводит текст и переводит строку
     *
     * @param string $message - текст сообщения
     * @param bool $exit - прерывать выполнение скрипта после вывода сообщения?
     */
    protected function showMessage($message, $exit = false)
    {
        $this->writeLine(date(self::DATE_FORMAT) . $message . PHP_EOL, $exit);
    }

    /**
     * Выводит сообщение о неудачном выполнении операции
     * Например: FAIL: не удалось обновить объект ID=XXXX
     *
     * @param string $message - текст сообщение об ошибке
     * @param bool $exit - прерывать выполнение скрипта после вывода ошибки?
     */
    public function fail($message, $exit = true)
    {
        $this->showMessage("FAIL (ошибка операции): $message", $exit);
    }

    /**
     * Возвращает ID
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}