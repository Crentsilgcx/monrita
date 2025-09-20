<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Repositories\EventRegistrationRepository;
use App\Repositories\EventRepository;
use App\Support\Auth;
use App\Support\Csrf;
use App\Support\View;

final class EventController
{
    public function __construct(
        private View $view,
        private EventRepository $events,
        private EventRegistrationRepository $registrations,
        private Auth $auth,
        private Csrf $csrf,
    ) {
    }

    public function index(): Response
    {
        $content = $this->view->render('pages/events/index', [
            'events' => $this->events->all(),
            'csrf' => $this->csrf->token(),
        ]);

        return new Response($content);
    }

    public function register(Request $request, int $eventId): Response
    {
        if (!$this->csrf->validate($request->input('_token'))) {
            return new Response('Invalid CSRF token', 419);
        }
        $user = $this->auth->user();
        if (!$user) {
            return new Response('', 302, ['Location' => '/login']);
        }
        if (!$this->registrations->findForEventUser($eventId, $user['id'])) {
            $this->registrations->create([
                'event_id' => $eventId,
                'user_id' => $user['id'],
                'status' => 'registered',
                'registered_at' => (new \DateTimeImmutable())->format(DATE_ATOM),
            ]);
        }

        return new Response('', 302, ['Location' => '/events']);
    }
}
