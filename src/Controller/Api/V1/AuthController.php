<?php
declare (strict_types = 1);

namespace App\Controller\Api\V1;

use App\Controller\Api\ApiController;
use App\Service\RabbitMQService;
use Cake\Core\Configure;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Routing\Router;
use Firebase\JWT\JWT;

class AuthController extends ApiController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Authentication.Authentication');

        // Allow unauthenticated users to access the login action
        $this->Authentication->allowUnauthenticated(['login', 'register', 'confirm']);
    }

    public function login()
    {
        $this->request->allowMethod(['post']);

        $user = $this->Authentication->getIdentity();

        if (!$user) {
            throw new UnauthorizedException('Invalid email or password.');
        }

        // Generate JWT token
        $secretKey = Configure::readOrFail('dbook.jwt.secret');

        $payload = [
            'sub' => $user->id,
            'exp' => time() + 60 * 60,
        ];

        $token = JWT::encode($payload, $secretKey, 'HS256');

        $this->set([
            'success' => true,
            'token'   => $token,
            'expires' => $payload['exp'],
        ]);

        $this->viewBuilder()->setOption('serialize', ['success', 'token', 'expires']);
    }

    /**
     * Register a new user
     */
    public function register()
    {
        try {
            $this->request->allowMethod(['post']);

            $this->Users = $this->fetchTable('Users');

            $user = $this->Users->newEmptyEntity();
            $user = $this->Users->patchEntity($user, $this->request->getData());

            // Check if the user already exists
            $userExists = $this->Users->exists(['email' => $user->email]);

            if ($userExists) {
                $this->response = $this->response->withStatus(409);

                $this->set([
                    'success' => false,
                    'message' => 'Unable to process registration. User already exists.',
                ]);

                $this->viewBuilder()->setOption('serialize', ['success', 'message']);
                return;
            }

            if ($user->hasErrors()) {
                $this->response = $this->response->withStatus(422);

                $this->set([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $user->getErrors(),
                ]);

                $this->viewBuilder()->setOption('serialize', ['success', 'message', 'errors']);
                return;
            }

            if (!$this->Users->save($user)) {
                $this->response = $this->response->withStatus(409);

                $this->set([
                    'success' => false,
                    'message' => 'Unable to register user.',
                ]);

                $this->viewBuilder()->setOption('serialize', ['success', 'message']);
                return;
            }

            $confirm_url = Router::url("/api/v1/auth/confirm?token={$user->register_token}", true);

            $emailTask = [
                'email'   => $user->email,
                'subject' => 'Confirm Your Account',
                'body'    => "Please confirm your account using this link: {$confirm_url}",
            ];

            RabbitMQService::getInstance()->sendToQueue('email_queue', $emailTask);

            $this->response = $this->response->withStatus(201);

            $this->set([
                'success' => true,
                'message' => 'User registered successfully. Please check your email to confirm your account.',
            ]);

            $this->viewBuilder()->setOption('serialize', ['success', 'message']);
        } catch (\Exception $e) {
            $this->response = $this->response->withStatus(500);

            $this->set([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error'   => $e->getMessage(),
            ]);

            $this->viewBuilder()->setOption('serialize', ['success', 'message', 'error']);
        }
    }

    public function confirm()
    {
        $this->request->allowMethod(['get']);

        $token = $this->request->getQuery('token');

        if (!$token) {
            throw new BadRequestException('Token is required.');
        }

        $this->Users = $this->fetchTable('Users');

        // Find user by register_token
        $user = $this->Users->find()
            ->where(['register_token' => $token, 'active' => false])
            ->first();

        if (!$user) {
            throw new NotFoundException('Invalid or expired token.');
        }

        // Activate the user
        $user->register_token = null; // Clear the token
        $user->active         = true; // Activate the account

        if (!$this->Users->save($user)) {
            throw new BadRequestException('Unable to confirm user.');
        }

        $this->set([
            'success' => true,
            'message' => 'User confirmed successfully.',
        ]);

        $this->viewBuilder()->setOption('serialize', ['success', 'message']);
    }
}
