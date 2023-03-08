<?php

use Keymaster\Infra\View;

if (!defined("CMSIMPLE_XH_VERSION")) {header("HTTP/1.1 403 Forbidden"); exit;}

/**
 * @var View $this
 * @var string $version
 * @var list<array{key:string,arg:string,class:string}> $checks
 */
?>
<!-- keymaster plugin info -->
<h1>Keymaster_XH <?=$version?></h1>
<h4><?=$this->text('syscheck_title')?></h4>
<ul class="keymaster_syscheck">
<?foreach ($checks as $check):?>
  <p class="<?=$check['class']?>"><?=$this->text($check['key'], $check['arg'])?></p>
<?endforeach?>
</ul>
