<?php

use Keymaster\Infra\View;

/**
 * @var View $this
 * @var string $version
 * @var array<string,string> $checks
 */

?>

<h1>Keymaster_XH <?=$version?></h1>
<h4><?=$this->text("syscheck_title")?></h4>
<ul class="keymaster_syscheck">
<?php foreach ($checks as $check => $state):?>
  <p class="<?=$state?>"><?=$check?></p>
<?php endforeach?>
</ul>
