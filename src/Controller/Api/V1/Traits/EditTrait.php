<?php
declare (strict_types = 1);

namespace App\Controller\Api\V1\Traits;

trait EditTrait
{

    use BaseTrait;

    public function edit($id)
    {
        try {
            $entity = $this->getModel()->get($id);

            $originalData = $entity->toArray();

            $entity = $this->getModel()->patchEntity($entity, $this->request->getData());

            if ($entity->hasErrors()) {
                $this->response = $this->response->withStatus(422);

                $entity = $this->getModel()->newEntity($originalData, ['accessibleFields' => ['*' => true]]);

                $this->set([
                    'message' => 'Validation failed',
                    'errors'  => $entity->getErrors(),
                    'item'    => $entity,
                ]);

                $this->viewBuilder()->setOption('serialize', ['message', 'errors', 'item']);
                return;
            }

            $message = $entity->getDirty() ? 'Record updated' : 'No changes were made to the record';

            $this->response = $this->response->withStatus(200);

            if (!$this->getModel()->save($entity)) {
                $this->response = $this->response->withStatus(500);

                $message = 'Error while updating record';
            }

            $this->set([
                'message' => $message,
                'item'    => $entity,
            ]);

            $this->viewBuilder()->setOption('serialize', ['message', 'item']);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->response = $this->response->withStatus(404);

            $this->set([
                'message' => 'Record not found.',
                'error'   => $e->getMessage(),
            ]);

            $this->viewBuilder()->setOption('serialize', ['message', 'error']);
        } catch (\Exception $e) {
            $this->response = $this->response->withStatus(500);

            $this->set([
                'message' => 'An unexpected error occurred.',
                'error'   => $e->getMessage(),
            ]);

            $this->viewBuilder()->setOption('serialize', ['message', 'error']);
        }
    }

}
