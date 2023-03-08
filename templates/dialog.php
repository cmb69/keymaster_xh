<?php

use Keymaster\Infra\View;

/**
 * @var View $this
 */

?>

<div id="keymaster" style="display: none">
  <div class="keymaster_message">
    <p><?=$this->text("warning_singular")?></p>
    <div class="keymaster_buttons">
      <button><?=$this->text("button_prolong")?></button>
    </div>
</div>
