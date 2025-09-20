<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Repositories\CommentRepository;
use App\Repositories\GroupRepository;
use App\Repositories\PostRepository;
use App\Repositories\ReactionRepository;
use App\Services\FeedService;
use App\Support\Auth;
use App\Support\Csrf;
use App\Support\ProfanityFilter;
use App\Support\View;

final class CommunityController
{
    public function __construct(
        private View $view,
        private GroupRepository $groups,
        private PostRepository $posts,
        private CommentRepository $comments,
        private ReactionRepository $reactions,
        private Auth $auth,
        private Csrf $csrf,
        private ProfanityFilter $profanity,
        private FeedService $feed,
    ) {
    }

    public function index(): Response
    {
        $content = $this->view->render('pages/communities/index', [
            'groups' => $this->groups->all(),
        ]);

        return new Response($content);
    }

    public function show(int $groupId): Response
    {
        $group = $this->groups->find($groupId);
        if (!$group) {
            return new Response('Group not found', 404);
        }
        $posts = $this->posts->forGroup($groupId);
        foreach ($posts as &$post) {
            $post['comments'] = $this->comments->forPost($post['id']);
            $post['reactions'] = array_filter(
                $this->reactions->all(),
                static fn (array $reaction): bool => (int) $reaction['post_id'] === $post['id']
            );
        }
        unset($post);
        $content = $this->view->render('pages/communities/show', [
            'group' => $group,
            'posts' => $posts,
            'csrf' => $this->csrf->token(),
        ]);

        return new Response($content);
    }

    public function post(Request $request, int $groupId): Response
    {
        if (!$this->csrf->validate($request->input('_token'))) {
            return new Response('Invalid CSRF token', 419);
        }
        $user = $this->auth->user();
        if (!$user) {
            return new Response('', 302, ['Location' => '/login']);
        }
        $body = trim((string) $request->input('body'));
        if ($body === '' || !$this->profanity->isClean($body)) {
            return new Response('Message rejected by safety filter.', 422);
        }
        $post = $this->posts->create([
            'group_id' => $groupId,
            'user_id' => $user['id'],
            'body' => $body,
            'attachments' => [],
            'created_at' => (new \DateTimeImmutable())->format(DATE_ATOM),
        ]);
        $this->feed->publish($user['id'], 'posted', 'group', (string) $groupId, 'group');

        return new Response('', 302, ['Location' => '/communities/' . $groupId . '#post-' . $post['id']]);
    }

    public function comment(Request $request, int $groupId, int $postId): Response
    {
        if (!$this->csrf->validate($request->input('_token'))) {
            return new Response('Invalid CSRF token', 419);
        }
        $user = $this->auth->user();
        if (!$user) {
            return new Response('', 302, ['Location' => '/login']);
        }
        $body = trim((string) $request->input('body'));
        if ($body === '' || !$this->profanity->isClean($body)) {
            return new Response('Comment rejected by safety filter.', 422);
        }
        $this->comments->create([
            'post_id' => $postId,
            'user_id' => $user['id'],
            'body' => $body,
            'created_at' => (new \DateTimeImmutable())->format(DATE_ATOM),
        ]);

        return new Response('', 302, ['Location' => '/communities/' . $groupId . '#post-' . $postId]);
    }

    public function react(Request $request, int $groupId, int $postId): Response
    {
        if (!$this->csrf->validate($request->input('_token'))) {
            return new Response('Invalid CSRF token', 419);
        }
        $user = $this->auth->user();
        if (!$user) {
            return new Response('', 302, ['Location' => '/login']);
        }
        $type = (string) $request->input('type');
        $allowed = ['like', 'insight', 'question', 'helpful'];
        if (!in_array($type, $allowed, true)) {
            return new Response('Invalid reaction type', 422);
        }
        $existing = $this->reactions->findForPostUser($postId, $user['id']);
        if ($existing) {
            $this->reactions->update($existing['id'], [
                'type' => $type,
                'created_at' => (new \DateTimeImmutable())->format(DATE_ATOM),
            ]);
        } else {
            $this->reactions->create([
                'post_id' => $postId,
                'user_id' => $user['id'],
                'type' => $type,
                'created_at' => (new \DateTimeImmutable())->format(DATE_ATOM),
            ]);
        }

        return new Response('', 302, ['Location' => '/communities/' . $groupId . '#post-' . $postId]);
    }
}
