<?php
declare (strict_types = 1);

namespace App\Controller\Api\V1\Traits;

use Cake\Datasource\Exception\RecordNotFoundException;

trait ViewTrait
{

    use BaseTrait;

    public function view($id)
    {
        try {
            $item = $this->getModel()->get($id);

            $this->response = $this->response->withStatus(200);

            $this->set('item', $item);
            $this->viewBuilder()->setOption('serialize', ['item']);
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
