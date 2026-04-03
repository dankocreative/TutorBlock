# TutorBlock

TutorBlock is a **zip-install ready WordPress plugin** for LMS/video enhancements with **no Node.js dependency**.

## Native TutorBlock blocks

All blocks appear under the dedicated Gutenberg category **TutorBlock LMS**:

- **TutorBlock Video Lesson** – lesson video player with progress tracking, behavior controls, and style controls.
- **TutorBlock Course Progress** – logged-in learner average completion widget.
- **TutorBlock Course Grid** – responsive course cards from published Tutor LMS courses.
- **TutorBlock Course Masonry** – Pinterest-style course masonry layout.
- **TutorBlock YouTube Shorts** – native short-form video widget for comma-separated YouTube short IDs.

## Shortcodes

- `[tutorblock_video_lesson src="https://.../lesson.mp4" lesson_id="123" title="Lesson 1"]`
- `[tutorblock_course_progress]`
- `[tutorblock_course_grid count="6" columns="3"]`
- `[tutorblock_course_masonry count="8" columns="3"]`
- `[tutorblock_youtube_shorts ids="abc123,def456" title="Lesson Shorts"]`

## Installation (ZIP)

1. Download this repository as a ZIP.
2. In WordPress admin, go to **Plugins > Add New > Upload Plugin**.
3. Upload ZIP and activate **TutorBlock for Tutor LMS**.
4. In the block editor, look for the **TutorBlock LMS** category or search `TutorBlock`.

## Why this is Node-free

- Browser-ready vanilla JavaScript and plain CSS assets.
- No package manager, transpiler, or build tooling required.
