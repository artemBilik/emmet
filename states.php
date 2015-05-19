<?php

namespace emmet;

include __DIR__.'/src/FiniteStateMachine.php';

function getStateName($number){
    $names = [
        1 => 'OPERATOR',
        2 => 'TAG',
        3 => 'ID',
        4 => 'CLASS',
        5 => 'ATTR',
        6 => 'AFTER_ATTR',
        7 => 'TEXT',
        8 => 'AFTER_TEXT',
        9 => 'TEXT_NODE',
        10 => 'AFTER_TEXT_NODE',
        11 => 'MULTI',
        12 => 'VAR',
        13 => 'FUNC',
        14 => 'ARGS',
        15 => 'ARG_TXT',
        16 => 'ARG_VAR',
        17 => 'ERROR',
        18 => 'END',
        19 => 'SAME',
        20 => 'PREV',
        21 => 'SKIP',
        22 => 'HTML',
    ];

    if(array_key_exists($number, $names)){
        return $number . '-'.$names[$number];
    } else {
        echo 'Неопределенно имя состояния №' . $number;exit(0);
    }
}

?>
<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>Таблица машины состояний</title>
    <style>
        #states_tabel{
            width: 90%;
            margin: 0 auto;
            border-collapse: collapse;
        }
        #states_tabel td{
            padding: 10px;
            text-align: center;
            border: 1px solid #cdcdcd;
        }
        #states_tabel{
            font-size: 9px;
        }
        #states_tabel tbody tr td:first-child, thead td, #states_tabel tfoot tr td:first-child{
            font-size: 12px;
            font-weight: bold;
        }
    </style>
</head>
<body>
<table id="states_tabel">
    <thead>
        <tr>
            <td>Входной символ</td>
            <td rowspan="2">"+"</td>
            <td rowspan="2">"&gt;"</td>
            <td rowspan="2"">"^"</td>
            <td rowspan="2">"("</td>
            <td rowspan="2">")"</td>
            <td rowspan="2">"a-z"</td>
            <td rowspan="2">"#"</td>
            <td rowspan="2">"."</td>
            <td rowspan="2">"["</td>
            <td rowspan="2">"]"</td>
            <td rowspan="2">"{"</td>
            <td rowspan="2">"}"</td>
            <td rowspan="2">"*"</td>
            <td rowspan="2">"`"</td>
            <td rowspan="2">"%"</td>
            <td rowspan="2">" "</td>
            <td rowspan="2">""</td>
            <td rowspan="2">","</td>
        </tr>
        <tr>
            <td>Состояние</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach(FiniteStateMachine::getMap() as $state_name => $state_values) { ?>
            <?php if($state_name == FiniteStateMachine::ERROR) { continue; } ?>
            <tr>
                <td><?php echo getStateName($state_name); ?></td>
                <?php foreach($state_values as $value) { ?>
                    <td>
                        <?php if(is_array($value)) {
                            foreach($value as $state){
                                echo getStateName($state) . '<br />';
                            }
                        } else {
                            echo getStateName($value);
                        } ?>
                    </td>
                <?php } ?>
            </tr>
        <?php } ?>
    </tbody>
    <tfoot>
        <tr>
            <td>ERROR</td>
            <td colspan="18">EXCEPTION</td>
        </tr>
    </tfoot>
</table>
</body>
</html>





