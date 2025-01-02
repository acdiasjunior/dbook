<?php
declare (strict_types = 1);

use Authentication\PasswordHasher\DefaultPasswordHasher;
use Migrations\AbstractSeed;

/**
 * Users seed.
 */
class UsersSeed extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeds is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     *
     * @return void
     */
    public function run(): void
    {
        $rs = $this->fetchRow('SELECT COUNT(*) as count FROM users');

        if ((int) $rs['count'] > 0) {
            echo "Users table already has records. Skipping seeding.\n";
            return;
        }

        $data = [
            [
                'email'          => 'user@dbook.local',
                'password'       => (new DefaultPasswordHasher())->hash('123456'),
                'register_token' => null,
                'active'         => true,
                'created'        => date('Y-m-d H:i:s'),
                'modified'       => date('Y-m-d H:i:s'),
            ],
        ];

        $table = $this->table('users');
        $table->insert($data)->save();
    }
}
