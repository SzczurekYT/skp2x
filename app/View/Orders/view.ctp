<?php
//echo '<pre>';	print_r($order['Event']); echo  '</pre>';
//echo '<pre>';	print_r($order); echo  '</pre>';
//echo '<pre>';	print_r($evcontrol); echo  '</pre>';
//echo $this->Html->url('/css/order-pdf.css', true);

echo $this->Html->css('order', array('inline' => false));
echo $this->Html->script(array('event', 'order-view'), array('inline' => false)); 
$this->Ma->displayActions('orders');

if( $order['Order']['nr'] ) {
	$this->set('title_for_layout', $this->Ma->bnr2nrh($order['Order']['nr'], $order['User']['inic'], false));
} else {
    $this->set('title_for_layout', 'Zamówienie (H)');     
}
//echo '<pre>';	print_r($evcontrol); echo  '</pre>';
//echo count($order['Card']).'<br>';
//echo '<pre>';	print_r($order); echo  '</pre>';
//echo '<pre>';	print_r($ludz); echo  '</pre>';
//echo '<pre>';	print_r($order['Event']); echo  '</pre>';
//echo '<pre>';	print_r($links); echo  '</pre>';
//echo '<pre>';	print_r($evcontrol['ile']); echo  '</pre>';
//echo '<pre>';	print_r($vju); echo  '</pre>';
//echo date('Y-m-d');
?>
<div class="orders view">
<h2 class="oj-naglowek"><?php echo //'Zamówienie '. 
                        '<strong>'.
			$this->Ma->bnr2nrh($order['Order']['nr'], $order['User']['inic'], false) .
                        '</strong>' .
			$this->Ma->editlink('order', $order['Order']['id']) 
                        . $this->Ma->pdflink('order', $order['Order']['id'])
                        ; 
    ?>
