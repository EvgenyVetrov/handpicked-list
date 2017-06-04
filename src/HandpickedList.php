<?php

namespace atlant5\handpickedlist;

use function Symfony\Component\Debug\Tests\testHeader;
use yii\base\Widget;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 13.05.17
 * Time: 0:54
 */
class HandpickedList extends Widget
{

    /**
     * Определение заголовка виджета
     * @var string
     */
    public $title = 'Handpicked list';

    /**
     * CSS classes for general block
     * @var string
     */
    public $class = 'box-warning box-solid';

    /**
     * allow/disallow collapse button
     * @var bool
     */
    public $collapse = true;

    /**
     * Readonly mode. Disable pop-up with all elements. Show only base box with selected items.
     * @var bool
     */
    public $readOnly = false;

    /**
     * Little information if you want.
     * @var null|string
     */
    public $hint = null;
    /**
     * All Elements
     * @var array|object
     */
    public $dataProvider = [];

    /**
     * Array of rules for display checked elements
     * Using OR rule
     * key - column
     * value - value
     * @var array
     */
    public $checkedRules = ['checked' => true, 'active' => true];

    public $bgLabels = ['bg-aqua', 'bg-blue', '']; // бэкграунды лейблов, меняются по очереди

    /**
     * колонки которые использовать при отображении
     * @var array
     */
    public $usingColumns = ['name', 'code', 'type'];

    /**
     * колонки и значения элементов по которым вычисляем отмеченные элементы
     * 'column_name' => 'value'
     * @var array
     */
    public $detectedColumns = ['active' => true];

    /**
     * Собственный список отмеченных элементов
     * @var array [1, 56, 89]
     */
    public $customSelection = [4];
    /**
     * Колонка для собственного списка отмеченных элементов
     * @var string
     */
    public $customSelectionColumn = 'id';

    /**
     * Модель со связями, куда должны записываться отмеченные элементы
     * пример: 'GeneralPages[manageWidgets][anyRelation]'
     * ужасный способ
     * @var string
     */
    public $modelWithRelations = '';

    /**
     * Все элементы после обработки данных
     * @var array
     */
    protected $allElements = [];

    /**
     * Выбранные элементы
     * @var array
     */
    protected $selectdElements = [];

    public function init(){
        $this->cookData();
        parent::init();
    }



    /**
     * Подготовка данных, приведение к одному формату
     * формат - массив ибо работает в 1,5-2 раза быстрее объектов
     */
    protected function cookData(): void
    {
        $finishArray = [];
        $selectedArray = [];
        if ($this->dataProvider instanceof ArrayDataProvider) {
            $this->dataProvider = $this->dataProvider->allModels;
        }

        foreach ($this->dataProvider as $item){
            $secondArray = []; // массив колонок иницилизируем
            if (is_object($item)) {
                $item = $item->attributes; // item - массив из 1 записи
            }

            // перебираем все поля (аттрибуты)
            foreach ($this->usingColumns as $column){
                if (isset($item[$column])) {
                    $secondArray[$column] = $item[$column];
                }
            }

            // если элемент отбирается
            if ($this->elementSelector($item)){
                $secondArray['selected-item'] = true;
                $selectedArray[] = $secondArray;
            } else {
                $secondArray['selected-item'] = false;
            }

            $finishArray[] = $secondArray;
        }

        // записываем результаты в свойства класса
        $this->selectdElements = $selectedArray;
        $this->allElements = $finishArray;

        //VarDumper::dump($this->allElements, 10, true);
    }



    /**
     * Метод, определяющий должен ли попасть элемент в список избранных или нет
     * @param $item array
     * @return bool
     *
     */
    protected function elementSelector($item)
    {
        // перебираем все поля элемента
        foreach ($item as $key => $value) {
            // перебираем все правила отбора
            foreach ($this->detectedColumns as $ruleKey => $ruleValue) {
                // что-то вроде этого if (['echo' => 45] == ['echo' => '45'])
                if ( [$ruleKey => $ruleValue] == [$key => $value]) { return true; }
            }
            // перебираем собственный список отмеченных элементов
            if (in_array($item[$this->customSelectionColumn], $this->customSelection)) { return true; }
        }
        return false;
    }



    /**
     * Составляет массив колонок которые НЕ должны попасть в лейблы
     * @return mixed
     */
    protected function getNoLabelColumns()
    {
        $systemColumns = [
            $this->customSelectionColumn,
            'selected-item'
        ];
        $humanColumns = array_diff($this->usingColumns, $systemColumns);
        $elementName = array_shift($humanColumns);
        $systemColumns[] = $elementName; //последним добавляем человеческое название. Оно должно быть последним, потом меняем на первый
        return array_values(array_reverse($systemColumns)); // за одно сбрасываем ключи массива
    }



    /**
     * Запуск рендринга функций
     * @return string
     */
    public function run()
    {
        HandpickedListAssets::register($this->getView());

        // готовим данные для GridView
        $dataProvider = new ArrayDataProvider([
            'allModels' => $this->allElements,
            'pagination' => false,
        ]);

        return $this->render('default', [
            'title' => $this->title,
            'class' => $this->class,
            'collapse' => $this->collapse,
            'readOnly' => $this->readOnly,
            'hint' => $this->hint,
            'dataProvider' => $dataProvider,
            'selectedItems' => $this->selectdElements,
            'bgLabels' => $this->bgLabels,
            'widgetID' => $this->id,
            'modelWithRelations' => $this->modelWithRelations,
            'selectionColumn' => $this->customSelectionColumn,
            'noLabelColumns' => $this->getNoLabelColumns()
        ]);
    }
}