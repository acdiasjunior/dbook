<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Book Entity
 *
 * @property int $id
 * @property string $title
 * @property int|null $year
 * @property string|null $handle
 * @property string|null $publisher
 * @property string|null $isbn
 * @property int|null $pages
 * @property string|null $notes
 * @property \Cake\I18n\FrozenTime|null $created_at
 * @property \Cake\I18n\FrozenTime $modified
 */
class Book extends Entity
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
        'title' => true,
        'year' => true,
        'handle' => true,
        'publisher' => true,
        'isbn' => true,
        'pages' => true,
        'notes' => true,
        'created_at' => true,
        'modified' => true,
    ];
}
