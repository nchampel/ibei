let currentX = 50; // Coordonnée initiale (centre de la carte)
let currentY = 50;

function addResource(type, gain, spanId) {
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

  //   console.log("x", startX);
  //   console.log("y", startY);
  //   console.log(resources);

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
    // console.log(resource.y);
    // console.log(startY)
    // console.log(posY);
    let imgSrc = window.baseImagePath + resource.image_url;

    if (resource.type !== "field") {
      let container = $("<div>", {
        class: "resource-container resource-icon-harvestable",
        css: {
          position: "absolute",
          left: posX + "px",
          top: posY + "px",
          cursor: resource.isResource ? "pointer" : "inherit",
        },
        attr: {
          "data-id": resource.id,
          "data-url": "/foret/recolter/" + resource.type + "/" + resource.id,
          "data-type": resource.type,
        },
      }).on("click", function () {
        addResource(
          resource.type,
          resource.gain,
          "#counter-harvest-" + resource.id
        );
      });

      let img = $("<img />", {
        src: imgSrc,
        class: "resource-icon",
        // css: {
        //   left: posX  + "px",
        //   top: posY  + "px",
        //   cursor: resource.isResource ? "pointer" : "inherit"
        // },
        // attr: {
        //   "data-id": resource.id,
        //   "data-url": '/foret/harvest/' + resource.id + "/" + resource.type,
        //   "data-type": resource.type
        // }
        // })
        // .on("click", function () {
        // Action au clic ici
        // console.log("Image cliquée :", resource);
        // Par exemple, afficher des infos ou déclencher un événement
        // addResource(resource.type, resource.gain);
      });
      let countDown = $("<span>", {
        id: "counter-harvest-" + resource.id,
        class: "", // tu peux styliser ça
        text: "", // ou un compte à rebours si tu veux
        attr: {
          "data-id": resource.id,
          // "data-url": '/foret/harvest/' + resource.id + "/" + resource.type,
          // "data-type": resource.type
        },
      });
      container.append(img).append(countDown);
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
        // attr: {
        //   "data-id": resource.id,
        //   "data-url": '/foret/harvest/' + resource.id + "/" + resource.type,
        //   "data-type": resource.type
        // }
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

  checkNavigationButtons(); // Vérifier l'état des boutons après chaque affichage
}

function checkNavigationButtons() {
  // Masquer le bouton "Gauche" si on est déjà à la limite gauche
  if (currentX <= 0) {
    $("#prev-left").hide();
  } else {
    $("#prev-left").show();
  }

  // Masquer le bouton "Haut" si on est déjà à la limite haute
  if (currentY <= 0) {
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

function getNewResources() {
  $(".resource-icon").click(function () {
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
    $.ajax({
      url: url,
      method: "GET", // ou POST selon ta route
      success: function (response) {
        if (response.isClaimable) {
          $("#" + type + "-resource").text(
            type + " : " + response.typeValue.toLocaleString("fr-FR")
          );
          $("#exp").text("Expérience : " + response.exp);
          // à implémenter
          startCountdown($button, $counter, response.cooldown);
        }
      },
      error: function (err) {
        console.error("Erreur AJAX :", err);
      },
    });
  });
}

function formatTime( seconds ) {
	const hours = Math.floor( seconds / 3600 ); // Nombre d'heures
	const minutes = Math.floor( ( seconds % 3600 ) / 60 ); // Nombre de minutes
	const remainingSeconds = seconds % 60;
	// Nombre de secondes restantes

	// Ajouter un zéro devant les chiffres inférieurs à 10
	return( String( hours ).padStart( 2, '0' ) + ":" + String( minutes ).padStart( 2, '0' ) + ":" + String( remainingSeconds ).padStart( 2, '0' ) );
}

function startCountdown( $button, $counter, cooldown ) {
	// $button.hide(); // Masquer le bouton
	$counter.show();

	const $bar = $( '<div class="custom-cooldown-bar"></div>' );
	const $text = $( '<div class="custom-cooldown-text"></div>' );
	const $container = $( '<div class="custom-cooldown-wrapper"></div>' ).append( $bar ).append( $text );

	$counter.html( $container ); // Remplace le contenu par le visuel du cooldown

	let timeLeft = cooldown;
	const total = cooldown;

	const interval = setInterval( () => {
		timeLeft--;

		let percent = ( ( total - timeLeft ) / total ) * 100;
		$bar.css( 'width', percent + '%' );
		$text.text( formatTime(timeLeft));

		if ( timeLeft <= 0 ) {
			clearInterval( interval );
			// $counter.text('Disponible !');
			$button.show();
			$counter.text('');
		}
	}, 1000 );
}

$(document).ready(function () {
  // console.log(resources);
  displayGrid(currentX, currentY, resources);

  $(".resource-icon-harvestable").click(function () {
    const $button = $(this).find("img");
    // jouer le son de la récolte
    $("#harvest-sound")[0].play();
    // au lieu de cacher, mettre compte à rebours
    $button.hide();
    const id = $button.data("id");
    const url = $button.data("url");
    const gain = $button.data("gain");
    const type = $button.data("type");
    const $counter = $("#counter-harvest-" + id);
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
    $.ajax({
      url: url,
      method: "GET", // ou POST selon ta route
      success: function (response) {
        if (response.isClaimable) {
          $("#" + type + "-resource").text(
            type + " : " + response.typeValue.toLocaleString("fr-FR")
          );
          $("#exp").text("Expérience : " + response.exp);
          startCountdown($button, $counter, response.cooldown);
        }
      },
      error: function (err) {
        console.error("Erreur AJAX :", err);
      },
    });
  });

  $("#prev-left").click(function () {
    if (currentX > 0) {
      currentX -= 3;

      displayGrid(currentX, currentY);
    }
  });

  $("#prev-up").click(function () {
    if (currentY > 0) {
      currentY -= 3;
      displayGrid(currentX, currentY);
    }
  });

  $("#next-right").click(function () {
    if (currentX < 97) {
      // Limite à 90 pour ne pas dépasser 100 cases
      currentX += 3;
      displayGrid(currentX, currentY);
    }
  });

  $("#next-down").click(function () {
    if (currentY < 97) {
      // Limite à 90 pour ne pas dépasser 100 cases
      currentY += 3;
      displayGrid(currentX, currentY);
    }
  });
  // récupérer les nouvelles cases et sauvegarder la nouvelle position
});
