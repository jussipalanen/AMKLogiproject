<?php

declare(strict_types=1);

use yii\db\Migration;

/**
 * Users (the original `users` table).
 *
 * MD5 `password` becomes a bcrypt `password_hash`, `register_date`/`login_date`
 * become `created_at`/`last_login_at`, and `auth_key` backs "remember me" cookies.
 */
final class m261014_000001_create_user_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string(30)->notNull()->unique(),
            'email' => $this->string(255)->notNull()->unique(),
            'password_hash' => $this->string(255)->notNull(),
            'auth_key' => $this->string(32)->notNull(),
            'role' => $this->string(16)->notNull()->defaultValue('student'),
            'last_login_at' => $this->integer(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $this->tableOptions());
    }

    public function safeDown(): void
    {
        $this->dropTable('{{%user}}');
    }

    private function tableOptions(): ?string
    {
        return $this->db->driverName === 'mysql'
            ? 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB'
            : null;
    }
}
