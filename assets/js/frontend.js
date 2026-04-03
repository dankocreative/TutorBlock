(function () {
  'use strict';

  function updateStatus(container, value) {
    var status = container.querySelector('.tutorblock-video__status');
    if (!status) return;
    status.textContent = 'Progress: ' + value + '%';
  }

  function persistProgress(lessonId, progress) {
    if (!window.TutorBlockData || !TutorBlockData.restUrl) return;

    fetch(TutorBlockData.restUrl + '/progress', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': TutorBlockData.nonce
      },
      body: JSON.stringify({ lessonId: lessonId, progress: progress })
    }).catch(function () {
      // Non-critical: keep UX smooth even if save fails.
    });
  }

  document.querySelectorAll('.tutorblock-video').forEach(function (container) {
    var player = container.querySelector('.tutorblock-video__player');
    if (!player) return;

    var lessonId = container.getAttribute('data-lesson-id') || '0';
    var storageKey = 'tutorblock_progress_' + lessonId;

    player.addEventListener('timeupdate', function () {
      if (!player.duration) return;
      var progress = Math.round((player.currentTime / player.duration) * 100);
      updateStatus(container, progress);

      if (progress % 10 === 0) {
        localStorage.setItem(storageKey, String(progress));
        persistProgress(lessonId, progress);
      }
    });

    var cached = parseInt(localStorage.getItem(storageKey), 10);
    if (!isNaN(cached)) {
      updateStatus(container, cached);
    }
  });
})();
