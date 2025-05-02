<?php

use Plib\View;

if (!defined("CMSIMPLE_XH_VERSION")) {header("HTTP/1.1 403 Forbidden"); exit;}

/**
 * @var View $this
 */
?>
<!-- keymaster dialog -->
<div id="keymaster" style="display: none">
  <div class="keymaster_message">
    <p></p>
    <div class="keymaster_buttons">
      <button><?=$this->text('button_prolong')?></button>
    </div>
</div>
