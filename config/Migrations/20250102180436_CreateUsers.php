<?php
declare (strict_types = 1);

use Migrations\AbstractMigration;

class CreateUsers extends AbstractMigration
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
        $table = $this->table('users');
        $table
            ->addColumn('email', 'string', [
                'limit' => 255,
                'null'  => false,
            ])
            ->addColumn('password', 'string', [
                'limit' => 255,
                'null'  => false,
            ])
            ->addColumn('register_token', 'string', [
                'limit'   => 255,
                'null'    => true,
                'default' => null,
            ])
            ->addColumn('active', 'boolean', [
                'default' => false,
                'null'    => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => 'CURRENT_TIMESTAMP',
                'null'    => false,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => 'CURRENT_TIMESTAMP',
                'update'  => 'CURRENT_TIMESTAMP',
                'null'    => false,
            ])
            ->addIndex(['email'], ['unique' => true])
            ->create();
    }
}
