<?php if($departament == SUA) { ?>
    <a href="/db" target="_blank" class="layout-link"><i class="fa fa-database" aria-hidden="true"></i></a>
<?php } ?>
<?php if($departament == SUA || $departament == KOR) { ?>
    <a href="/handlowe" target="_blank" class="layout-link"><i class="fa fa-search" aria-hidden="true"></i></a>
<?php } ?>
<?php if($departament == SUA || $departament == PER || $departament == KON) { ?>
    <a href="/etykiety" target="_blank" class="layout-link"><i class="fa fa-tags" aria-hidden="true"></i></a>
<?php } ?>