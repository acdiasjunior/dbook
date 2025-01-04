<?php
declare (strict_types = 1);

use Migrations\AbstractMigration;

class CreateBookVillains extends AbstractMigration
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
        $table = $this->table('book_villains');
        $table
            ->addColumn('book_id', 'integer')
            ->addColumn('villain_id', 'integer')
            ->addForeignKey('book_id', 'books', 'id', [
                'delete' => 'CASCADE',
            ])
            ->addForeignKey('villain_id', 'villains', 'id', [
                'delete' => 'CASCADE',
            ])
            ->addIndex(['book_id', 'villain_id'], ['unique' => true])
            ->create();
    }
}
