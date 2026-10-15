/* ==========================================================================
   Reproductor MP3 personalizado · Podcasts
   Controla play/pausa, barra de avance, tiempo y volumen.
   Se aplica a cada .reproductor que exista en la página.
   ========================================================================== */
(function () {
  'use strict';

  var reproductores = document.querySelectorAll('.reproductor');
  if (!reproductores.length) return;

  function formatearTiempo(segundos) {
    if (!isFinite(segundos) || segundos < 0) return '0:00';
    var m = Math.floor(segundos / 60);
    var s = Math.floor(segundos % 60);
    return m + ':' + (s < 10 ? '0' : '') + s;
  }

  Array.prototype.forEach.call(reproductores, function (caja) {

    var audio       = caja.querySelector('audio');
    var btnPlay     = caja.querySelector('.btn-play');
    var iconoPlay   = btnPlay ? btnPlay.querySelector('i') : null;
    var barra       = caja.querySelector('.player__barra');
    var avance      = caja.querySelector('.player__avance');
    var tActual     = caja.querySelector('.tiempo-actual');
    var tTotal      = caja.querySelector('.tiempo-total');
    var btnVol      = caja.querySelector('.btn-vol');
    var iconoVol    = btnVol ? btnVol.querySelector('i') : null;
    var volSlider   = caja.querySelector('.vol-slider');

    if (!audio || !btnPlay || !barra || !avance) return;

    /* ── Play / Pausa ── */
    btnPlay.addEventListener('click', function () {
      if (audio.paused) {
        audio.play();
      } else {
        audio.pause();
      }
    });

    audio.addEventListener('play', function () {
      iconoPlay.className = 'bi bi-pause-fill';
      btnPlay.setAttribute('aria-label', 'Pausar');
    });

    audio.addEventListener('pause', function () {
      iconoPlay.className = 'bi bi-play-fill';
      btnPlay.setAttribute('aria-label', 'Reproducir');
    });

    /* ── Duración total disponible ── */
    audio.addEventListener('loadedmetadata', function () {
      if (tTotal) tTotal.textContent = formatearTiempo(audio.duration);
    });

    /* ── Actualizar barra y tiempo mientras suena ── */
    audio.addEventListener('timeupdate', function () {
      if (!audio.duration) return;
      var pct = (audio.currentTime / audio.duration) * 100;
      avance.style.width = pct + '%';
      barra.setAttribute('aria-valuenow', Math.round(pct));
      if (tActual) tActual.textContent = formatearTiempo(audio.currentTime);
    });

    /* ── Adelantar / retroceder al pulsar la barra ── */
    function saltarA(clientX) {
      var rect = barra.getBoundingClientRect();
      var x    = clientX - rect.left;
      var pct  = Math.max(0, Math.min(1, x / rect.width));
      if (audio.duration) {
        audio.currentTime = pct * audio.duration;
      }
    }

    barra.addEventListener('click', function (e) {
      saltarA(e.clientX);
    });

    /* ── Control por teclado en la barra (accesibilidad) ── */
    barra.addEventListener('keydown', function (e) {
      if (!audio.duration) return;
      var paso = 5; /* segundos */
      if (e.key === 'ArrowRight') { audio.currentTime = Math.min(audio.duration, audio.currentTime + paso); e.preventDefault(); }
      if (e.key === 'ArrowLeft')  { audio.currentTime = Math.max(0, audio.currentTime - paso); e.preventDefault(); }
      if (e.key === 'Home')       { audio.currentTime = 0; e.preventDefault(); }
      if (e.key === 'End')        { audio.currentTime = audio.duration; e.preventDefault(); }
    });

    /* ── Volumen ── */
    if (volSlider) {
      volSlider.addEventListener('input', function () {
        audio.volume = parseFloat(this.value);
        actualizarIconoVolumen();
      });
    }

    if (btnVol) {
      btnVol.addEventListener('click', function () {
        audio.muted = !audio.muted;
        actualizarIconoVolumen();
      });
    }

    function actualizarIconoVolumen() {
      if (!iconoVol) return;
      if (audio.muted || audio.volume === 0) {
        iconoVol.className = 'bi bi-volume-mute-fill';
      } else if (audio.volume < 0.5) {
        iconoVol.className = 'bi bi-volume-down-fill';
      } else {
        iconoVol.className = 'bi bi-volume-up-fill';
      }
    }

  });

})();