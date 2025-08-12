<?php

use humhub\components\Migration;

class m000001_000000_initial extends Migration
{
    public function safeUp()
    {
        // Support Categories table
        $this->safeCreateTable('requestsupport_category', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'description' => $this->text(),
            'space_id' => $this->integer()->notNull(),
            'sort_order' => $this->integer()->defaultValue(0),
            'is_active' => $this->boolean()->defaultValue(true),
            'created_at' => $this->dateTime(),
            'created_by' => $this->integer(),
            'updated_at' => $this->dateTime(),
            'updated_by' => $this->integer(),
        ]);

        // Support Requests table
        $this->safeCreateTable('requestsupport_request', [
            'id' => $this->primaryKey(),
            'subject' => $this->string(255)->notNull(),
            'description' => $this->text()->notNull(),
            'category_id' => $this->integer()->notNull(),
            'status' => $this->string(20)->defaultValue('open'),
            'created_at' => $this->dateTime(),
            'created_by' => $this->integer(),
            'updated_at' => $this->dateTime(),
            'updated_by' => $this->integer(),
        ]);

        // Support Responses table
        $this->safeCreateTable('requestsupport_response', [
            'id' => $this->primaryKey(),
            'request_id' => $this->integer()->notNull(),
            'message' => $this->text()->notNull(),
            'created_at' => $this->dateTime(),
            'created_by' => $this->integer(),
            'updated_at' => $this->dateTime(),
            'updated_by' => $this->integer(),
        ]);

        // Indexes
        $this->createIndex('idx_requestsupport_category_space_id', 'requestsupport_category', 'space_id');
        $this->createIndex('idx_requestsupport_category_active', 'requestsupport_category', 'is_active');
        $this->createIndex('idx_requestsupport_request_status', 'requestsupport_request', 'status');
        $this->createIndex('idx_requestsupport_request_created_by', 'requestsupport_request', 'created_by');
        $this->createIndex('idx_requestsupport_response_request_id', 'requestsupport_response', 'request_id');

        // Foreign keys
        $this->addForeignKey(
            'fk_requestsupport_category_space_id',
            'requestsupport_category',
            'space_id',
            'space',
            'id',
            'CASCADE',
        );

        $this->addForeignKey(
            'fk_requestsupport_category_created_by',
            'requestsupport_category',
            'created_by',
            'user',
            'id',
            'SET NULL',
        );

        $this->addForeignKey(
            'fk_requestsupport_request_created_by',
            'requestsupport_request',
            'created_by',
            'user',
            'id',
            'CASCADE',
        );

        $this->addForeignKey(
            'fk_requestsupport_response_request_id',
            'requestsupport_response',
            'request_id',
            'requestsupport_request',
            'id',
            'CASCADE',
        );

        $this->addForeignKey(
            'fk_requestsupport_response_created_by',
            'requestsupport_response',
            'created_by',
            'user',
            'id',
            'CASCADE',
        );
    }

    public function safeDown()
    {
        echo "m000001_000000_initial cannot be reverted.\n";

        return false;
    }
}
