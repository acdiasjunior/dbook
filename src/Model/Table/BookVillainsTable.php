<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * BookVillains Model
 *
 * @property \App\Model\Table\BooksTable&\Cake\ORM\Association\BelongsTo $Books
 * @property \App\Model\Table\VillainsTable&\Cake\ORM\Association\BelongsTo $Villains
 *
 * @method \App\Model\Entity\BookVillain newEmptyEntity()
 * @method \App\Model\Entity\BookVillain newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\BookVillain[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\BookVillain get($primaryKey, $options = [])
 * @method \App\Model\Entity\BookVillain findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\BookVillain patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\BookVillain[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\BookVillain|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\BookVillain saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\BookVillain[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\BookVillain[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\BookVillain[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\BookVillain[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class BookVillainsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('book_villains');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Books', [
            'foreignKey' => 'book_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Villains', [
            'foreignKey' => 'villain_id',
            'joinType' => 'INNER',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('book_id')
            ->notEmptyString('book_id');

        $validator
            ->integer('villain_id')
            ->notEmptyString('villain_id');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn('book_id', 'Books'), ['errorField' => 'book_id']);
        $rules->add($rules->existsIn('villain_id', 'Villains'), ['errorField' => 'villain_id']);

        return $rules;
    }
}
