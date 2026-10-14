<?php

declare(strict_types=1);

use yii\db\Migration;

/**
 * Uploaded images. `album_id` NULL means "Lajittelemattomat" (unsorted, visible only to the
 * owner); deleting an album moves its images there.
 */
final class m261014_000005_create_image_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%image}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'album_id' => $this->integer(),
            'file_path' => $this->string(255)->notNull(),
            'thumb_path' => $this->string(255)->notNull(),
            'width' => $this->integer(),
            'height' => $this->integer(),
            'caption' => $this->string(255),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            // Inline so the same migration works on SQLite, which cannot ALTER TABLE ADD CONSTRAINT.
            'FOREIGN KEY ([[user_id]]) REFERENCES {{%user}} ([[id]]) ON DELETE CASCADE',
            'FOREIGN KEY ([[album_id]]) REFERENCES {{%album}} ([[id]]) ON DELETE SET NULL',
        ], $this->tableOptions());

        $this->createIndex('idx-image-user_id-album_id', '{{%image}}', ['user_id', 'album_id']);
        $this->createIndex('idx-image-album_id', '{{%image}}', 'album_id');
    }

    public function safeDown(): void
    {
        $this->dropTable('{{%image}}');
    }

    private function tableOptions(): ?string
    {
        return $this->db->driverName === 'mysql'
            ? 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB'
            : null;
    }
}
