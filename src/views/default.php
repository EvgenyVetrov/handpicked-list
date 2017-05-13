<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 25.02.17
 * Time: 13:53
 * @var $this \yii\web\View
 */


$js = <<<JS
    // фильтр таблиц
    $("#handpicked-list-$widgetID").filterTable({
        minRows: 0,
        inputSelector: '#handpicked-list-filter-$widgetID'
    });
    
    // формирование массива отмеченных в модальном окне строк
    function newCheckedList(widgetPostfix) {
        
        var checkedItems=[];
        $('#handpicked-list-'+ widgetPostfix +' input:checked').each(function() {
            var item = {};
            item.id = $(this).val();
            item.textField = $(this).parents('td').next('td').html();
            item.labels = $(this).parents('td').next('td').next('td').html();
            checkedItems.push(item);
        });
        return checkedItems;
    }
    
    // нажатие на кнопку сохранения в модальном окне
    $('#handpicked-list-modal-button-$widgetID').on('click', function() {
        var countHpElements = 0;
        $("#has-elements-$widgetID").empty(); // очищаем ul тег от элементов
        if (fillHpBlock('$widgetID') != 0){
            $('#no-elements-$widgetID').hide();
            $('#has-elements-$widgetID').show();
        }
        else {
            $('#no-elements-$widgetID').show();
            $('#has-elements-$widgetID').hide();
        }
    });
    // todo id="no-elements-<?=widgetID?> - переделать отображение при отсутствии элементов с php на js
    
    //считываем количество элементов из оранжевого блока
    function countCurrentHpElements(elementID){
        return $(elementID).children().length;
    }
    
    // заполнение оранжевого HandPicked блока отмеченными элементами
    function fillHpBlock(widgetPostfix) {
        var list = newCheckedList('$widgetID');
        
        for (var i=0; i<list.length; i++){
            console.log("#has-elements-"+widgetPostfix);
            $("#has-elements-"+widgetPostfix).append('<li><input type="hidden" name="GeneralPages[manageWidgets][]" value="'+list[i].id+'"><a href="#">'+list[i].textField+list[i].labels+'</a></li>');
        }
        
        return list.length;
    }
    
    // если нет элементов для показа в оранжевом блоке, показываем уведомление
    if (countCurrentHpElements('#has-elements-$widgetID') == 0){
        $('#no-elements-$widgetID').show();
        $('#has-elements-$widgetID').hide();
    }
    else {
        $('#no-elements-$widgetID').hide();
        $('#has-elements-$widgetID').show();
    }

JS;

$this->registerJs($js);

?>


<div class="box <?= $class ?>">
    <div class="box-header with-border">
        <h3 class="box-title"><?= $title ?></h3>

        <div class="box-tools pull-right">
            <?php if ($collapse === true): ?>
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
            <?php endif; ?>

            <?php if ($readOnly === false): ?>
                <button type="button" id="handpicked-modal" class="btn btn-box-tool" data-toggle="modal" data-target="#handpicked-list-modal-<?=$widgetID?>"><i class="fa fa-pencil"></i>
                </button>
            <?php endif; ?>

            <?php if ($hint !== null): ?>
                <button
                    type="button"
                    class="btn btn-box-tool"
                    data-toggle="tooltip"
                    data-placement="top"
                    data-html="true"
                    title="<p style='width: 180px; text-align: left; line-height: 1.1; letter-spacing: 0.3px;'><small><?= $hint ?></small></p>"

                >
                    <i class="fa fa-info"></i>
                </button>
            <?php endif; ?>
        </div>
        <!-- /.box-tools -->
    </div>
    <!-- /.box-header -->
    <div class="box-body no-padding">
        <input type="hidden" name="GeneralPages[manageWidgets][]" value="">
        <div class="text-center" id="no-elements-<?=$widgetID?>">Нет элементов</div>
        <ul class="nav nav-stacked" id="has-elements-<?=$widgetID?>">

            <?php foreach ($selectedItems as $item): ?>
                <li>
                    <a href="<?= \yii\helpers\Url::toRoute(['widgets/update', 'id' => $item['id']]) ?>"><?= $item['name'] ?? array_values($item)[0] ?>

                        <?php //перебираем лейблы
                            $i=0;
                            foreach ($item as $key => $value):
                            if ($key == 'name' OR $key == 'checked') { continue; } ?>
                            <span class="pull-right"> &nbsp;</span>  <span class="pull-right badge <?= $bgLabels[$i++]  ?>"><?= $value ?></span>
                        <?php
                            if (count($bgLabels) <= $i) { $i = 0; }
                            endforeach; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

    </div>
    <!-- /.box-body -->
</div>


<!-- HTML-код модального окна-->
<div id="handpicked-list-modal-<?=$widgetID?>" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Заголовок модального окна -->
            <div class="modal-header bg-gray">
                <div class="col-sm-8">
                    <h4 class="modal-title"><i class="fa fa-fw fa-list"></i> Изменить набор</h4>
                </div>
                <div class="col-sm-4 no-padding">
                    <div class="input-group input-group-sm">
                        <input class="form-control table-search" type="search" id="handpicked-list-filter-<?=$widgetID?>">
                        <span class="input-group-btn" >
                            <button type="button" class="btn btn-primary btn-flat table-search-button">
                                <i class="fa fa-fw fa-search"></i>
                            </button>
                        </span>
                    </div>
                </div>


            </div>
            <!-- Основное содержимое модального окна -->
            <div class="modal-body">
                <div class="box-body no-padding">

                    <div style="overflow: scroll; height: 300px;">
                        <?= \yii\grid\GridView::widget([
                            'tableOptions' => [
                                'id' => 'handpicked-list-'.$widgetID,
                                'class' => 'table table-striped table-bordered all-widgets-table'
                            ],
                            'dataProvider' => $dataProvider,
                            'showHeader' => false,
                            'layout' => '{items}',

                            'columns' => [
                                [
                                    'class' => 'yii\grid\CheckboxColumn',
                                    /* todo некая функция которая передает отмеченные чекбоксы из попапа в оранжевую штуку */
                                    'checkboxOptions' =>  function ($model, $key, $index, $column) {

                                        return [
                                                 'checked' => $model['checked'],
                                                 'value' => $model['id'],
                                             ];
                                        },
                                ],
                                'name',
                                [
                                    'attribute' => '',
                                    'label' => '',
                                    'format' => 'html',
                                    'value' => function($model){
                                        \yii\helpers\ArrayHelper::remove($model, 'name');
                                        \yii\helpers\ArrayHelper::remove($model, 'checked');
                                        \yii\helpers\ArrayHelper::remove($model, 'id');
                                        $values = [];
                                        foreach ($model as $value){
                                            $values[] = '<span class="pull-right badge bg-aqua">'.\yii\helpers\Html::encode($value).'</span>';
                                        }
                                        return implode('&nbsp; ', $values);
                                    }
                                ]
                            ],
                        ]); ?>
                    </div>


                </div>
            </div>
            <!-- Футер модального окна -->
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Закрыть</button>
                <button type="button"  id="handpicked-list-modal-button-<?=$widgetID?>" class="btn btn-primary" data-dismiss="modal">Сохранить изменения</button>
            </div>
        </div>
    </div>
</div>