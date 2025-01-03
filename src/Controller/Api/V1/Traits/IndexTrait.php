<?php
declare (strict_types = 1);

namespace App\Controller\Api\V1\Traits;

trait IndexTrait
{

    use BaseTrait;

    public function index()
    {
        $items = $this->getModel()->find('all')->all();

        $this->set('items', $items);
        $this->viewBuilder()->setOption('serialize', ['items']);
    }

}
