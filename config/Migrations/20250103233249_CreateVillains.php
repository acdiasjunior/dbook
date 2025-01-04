<?php
declare (strict_types = 1);

use Migrations\AbstractMigration;

class CreateVillains extends AbstractMigration
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
        $table = $this->table('villains');
        $table
            ->addColumn('name', 'string', [
                'limit' => 255,
            ])
            ->addColumn('url', 'string', [
                'limit' => 255,
                'null'  => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => 'CURRENT_TIMESTAMP',
            ])
            ->addColumn('modified', 'datetime', [
                'default' => 'CURRENT_TIMESTAMP',
            ])
            ->addIndex(['name'], ['unique' => true])
            ->create();
    }
}
