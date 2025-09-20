<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Http\Response;
use App\Services\AnalyticsService;
use App\Support\Auth;
use App\Support\View;

final class AdminController
{
    public function __construct(
        private View $view,
        private AnalyticsService $analytics,
        private Auth $auth,
    ) {
    }

    public function analytics(): Response
    {
        if (!$this->auth->checkPermission('analytics.view')) {
            return new Response('Forbidden', 403);
        }
        $content = $this->view->render('admin/analytics', [
            'metrics' => $this->analytics->metrics(),
        ]);

        return new Response($content);
    }
}
