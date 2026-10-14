<?php

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;

/**
 * @method Comment[] all($db = null)
 * @method Comment|null one($db = null)
 */
class CommentQuery extends ActiveQuery
{
    /**
     * Public comments, plus private ones the viewer wrote or received. Admins see everything.
     */
    public function visibleTo(?User $viewer): static
    {
        if ($viewer?->isAdmin()) {
            return $this;
        }

        $condition = ['is_private' => false];

        if ($viewer !== null) {
            $condition = [
                'or',
                $condition,
                ['author_id' => $viewer->id],
                ['recipient_id' => $viewer->id],
            ];
        }

        return $this->andWhere($condition);
    }

    public function unread(): static
    {
        return $this->andWhere(['read_at' => null]);
    }
}
