<?php
declare (strict_types = 1);

namespace App\Controller\Api\V1\Traits;

trait BaseTrait
{

    /**
     * Get the associated model for the controller.
     *
     * @return \Cake\ORM\Table
     */
    protected function getModel()
    {
        // Dynamically resolve the model class based on the controller's name
        return $this->getTableLocator()->get($this->name);
    }

}
