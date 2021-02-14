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
?>