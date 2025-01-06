<?php
declare (strict_types = 1);

namespace App\Controller\Api\V1\Traits;

trait AddTrait
{

    use BaseTrait;

    public function add()
    {
        try {
            $entity = $this->getModel()->newEntity($this->request->getData());

            if ($entity->hasErrors()) {
                $this->response = $this->response->withStatus(422);

                $this->set([
                    'message' => 'Validation failed',
                    'errors'  => $entity->getErrors(),
                ]);

                $this->viewBuilder()->setOption('serialize', ['message', 'errors']);

                return;
            }

            if ($this->getModel()->save($entity)) {
                $this->response = $this->response->withStatus(201);

                $this->set([
                    'message' => 'Record created',
                    'item'    => $entity,
                ]);

                $this->viewBuilder()->setOption('serialize', ['message', 'item']);

                return;
            }

            $this->response = $this->response->withStatus(409);

            $this->set([
                'message' => 'Conflict: Unable to save record',
            ]);

            $this->viewBuilder()->setOption('serialize', ['message']);
        } catch (\Exception $e) {
            $this->response = $this->response->withStatus(500);

            $this->set([
                'message' => 'An unexpected error occurred',
                'error'   => $e->getMessage(),
            ]);

            $this->viewBuilder()->setOption('serialize', ['message', 'error']);
        }
    }

}
