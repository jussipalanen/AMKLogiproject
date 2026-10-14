<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'Tietoa';
?>
<article class="amk-box site-about">
    <div class="amk-box-body">
        <h1 class="amk-box-title"><?= Html::encode($this->title) ?></h1>

        <p>
            AMKLogi on sosiaalinen mediapalvelu ammattikorkeakoulun opiskelijoille ja opettajille. Se syntyi
            vuonna 2013 opinnäytetyönä Yii 1.1 -sovelluskehyksellä, ja on sittemmin päivitetty
            Yii 2:een ja nykyaikaiseen PHP:hen alkuperäistä ulkoasua kunnioittaen.
        </p>

        <h2>Ominaisuudet</h2>
        <ul class="amk-list">
            <li>Rekisteröityminen ja kirjautuminen</li>
            <li>Profiilisivu osoitteessa <code><?= Html::encode(Yii::$app->request->hostInfo) ?>/<em>tunnus</em></code></li>
            <li>Profiilikuvan lataus</li>
            <li>Kommenttiseinä julkisilla ja yksityisillä kommenteilla sekä ilmoitus uusista kommenteista</li>
        </ul>
    </div>
</article>
