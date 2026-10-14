<?php

declare(strict_types=1);

use yii\db\Migration;

/**
 * Profile details shown on a user's page (the original `profile` table).
 * Every column except user_id is optional; the row is created when the user registers.
 */
final class m261014_000002_create_profile_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%profile}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull()->unique(),
            'firstname' => $this->string(60),
            'surname' => $this->string(60),
            'gender' => $this->string(10),
            'birthday' => $this->date(),
            'email' => $this->string(255),
            'city' => $this->string(100),
            'relationship' => $this->string(30),
            'homepage' => $this->string(255),
            'skype' => $this->string(64),
            'avatar_path' => $this->string(255),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            // Inline so the same migration works on SQLite, which cannot ALTER TABLE ADD CONSTRAINT.
            'FOREIGN KEY ([[user_id]]) REFERENCES {{%user}} ([[id]]) ON DELETE CASCADE',
        ], $this->tableOptions());
    }

    public function safeDown(): void
    {
        $this->dropTable('{{%profile}}');
    }

    private function tableOptions(): ?string
    {
        return $this->db->driverName === 'mysql'
            ? 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB'
            : null;
    }
}
