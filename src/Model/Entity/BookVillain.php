<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * BookVillain Entity
 *
 * @property int $id
 * @property int $book_id
 * @property int $villain_id
 *
 * @property \App\Model\Entity\Book $book
 * @property \App\Model\Entity\Villain $villain
 */
class BookVillain extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected $_accessible = [
        'book_id' => true,
        'villain_id' => true,
        'book' => true,
        'villain' => true,
    ];
}
