<?php
declare (strict_types = 1);

namespace App\Controller\Api;

use App\Controller\AppController;

class ApiController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Authentication.Authentication');
    }
}
