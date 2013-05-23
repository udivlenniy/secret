<?php
/**
 * Created by JetBrains PhpStorm.
 * User: artem
 * Date: 21.05.13
 * Time: 10:59
 * To change this template use File | Settings | File Templates.
 */
?>
<strong>Номер счета</strong>:<?=$partner->partner_soc_id?><br>
<strong>Статус пользователя</strong>:<?=$partner->statuspartner?><br>
<strong>Уровень в партнерской программе</strong>:<?=$partner->partnerlevel?><br>
<strong>Баллы активности</strong>:<?=$partner->active_points?><br>

<strong>Уровень сотрудника в моей структуре (расстояние от текущего аккаунта)</strong>:<?=$data['partner_level_from_me']?><br>
