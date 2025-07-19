CoachOS Design & Navigation Guide
This document outlines the official UI/UX and navigation patterns for the CoachOS WordPress plugin. Adhering to these guidelines ensures a consistent, predictable, and professional user experience across the entire application.

1. UI Pattern for Buttons & Links
All user actions are categorized into three types, each with a distinct style and placement.

a. Primary Actions (Add New...)
These are the most important, top-level actions in any given section, used for creating new data entries.

Style: A prominent, bright orange/yellow button.

CSS Class: .button .button-primary

Text: "Add [Item Name]" (e.g., "Add New Skater", "Add Goal").

Placement: Placed at the top of a section, typically right after the <h2> heading, for maximum visibility.

b. In-Context Actions (View, Update)
These actions apply to a specific item within a list or table.

Style: Simple text links, separated by a pipe character (|). This keeps tables clean and easy to scan.

Text: "View", "Update", "Edit".

Placement: Grouped together in the last column of a table, which should be titled "Actions".

c. Navigational Actions (Back, Cancel)
These actions move the user between different pages or views.

Style: A standard, blue button.

CSS Class: .button

Text: "← Back", "Cancel", "← Back to Dashboard".

Placement: At the top or bottom of a page, outside the main content blocks, to clearly signal a move away from the current view.

2. Navigation & Redirect Logic
This section governs how users move between pages, especially after performing an action. The goal is to create a seamless and logical flow.

a. "Back" & "Cancel" Links
To avoid hard-coded and brittle links, all navigational "back" or "cancel" links will be generated dynamically.

Method: Use the wp_get_referer() WordPress function to get the URL of the previous page the user was on.

Fallback: If wp_get_referer() returns empty (which can happen if a user navigates directly to a page), the link must default to a safe, logical location (e.g., site_url('/coach-dashboard/')).

Result: The "Back" button will always take the user to their actual previous page, whether it was the Coach Dashboard, a specific Skater's page, or a Yearly Plan view.

b. Form Submission Redirects
The user should always be returned to a logical location after submitting a form.

Creating a New Item:

When a user submits a form to create a new item (e.g., a new Goal from the Skater Dashboard), the form's 'return' parameter will be dynamically set using wp_get_referer().

Result: The user is immediately returned to the page they started from (e.g., the Skater Dashboard).

Updating an Existing Item:

When a user submits a form to update an existing item, the form's 'return' parameter will be set to the permalink of the item they just edited (e.g., get_permalink($post_id)).

Result: The user is taken to the "view" page for that specific item, allowing them to immediately see their saved changes.