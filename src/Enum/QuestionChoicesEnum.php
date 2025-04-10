<?php

namespace App\Enum;

class QuestionChoicesEnum
{
    const QUESTION1 = ["rb" => "Vous le caressez",
    "eb" => "Vous ne fixez pas son regard",
    "ob" => "Vous lui donnez à manger",
    "ab" => "Vous utilisez un amuse-chat",
    "sb" => "Vous l'ignorez"];
    const QUESTION2 = [
        "rb" => "Vous lui demandez pourquoi elle pleure",
    "rm" => "Vous vous moquez d'elle",
    "eb" => "Vous lui envoyez un sourire compatissant",
    "em" => "Vous vous éloignez",
    "ob" => "Vous lui donnez un mouchoir",
    "om" => "Vous la prenez dans vos bras",
    "ab" => "Vous faites une blague",
    "am" => "Vous baragouinez quelque chose d'inintéressant",
    "sb" => "Vous demandez votre chemin",
    "sm" => "Vous l'ignorez"
    ];
    const QUESTION3 = [
         "rb" =>
              "Vous acceptez de l'entendre, et lui confiez un de vos secrets en retour",
          
           "rm" =>
              "Vous lui dites de garder son secret, que vous en avez rien à faire",
          
          "eb" => "Vous l'écoutez sans l'interrompre", 
           "em" => "Vous l'arrêtez, vous ne savez pas garder un secret",
          
          "om" => "Vous vous mettez en mode psychologue" ,
          "ab" => "Vous lui promettez de l'emporter dans la tombe" ,
          "am" => "Vous dites oui, et écoutez à peine" ,
           "sb" => "Vous l'écoutez en sachant que cela vous laissera de marbre",
          
           "sm" => "Vous l'interrompez et lui parlez d'un de vos secrets à la place",
          
    ];
    const QUESTION4 = [
         "rb" => "Vous gardez la tête haute, mais vous êtes détruit intérieurement",
          
          "rm" => "Vous l'insultez de tous les noms" ,
          "eb" => "Vous vous serez la main bons amis" ,
           "em" => "Vous n'osez prononcer un mot de peur de tout perdre",
          
          "ob" => "Vous reconnaissez vos torts" ,
           "om" =>
              "Vous proposez de changer totalement pour correspondre à ses envies afin de sauver votre couple",
          
          "ab" => "Vous lui proposez de devenir sex-friends" ,
           "am" => "Vous refusez la situation et vous vous enfermez dans votre bulle",
          
           "sb" =>
              "Vous ne dites rien car être célibataire vous convient autant que d'être en couple",
          
           "sm" => "Vous ne réagissez pas et attendez la suite des événements",
          
    ];
}