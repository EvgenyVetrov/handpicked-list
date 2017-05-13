<?php

namespace atlant5\handpickedlist;

use yii\base\Widget;
use yii\data\ArrayDataProvider;

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
     * @var array
     */
    public $dataProvider = [];

    public $bgLabels = ['bg-aqua', 'bg-blue', '']; //бэкграунды лейблов, меняются по очереди

    //public $usingColumns = ['name', 'code', 'type']; //колонки которые использовать при отображении

    //public $detectedColumns = ['code' => 'code', 'widget_type' => 'type']; //колонки по которым вычисляем отмеченные элементы

    public function init(){
        parent::init();
    }


    public function selectedItems(){
        $selectedItems = [];

        foreach ($this->dataProvider as $item){
            if (!isset($item['checked'])) { continue; }
            if ($item['checked'] == true) {
                $selectedItems[] = $item;
            }
        }
        return $selectedItems;
    }


    public function run(){
        HandpickedListAssets::register($this->getView());
        $dataProvider = new ArrayDataProvider([
            'allModels' => $this->dataProvider,
            'pagination' => false,
        ]);

        return $this->render('default', [
            'title' => $this->title,
            'class' => $this->class,
            'collapse' => $this->collapse,
            'readOnly' => $this->readOnly,
            'hint' => $this->hint,
            'dataProvider' => $dataProvider,
            'selectedItems' => $this->selectedItems(),
            'bgLabels' => $this->bgLabels,
            'widgetID' => $this->id,
        ]);
    }
}