<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Support\AuditLogger;
use App\Support\Auth;
use App\Support\Csrf;
use App\Support\SessionManager;
use App\Support\View;

final class AuthController
{
    private const RATE_LIMIT_MAX = 5;

    public function __construct(
        private View $view,
        private Auth $auth,
        private Csrf $csrf,
        private SessionManager $session,
        private AuditLogger $audit,
    ) {
    }

    public function showLogin(): Response
    {
        $content = $this->view->render('pages/login', [
            'csrf' => $this->csrf->token(),
            'error' => $this->session->get('login_error'),
        ]);
        $this->session->forget('login_error');

        return new Response($content);
    }

    public function login(Request $request): Response
    {
        $attempts = (int) $this->session->get('login_attempts', 0);
        if ($attempts >= self::RATE_LIMIT_MAX) {
            $this->session->put('login_error', 'Too many attempts. Please wait and try again.');

            return $this->redirect('/login');
        }

        if (!$this->csrf->validate($request->input('_token'))) {
            $this->session->put('login_error', 'Invalid CSRF token.');

            return $this->redirect('/login');
        }

        $email = trim((string) $request->input('email'));
        $password = (string) $request->input('password');

        if ($this->auth->attempt($email, $password)) {
            $user = $this->auth->user();
            $this->session->forget('login_attempts');
            $this->audit->log($user['id'] ?? null, 'login', 'user', (string) ($user['id'] ?? '0'), $request->ip(), $request->userAgent());

            return $this->redirect('/dashboard');
        }

        $this->session->put('login_attempts', $attempts + 1);
        $this->session->put('login_error', 'Invalid credentials.');
        $this->audit->log(null, 'login_failed', 'user', null, $request->ip(), $request->userAgent(), ['email' => $email]);

        return $this->redirect('/login');
    }

    public function logout(Request $request): Response
    {
        $user = $this->auth->user();
        $this->auth->logout();
        $this->audit->log($user['id'] ?? null, 'logout', 'user', $user ? (string) $user['id'] : null, $request->ip(), $request->userAgent());

        return $this->redirect('/');
    }

    private function redirect(string $path): Response
    {
        return new Response('', 302, ['Location' => $path]);
    }
}
