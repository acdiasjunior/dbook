<?php
declare (strict_types = 1);

namespace App\Controller\Api\V1\Traits;

trait EditTrait
{

    use BaseTrait;

    public function edit($id)
    {
        $entity = $this->getModel()->get($id);

        $originalData = $entity->toArray();

        $entity = $this->getModel()->patchEntity($entity, $this->request->getData());

        $message = $entity->getDirty() ? 'Record updated' : 'No changes were made to the record';

        if (!$this->getModel()->save($entity)) {
            $message = 'Error while updating record';
        }

        $serialize = ['item', 'message'];

        if ($entity->getErrors()) {
            $this->set(['errors' => $entity->getErrors()]);
            $serialize[] = 'errors';

            $entity = $this->getModel()->newEntity($originalData, ['accessibleFields' => ['*' => true]]);
        }

        $this->set([
            'message' => $message,
            'item'    => $entity,
        ]);

        $this->viewBuilder()->setOption('serialize', $serialize);
    }

}
