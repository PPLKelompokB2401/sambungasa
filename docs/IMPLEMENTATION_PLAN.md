# SambungAsa — Implementation Plan & Progress Tracking

> **Rencana Implementasi & Lembar Kendali Pengerjaan SambungAsa**  
> Terakhir diperbarui: 28 Oktober 2026  
> Baseline: `ARCHITECTURE_SAMBUNGASA.md`, `design.md`, `PRD.md`

---

## Progress Overview

| Phase | Modul / Fokus | Status | Selesai |
|---|---|---|---|
| Phase 1 | Project Inspection & Requirements Audit | `Completed` | 100% |
| Phase 2 | Setup Laravel, Git, & Database Schema (`DATABASE_SCHEMA.md`) | `In Progress` | 10% |
| Phase 3 | Application Foundation & Design System (Red Broadcast) | `Pending` | 0% |
| Phase 4 | Authentication, Role Authorization, & User Management | `Pending` | 0% |
| Phase 5 | School Profiles & 3T Region Verification | `Pending` | 0% |
| Phase 6 | School Needs Management & Admin Moderation | `Pending` | 0% |
| Phase 7 | Public Discovery, Directory, Search & Need Detail | `Pending` | 0% |
| Phase 8 | Pledge Submission, Tracking Code & Pledge Verification | `Pending` | 0% |
| Phase 9 | Tracking History, Evidence Upload & Internal Notifications | `Pending` | 0% |
| Phase 10 | Dashboard Statistics & Report Exports (CSV) | `Pending` | 0% |
| Phase 11 | Activity Logs (F-24) & Security Hardening | `Pending` | 0% |
| Phase 12 | Automated Testing, Verification & Final Polish | `Pending` | 0% |

---

## Detailed Task Checklist

### Phase 1: Project Inspection & Requirements Audit
- [x] Read `ARCHITECTURE_SAMBUNGASA.md`, `PRD.md`, and `red-broadcast-DESIGN.md` / `design.md`.
- [x] Inspect project environment (PHP 8.1 / 8.0, Node.js 24, Composer 2.8, MySQL 8.0).
- [x] Identify documentation inconsistencies (Red Broadcast adaptation, `design.md` alias, F-23 exclusion).
- [x] Create `docs/PROJECT_AUDIT.md`.
- [x] Create `docs/IMPLEMENTATION_PLAN.md`.

### Phase 2: Setup Laravel, Git & Database Schema
- [ ] Initialize Git repository with proper `.gitignore`.
- [ ] Initialize Laravel 10 skeleton with PHP 8.1 runtime.
- [ ] Configure `.env` with MySQL database `sambungasa` (`DB_USERNAME=root`, `DB_PASSWORD=`).
- [ ] Create `docs/DATABASE_SCHEMA.md` with complete Mermaid ERD, tables, indexes, constraints.
- [ ] Create database migrations for all tables:
  - [ ] `users` (multi-role: admin, school, contributor; status: pending, active, rejected, suspended).
  - [ ] `regions` (Papua, Kalimantan, Sulawesi regions with `is_3t` flag).
  - [ ] `school_profiles` (3T verification state, verification notes, location coordinates).
  - [ ] `contributor_profiles` & `volunteer_profiles` (donor, volunteer, organization).
  - [ ] `need_categories` (material/non-material categories).
  - [ ] `needs` (title, description, priority, assistance_type, quantity_required, quantity_fulfilled, status).
  - [ ] `pledges` (unique tracking code, quantity, status, notes, rejection reasons).
  - [ ] `tracking_logs` (historical status transitions, notes, created_by).
  - [ ] `evidence_files` (file_path, file_size, mime_type, caption, evidence_type).
  - [ ] `notifications` (internal recipient, title, body, link, read_at).
  - [ ] `activity_logs` (actor, action, entity_type, entity_id, description, ip_address).
- [ ] Create database seeders:
  - [ ] Admin account seeder (`admin@sambungasa.id`).
  - [ ] 3T regions seeder (Papua, Kalimantan, Sulawesi reference data).
  - [ ] Need categories seeder (Buku/Alat Tulis, Elektronik/Komputer, Infrastruktur, Pelatihan, Lainnya).
  - [ ] Demo school & contributor accounts for verification testing.
- [ ] Execute `php artisan migrate --seed` and verify database tables.

### Phase 3: Application Foundation & Design System
- [ ] Configure design tokens based on `design.md` / Red Broadcast:
  - [ ] Primary: `#FF0000`, Primary Hover: `#CC0000`.
  - [ ] Secondary: `#065FD4`, Neutral: `#606060`, Background: `#FFFFFF`, Surface: `#F2F2F2`.
  - [ ] Typography: Roboto display/body, Roboto Mono.
  - [ ] Elevation levels 0 to 3, pill buttons (`rounded-full`), chip pills.
