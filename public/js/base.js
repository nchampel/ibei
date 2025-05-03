$(document).ready(function () {
  let isPlaying = false;
  const audioKey = "ambiantMusicPosition";
  const music = $("#ambiant-music")[0];
  // const playStateKey = 'ambiantIsPlaying';
  // ➤ 1. Reprendre la position si sauvegardée
  const savedTime = localStorage.getItem(audioKey);
  if (savedTime !== null) {
    music.currentTime = parseFloat(savedTime);
  }
  // ➤ 2. Reprendre l'état de lecture
  // const wasPlaying = localStorage.getItem(playStateKey) === 'true';
  // // ➤ 3. Si musique jouait avant => rejoue automatiquement (si autorisé)
  // if (wasPlaying) {
  //     // Parfois nécessaire de différer un peu
  //     setTimeout(() => {
  //         const playPromise = music.play();
  //         if (playPromise !== undefined) {
  //             playPromise.then(() => {
  //                 isPlaying = true;
  //                 $('#toggle-music').html('<i class="fa-solid fa-volume-xmark" style="color: white;"></i>');
  //             }).catch((error) => {
  //                 // Autoplay bloqué → attendra le clic utilisateur
  //                 console.log('Autoplay bloqué, musique en attente d’un clic utilisateur.');
  //             });
  //         }
  //     }, 200);
  // }
  $(document).one("click", function () {
    $("#ambiant-music")[0].play();
    isPlaying = true;
    $("#toggle-music").html(
      '<i class="fa-solid fa-volume-xmark" style="color: white;"></i>'
    );
  });
  // pour la musique d'ambiance
  // let music = $('#ambiant-music')[0];

  $("#toggle-music").on("click", function () {
    if (!isPlaying) {
      music.play();
      $("#toggle-music").html(
        '<i class="fa-solid fa-volume-xmark" style="color: white;"></i>'
      );
      isPlaying = true;
    } else {
      music.pause();
      $("#toggle-music").html(
        '<i class="fa-solid fa-volume-high" style="color: white;"></i>'
      );
      isPlaying = false;
    }
    // localStorage.setItem(playStateKey, isPlaying);
  });

  // ➤ 4. Sauvegarde automatique de la position
  music.addEventListener("timeupdate", function () {
    localStorage.setItem(audioKey, music.currentTime);
  });
  // ➤ 8. Si on met pause directement (ex. avec devtools ou bouton pause)
  // music.addEventListener('pause', function () {
  //     localStorage.setItem(playStateKey, false);
  // });

  // music.addEventListener('play', function () {
  //     localStorage.setItem(playStateKey, true);
  // });
  // gérer le son
  $("#volumeControl").on("input", function () {
    music.volume = parseFloat(this.value);
    localStorage.setItem("userVolume", this.value);
    // console.log('Volume slider:', music.volume);
  });
  const savedVolume = localStorage.getItem("userVolume");
  if (savedVolume !== null) {
    music.volume = parseFloat(savedVolume);
    $("#volumeControl").val(savedVolume);
  }

  // Affiche ou masque le menu au clic sur l’icône
  $("#settings-toggle").on("click", function (e) {
    e.stopPropagation(); // Évite de déclencher le "click outside"
    $("#settings-menu").toggle();
  });

  // Fermer le menu si on clique ailleurs
  $(document).on("click", function (e) {
    const $menu = $("#settings-menu");
    const $toggle = $("#settings-toggle");

    if (
      !$menu.is(e.target) &&
      !$menu.has(e.target).length &&
      !$toggle.is(e.target) &&
      !$toggle.has(e.target).length
    ) {
      $menu.hide();
    }
  });

  // remettre jackpot is_claimend à false
  $("#close-modal-jackpot").click(function () {
    const $button = $(this);
    // $("#modal-jackpot").addClass("custom-hide");
    const url = "/app/reset-jackpot/" + "{{ token }}";
    // Appel AJAX vers la route Symfony
    $.ajax({
      url: url,
      method: "GET", // ou POST selon ta route
      success: function (response) {
        $("#modal-jackpot").addClass("custom-hide");
      },
      error: function (err) {
        console.error("Erreur AJAX :", err);
      },
    });
  });
});
