<?php ?>
<html>
 <head>
  <title>test-phpSdk</title>
 </head>
 <body>
 <form action="PaymentTest.php" method="post" id="cinetPayForm">
      <input type="number" placeholder="montant" value="100" name="amount" id=""/>
      <select class="form-select" name="currency" id="currency">
        <option value="XOF">XOF</option>
        <option value="XAF">XAF</option>
        <option value="CDF">CDF</option>
        <option value="GNF">GNF</option>
      </select>
      <input type="submit" value="Valider">
     </form>

 </body>
</html>