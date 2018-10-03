<?php
/**
* Layout for webix: https://webix.com/
*/
?>

<!DOCTYPE HTML>
<html lang="pl">
    <head>
        <title><?php echo $title_for_layout; ?></title>
        <meta charset="utf-8">
        <?php        
        echo $this->Html->meta('icon');

        // Webix CSS & JavaScript =====================
        /*
        echo $this->Html->css(['/webix/v5.2.1/codebase/webix', '/webix/core.css?v=' . time()]);
        echo $this->Html->script(['/webix/v5.2.1/codebase/webix_debug']);
        Nowsza wersja poniżej*/
        echo $this->Html->css(['/webix/v5.4.0/codebase/webix', '/webix/core.css?v=' . time()]);
        echo $this->Html->script(['/webix/v5.4.0/codebase/webix_debug']);
        
        ?>
    </head>
    <body>
        <div id="myApp"></div> <!-- kontener dla naszej aplikacji -->
        <?php
            echo $this->fetch('content');         
            echo $this->Html->script(
                [
                    'request/vars.1.js?v=' . time(),
                    'request/app.1.js?v=' . time()
                ],
                ['charset' => 'utf-8']
            );
        ?>
    </body>
</html>