<?php
declare (strict_types = 1);

namespace App\Controller\Api\V1;

use App\Controller\Api\ApiController;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Mailer\Mailer;
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
        $secretKey = env('JWT_SECRET', 'your-secret-key');

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
        $this->request->allowMethod(['post']);

        $this->Users = $this->fetchTable('Users');

        $user = $this->Users->newEmptyEntity();
        $user = $this->Users->patchEntity($user, $this->request->getData());

        if (!$this->Users->save($user)) {
            throw new BadRequestException('Unable to register user.');
        }

        $confirm_url = Router::url("/api/v1/auth/confirm?token={$user->register_token}", true);

        // Send confirmation email with the register token
        $mailer = new Mailer('default');
        $mailer->setTo($user->email)
            ->setSubject('Confirm Your Account')
            ->deliver("Please confirm your account using this link: {$confirm_url}");

        $this->set([
            'success' => true,
            'message' => 'User registered successfully. Please check your email to confirm your account.',
        ]);

        $this->viewBuilder()->setOption('serialize', ['success', 'message']);
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
