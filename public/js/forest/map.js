// let currentX = 47; // Coordonnée initiale (centre de la carte)
// let currentY = 50;

function addResource(type, gain, spanId) {
  // jouer le son de la récolte
  $("#harvest-sound")[0].play();
  const $resource = $("#" + type + "-resource");
  const value = parseInt($resource.html());
  $resource.html(value + gain);
  $(spanId).html("Récupération");
}

function displayGrid(x, y, resources) {
  $("#map-container img.resource-icon").remove(); // Nettoyer les anciennes ressources ===========>>>>>>>>>>> attention, je pense pas utile car on charge que les ressources de la carte

  const startX = x - 1;
  const startY = y - 1;
  const endX = x + 1;
  const endY = y + 1;

  // console.log("x", startX);
  // console.log("y", startY);
  // console.log(resources);

  //   const pageResources = resources.filter(
  //     (r) => r.x >= startX && r.x <= endX && r.y >= startY && r.y <= endY
  //   );

  let offsetCoord = 140;

  resources.forEach((resource) => {
    // console.log("resource at", resource.x, resource.y);
    // let posX = resource.x * 6 + 30;
    // let posY = resource.y * 6 + 30;
    let posX = 70 + (resource.x - startX) * offsetCoord;
    let posY = 70 + (resource.y - startY) * offsetCoord;
    // let posX = 70 + (resource.x - x) * offsetCoord;
    // let posY = 70 + (resource.y - y) * offsetCoord;
    // console.log(resource.y);
    // console.log(startX)
    // console.log(posY);
    let imgSrc = window.baseImagePath + resource.image_url;

    if (resource.type !== "field") {
      let container = $("<div>", {
        class: "resource-container resource-icon-harvestable",
        css: {
          position: "absolute",
          left: posX + "px",
          top: posY + "px",
          // cursor: resource.isResource ? "pointer" : "inherit",
        },
        attr: {
          "data-id": resource.id,

          "data-type": resource.type,
        },
      });

      let img = $("<img />", {
        src: imgSrc,
        class: "resource-icon",
        id: "img-resource-" + resource.id,

        css: {
          //   left: posX  + "px",
          //   top: posY  + "px",
          cursor: resource.isResource ? "pointer" : "inherit",
        },
        attr: {
          "data-id": resource.id,
          // "data-url": "/foret/recolter/" + resource.type + "/" + resource.id,
          "data-type": resource.type,
        },
        // })
        // .on("click", function () {
        // Action au clic ici
        // console.log("Image cliquée :", resource);
        // Par exemple, afficher des infos ou déclencher un événement
        // addResource(resource.type, resource.gain);
      });
      // .on("click", function () {
      //   addResource(
      //     resource.type,
      //     resource.gain,
      //     "#counter-harvest-" + resource.id
      //   );
      // });
      //       <div id="popup" style="display: none;">
      //     <button class="action-btn" data-action="edit">Modifier</button>
      //     <button class="action-btn" data-action="delete">Supprimer</button>
      //     <button class="action-btn" data-action="details">Détails</button>
      // </div>
      let popup = $("<div>", {
        id: "popup-harvest-" + resource.id,
        class: "popup", // tu peux styliser ça
        text: "", // ou un compte à rebours si tu veux
        attr: {
          // "data-id": resource.id,
          // "data-url": '/foret/harvest/' + resource.id + "/" + resource.type,
          // "data-type": resource.type
        },
        css: {
          display: "none", // ici on bloque les interactions
        },
      });
      let harvestButton = $("<button>", {
        id: "popup-harvest-button-" + resource.id,
        class: "harvest-button-forest", // tu peux styliser ça
        text: "Récolter", // ou un compte à rebours si tu veux
        attr: {
          "data-id": resource.id,
          "data-url": "/foret/recolter/" + resource.type + "/" + resource.id,
          "data-action": "récolte",
        },
        // css: {
        //   "display": "none" // ici on bloque les interactions
        // }
      });
      let markButton = $("<button>", {
        id: "popup-mark-button-" + resource.id,
        // class: "popup", // tu peux styliser ça
        text: "Marquer", // ou un compte à rebours si tu veux
        attr: {
          // "data-id": resource.id,
          // "data-url": '/foret/harvest/' + resource.id + "/" + resource.type,
          // "data-type": resource.type
        },
        // css: {
        //   "display": "none" // ici on bloque les interactions
        // }
      });
      popup.append(harvestButton).append(markButton);

      let countDown = $("<div>", {
        id: "counter-harvest-" + resource.id,
        class: "", // tu peux styliser ça
        text: "", // ou un compte à rebours si tu veux
        attr: {
          "data-id": resource.id,
          // "data-url": '/foret/harvest/' + resource.id + "/" + resource.type,
          // "data-type": resource.type
        },
        css: {
          "pointer-events": "none", // ici on bloque les interactions
        },
      });
      container.append(img).append(countDown).append(popup);
      $("#map-container").append(container);
    } else {
      let container = $("<div>", {
        class: "resource-container",
        css: {
          position: "absolute",
          left: posX + "px",
          top: posY + "px",
          cursor: resource.isResource ? "pointer" : "inherit",
        },
        attr: {
          // "data-id": resource.id,
          // "data-url": '/foret/harvest/' + resource.id + "/" + resource.type,
          "data-type": resource.type,
        },
      });

      let img = $("<img />", {
        src: imgSrc,
        class: "resource-icon",
        
        // css: {
        //   left: posX  + "px",
        //   top: posY  + "px",
        //   cursor: resource.isResource ? "pointer" : "inherit"
        // },
        attr: {
          "data-url": '/foret/harvest/' + resource.id + "/" + resource.type,
          "data-type": resource.type
        }
        // })
        // .on("click", function () {
        // Action au clic ici
        // console.log("Image cliquée :", resource);
        // Par exemple, afficher des infos ou déclencher un événement
        // addResource(resource.type, resource.gain);
      });
      container.append(img);
      $("#map-container").append(container);
    }
  });
  $(".navigation-button").prop("disabled", false);
  checkNavigationButtons(); // Vérifier l'état des boutons après chaque affichage
}

