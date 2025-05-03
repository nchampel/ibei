let currentX = 50; // Coordonnée initiale (centre de la carte)
let currentY = 50;

function addResource(type, gain){
    const $resource = $("#" + type + "-resource");
    const value = parseInt($resource.html());
    $resource.html(value + gain);

}


function displayGrid(x, y) {
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

    let img = $("<img />", {
      src: imgSrc,
      class: "resource-icon",
      css: {
        left: posX  + "px",
        top: posY  + "px",
        cursor: resource.isResource ? "pointer" : "inherit"
      },
    }).on("click", function () {
        // Action au clic ici
        // console.log("Image cliquée :", resource);
        // Par exemple, afficher des infos ou déclencher un événement
        addResource(resource.type, resource.gain);
      });

    $("#map-container").append(img);
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

$(document).ready(function () {
    console.log(resources);
  displayGrid(currentX, currentY);

  $("#prev-left").click(function () {
    if (currentX > 0) {
      currentX--;
      displayGrid(currentX, currentY);
    }
  });

  $("#prev-up").click(function () {
    if (currentY > 0) {
      currentY--;
      displayGrid(currentX, currentY);
    }
  });

  $("#next-right").click(function () {
    if (currentX < 97) {
      // Limite à 90 pour ne pas dépasser 100 cases
      currentX++;
      displayGrid(currentX, currentY);
    }
  });

  $("#next-down").click(function () {
    if (currentY < 97) {
      // Limite à 90 pour ne pas dépasser 100 cases
      currentY++;
      displayGrid(currentX, currentY);
    }
  });
});
