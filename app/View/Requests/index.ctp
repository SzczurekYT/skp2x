<?php 
    //
    $jscode = "var " . $config['varname'] . "= " . json_encode($config['theobj']) . ";";

    $this->Html->scriptBlock($jscode, array('inline' => false));     
    $this->set('title_for_layout', 'Szukaj');    
?>

<div class="row filter-panel">    
    <div class="col-md-6">
    <?php
        echo $this->element('bootstrap/datepickers/myBPdatePicker', array(
            'config' => [$config['od'], $config['do']]
            //'config' => [$config['od']]
        ));
    ?>    
    </div>
</div>
<div class="row">
    <div class="col-md-6">
    <?php
        $options = [
            'els' => ['opcja1', 'opcja2', 'opcja3'],
            'default' => 0
        ];
        
        echo $this->element('bootstrap/selects/select', array(
            'opcje' => $options
        ));
        
    ?>
    </div>
</div>