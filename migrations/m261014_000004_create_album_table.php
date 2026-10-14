<?php

declare(strict_types=1);

use yii\db\Migration;

/**
 * Photo albums. The original app only had an empty "album" page and an unused `images` table.
 */
final class m261014_000004_create_album_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%album}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'name' => $this->string(80)->notNull(),
            'description' => $this->string(500),
            'is_private' => $this->boolean()->notNull()->defaultValue(false),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            // Inline so the same migration works on SQLite, which cannot ALTER TABLE ADD CONSTRAINT.
            'FOREIGN KEY ([[user_id]]) REFERENCES {{%user}} ([[id]]) ON DELETE CASCADE',
        ], $this->tableOptions());

        $this->createIndex('idx-album-user_id', '{{%album}}', 'user_id');
    }

    public function safeDown(): void
    {
        $this->dropTable('{{%album}}');
    }

    private function tableOptions(): ?string
    {
        return $this->db->driverName === 'mysql'
            ? 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB'
            : null;
    }
}
