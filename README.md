# TutorBlock

> Powerful Gutenberg / Block Editor blocks for **TutorLMS** — the leading WordPress LMS plugin.

TutorBlock adds 7 rich, server-side-rendered blocks that make it easy to build stunning course pages, landing pages, and learning portals without writing a single line of code.

---

## 📦 Blocks Included

| Block | Description |
|---|---|
| 🎡 **Course Carousel** | Responsive slider of courses with autoplay, arrows, dots, and touch/swipe support |
| ⊞ **Course Grid** | Filterable, paginated course grid with multiple layout styles (Card, List, Minimal) |
| 📋 **Course Preview Card** | Showcase a single course with a horizontal, vertical, or hero layout |
| 🗂 **Category Course Grid** | Display courses organized by their categories with a "View All" link per category |
| 👤 **Instructor Profile** | Rich instructor card with bio, stats, social links, and recent courses |
| 📊 **Course Platform Stats** | Animated counter block showing total courses, students, instructors, and reviews |
| 🎯 **Enrollment CTA** | High-converting enrollment call-to-action with course thumbnail, price, and rating |

---

## ✨ Key Features

- **Server-side rendering** — all blocks render via PHP for perfect SEO and caching compatibility
- **Gutenberg InspectorControls** — every block has a full sidebar settings panel
- **No external JS dependencies** — the carousel is pure vanilla JS (~8 KB)
- **TutorLMS integration** — reads real course data, ratings, enrollments, and instructor info
- **Color customization** — per-block primary color pickers with CSS custom properties
- **RTL support** — included with `@wordpress/scripts` build pipeline
- **Responsive** — all blocks adapt gracefully to mobile, tablet, and desktop
- **Accessible** — ARIA labels, keyboard navigation, and `prefers-reduced-motion` support

---

## 🚀 Installation

### Requirements

- WordPress 6.0+
- PHP 7.4+
- [TutorLMS](https://wordpress.org/plugins/tutor/) (free or pro)
- Node.js 18+ and npm (for development only)

### From ZIP

1. Download or clone this repository
2. Install dependencies and build: `npm install && npm run build`
3. Upload the plugin folder to `/wp-content/plugins/`
4. Activate via **Plugins → Installed Plugins**

### Development

```bash
# Install dependencies
npm install

# Start watch mode (hot rebuild)
npm start

# Production build
npm run build

# Lint JavaScript
npm run lint:js

# Lint CSS/SCSS
npm run lint:css

# Auto-format JS
npm run format
```

---

## 🧩 Block Details

### Course Carousel
- **Settings**: Number of courses, category filter, order by, slides to show (desktop/tablet/mobile), autoplay + speed, arrows, dots, card display options, border radius, color
- **Features**: Touch swipe, keyboard navigation (←/→), pause on hover, animated slide transitions

### Course Grid
- **Settings**: Number of courses, columns (desktop/tablet), category filter, order by, layout (Card/List/Minimal), filter bar, pagination, card display toggles, color
- **Features**: Client-side category filter (no page reload), WordPress pagination support

### Course Preview Card
- **Settings**: Course ID, layout (Horizontal/Vertical/Hero), image position, description, instructor, stats, rating, requirements, curriculum preview, enroll button text, colors
- **Features**: Three distinct layouts; curriculum preview shows topic list

### Category Course Grid
- **Settings**: Category slugs (comma-separated), courses per category, columns, section spacing, category header (title, description, count, "View All" link), card display options
- **Features**: Dynamically groups courses by their TutorLMS categories

### Instructor Profile
- **Settings**: Instructor User ID, layout (Card/Horizontal/Minimal), bio, stats, social links, ratings, courses list
- **Features**: Reads real TutorLMS instructor data; calculates average rating across all their courses

### Course Platform Stats
- **Settings**: Toggle which stats to show, custom labels, layout (Horizontal/Grid/Vertical), style (Default/Filled/Gradient/Minimal), animated counter, icon style, colors
- **Features**: IntersectionObserver-based counter animation; reads live DB counts

### Enrollment CTA
- **Settings**: Course ID, custom URL, headline, sub-headline, button text, display toggles (thumbnail, title, price, stats, rating, money-back guarantee), layout, background style, button colors
- **Features**: Four background styles including gradient; auto-pulls course data when ID is set

---

## 🎨 Styling & Customization

All blocks use **CSS custom properties** for theming, making them easy to override:

```css
/* Override primary color site-wide */
.tutorblock-course-carousel {
    --tutorblock-primary: #e11d48;
}

/* Or scope per block via the color picker in block settings */
```

---

## 📂 File Structure

```
tutorblock/
├── tutorblock.php              # Main plugin file
├── package.json                # npm config
├── webpack.config.js           # Webpack (extends @wordpress/scripts)
├── src/
│   └── blocks/
│       ├── course-carousel/    # Source JS + SCSS per block
│       ├── course-grid/
│       ├── course-preview/
│       ├── category-course-grid/
│       ├── instructor-profile/
│       ├── course-stats/
│       └── enrollment-cta/
├── build/                      # Compiled JS + CSS (committed for distribution)
│   └── blocks/
├── includes/
│   ├── admin/
│   │   └── rest-api.php        # REST endpoints for editor (courses, categories, instructors)
│   └── render/
│       ├── helpers.php         # Shared PHP render helpers
│       ├── course-carousel.php
│       ├── course-grid.php
│       ├── course-preview.php
│       ├── category-course-grid.php
│       ├── instructor-profile.php
│       ├── course-stats.php
│       └── enrollment-cta.php
└── assets/
    ├── css/
    │   ├── tutorblock-global.css   # Global shared styles
    │   └── tutorblock-editor.css   # Editor-only styles
    └── js/
        └── tutorblock-carousel.js  # Vanilla JS for carousel + counters + filters
```

---

## 🔌 REST API Endpoints

TutorBlock registers the following REST endpoints (authentication required: `edit_posts` capability):

| Endpoint | Description |
|---|---|
| `GET /wp-json/tutorblock/v1/courses` | Returns a list of published courses for editor selects |
| `GET /wp-json/tutorblock/v1/categories` | Returns all course categories |
| `GET /wp-json/tutorblock/v1/instructors` | Returns instructor users |

---

## 📝 License

GPL-2.0-or-later — see [LICENSE](LICENSE)
