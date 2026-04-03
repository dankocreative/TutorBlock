# TutorBlock

TutorBlock is a **zip-install ready WordPress plugin** for LMS/video enhancements with **no Node.js dependency**.

## What changed

- Removed any build-step expectation and shipped plain PHP/CSS/JS assets.
- Added two native Gutenberg blocks that appear in the inserter under Widgets:
  - **TutorBlock Video Lesson**
  - **TutorBlock Course Progress**
- Added LMS-friendly video lesson shortcode: `[tutorblock_video_lesson src="https://.../lesson.mp4" lesson_id="123" title="Lesson 1"]`
- Added learner progress shortcode: `[tutorblock_course_progress]`
- Added REST-based progress persistence for logged-in students.
- Added plugin settings page (`Settings > TutorBlock`) for video UX options.

## Installation (ZIP)

1. Download this repository as a ZIP.
2. In WordPress admin, go to **Plugins > Add New > Upload Plugin**.
3. Upload ZIP and activate **TutorBlock for Tutor LMS**.
4. In the block editor, search for `TutorBlock` to add blocks.

## Why this is Node-free

- All frontend code is shipped as browser-ready vanilla JavaScript.
- All block editor code is shipped as browser-ready vanilla JavaScript using WordPress `wp.*` globals.
- All styling is shipped as plain CSS.
- No package manager, transpiler, or build tooling required.

## Notes for Tutor LMS users

- Works with Tutor LMS course/lesson pages and generic WordPress pages.
- If Tutor LMS is not active, blocks and shortcodes still work as standard WordPress features.
