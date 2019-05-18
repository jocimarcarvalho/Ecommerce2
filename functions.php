<?php
  function formatPrice(float $vlprice)
  {
      return number_format($vlprice, 2, ",", ".");

      // Parâmetros: 1 - valor, 2 - casas decimais, 3 - separador decimal, 4 - separador de milhar
  }


?>