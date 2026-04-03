(function (wp) {
  'use strict';

  if (!wp || !wp.blocks || !wp.element || !wp.components || !wp.i18n) return;

  var blocks = wp.blocks;
  var element = wp.element;
  var blockEditor = wp.blockEditor || wp.editor;
  var components = wp.components;
  var i18n = wp.i18n;
  if (!blockEditor || !blockEditor.InspectorControls) return;

  var el = element.createElement;
  var __ = i18n.__;
  var InspectorControls = blockEditor.InspectorControls;
  var PanelBody = components.PanelBody;
  var TextControl = components.TextControl;
  var RangeControl = components.RangeControl;
  var ToggleControl = components.ToggleControl;
  var ColorPalette = components.ColorPalette;

  var colors = [
    { name: 'Blue', color: '#1d4ed8' },
    { name: 'Green', color: '#15803d' },
    { name: 'Purple', color: '#7e22ce' },
    { name: 'Orange', color: '#c2410c' }
  ];

  function courseLayoutEdit(title, attrs, setAttributes) {
    return el('div', { className: 'tutorblock-editor-card' },
      el(InspectorControls, null,
        el(PanelBody, { title: __('Layout', 'tutorblock'), initialOpen: true },
          el(RangeControl, {
            label: __('Course Count', 'tutorblock'), min: 1, max: 24, value: attrs.count || 6,
            onChange: function (value) { setAttributes({ count: value || 1 }); }
          }),
          el(RangeControl, {
            label: __('Columns', 'tutorblock'), min: 1, max: 4, value: attrs.columns || 3,
            onChange: function (value) { setAttributes({ columns: value || 1 }); }
          }),
          el(TextControl, {
            label: __('CTA Button Text', 'tutorblock'), value: attrs.cta_text || '',
            onChange: function (value) { setAttributes({ cta_text: value }); }
          })
        )
      ),
      el('strong', null, title),
      el('p', null, __('Course cards render on frontend from published Tutor LMS courses.', 'tutorblock'))
    );
  }

  blocks.registerBlockType('tutorblock/video-lesson', {
    apiVersion: 2,
    title: __('TutorBlock Video Lesson', 'tutorblock'),
    icon: 'video-alt3',
    category: 'tutorblock',
    description: __('Embed a lesson video with progress tracking and style controls.', 'tutorblock'),
    attributes: {
      src: { type: 'string', default: '' }, poster: { type: 'string', default: '' }, lesson_id: { type: 'number', default: 0 },
      title: { type: 'string', default: __('Video Lesson', 'tutorblock') }, accent_color: { type: 'string', default: '#1d4ed8' },
      padding: { type: 'number', default: 16 }, border_radius: { type: 'number', default: 8 }, autoplay: { type: 'boolean', default: false }, muted: { type: 'boolean', default: false }, show_download_button: { type: 'boolean', default: false }
    },
    edit: function (props) {
      var a = props.attributes, set = props.setAttributes;
      return el('div', { className: 'tutorblock-editor-card', style: { borderLeft: '4px solid ' + (a.accent_color || '#1d4ed8') } },
        el(InspectorControls, null,
          el(PanelBody, { title: __('Content', 'tutorblock'), initialOpen: true },
            el(TextControl, { label: __('Video URL (MP4)', 'tutorblock'), value: a.src, onChange: function (v) { set({ src: v }); } }),
            el(TextControl, { label: __('Poster image URL', 'tutorblock'), value: a.poster, onChange: function (v) { set({ poster: v }); } }),
            el(TextControl, { label: __('Lesson ID', 'tutorblock'), type: 'number', value: a.lesson_id || '', onChange: function (v) { set({ lesson_id: parseInt(v || '0', 10) || 0 }); } }),
            el(TextControl, { label: __('Title', 'tutorblock'), value: a.title, onChange: function (v) { set({ title: v }); } })
          ),
          el(PanelBody, { title: __('Behavior', 'tutorblock'), initialOpen: false },
            el(ToggleControl, { label: __('Autoplay', 'tutorblock'), checked: !!a.autoplay, onChange: function (v) { set({ autoplay: !!v }); } }),
            el(ToggleControl, { label: __('Muted', 'tutorblock'), checked: !!a.muted, onChange: function (v) { set({ muted: !!v }); } }),
            el(ToggleControl, { label: __('Show download link', 'tutorblock'), checked: !!a.show_download_button, onChange: function (v) { set({ show_download_button: !!v }); } })
          ),
          el(PanelBody, { title: __('Style', 'tutorblock'), initialOpen: false },
            el('p', null, __('Accent color', 'tutorblock')),
            el(ColorPalette, { colors: colors, value: a.accent_color, onChange: function (v) { set({ accent_color: v || '#1d4ed8' }); } }),
            el(RangeControl, { label: __('Padding', 'tutorblock'), min: 0, max: 60, value: a.padding, onChange: function (v) { set({ padding: v || 0 }); } }),
            el(RangeControl, { label: __('Border radius', 'tutorblock'), min: 0, max: 40, value: a.border_radius, onChange: function (v) { set({ border_radius: v || 0 }); } })
          )
        ),
        el('strong', null, a.title || __('TutorBlock Video Lesson', 'tutorblock')),
        el('p', null, a.src ? __('Configured. Publish/update to view interactive player.', 'tutorblock') : __('Set a video URL in block settings.', 'tutorblock'))
      );
    },
    save: function () { return null; }
  });

  blocks.registerBlockType('tutorblock/course-progress', {
    apiVersion: 2,
    title: __('TutorBlock Course Progress', 'tutorblock'),
    icon: 'chart-bar',
    category: 'tutorblock',
    attributes: { label: { type: 'string', default: __('Average video completion:', 'tutorblock') }, accent_color: { type: 'string', default: '#1d4ed8' } },
    edit: function (props) {
      var a = props.attributes, set = props.setAttributes;
      return el('div', { className: 'tutorblock-editor-card', style: { borderLeft: '4px solid ' + (a.accent_color || '#1d4ed8') } },
        el(InspectorControls, null,
          el(PanelBody, { title: __('Content', 'tutorblock'), initialOpen: true },
            el(TextControl, { label: __('Label', 'tutorblock'), value: a.label, onChange: function (v) { set({ label: v }); } })
          ),
          el(PanelBody, { title: __('Style', 'tutorblock'), initialOpen: false },
            el('p', null, __('Accent color', 'tutorblock')),
            el(ColorPalette, { colors: colors, value: a.accent_color, onChange: function (v) { set({ accent_color: v || '#1d4ed8' }); } })
          )
        ),
        el('strong', null, __('TutorBlock Course Progress', 'tutorblock')),
        el('p', null, a.label || __('Average video completion:', 'tutorblock'))
      );
    },
    save: function () { return null; }
  });

  blocks.registerBlockType('tutorblock/course-grid', {
    apiVersion: 2,
    title: __('TutorBlock Course Grid', 'tutorblock'),
    icon: 'screenoptions',
    category: 'tutorblock',
    attributes: { count: { type: 'number', default: 6 }, columns: { type: 'number', default: 3 }, cta_text: { type: 'string', default: __('View Course', 'tutorblock') } },
    edit: function (props) { return courseLayoutEdit(__('TutorBlock Course Grid', 'tutorblock'), props.attributes, props.setAttributes); },
    save: function () { return null; }
  });

  blocks.registerBlockType('tutorblock/course-masonry', {
    apiVersion: 2,
    title: __('TutorBlock Course Masonry', 'tutorblock'),
    icon: 'grid-view',
    category: 'tutorblock',
    attributes: { count: { type: 'number', default: 8 }, columns: { type: 'number', default: 3 }, cta_text: { type: 'string', default: __('View Course', 'tutorblock') } },
    edit: function (props) { return courseLayoutEdit(__('TutorBlock Course Masonry', 'tutorblock'), props.attributes, props.setAttributes); },
    save: function () { return null; }
  });

  blocks.registerBlockType('tutorblock/youtube-shorts', {
    apiVersion: 2,
    title: __('TutorBlock YouTube Shorts', 'tutorblock'),
    icon: 'format-video',
    category: 'tutorblock',
    attributes: { ids: { type: 'string', default: '' }, title: { type: 'string', default: __('Lesson Shorts', 'tutorblock') } },
    edit: function (props) {
      var a = props.attributes, set = props.setAttributes;
      return el('div', { className: 'tutorblock-editor-card' },
        el(InspectorControls, null,
          el(PanelBody, { title: __('Content', 'tutorblock'), initialOpen: true },
            el(TextControl, { label: __('Heading', 'tutorblock'), value: a.title, onChange: function (v) { set({ title: v }); } }),
            el(TextControl, { label: __('Short IDs (comma-separated)', 'tutorblock'), value: a.ids, onChange: function (v) { set({ ids: v }); } })
          )
        ),
        el('strong', null, a.title || __('Lesson Shorts', 'tutorblock')),
        el('p', null, __('Add YouTube short IDs like: abc123,def456', 'tutorblock'))
      );
    },
    save: function () { return null; }
  });
})(window.wp);
