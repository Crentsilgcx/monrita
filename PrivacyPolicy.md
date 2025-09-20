# Privacy Policy

Race JHS respects the privacy of Junior High School learners, guardians, mentors, and administrators. Personal information collected through this demo platform remains on the hosting server in JSON files (`storage/data`). No information is transmitted to third parties.

## Data We Collect

- Learner onboarding details (demographics, guardian contact, consent status).
- Course progress, assessments, and community interactions.
- Audit trails for authentication and key actions.

## How We Use Data

- To provide personalised learning recommendations and analytics to authorised administrators.
- To enforce safeguarding measures (onboarding gating, consent tracking, content moderation).

## Retention and Access

- Records remain until an administrator deletes them from the JSON data store.
- Only signed-in users with appropriate roles may access restricted modules.

## Safeguarding Minors

- Guardian consent is mandatory before learners access the dashboard.
- Sensitive profile fields are marked in code comments and limited to onboarding views.

For full compliance, deploy behind secure HTTPS, rotate credentials regularly, and review log output in `storage/audit`.
