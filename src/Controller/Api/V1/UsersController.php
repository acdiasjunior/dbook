<?php
declare (strict_types = 1);

namespace App\Controller\Api\V1;

use App\Controller\Api\ApiController;
use App\Controller\Api\V1\Traits\CrudTrait;
use Cake\View\JsonView;

class UsersController extends ApiController
{
    use CrudTrait;

    public function viewClasses(): array
    {
        return [JsonView::class];
    }
}
