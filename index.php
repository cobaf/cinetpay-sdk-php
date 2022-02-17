
<html>
    <head>
        <title>CinetPay-SDK-PHP</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css">
        <link rel="stylesheet" href="src/interface.css">
    </head>

  <body>
    <div class="container-fluid">
        <div class="row d-flex justify-content-center">
            <div class="col-sm-12">
                <div class="card mx-auto">
                    <p class="heading">EXEMPLE INTEGRATION PHP</p>
                    <form action="action.php" method="post" class="card-details ">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <p class="text-warning mb-0">Nom</p> 
                                    <input type="text" name="customer_name" id="customer_name" value="Colle"> 
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <p class="text-warning mb-0">Prenom</p>
                                    <input type="text" name="customer_surname" id="customer_surname" value="Jeremie">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <p class="text-warning mb-0">Montant</p> 
                                    <input type="number" name="amount" id="amount" value="100"> 
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <p class="text-warning mb-0">Devise</p>
                                    <select class="form-select" name="currency" id="currency">
                                        <option value="XOF">XOF</option>
                                        <option value="XAF">XAF</option>
                                        <option value="CDF">CDF</option>
                                        <option value="GNF">GNF</option>
                                        <option value="USD">USD</option>
                                    </select>

                                </div>
                            </div>
                        </div>

                       <div class="row">
                           <div class="col-sm-12">
                               <div class="form-group mb-3">
                                    <p class="text-warning mb-0">Description</p>
                                    <input type="text" name="description" value="Achat sdk">
                                </div>
                           </div>
                        
                       </div>
                       

                      <div class="pt-0"> <button type="submit" name="valider" class="btn btn-success">Valider<i class="fas fa-arrow-right px-3 py-2"></i></button> </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  </body>
</html>