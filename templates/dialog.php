<?php

use Plib\View;

if (!defined("CMSIMPLE_XH_VERSION")) {header("HTTP/1.1 403 Forbidden"); exit;}

/**
 * @var View $this
 * @var string $action
 */
?>
<!-- keymaster dialog -->
<div id="keymaster">
  <div class="keymaster_message">
    <p><?=$this->text("editing")?></p>
    <form method="post" action="<?=$this->esc($action)?>" class="keymaster_buttons">
      <button><?=$this->text('label_logout')?></button>
    </form>
  </div>
</div>
