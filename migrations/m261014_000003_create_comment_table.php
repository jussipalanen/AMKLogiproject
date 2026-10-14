<?php

declare(strict_types=1);

use yii\db\Migration;

/**
 * Wall comments (the original `comments` table).
 *
 * The "true"/"false" strings of `isprivate` and `isnew` become a boolean and a nullable
 * `read_at` timestamp.
 */
final class m261014_000003_create_comment_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%comment}}', [
            'id' => $this->primaryKey(),
            'author_id' => $this->integer()->notNull(),
            'recipient_id' => $this->integer()->notNull(),
            'body' => $this->text()->notNull(),
            'is_private' => $this->boolean()->notNull()->defaultValue(false),
            'read_at' => $this->integer(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            // Inline so the same migration works on SQLite, which cannot ALTER TABLE ADD CONSTRAINT.
            'FOREIGN KEY ([[author_id]]) REFERENCES {{%user}} ([[id]]) ON DELETE CASCADE',
            'FOREIGN KEY ([[recipient_id]]) REFERENCES {{%user}} ([[id]]) ON DELETE CASCADE',
        ], $this->tableOptions());

        $this->createIndex('idx-comment-recipient_id-created_at', '{{%comment}}', ['recipient_id', 'created_at']);
        $this->createIndex('idx-comment-recipient_id-read_at', '{{%comment}}', ['recipient_id', 'read_at']);
        $this->createIndex('idx-comment-author_id', '{{%comment}}', 'author_id');
    }

    public function safeDown(): void
    {
        $this->dropTable('{{%comment}}');
    }

    private function tableOptions(): ?string
    {
        return $this->db->driverName === 'mysql'
            ? 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB'
            : null;
    }
}
