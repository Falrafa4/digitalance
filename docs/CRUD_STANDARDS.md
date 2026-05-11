# Digitalance CRUD UI Standardization Guide

**Version**: 1.0  
**Last Updated**: May 2026  
**Status**: Active

---

## Overview

This document outlines the standardized CRUD (Create, Read, Update, Delete) UI patterns across all Digitalance dashboards (Admin, Client, Freelancer). These patterns ensure consistency, improve user experience, and reduce code duplication.

---

## Global Components

All CRUD pages now use reusable Blade components from `resources/views/components/`. These ensure visual and structural consistency across all modules.

### 1. **x-crud-header** - List Page Header

**Location**: `resources/views/components/crud-header.blade.php`

**Purpose**: Standardized header for list pages with title, subtitle, stats, and action buttons.

**Usage**:

```blade
<x-crud-header
    title="Resources"
    subtitle="Manage your resources here."
    count="{{ $total }}"
    countLabel="Total Items"
    actionUrl="{{ route('create') }}"
    actionLabel="Create New"
    actionIcon="ri-add-line" />
```

**Props**:

- `title` (string) - Page title
- `subtitle` (string) - Descriptive text
- `count` (int|null) - Total item count
- `countLabel` (string) - Label for count
- `actionUrl` (string|null) - Create button link
- `actionLabel` (string) - Create button text
- `actionIcon` (string) - Remixicon class

**Features**:

