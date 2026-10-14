<?php

declare(strict_types=1);

use yii\db\Migration;

/**
 * Removes the "Parisuhdetilanne" (relationship status) profile field.
 */
final class m261014_000006_drop_relationship_from_profile extends Migration
{
    public function safeUp(): void
    {
        if ($this->db->driverName === 'sqlite') {
            // Yii's SQLite query builder has no dropColumn(); SQLite 3.35+ supports it natively.
            $this->execute('ALTER TABLE {{%profile}} DROP COLUMN [[relationship]]');
        } else {
            $this->dropColumn('{{%profile}}', 'relationship');
        }
    }

    public function safeDown(): void
    {
        $this->addColumn('{{%profile}}', 'relationship', $this->string(30));
    }
}
