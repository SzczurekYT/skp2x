<?php
echo $this->Html->css('etykiety/label.css?v=' . time(), array('inline' => false));
echo $this->Html->script(array('etykiety/label'), array('block' => 'scriptBottom')); 
$this->set('title_for_layout', 'Etykiety');
$this->layout='bootstrap';

// formularz do znajdowania
echo $this->element('tasks/label/getTaskForm', array('msg' => $result['msg']));

if( $result != null ) { // znaczy było POST
    if( !empty($result['data']) ) { // mamy coś ?>
        <div class="row">
        <?php $i=0;
        foreach( $result['data']['Ticket'] as $karta ) {            
            echo $this->element('tasks/label/panelKarty', array(
                'karta' => $karta,
                'divclass' => 'col-sm-6',
                'lp' => ++$i
            ));
        } ?>
        </div>
        <?php
        echo "<br>";
        $this->App->print_r2($result['data']/*['Ticket']*/); // prezentuj
    } 
}