- Responsive layout (title stacks on mobile)
- Stats card with item count
- Primary action button (teal #0f766e)
- Subtitle for context

---

### 2. **x-crud-status-badge** - Status Display

**Location**: `resources/views/components/crud-status-badge.blade.php`

**Purpose**: Unified status display component with consistent colors across all statuses.

**Usage**:

```blade
<x-crud-status-badge status="Pending" />
<x-crud-status-badge status="Approved" />
<x-crud-status-badge status="Rejected" />
```

**Supported Statuses**:

- **Service Status**: Draft, Pending, Approved, Rejected
- **Order Status**: Pending, Paid, In Progress, Revision, Completed, Cancelled
- **Offer/Negotiation**: Sent, Accepted, Negotiated
- **Unknown/Invalid**: Shows "Unknown" label with default styling

**Color Mapping**:

```
Draft       → bg-slate-100, text-slate-700
Pending     → bg-amber-100, text-amber-700
Approved    → bg-emerald-100, text-emerald-700
Completed   → bg-emerald-100, text-emerald-700
Paid        → bg-emerald-100, text-emerald-700
Rejected    → bg-red-100, text-red-700
Cancelled   → bg-red-100, text-red-700
In Progress → bg-blue-100, text-blue-700
Revision    → bg-orange-100, text-orange-700
Sent        → bg-amber-100, text-amber-700
Accepted    → bg-emerald-100, text-emerald-700
Negotiated  → bg-blue-100, text-blue-700
```

**Benefits**:

- Single source of truth for status colors
- Automatic icon selection per status
- Consistent label formatting
- Fallback for unknown statuses

---

### 3. **x-crud-empty-state** - No Data Display

**Location**: `resources/views/components/crud-empty-state.blade.php`

**Purpose**: Standardized empty state for pages with no results.

**Usage**:

```blade
<x-crud-empty-state
    icon="ri-tools-line"
    title="No Services Found"
    description="No services match your criteria."
    actionUrl="{{ route('create') }}"
    actionLabel="Create First Item" />
```

**Props**:

- `icon` (string) - Remixicon class
- `title` (string) - Main message
- `description` (string) - Supporting text
- `actionUrl` (string|null) - CTA link
- `actionLabel` (string) - CTA text

**Features**:

- Centered layout
- Large icon display
- Optional action button
- Contextual messaging

---

### 4. **x-form-layout** - Form Page Wrapper

**Location**: `resources/views/components/form-layout.blade.php`

**Purpose**: Standardized wrapper for create/edit form pages.

**Usage**:

```blade
<x-form-layout
    title="Create Service"
    backUrl="{{ route('services.index') }}"
    backLabel="Back to Services">

    <form action="{{ route('services.store') }}" method="POST">
        @csrf
        <!-- Form fields here -->
        <x-form-actions
            submitLabel="Create Service"
            cancelUrl="{{ route('services.index') }}" />
    </form>
</x-form-layout>
```

**Props**:

- `title` (string) - Form title
- `backUrl` (string|null) - Back link URL
- `backLabel` (string) - Back link text

**Features**:

- Back navigation link
- Page title styling
- White card with border
- Consistent padding

---

### 5. **x-form-field** - Input Field Wrapper

**Location**: `resources/views/components/form-field.blade.php`

**Purpose**: Standardized form input with label and error display.

**Usage**:

```blade
<x-form-field
    name="title"
    label="Service Title"
    type="text"
    value="{{ old('title') }}"
    placeholder="Enter title"
    required
    :errors="$errors" />

<x-form-field
    name="category_id"
    label="Category"
    type="select"
    :errors="$errors">
    @foreach($categories as $cat)
        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
    @endforeach
</x-form-field>

<x-form-field
    name="description"
    label="Description"
    type="textarea"
    :errors="$errors" />
```

**Props**:

- `name` (string) - Field name/ID
- `label` (string) - Display label
- `type` (string) - Input type (text, select, textarea)
- `value` (string|null) - Current value
- `placeholder` (string) - Placeholder text
- `required` (bool) - Required indicator
- `disabled` (bool) - Disabled state
- `errors` (MessageBag) - Validation errors

**Features**:

- Auto-detects input type
- Error display with icon
- Red highlighting on error
- Required indicator (\*)
- Consistent spacing

---

### 6. **x-form-actions** - Form Buttons

**Location**: `resources/views/components/form-actions.blade.php`

**Purpose**: Standardized form action buttons (Submit/Cancel/Delete).

**Usage**:

```blade
<x-form-actions
    submitLabel="Save Service"
    cancelUrl="{{ route('services.index') }}"
    deleteUrl="{{ route('services.destroy', $service->id) }}"
    isDangerous="false" />
```

**Props**:

- `submitLabel` (string) - Submit button text
- `cancelUrl` (string|null) - Cancel link URL
- `deleteUrl` (string|null) - Delete form action
- `isDangerous` (bool) - Red styling for dangerous actions

**Features**:

- Delete button on left (if deleteUrl provided)
- Cancel/Submit buttons on right
- Submit button prominent (teal)
- Delete button red (#991b1b)
- Confirmation dialog for delete

---

## CRUD Pattern by Module

### Freelancer Services (✅ Fully Standardized)

**Files Involved**:

- `resources/views/dashboard/freelancer/services.blade.php` - List page
- `resources/views/dashboard/freelancer/services/create.blade.php` - Create form
- `resources/views/dashboard/freelancer/services/edit.blade.php` - Edit form
- `resources/views/dashboard/freelancer/services/show.blade.php` - Detail page
- `resources/views/dashboard/freelancer/services/_form-fields.blade.php` - Form partial

**Pattern**:

```
LIST PAGE
├── x-crud-header (with Create button)
├── Status filters (Blade loop)
├── Search form
└── Service grid with x-crud-status-badge

CREATE FORM
├── x-form-layout
├── x-form-field (category, title, prices, delivery, description)
└── x-form-actions

EDIT FORM
├── x-form-layout
├── x-form-field (same as create)
└── x-form-actions (with cancel back to show)

DETAIL PAGE
├── Back link + Edit button
├── x-crud-status-badge
└── Metadata display (prices, delivery, description)
```

**Key Files**:

- Form validation: `StoreServiceRequest`, `UpdateServiceRequest`
- Controller: `ServiceController@create`, `@store`, `@edit`, `@update`
- Routes: `freelancer.services.{index|create|show|edit}`

---

### Admin Services (✅ Standardized)

**Pattern**:

```
LIST PAGE
├── x-crud-header (with count)
├── Status filter tabs
├── Search form
├── Service cards grid with x-crud-status-badge
├── Modal detail view (existing pattern maintained)
└── x-crud-empty-state (if no results)
```

**Status Filter UI**:

- Uses route parameters: `?status=Pending`
- Tailwind toggle styling (active = teal, inactive = slate)
- Filters on Semua/Pending/Approved/Rejected

---

### Client Services (✅ Standardized)

**Pattern**:

```
BROWSE PAGE
├── x-crud-header (with "My Orders" link)
├── Service cards with x-crud-status-badge
├── Detail & Order buttons per card
├── Pagination controls
└── x-crud-empty-state (if no services)
```

**Key Actions**:

- View service detail
- Create order for service
- Share service link

---

## Implementation Guide

### Converting an Existing CRUD Module

**Step 1**: Replace header HTML with `x-crud-header`

```blade
<!-- Before -->
<h1>Resources</h1>
<p>Description</p>
<div>{{ $count }} total</div>
<a href="{{ route('create') }}">Create</a>

<!-- After -->
<x-crud-header
    title="Resources"
    subtitle="Description"
    count="{{ $count }}"
    actionUrl="{{ route('create') }}"
    actionLabel="Create" />
```

**Step 2**: Replace status displays with `x-crud-status-badge`

```blade
<!-- Before -->
<span class="status-{{ strtolower($status) }}">{{ $status }}</span>

<!-- After -->
<x-crud-status-badge :status="$status" />
```

**Step 3**: Replace empty states with `x-crud-empty-state`

```blade
<!-- Before -->
<div class="text-center">
    <i class="ri-inbox-line"></i>
    <h3>No Data</h3>
    <a href="{{ route('create') }}">Create</a>
</div>

<!-- After -->
<x-crud-empty-state
    icon="ri-inbox-line"
    title="No Data"
    actionUrl="{{ route('create') }}"
    actionLabel="Create" />
```

**Step 4**: Replace form wrappers with `x-form-layout` and form fields

```blade
<!-- Before -->
<div class="form-wrapper">
    <h1>Form Title</h1>
    <form>
        <input type="text" name="field1" />
    </form>
</div>

<!-- After -->
<x-form-layout title="Form Title" backUrl="{{ route('index') }}">
    <form>
        <x-form-field
            name="field1"
            label="Field 1"
            type="text"
            :errors="$errors" />
        <x-form-actions submitLabel="Save" />
    </form>
</x-form-layout>
```

---

## Color & Styling Standards

### Tailwind Colors

- **Primary Action**: `bg-[#0f766e]` (teal) → hover `bg-teal-800`
- **Danger Action**: `bg-red-600` → hover `bg-red-700`
- **Secondary Action**: `border border-slate-200` → hover `bg-slate-50`
- **Text Primary**: `text-slate-900`
- **Text Secondary**: `text-slate-500`
- **Background**: `bg-white` with `border border-slate-200`
- **Focus Ring**: `ring-2 ring-[#0f766e]`

### Border Radius

- Cards: `rounded-lg` or `rounded-xl`
- Buttons: `rounded-lg`
- Inputs: `rounded-lg`
- Badges: `rounded-full`

### Spacing

- Page padding: `px-8 py-7` (dashboard container)
- Card padding: `p-8` (form cards)
- Field spacing: `mb-6` (form fields)
- Grid gaps: `gap-4` to `gap-6`

---

## Error Handling

### Validation Errors

All form fields automatically display validation errors using Laravel's `$errors` MessageBag:

```blade
<x-form-field
    name="email"
    label="Email"
    type="email"
    value="{{ old('email') }}"
    :errors="$errors" />

<!-- Renders error message if validation fails -->
<!-- Shows red border and error icon/text -->
```

### Empty State Handling

Use `x-crud-empty-state` to show contextual messaging when:

- No items exist in list
- Search returns no results
- Access denied/not found

---

## Best Practices

### ✅ Do's

- Use form partials (`_form-fields.blade.php`) to share fields between create/edit
- Pass validation errors to components: `:errors="$errors"`
- Use `old()` helper for sticky form values on error
- Use `Route::has()` before linking to optional routes
- Keep status values consistent (use enums if available)
- Use `Route::resourceful()` and follow REST conventions

### ❌ Don'ts

- Don't duplicate form field HTML between create/edit
- Don't create custom status color mappings (use `x-crud-status-badge`)
- Don't use custom empty state designs (use `x-crud-empty-state`)
- Don't hardcode button styles (use components)
- Don't mix CSS from multiple eras (use Tailwind exclusively)

---

## Testing Checklist

When implementing CRUD pages, verify:

- [ ] List page loads with `x-crud-header`
- [ ] Status badges display correct colors
- [ ] Empty state appears with no items
- [ ] Search/filters work correctly
- [ ] Create form validates all fields
- [ ] Edit form pre-fills values
- [ ] Validation errors display correctly
- [ ] Cancel buttons link to correct page
- [ ] Back navigation works
- [ ] Mobile responsive (test on mobile breakpoints)
- [ ] Pagination works (if applicable)
- [ ] Delete confirmation appears (if applicable)

---

## Common Issues & Solutions

### Issue: Status badge colors not showing

**Solution**: Ensure status value matches keys in `crud-status-badge.blade.php`

- Check exact casing: `"Pending"` not `"pending"`
- Use full phrase: `"In Progress"` not `"InProgress"`

### Issue: Form fields have errors but don't show red

**Solution**: Ensure `:errors="$errors"` prop is passed to component

```blade
<x-form-field :errors="$errors" /> ✅
<x-form-field /> ❌
```

### Issue: Back link goes to wrong page

**Solution**: Ensure backUrl points to correct route

```blade
<x-form-layout backUrl="{{ route('items.index') }}" />
```

### Issue: Create button doesn't appear in header

**Solution**: Ensure actionUrl is provided

```blade
<x-crud-header actionUrl="{{ route('create') }}" />
```

---

## Future Enhancements

Potential improvements for next phase:

1. Add `x-crud-table` component for table-based lists
2. Add `x-crud-filter-tabs` component for standardized filters
3. Create global form input library (`x-input`, `x-select`, `x-textarea`)
4. Add `x-crud-pagination` component
5. Create Livewire versions for real-time filtering
6. Add batch action toolbar component

---

## Support

For questions or issues with CRUD components:

1. Check this guide's troubleshooting section
2. Review the component source files in `resources/views/components/`
3. Look at examples in Freelancer Services module
4. Check Laravel error logs

---

**Maintained by**: Development Team  
**Last Review**: May 2026  
**Next Review**: August 2026