// getNewResources();

function getNewResources(resourceId, action, $button) {
  $(".harvest-button-forest").click(function () {
    const $button = $(this);
    // jouer le son de la récolte
    $("#harvest-sound")[0].play();
    // $button.hide();
    const id = $button.data("id");
    const url = $button.data("url");
    const gain = $button.data("gain");
    const type = $button.data("type");
    const $counter = $("#compteur-harvest-" + id);
    // console.log('#compteur-' + id);
    $counter.addClass("fw-600").text("Récupération de " + type);

    // pour les animations argent et xp gagnées
    let $gainInfo = $("#gain-info-" + id);

    $gainInfo
      .text("+ " + gain + " " + type + " + 2xp")
      .css({ top: "30px", opacity: 1 }) // Position de départ
      .show()
      .animate(
        { top: "10px" },
        {
          duration: 1500, // plus lent
          easing: "swing", // "swing" est plus doux que "linear"
          step: function (now, fx) {
            if (fx.prop === "top" && parseInt(now) <= 10) {
              $gainInfo.fadeOut(400); // fondu quand arrivé à 10px
            }
          },
        }
      );

    // Appel AJAX vers la route Symfony
  //   $.ajax({
  //     url: url,
  //     method: "GET", // ou POST selon ta route
  //     success: function (response) {
  //       if (response.isClaimable) {
  //         $("#" + type + "-resource").text(
  //           type + " : " + response.typeValue.toLocaleString("fr-FR")
  //         );
  //         $("#exp").text("Expérience : " + response.exp);
  //         // à implémenter
  //         startCountdown($button, $counter, response.cooldown, resourceId);
  //       }
  //     },
  //     error: function (err) {
  //       console.error("Erreur AJAX :", err);
  //     },
  //   });
  // });
  $.ajax({
    url: url,
    method: "GET", // ou "POST" selon ton backend
    success: function (response) {
      // Traitement spécifique pour la récolte
      if (action === "récolte") {
        const type = response.type;
        $("#" + type + "-resource").text(
          type + " : " + response.typeValue.toLocaleString("fr-FR")
        );
        $("#exp").text("Expérience : " + response.exp);

        // // Si tu veux lancer un cooldown sur le bouton
        const $counter = $("#counter-harvest-" + resourceId);
        const $img = $("#img-resource-" + resourceId);
        console.log(resourceId)
        console.log($img)
        $img.hide();
        $("#popup-harvest-" + resourceId).hide();

        // const $img = $("img[data-id='" + response.resourceId + "']");
        startCountdown($button, $counter, response.cooldown, resourceId);
      }

      // Traitement pour "marquer", si besoin, ou juste un log
      if (action === "marquer") {
        console.log("Ressource marquée !");
        // Tu peux aussi mettre une animation, icône, etc.
      }
    },
    error: function (err) {
      console.error("Erreur AJAX :", err);
    },
    complete: function () {
      // Fermer le popup quoi qu'il arrive (succès ou erreur)
      $(this).closest(".popup").hide();
    }
  });
}
)};

