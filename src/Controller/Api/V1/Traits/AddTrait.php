<?php
declare (strict_types = 1);

namespace App\Controller\Api\V1\Traits;

trait AddTrait
{

    use BaseTrait;

    public function add()
    {
        $entity = $this->getModel()->newEntity($this->request->getData());

        if ($this->getModel()->save($entity)) {
            $message = 'Record created';
        } else {
            $message = 'Error while creating record';
        }

        $serialize = ['item', 'message'];

        if ($entity->getErrors()) {
            $this->set(['errors' => $entity->getErrors()]);
            $serialize[] = 'errors';
        }

        $this->set([
            'message' => $message,
            'item'    => $entity,
        ]);

        $this->viewBuilder()->setOption('serialize', $serialize);
    }

}
