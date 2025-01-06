<?php
declare (strict_types = 1);

namespace App\Controller\Api\V1\Traits;

trait IndexTrait
{

    use BaseTrait;

    public function index()
    {
        $query = $this->getModel()->find();

        $validColumns = $this->getModel()->getSchema()->columns();

        foreach ($this->request->getQuery() as $key => $value) {
            if (in_array($key, $validColumns, true)) {
                $query->where([$key => $value]);
            }
        }

        $items = $query->all();

        $this->set('items', $items);
        $this->viewBuilder()->setOption('serialize', ['items']);
    }

}
