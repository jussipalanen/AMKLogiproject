<?php

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * @method Album[] all($db = null)
 * @method Album|null one($db = null)
 */
class AlbumQuery extends ActiveQuery
{
    /**
     * Public albums, plus the viewer's own private albums.
     */
    public function visibleTo(?User $viewer): static
    {
        $condition = ['{{%album}}.[[is_private]]' => false];

        if ($viewer !== null) {
            $condition = ['or', $condition, ['{{%album}}.[[user_id]]' => $viewer->id]];
        }

        return $this->andWhere($condition);
    }

    /**
     * Adds the number of images as Album::$imageCount.
     */
    public function withImageCount(): static
    {
        return $this->addSelect([
            '{{%album}}.*',
            'imageCount' => new Expression('(SELECT COUNT(*) FROM {{%image}} WHERE {{%image}}.[[album_id]] = {{%album}}.[[id]])'),
        ]);
    }
}
