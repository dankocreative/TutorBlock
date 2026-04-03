(function (blocks, element, blockEditor, components, i18n) {
  'use strict';

  var el = element.createElement;
  var __ = i18n.__;
  var InspectorControls = blockEditor.InspectorControls;
  var PanelBody = components.PanelBody;
  var TextControl = components.TextControl;

  blocks.registerBlockType('tutorblock/video-lesson', {
    apiVersion: 2,
    title: __('TutorBlock Video Lesson', 'tutorblock'),
    icon: 'format-video',
    category: 'widgets',
    description: __('Embed a lesson video with progress tracking.', 'tutorblock'),
    attributes: {
      src: { type: 'string', default: '' },
      lesson_id: { type: 'number', default: 0 },
      title: { type: 'string', default: __('Video Lesson', 'tutorblock') }
    },
    edit: function (props) {
      var attrs = props.attributes;
      var setAttributes = props.setAttributes;

      return el(
        'div',
        { className: 'tutorblock-editor-card' },
        el(
          InspectorControls,
          null,
          el(
            PanelBody,
            { title: __('Video Settings', 'tutorblock'), initialOpen: true },
            el(TextControl, {
              label: __('Video URL (MP4)', 'tutorblock'),
              value: attrs.src,
              onChange: function (value) { setAttributes({ src: value }); }
            }),
            el(TextControl, {
              label: __('Lesson ID', 'tutorblock'),
              type: 'number',
              value: attrs.lesson_id || '',
              onChange: function (value) { setAttributes({ lesson_id: parseInt(value || '0', 10) || 0 }); }
            }),
            el(TextControl, {
              label: __('Block Title', 'tutorblock'),
              value: attrs.title,
              onChange: function (value) { setAttributes({ title: value }); }
            })
          )
        ),
        el('strong', null, __('TutorBlock Video Lesson', 'tutorblock')),
        el('p', null, attrs.src ? __('Video is configured. Frontend player appears on publish.', 'tutorblock') : __('Set a Video URL in block settings.', 'tutorblock'))
      );
    },
    save: function () {
      return null;
    }
  });

  blocks.registerBlockType('tutorblock/course-progress', {
    apiVersion: 2,
    title: __('TutorBlock Course Progress', 'tutorblock'),
    icon: 'chart-line',
    category: 'widgets',
    description: __('Display average learner video completion.', 'tutorblock'),
    edit: function () {
      return el(
        'div',
        { className: 'tutorblock-editor-card' },
        el('strong', null, __('TutorBlock Course Progress', 'tutorblock')),
        el('p', null, __('Frontend renders learner-specific progress for logged in users.', 'tutorblock'))
      );
    },
    save: function () {
      return null;
    }
  });
})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components, window.wp.i18n);
