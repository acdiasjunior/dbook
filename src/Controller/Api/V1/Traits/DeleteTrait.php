<?php
declare (strict_types = 1);

namespace App\Controller\Api\V1\Traits;

trait DeleteTrait
{

    use BaseTrait;

    public function delete($id)
    {
        $entity  = $this->getModel()->get($id);

        $message = 'Record deleted';

        if (!$this->getModel()->delete($entity)) {
            $message = 'Error while deleting record';
        }

        $this->set(compact('message', 'entity'));
        $this->viewBuilder()->setOption('serialize', ['message', 'entity']);
    }

}
