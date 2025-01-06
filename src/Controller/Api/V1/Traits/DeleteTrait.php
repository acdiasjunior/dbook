<?php
declare (strict_types = 1);

namespace App\Controller\Api\V1\Traits;

use Cake\Datasource\Exception\RecordNotFoundException;

trait DeleteTrait
{

    use BaseTrait;

    public function delete($id)
    {
        try {
            $entity = $this->getModel()->get($id);

            if ($this->getModel()->delete($entity)) {
                $this->response = $this->response->withStatus(200);
                $message        = 'Record deleted successfully.';
            } else {
                $this->response = $this->response->withStatus(500);
                $message        = 'Error while deleting the record.';
            }

            $this->set(compact('message', 'entity'));
            $this->viewBuilder()->setOption('serialize', ['message', 'entity']);
        } catch (RecordNotFoundException $e) {
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
