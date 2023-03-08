<?php

use Keymaster\Infra\View;

/**
 * @var View $this
 * @var string $version
 * @var list<array{key:string,arg:string,class:string}> $checks
 */

?>

<h1>Keymaster_XH <?=$version?></h1>
<h4><?=$this->text("syscheck_title")?></h4>
<ul class="keymaster_syscheck">
<?php foreach ($checks as $check):?>
  <p class="<?=$check['class']?>"><?=$this->text($check['key'], $check['arg'])?></p>
<?php endforeach?>
</ul>
