<?php
declare (strict_types = 1);

namespace App\Controller\Api\V1\Traits;

trait CrudTrait
{

    use IndexTrait;
    use ViewTrait;
    use AddTrait;
    use EditTrait;
    use DeleteTrait;

}
