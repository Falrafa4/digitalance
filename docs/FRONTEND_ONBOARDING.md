# Frontend Onboarding Guide

## Quick Start

### Dev Server

```bash
composer dev
# Runs: php artisan serve, queue:listen, pail, npm run dev
```

### Build Assets

```bash
npm run dev      # Development (hot reload)
npm run build    # Production
npm run preview  # Preview production build
```

---

## Project Structure

### Frontend Asset Locations

```
public/
├── css/
│   ├── app.css           # Public pages (compiled via Vite)
│   ├── style.css         # Public pages (plain CSS, 1535 lines)
│   ├── login.css         # Auth page styles (312 lines)
│   └── dashboard/
│       ├── dashboard.css       # Shared dashboard styles
│       ├── _shared.css          # Shared CSS variables + utilities
│       ├── admin/
│       │   └── dashboard.css   # Admin overrides
│       ├── client/
│       │   └── dashboard.css    # Client overrides
│       └── freelancer/
│           └── dashboard.css   # Freelancer overrides
│
├── js/
│   ├── utils.js          # Global utilities (SINGLE SOURCE OF TRUTH)
│   ├── api.js            # API fetch wrapper
│   ├── main.js           # Public page interactions
│   ├── sign-in.js        # Auth page logic
│   ├── footer.js         # Footer interactions
│   └── dashboard/
│       ├── global.js             # Dashboard shared JS (toast, modal, error boundary)
│       ├── confirm-modal.js      # Confirmation dialogs
│       ├── search.js            # Admin search
│       ├── admin/
│       │   ├── dashboard.js     # Admin dashboard logic
│       │   ├── clients.js
│       │   ├── services.js
│       │   └── ...
│       ├── client/
│       │   └── dashboard.js     # Client dashboard logic
│       ├── freelancer/
│       │   └── dashboard.js     # Freelancer dashboard logic
│       └── shared/
│           ├── utils.js         # DEPRECATED - aliased to utils.js
│           ├── flash.js
│           ├── footer.js
│           └── notification-drawer.js

resources/
├── js/
│   ├── app.js             # Vite entry
│   ├── bootstrap.js       # Axios + Echo bootstrap
│   ├── echo.js           # Laravel Echo (realtime)
│   └── services/         # API service modules
└── css/
    └── app.css           # Vite CSS entry (Tailwind + custom)

resources/views/
├── layouts/
│   ├── app.blade.php     # Public pages layout
│   └── dashboard.blade.php # Dashboard layout
├── components/
│   ├── sidebar.blade.php
│   ├── header.blade.php
│   ├── navbar.blade.php
│   ├── footer.blade.php
│   ├── notification-drawer.blade.php
│   ├── flash.blade.php
│   └── ui/
│       ├── status-badge.blade.php  # Universal status badge
│       ├── empty-state.blade.php   # Universal empty state
│       ├── alert.blade.php
│       ├── button.blade.php
│       ├── modal.blade.php
│       ├── ...
├── dashboard/
│   ├── admin/
│   ├── client/
│   └── freelancer/
└── public/
```

---

## CSS Loading Flow

### Dashboard Pages

1. `layouts/dashboard.blade.php` loads `dashboard.css`
2. `<x-dashboard-css />` dynamically loads role-specific CSS:
    - `/admin` → `css/dashboard/admin/dashboard.css`
    - `/client` → `css/dashboard/client/dashboard.css`
    - `/freelancer` → `css/dashboard/freelancer/dashboard.css`

### CSS Architecture

```
dashboard.css
├── _shared.css (CSS variables, sidebar, header, table, modal, form)
└── role-specific CSS (role overrides only)
```

### Tailwind

- Loaded via CDN: `https://cdn.tailwindcss.com`
- Custom config defined inline in layout `<script>` blocks
- Custom colors: `primary`, `secondary`, `accent`
- Custom shadows: `teal-sm`, `teal-md`
- Custom fonts: `sans`, `display`

---

## JS Architecture

### Global Utilities (`public/js/utils.js`)

Single source of truth. Available as `window.DigitalanceUtils`:

| Method                     | Description               |
| -------------------------- | ------------------------- |
| `escapeHtml(str)`          | XSS prevention            |
| `focusTrap(element)`       | Accessibility modal trap  |
| `debounce(fn, wait)`       | Rate-limit function calls |
| `formatCurrency(value)`    | Format number as IDR      |
| `getCsrfToken()`           | Get CSRF token            |
| `safeText(v)`              | Safe string conversion    |
| `apiRequest(url, options)` | Async fetch wrapper       |
| `ready(fn)`                | DOM ready helper          |

### Dashboard Global (`public/js/dashboard/global.js`)

Loaded on every dashboard page. Provides:

- `window.showToast(message, type)` - Toast notifications
- `window.openModal(id)` / `window.closeModal(id)` - Modal system
- Error boundary handler
- Flash message display
- Form loading states
- AJAX loading helpers

### Role-specific JS

Each role has a `dashboard.js` file that:

- Reads `window.__PAGE__` data injected by controller
- Renders dynamic content
- Handles page-specific interactions

### Data Injection Pattern

Controllers inject data via `window.__PAGE__`:

```php
// Controller
return view('dashboard.client.dashboard', [
    'projects' => $projects,
    'stats' => $stats,
]);

// In Blade, inject:
<script>
    window.__PAGE__ = @json([
        'projects' => $projects,
        'stats' => $stats,
    ]);
</script>
```

---

## Component Library

### Available Blade Components

