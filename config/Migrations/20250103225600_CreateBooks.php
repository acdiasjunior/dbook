<?php
declare (strict_types = 1);

use Migrations\AbstractMigration;

class CreateBooks extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('books');
        $table
            ->addColumn('title', 'string', [
                'limit' => 255,
            ])
            ->addColumn('year', 'integer', [
                'null' => true,
            ])
            ->addColumn('handle', 'string', [
                'limit' => 255,
                'null'  => true,
            ])
            ->addColumn('publisher', 'string', [
                'limit' => 255,
                'null'  => true,
            ])
            ->addColumn('isbn', 'string', [
                'limit' => 50,
                'null'  => true,
            ])
            ->addColumn('pages', 'integer', [
                'null' => true,
            ])
            ->addColumn('notes', 'text', [
                'null' => true,
            ])
            ->addColumn('created_at', 'datetime', [
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => 'CURRENT_TIMESTAMP',
            ])
            ->addIndex(['title', 'year'], ['unique' => true])
            ->addIndex(['isbn'], ['unique' => true])
            ->create();
    }
}
