---
title: Getting Started
# group: Guides              # Bucket this page sits under in the sidebar
# order: 1                   # Lower numbers appear first; omit to sort alphabetically
# description: A short summary used for <meta> tags and SEO
# slug: custom-url           # Override the URL slug (defaults to the file path)
# hidden: true               # Hide from the sidebar and listings
# badge: New                 # Small label shown next to the title in the sidebar
# icon: book                 # Icon name (consumed by your views/macros)
# tags: [intro, basics]      # Free-form tags
# updated_at: 2026-01-01     # Shown in the page footer when set
# author: Jane Doe
# layout: docs               # Override the Blade layout used to render this page
# image: /img/social.png     # Social/OG image
# redirect: /docs/other      # Permanent redirect to another URL
---

# Getting Started

Start writing your documentation here.

Run `php artisan laradocs:cache` to update.

### 4. Git Workflow for Changes to OrgService (or Any File)
Follow the beginner Git guide (from attached PDF):

1. `git checkout main && git pull origin main`
2. `git checkout -b feature/improve-orgservice-filters`
3. Edit `OrgService.php` + update docs.
4. `git add app/Services/OrgService.php resources/docs/orgservice.md`
5. `git commit -m "feat(service): enhance OrgService getUsers with better scopes"`
   (Use multiline for details.)
6. `git push origin feature/improve-orgservice-filters`
7. Create PR → Senior reviews → Merge.

**For Conflicts** (common on shared services):
- Pull latest.
- Fix marked sections in file.
- `git add` the file.
- Commit the merge.

**README.md Update**:
Add to root README:
```markdown
## Services

- **OrgService**: Central org & user queries. See docs/orgservice.md
