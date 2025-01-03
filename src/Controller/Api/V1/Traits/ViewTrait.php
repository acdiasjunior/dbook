<?php
declare (strict_types = 1);

namespace App\Controller\Api\V1\Traits;

trait ViewTrait
{

    use BaseTrait;

    public function view($id)
    {
        $item = $this->getModel()->get($id);

        $this->set('item', $item);
        $this->viewBuilder()->setOption('serialize', ['item']);
    }

}
