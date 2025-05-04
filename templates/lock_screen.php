<?php

use Plib\View;

if (!defined("CMSIMPLE_XH_VERSION")) {header("HTTP/1.1 403 Forbidden"); exit;}

/**
 * @var View $this
 * @var string $action
 * @var string $stylesheet
 * @var int $retry_min
 * @var int $retry_sec
 */
?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="refresh" content="<?=$retry_sec?>">
    <link href="<?=$this->esc($stylesheet)?>" rel="stylesheet">
  </head>
  <body class="keymaster_lock_screen">
    <div class="keymaster_lock_message">
      <p><?=$this->text("message_editing", $retry_min)?></p>
      <form method="post" action="<?=$this->esc($action)?>" class="keymaster_buttons">
        <button><?=$this->text('label_logout')?></button>
      </form>
    </div>
  </body>
</html>