</h2>
	<?php //$this->Ma->nawiguj( $links, $order['Order']['id'] ); //nawigacyjne do dodaj, usuń, edycja itp. ?>
	<dl id="dexview">
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($order['Order']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Numer'); ?></dt>
		<dd class="corenr">
			<?php echo $this->Ma->bnr2nrh($order['Order']['nr'], $order['User']['inic']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Klient'); ?></dt>
		<dd>
			<?php echo $this->Html->link($order['Customer']['name'], array('controller' => 'customers', 'action' => 'view', $order['Customer']['id'])); ?>
			&nbsp;
		</dd>
		
		<dt><?php echo 'Opiekun'; ?></dt>
		<dd>
			<?php echo $order['User']['name'];
				//echo $this->Html->link($order['User']['name'], array('controller' => 'users', 'action' => 'view', $order['User']['id'])); ?>
			&nbsp;
		</dd>
		
		<dt><?php echo 'Złożone'; ?></dt>
		<dd>
			<?php echo $this->Ma->md($order['Order']['data_publikacji']); ?>
			&nbsp;
		</dd>
		<dt><?php echo 'Data Zakończenia'; ?></dt>
		<dd>
			<?php echo $this->Ma->md($order['Order']['stop_day']); ?>
			&nbsp;
			<?php if( $order['Order']['isekspres'] )
					echo '<span class="ekspres">EKSPRES</span>'; ?>
			&nbsp;
		</dd>
		

		<dt><?php echo __('Osoba Kontaktowa'); ?></dt>
		<dd>
			<?php echo $order['Order']['osoba_kontaktowa']; ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Telefon'); ?></dt>
		<dd>
			<?php echo h($order['Order']['tel']); ?>
			&nbsp;
		</dd>
		<dt><?php echo 'Przedpłata' ?></dt>
		<dd>
			<?php echo $vju['forma_zaliczki']['options'][$order['Order']['forma_zaliczki']];
			if( $order['Order']['procent_zaliczki'] )
				echo ', ' . $order['Order']['procent_zaliczki'] . '%';
			?>
			&nbsp;
		</dd>
		<dt><?php echo 'Forma Płatnosci'; ?></dt>
		<dd>
			<?php echo $vju['forma_platnosci']['options'][$order['Order']['forma_platnosci']];
			if( ($order['Order']['forma_platnosci'] == PRZE || $order['Order']['forma_platnosci'] == CASH)
					&& $order['Order']['termin_platnosci'] )
						echo ', ' . $order['Order']['termin_platnosci'] . ' dni';
			?>
			&nbsp;
		</dd>
		<dt><?php echo 'Waluta'; ?></dt>
		<dd><?php echo $order['Customer']['waluta']; ?></dd>
		<dt><?php echo 'Uwagi'; ?></dt>
		<dd>
			<?php echo nl2br($order['Order']['comment']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Status'); ?></dt>
		<dd><?php echo $this->Ma->status_zamow($order['Order']['status']);	?>
			&nbsp;			
		</dd>
		<dt><?php echo 'Dane do faktury'; ?></dt>
		<dd><?php echo $this->Ma->adresFaktury($order); ?> &nbsp;</dd>
		<dt><?php echo 'Adres dostawy'; ?></dt>
		<dd><?php echo $this->Ma->adresDostawy($order); ?>&nbsp;</dd>
		
		<!--
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($order['Order']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo h($order['Order']['modified']); ?>
			&nbsp;
		</dd>
		-->
	</dl>
</div>


<!-- <?php $this->Ma->kontrolka_ord($order, $evcontrol);	?>	-->
	
<div class="related">
	<!--<h3>-->
	<?php echo $this->Ma->viewheader('KARTY', array('class' => 'margin02') ); ?>
		
	<!--</h3>-->
	<?php if (!empty($order['Card'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th class="card_id_fix"><?php echo 'Id'; ?></th>
		<th><?php echo __('Nazwa'); ?></th>
		<th class="card_opcje_fix">Opcje</th>
		<!--<th><?php echo __('User Id'); ?></th>
		<th><?php echo __('Klient'); ?></th>
		<th><?php echo __('Order Id'); ?></th>-->
		<th class="card_zlec_fix"><?php echo 'Zlecenie(P)'; ?></th>		
		<th class="card_ilosc_fix"><?php echo 'Ilość'; ?></th>
		<th class="card_cena_fix"><?php echo 'Cena'; ?></th>
		<th class="card_status_fix"><?php echo 'Status'; ?></th>
		<?php
			if( $evcontrol['bcontr'][push4checking] ) {
				echo '<th class="card_dpcheck_fix">'.'D'.'</th>'.'<th class="card_dpcheck_fix">'.'P'.'</th>';
			}
		?>
		<!--
		<th><?php echo __('Wzor'); ?></th>
		<th><?php echo __('Comment'); ?></th>
		
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>-->
	</tr>
	<?php $k=0; $sigma = 0;
		foreach ($order['Card'] as $card): ?>
		<?php $karty[ $card['id'] ]= $card['name']; ?>
		<tr>
			<td class="card_id_fix"><?php echo $card['id']; ?></td>
			<td><?php echo $this->Html->link($card['name'], array(
						'controller' => 'cards', 'action' => 'view', $card['id']
						), array('title' => $card['name']));	?> </td>
			<td class="card_opcje_fix"><?php if( $card['isperso'] ) echo '<span class="perso">P</span>';
			?>
			</td>			
			<td class="card_zlec_fix"><?php
				if( $card['job_nr'] ) {
					echo $this->Html->link($this->Ma->bnr2nrj($card['job_nr'], null)/*$card['job_nr']*/, array(
						'controller' => 'jobs', 'action' => 'view', $card['job_id']),
						array('escape' => false)
					);
				}				
				$this->Ma->bnr2nrj($card['job_nr'], null)
			?>	
			</td>
			<td class="card_ilosc_fix"><?php 
                            $sigma += $card['quantity'];
                            echo $this->Ma->tys($card['quantity']); ?></td>
			<td class="card_cena_fix"><?php echo $this->Ma->colon($card['price']);
			 ?></td>
			 <td class="card_status_fix"><?php 
			 		if( $card['status'] == PRIV && $order['Order']['id'] )
						echo 'ZAŁĄCZONA';
					else
						echo $this->Ma->status_karty($card['status']); ?></td>
			<?php
				if( $evcontrol['bcontr'][push4checking] ) {
					$d_checked = $p_checked = false;
					switch($card['status']) {						
						case DNO:
						case DNOPOK:
						case W4D:
						case W4DPOK:
							$d_checked = true;
						break;
						case W4PDOK:
						case DOKPNO:
							 $p_checked = true;
						break;
						case W4DPNO:
						case W4DP:
						case W4PDNO:
						case DNOPNO: $d_checked = $p_checked = true;
						break;
					}
					// 'ard' w inpucie specjanie
					$dtp =  $this->Form->input('ard.'.$k.'.D', array(
					'type' => 'checkbox',
					'checked'=> $d_checked,
					//'checked' => true,
					'label' => false,
					'div' => false,
					'class' => 'dlajoli',
					'idlike' => 'Card'.$k.'D',
					'disabled' => $d_checked
					//'order_id' => $oid,
					//'card_id' => $cid,
					//'ilosc' => $ile
					));
					$per =  $this->Form->input('ard.'.$k.'.P', array(
						'type' => 'checkbox',
						'checked'=> $p_checked,
						'label' => false,
						'div' => false,
						'class' => 'dlajoli',
						'idlike' => 'Card'.$k++.'P',
						'disabled' => !$card['isperso'] || $p_checked
					));
					echo '<td class="card_dpcheck_fix">'.$dtp.'</td>'.'<td class="card_dpcheck_fix">'.$per.'</td>';
				}
			?>			
			<!--
			 
			<td><?php echo $card['wzor']; ?></td>
			<td><?php echo $card['comment']; ?></td>
			
			<td><?php echo $card['created']; ?></td>
			<td><?php echo $card['modified']; ?></td>-->
			<!--
			<td class="actions">
				<?php echo $this->Html->link(__('V'), array('controller' => 'cards', 'action' => 'view', $card['id'])); ?>
				<?php echo $this->Html->link(__('E'), array('controller' => 'cards', 'action' => 'edit', $card['id'])); ?>
				<?php echo $this->Form->postLink(__('D'), array('controller' => 'cards', 'action' => 'delete', $card['id']), null, __('Are you sure you want to delete # %s?', $card['id'])); ?>
			</td>-->
		</tr>
	<?php endforeach; 
            if( $sigma ) {
                echo '<tr class="sumainfo"><td></td><td></td><td></td><td class="sumatd">Suma:</td><td class="card_ilosc_fix">' . $this->Ma->tys($sigma) .
                        '</td><td></td><td></td></tr>';
            }
        ?>  
	</table>
<?php endif; ?>

</div>




<div class="ul-events">
	<?php $this->Ma->kontrolka_ord($order, $evcontrol);	?>
	<ul>
            <?php
            $start = count($order['Event'])-1;
            for ($i = $start; $i >=0; $i--) {
                    $event = $order['Event'][$i];
                    echo $this->Html->tag('li', null, array('class' => 'post'));
                            echo $this->Html->tag('div', null, array('class' => 'postinfo'));
                            /*
                            if( $event['card_id'] ) $co = //'<span>kartę: </span>'.
                                            $karty[$event['card_id']]; 
                            else
                            */
                            switch( $event['co'] ) {
                                    case put_kom:
                                            $co ='';
                                    break;
                                    case p_ov:
                                    case p_no: 
                                    case p_ok: 
                                    case d_no: 
                                    case d_ok: 
                                            $co = $karty[$event['card_id']]; 
                                    break;
                                    default:
                                            $co ='';
                            }

                            //substr($event['created'],0,10)

                            if( $event['co'] == put_kom && $event['card_id']  ) {
                                    $kartkomm = ' odnośnie karty:';
                                    foreach( $order['Card'] as $karta )
                                    if( $karta['id'] == $event['card_id'] )
                                            $kartkomm .= ' ' . $karta['name'];
                            }

                            else
                                    $kartkomm = null;

                            echo '<p>'.$ludz[$event['user_id']]['name'].'</p>'.'<p class="gibon"><span class="' . 
                                            //$this->Ma->evtext[$event['co']]['class'].'">' . 
                                            $evtext[$event['co']]['class'] . '">' .
                                            //$this->Ma->evtext[$event['co']][$ludz[$event['user_id']]['k']]. ' ' .
                                            $evtext[$event['co']][$ludz[$event['user_id']]['k']]. ' ' .
                                            $co . $kartkomm .

                                            '</span></p>'.'<span>'.$this->Ma->mdt($event['created']).'</span>';
                            $list = array( nl2br($event['post']) );

                            echo $this->Html->tag('/div');
                            echo $this->Html->tag('div');
                                    echo $this->Html->nestedList(
                                    $list,
                                    array('class'=>'olevent'),
                                    null,
                                    'ol'
                                    );
                            echo $this->Html->tag('span', $i + 1, array('class' => 'event_nr'));	
                            echo $this->Html->tag('/div');

                    echo $this->Html->tag('/li');
            } ?>
	</ul>
</div>


<!--
<div class="related">-->
<!--
	<h3><?php echo __('Related Events'); ?></h3>
	<?php if (!empty($order['Event'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('User Id'); ?></th>
		<th><?php echo __('Order Id'); ?></th>
		<th><?php echo __('Job Id'); ?></th>
		<th><?php echo __('Card Id'); ?></th>
		<th><?php echo __('Co'); ?></th>
		<th><?php echo __('Post'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($order['Event'] as $event): ?>
		<tr>
			<td><?php echo $event['id']; ?></td>
			<td><?php echo $event['user_id']; ?></td>
			<td><?php echo $event['order_id']; ?></td>
			<td><?php echo $event['job_id']; ?></td>
			<td><?php echo $event['card_id']; ?></td>
			<td><?php echo $event['co']; ?></td>
			<td><?php echo $event['post']; ?></td>
			<td><?php echo $event['created']; ?></td>
			<td><?php echo $event['modified']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'events', 'action' => 'view', $event['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'events', 'action' => 'edit', $event['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'events', 'action' => 'delete', $event['id']), null, __('Are you sure you want to delete # %s?', $event['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>
	-->
	<!--
	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Event'), array('controller' => 'events', 'action' => 'add')); ?> </li>
		</ul>
	</div>-->
<!--</div>-->