| Component                                     | Location         | Props                                                      |
| --------------------------------------------- | ---------------- | ---------------------------------------------------------- |
| `<x-ui.status-badge status="..." />`          | `components/ui/` | `status`, `border`, `showIcon`                             |
| `<x-ui.empty-state icon="..." title="..." />` | `components/ui/` | `icon`, `title`, `description`, `actionUrl`, `actionLabel` |
| `<x-ui.button variant="..." />`               | `components/ui/` | variant, size, etc                                         |
| `<x-ui.modal id="..." />`                     | `components/ui/` | id, title, etc                                             |
| `<x-ui.alert type="..." />`                   | `components/ui/` | type, message                                              |

### Status Badge Usage

```blade
<x-ui.status-badge :status="$order->status" />
<x-ui.status-badge status="Pending" />
<x-ui.status-badge status="Completed" :border="true" />
```

### Empty State Usage

```blade
<x-ui.empty-state
    icon="ri-inbox-line"
    title="No Data"
    description="Start by creating your first item."
    actionUrl="/create"
    actionLabel="Create New"
/>
```

---

## Tailwind Classes Reference

### Custom Colors

- `text-primary` / `bg-primary` → `#0f766e`
- `text-secondary` / `bg-secondary` → `#10b981`
- `text-accent` / `bg-accent` → `#f97316`
- `text-teal` / `bg-teal-*` → Teal scale

### Custom Shadows

- `shadow-teal-sm` → `0 4px 14px rgba(15,118,110,0.15)`
- `shadow-teal-md` → `0 6px 20px rgba(15,118,110,0.2)`

### Custom Fonts

- `font-sans` → Plus Jakarta Sans
- `font-display` → Sora (headings)

---

## Common Patterns

### Empty State

```blade
@if($items->isEmpty())
    <x-ui.empty-state
        icon="ri-inbox-line"
        title="No Items"
        description="Create your first item to get started."
        actionUrl="{{ route('items.create') }}"
        actionLabel="Create Item"
    />
@endif
```

### Status Badge with Conditional Styling

```blade
<x-ui.status-badge :status="$item->status" />
```

### Client-side Pagination (Manual)

Use `data-client-pager` attribute:

```blade
<div data-client-pager data-page-size="8">
    <div data-pager-list>
        @foreach($items as $item)
            <div data-pager-item>...</div>
        @endforeach
    </div>
    <div class="flex items-center gap-2">
        <button data-pager-prev>Prev</button>
        <div data-pager-numbers></div>
        <button data-pager-next>Next</button>
    </div>
</div>
@include('dashboard.client._ui.client-pager')
```

### Form with Loading State

```blade
<form action="..." method="POST">
    <button type="submit" class="btn-primary">
        Submit
    </button>
</form>
```

Global handler automatically adds loading state to submit buttons (uses `.btn-loading` class).

### Toast Notifications

```js
window.showToast("Success message", "success");
window.showToast("Error message", "danger");
window.showToast("Info message", "info");
window.showToast("Warning message", "warning");
```

### Modal Open/Close

```js
window.openModal("modal-id"); // Opens modal
window.closeModal("modal-id"); // Closes modal
// Click outside modal to close
```

---

## Naming Conventions

### File Naming

- Blade components: `kebab-case.blade.php`
- JS files: `kebab-case.js`
- CSS files: `kebab-case.css`

### CSS Classes

- Use Tailwind utilities when possible
- Custom classes: lowercase with hyphens
- BEM-ish for complex components: `.block__element--modifier`

### Status Values (case-sensitive)

- `Pending`, `Negotiated`, `Paid`, `In Progress`, `Revision`, `Completed`, `Cancelled`, `Approved`, `Rejected`, `Sent`

---

## Adding New Features

### 1. Create Blade View

```blade
@extends('layouts.dashboard')
@section('title', 'Feature Name')
@section('content')
    {{-- Your content --}}
@endsection
```

### 2. Add Role CSS (if needed)

Create or extend `public/css/dashboard/{role}/feature.css`
Only add role-specific overrides. Don't duplicate shared styles.

### 3. Add Role JS (if needed)

Create `public/js/dashboard/{role}/feature.js`
Inject data via `window.__PAGE__`:

```blade
@push('scripts')
<script>
    window.__FEATURE__ = @json($data);
</script>
@endpush
```

### 4. Register Component

```php
// In a view that uses the feature:
<x-ui.status-badge :status="$item->status" />
<x-ui.empty-state icon="ri-star-line" title="No Stars" />
```

---

## Important Notes

### DO NOT

- Don't add React/Vue/Alpine
- Don't move business logic to frontend
- Don't create API endpoints without backend coordination
- Don't change `window.__PAGE__` variable names without backend sync
- Don't modify `resources/js/` without checking Vite config

### DO

- Use existing components before creating new ones
- Follow existing naming conventions
- Use `window.DigitalanceUtils` instead of duplicating utility functions
- Use `window.showToast` instead of `alert()`
- Use `x-ui-*` components for reusable UI patterns
- Keep CSS in `public/css/dashboard/` for dashboard pages
- Keep page-specific JS in `public/js/dashboard/{role}/`

---

## Troubleshooting

### Styles not loading?

1. Check browser DevTools Network tab for 404 on CSS
2. Verify `<x-dashboard-css />` is present in layout
3. Check CSS file exists at expected path

### JS not working?

1. Check browser DevTools Console for errors
2. Verify `window.__PAGE__` is properly injected
3. Check script loading order in layout
4. Verify `window.DigitalanceUtils` is available

### Component not found?

1. Check component file exists in `resources/views/components/`
2. Verify namespace matches (use `x-ui-` prefix for `components/ui/`)

### Tailwind classes not working?

1. Check CDN script is loaded in layout
2. Verify `tailwind.config` is properly defined
3. Use `@extends` to inherit config from layout
