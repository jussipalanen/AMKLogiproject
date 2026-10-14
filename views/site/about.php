<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'Tietoa';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about col-lg-8">
    <h1 class="h2"><?= Html::encode($this->title) ?></h1>

    <p>
        AMKLogi on sosiaalinen mediapalvelu ammattikorkeakoulun opiskelijoille. Se syntyi
        vuonna 2013 opinnäytetyönä Yii 1.1 -sovelluskehyksellä, ja on sittemmin päivitetty
        Yii 2:een ja nykyaikaiseen PHP:hen.
    </p>

    <h2 class="h5 mt-4">Ominaisuudet</h2>
    <ul>
        <li>Rekisteröityminen ja kirjautuminen</li>
        <li>Profiilisivu osoitteessa <code><?= Html::encode(Yii::$app->request->hostInfo) ?>/<em>tunnus</em></code></li>
        <li>Profiilikuvan lataus</li>
        <li>Kommenttiseinä julkisilla ja yksityisillä kommenteilla sekä ilmoitus uusista kommenteista</li>
    </ul>
</div>