function formatTime(seconds) {
  const hours = Math.floor(seconds / 3600); // Nombre d'heures
  const minutes = Math.floor((seconds % 3600) / 60); // Nombre de minutes
  const remainingSeconds = seconds % 60;
  // Nombre de secondes restantes

  // Ajouter un zéro devant les chiffres inférieurs à 10
  return (
    String(hours).padStart(2, "0") +
    ":" +
    String(minutes).padStart(2, "0") +
    ":" +
    String(remainingSeconds).padStart(2, "0")
  );
}

function startCountdown($button, $counter, cooldown, resourceId) {
  // $button.hide(); // Masquer le bouton
  $counter.show();

  const $bar = $('<div class="custom-cooldown-bar"></div>');
  const $text = $('<div class="custom-cooldown-text"></div>');
  const $container = $('<div class="custom-cooldown-wrapper"></div>')
    .append($bar)
    .append($text);

  $counter.html($container); // Remplace le contenu par le visuel du cooldown

  let timeLeft = cooldown;
  const total = cooldown;

  const interval = setInterval(() => {
    timeLeft--;

    let percent = ((total - timeLeft) / total) * 100;
    $bar.css("width", percent + "%");
    $text.text(formatTime(timeLeft));

    if (timeLeft <= 0) {
      clearInterval(interval);
      // $counter.text('Disponible !');
      $("img").css("pointer-events", "auto");
      // $button.css('pointer-events', 'auto');
      // $button.show();
      const $img = $("#img-resource-" + resourceId);
      $img.show();
      $counter.text("");
    }
  }, 1000);
}

// affichage bulle

$(document).on("click", ".resource-icon-harvestable", function (e) {
  const resourceId = $(this).data("id");
  const popup = $("#popup-harvest-" + resourceId);
  // console.log(popup);

  popup
    // .css({
    //   top: e.pageY + "px",
    //   left: e.pageX + "px",
    // })
    .show();

  // Fermer la bulle si on clique ailleurs
  $(document).on("click.popup", function (event) {
    if (
      !$(event.target).closest(
        "#popup-harvest-" + resourceId + ", .resource-icon-harvestable"
      ).length
    ) {
      popup.hide();
      $(document).off("click.popup");
    }
  });
});