- [ ] Create base Blade layouts (`layouts/app.blade.php`, `layouts/guest.blade.php`, `layouts/dashboard.blade.php`).
- [ ] Create reusable UI components (Navbar, Footer, Flash alerts, Status badges, Form inputs, Modal, Cards).
- [ ] Error pages: 403, 404, 419, 500 with friendly Indonesian messaging.

### Phase 4: Authentication & User Management (F-02, F-03, F-19, F-20)
- [ ] Register form with role selection (School vs Contributor/Donor/Volunteer/Org).
- [ ] Secure password hashing via `Hash::make()`.
- [ ] Login / Logout with rate limiting & session protection.
- [ ] Role & Status middleware (`RoleMiddleware`, `EnsureAccountIsActive`).
- [ ] Admin user management: list, detail, approve/verify, reject with reason, suspend.
- [ ] Contributor & Volunteer profile management.

### Phase 5: School Profiles & 3T Region Verification (F-01, F-04, F-17/F-18)
- [ ] School profile edit form for school representative.
- [ ] Location assignment linked to `regions` reference data.
- [ ] Admin 3T verification queue and workflow (Approve, Reject with note).
- [ ] Enforcement: only verified schools can post needs.
- [ ] Public 3T regions reference page (`/regions/3t`).

### Phase 6: School Needs Management & Admin Moderation (F-05, F-06, F-07, F-20)
- [ ] Need CRUD for school accounts with ownership check.
- [ ] Categories (material vs non-material), Priorities (Rendah, Sedang, Tinggi, Mendesak).
- [ ] Admin moderation queue: review pending needs, publish, reject with note, close.
- [ ] Prevent edit/delete of needs with active pledges; allow close/archive.

### Phase 7: Public Discovery, Directory, Search & Need Detail (F-08, F-09, F-18, F-19)
- [ ] Landing page (`/`) with Hero CTA, impact stats, category chips, recent verified needs.
- [ ] Public school directory (`/schools`) with search and region filters.
- [ ] Public school profile page (`/schools/{school}`) with privacy protection.
- [ ] Public needs directory (`/needs`) with keyword search, region, category, and priority filter.
- [ ] Need detail page (`/needs/{need}`) with progress bar, school info, public timeline.

### Phase 8: Pledge Submission, Tracking Code & Verification (F-10, F-11, F-16, F-21)
- [ ] Pledge submission modal/form from need detail page.
- [ ] Unique tracking code generation (e.g. `SA-202610-XXXX`).
- [ ] Quantity validation: pledge quantity <= remaining needed quantity (with DB transaction lock).
- [ ] Admin pledge queue: review, approve, reject with reason.
- [ ] Contributor pledge list and detail views.

### Phase 9: Tracking History, Evidence Upload & Notifications (F-12, F-13, F-14, F-15)
- [ ] Strict status transitions: `submitted` → `verified` → `in_delivery` → `received` → `completed`.
- [ ] Tracking logs created on each transition with actor, timestamp, and notes.
- [ ] Evidence upload (photos/documents) with MIME validation, file size limit, and private storage in `storage/app/private/evidence`.
- [ ] Secure evidence download route with Policy authorization.
- [ ] School receipt confirmation workflow.
- [ ] Update need `quantity_fulfilled` when pledge reaches completed stage.
- [ ] Internal notifications system for school, contributor, and admin.

### Phase 10: Dashboard Statistics & Report Exports (F-16/F-17, F-22)
- [ ] Public statistics dashboard (`/statistics`) with aggregated verified metrics.
- [ ] Admin analytics dashboard with system KPIs, gap analysis, regional distributions.
- [ ] CSV report export (`/admin/reports/export`) with date/region/status filters.
- [ ] CSV Formula Injection sanitization (`=`, `+`, `-`, `@`).

### Phase 11: Activity Logs (F-24) & Security Hardening
- [ ] Automatic audit trail in `activity_logs` for critical actions.
- [ ] Admin activity logs browser with filtering.
- [ ] CSRF, XSS escaping, IDOR policies on all resources.
- [ ] Verification that no PWA/offline code exists.

### Phase 12: Automated Testing, Verification & Final Polish
- [ ] Unit & Feature tests (Authentication, School verification, Need moderation, Pledge flow, Tracking, Export).
- [ ] Visual verification of all pages following Red Broadcast design specs.
- [ ] Mobile responsive testing.
- [ ] Final documentation update (`CHANGELOG.md`, `TESTING.md`).
