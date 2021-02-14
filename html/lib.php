<?php
  // Permet de donner une value balises input text
  function value_text($field) {
      if (isset($_REQUEST[$field])) {
        return $_REQUEST[$field];
      }
      else {
        return "";
      }
  }
  
  // Affiche le header de navigatiton du site
  function website_header() {
    echo 
    '<div class="menu">
      <ul>
        <li><a href="index.php"> Accueil </a></li>
        <li><a href="view_entry.php"> Recherche par entrée </a></li>
        <li><a href="filter_entry.php"> Recherche par filtre </a></li>
      </ul>
    </div>
    <hr>';
  }
?>