$(document).on("click", ".harvest-button-forest", function () {
  const action = $(this).data("action");
  const url = $(this).data("url");
  const resourceId = $(this).data("id");
  console.log("Action choisie : " + action);
  console.log(url);
  getNewResources(resourceId, action, $(this))
  // $(this).closest(".popup").hide();

  //  $.ajax({
  //   url: url,
  //   method: "GET", // ou "POST" selon ton backend
  //   success: function (response) {
  //     // Traitement spécifique pour la récolte
  //     if (action === "récolte") {
  //       const type = response.type;
  //       $("#" + type + "-resource").text(
  //         type + " : " + response.typeValue.toLocaleString("fr-FR")
  //       );
  //       $("#exp").text("Expérience : " + response.exp);

  //       // // Si tu veux lancer un cooldown sur le bouton
  //       const $counter = $("#counter-harvest-" + resourceId);
  //       const $img = $("#img-resource-" + resourceId);
  //       $img.hide();

  //       // const $img = $("img[data-id='" + response.resourceId + "']");
  //       startCountdown($(this), $counter, response.cooldown, resourceId);
  //     }

  //     // Traitement pour "marquer", si besoin, ou juste un log
  //     if (action === "marquer") {
  //       console.log("Ressource marquée !");
  //       // Tu peux aussi mettre une animation, icône, etc.
  //     }
  //   },
  //   error: function (err) {
  //     console.error("Erreur AJAX :", err);
  //   },
  //   complete: function () {
  //     // Fermer le popup quoi qu'il arrive (succès ou erreur)
  //     $(this).closest(".popup").hide();
  //   }
  // });
});


// navigation
$(document).ready(function () {
  // console.log(resources);
  displayGrid(currentX, currentY, resources);
});

function navigate(direction) {
  $(".navigation").prop("disabled", true);
  $.ajax({
    url: "carte/navigation/" + direction,
    method: "GET", // ou POST selon ta route
    success: function (response) {
      // $("#" + type + "-resource").text(
      //   type + " : " + response.typeValue.toLocaleString("fr-FR")
      // );
      // $("#exp").text("Expérience : " + response.exp);
      // startCountdown($button, $counter, response.cooldown);
      displayGrid(currentX, currentY, response.forestResources);
    },
    error: function (err) {
      console.error("Erreur AJAX :", err);
    },
  });
}

$("#prev-left").click(function () {
  if (currentX > 0) {
    currentX -= 3;
    $("#position-x").text(currentX);
    navigate("left");
  }
});

$("#prev-up").click(function () {
  if (currentY > 0) {
    currentY -= 3;
    $("#position-y").text(currentY);
    navigate("up");
    // displayGrid(currentX, currentY, resources);
  }
});

$("#next-right").click(function () {
  if (currentX < 97) {
    // Limite à 90 pour ne pas dépasser 100 cases
    currentX += 3;
    $("#position-x").text(currentX);
    navigate("right");
    // displayGrid(currentX, currentY, resources);
  }
});

$("#next-down").click(function () {
  if (currentY < 97) {
    // Limite à 90 pour ne pas dépasser 100 cases
    currentY += 3;
    $("#position-y").text(currentY);
    navigate("down");
    // displayGrid(currentX, currentY, resources);
  }
});
// récupérer les nouvelles cases et sauvegarder la nouvelle position
// });

function checkNavigationButtons() {
  // Masquer le bouton "Gauche" si on est déjà à la limite gauche
  if (currentX <= 2) {
    $("#prev-left").hide();
  } else {
    $("#prev-left").show();
  }

  // Masquer le bouton "Haut" si on est déjà à la limite haute
  if (currentY <= 2) {
    $("#prev-up").hide();
  } else {
    $("#prev-up").show();
  }

  // Masquer le bouton "Droite" si on est déjà à la limite droite
  if (currentX >= 97) {
    // 100 cases - 10 visibles = 90
    $("#next-right").hide();
  } else {
    $("#next-right").show();
  }

  // Masquer le bouton "Bas" si on est déjà à la limite basse
  if (currentY >= 97) {
    // 100 cases - 10 visibles = 90
    $("#next-down").hide();
  } else {
    $("#next-down").show();
  }
}
