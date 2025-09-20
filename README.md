# Race JHS Raw PHP Demo

This repository provides a lightweight PHP 8.2 application that simulates the multi-tenant Race JHS learning experience using file-based storage. It replaces the Laravel specification with framework-free PHP suitable for environments without Composer access while maintaining core user journeys.

## Features

- File-backed repositories with caching and audit logging.
- Invite-style learner onboarding workflow (identity, profile, interests).
- Session authentication with CSRF protection and basic rate limiting.
- Courses, modules, lessons, and lesson completion tracking with activity feed updates.
- Assessments with scoring and simple career recommendation snapshots.
- Communities with posts, comments, reactions, and a profanity safe-list.
- Events listing and registrations.
- Admin analytics dashboard gated by RBAC permissions.

## Getting Started

1. Ensure PHP 8.2+ is available.
2. Seed the JSON data store:

   ```bash
   php scripts/seed.php
   ```

   The script prints demo credentials for the seeded super administrator.

3. Start the built-in PHP development server:

   ```bash
   php -S localhost:8000 index.php
   ```

4. Visit `http://localhost:8000` in your browser. Sign in with the seeded super admin or learner user (`kojo.asare@example.com` / `Password123!`).

Learners who have not completed onboarding are redirected to the wizard before accessing the dashboard.

## Testing

Run the lightweight feature checks:

```bash
php tests/run.php
```

The script executes smoke assertions across repositories and services.

## Data Protection

See `PrivacyPolicy.md` and `DataProtection.md` for notes on safeguarding learner information in line with Ghana Data Protection Act expectations.
