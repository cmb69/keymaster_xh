<?php

use Keymaster\Infra\View;

if (!defined("CMSIMPLE_XH_VERSION")) {header("HTTP/1.1 403 Forbidden"); exit;}

/**
 * @var View $this
 */
?>
<!-- keymaster dialog -->
<div id="keymaster" style="display: none">
  <div class="keymaster_message">
    <p><?=$this->text('warning_singular')?></p>
    <div class="keymaster_buttons">
      <button><?=$this->text('button_prolong')?></button>
    </div>
</div>
