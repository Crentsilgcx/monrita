# Data Protection Statement

This demo aligns with principles from the Ghana Data Protection Act, 2012 (Act 843):

- **Lawfulness and transparency:** Users are informed about the collection of onboarding and learning data through interface copy.
- **Purpose limitation:** Stored data supports educational guidance, mentorship, analytics, and safeguarding only.
- **Data minimisation:** Only essential learner and guardian attributes are captured during onboarding.
- **Accuracy:** Users can update onboarding details by re-running the wizard (manually via JSON data adjustments in this demo).
- **Retention:** Administrators can purge JSON files when access is no longer required.
- **Integrity and confidentiality:**
  - CSRF protection and session hardening reduce tampering.
  - Audit logs capture authentication and key changes (`storage/audit/audit.log`).
  - Rate limiting guards against brute-force login attempts.

For production use, deploy encrypted storage, enforce HTTPS, and integrate with institutional policies on parental consent and safeguarding.
