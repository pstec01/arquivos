<?php
$pagina = "Consultrar Cadastros";
$menu_rapido = addslashes($_GET['r']);


session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  header("location: login.php");
  exit;
}

if($_GET['tipo']){
	$tipo = $_GET['tipo'];
}else{
	$tipo = 'Fisica';
}

require_once('Connections/accolita.php'); 
mysqli_select_db($accolita,$database_accolita);
mysqli_set_charset($accolita, 'utf8');

if($_POST['busca_filtro']){
	$busca_filtro = $_POST['busca_filtro'];
}
if($_POST['pesquisa']){
	$pesquisa = $_POST['pesquisa'];
}

if ($pesquisa != ''){
$query_datas = "SELECT * FROM clientes WHERE $busca_filtro LIKE '%$pesquisa%' AND tipo = '$tipo' ORDER BY nome_rasao_social ASC";	

}else{

  $query_datas = "SELECT * FROM clientes WHERE tipo = '$tipo' ORDER BY nome_rasao_social ASC";	

}





$ano_mes_atual = date('Ym');

	
$datas = mysqli_query($accolita,$query_datas) or die(mysql_error());
$row_datas = mysqli_fetch_assoc($datas);
$totalRows_datas = mysqli_num_rows($datas);


$total_reg = "21"; // número de registros por página
$pagina=$_GET['pagina'];

if (!$pagina) {
$pc = "1";
} else {
$pc = $pagina;
}


$inicio = $pc - 1;
$inicio = $inicio * $total_reg;

$limite = mysqli_query($accolita,"$query_datas LIMIT $inicio,$total_reg");

$todos = mysqli_query($accolita,"$query_datas");


$tr = mysqli_num_rows($todos); // verifica o número total de registros
$tp = $tr / $total_reg; // verifica o número total de páginas
$dados = mysqli_fetch_array($limite);
?>

<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Accolita - Sistema</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="assets/vendors/jvectormap/jquery-jvectormap.css">
    <link rel="stylesheet" href="assets/vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="assets/vendors/owl-carousel-2/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/vendors/owl-carousel-2/owl.theme.default.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <link rel="stylesheet" href="css/theme.default.min.css">
<script src="js/tablesorter.min.js"></script>
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="assets/images/favicon.png" />
  </head>

  <script>
  window.addEventListener('DOMContentLoaded', function() {
    // Obtém todas as colunas de cabeçalho
    const headers = document.querySelectorAll('th[data-sortable="true"]');

    // Adiciona um evento de clique a cada coluna de cabeçalho
    headers.forEach(function(header) {
      header.addEventListener('click', function() {
        const table = document.querySelector('table');
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));

        const columnIndex = Array.from(header.parentNode.children).indexOf(header);

        // Verifica se a coluna contém valores numéricos
        const isNumeric = rows.every(function(row) {
          const cell = row.children[columnIndex];
          const cellValue = cell ? cell.innerText.trim() : '';
          return !isNaN(parseFloat(cellValue));
        });

        // Verifica o estado atual de ordenação da coluna
        const currentSortOrder = header.getAttribute('data-sort-order');
        let sortOrder;
        if (currentSortOrder === 'asc') {
          sortOrder = 'desc';
        } else {
          sortOrder = 'asc';
        }

        // Remove as setas existentes em todos os cabeçalhos
        headers.forEach(function(header) {
          const arrow = header.querySelector('.sort-arrow');
          if (arrow) {
            header.removeChild(arrow);
          }
        });

        // Cria o elemento <span> para exibir a seta
        const arrow = document.createElement('span');
        arrow.classList.add('sort-arrow');

        // Define a classe CSS e a direção da seta com base no estado de ordenação
        if (sortOrder === 'asc') {
          arrow.classList.add('asc');
          arrow.textContent = '▲';
        } else {
          arrow.classList.add('desc');
          arrow.textContent = '▼';
        }

        // Adiciona a seta ao cabeçalho clicado
        header.appendChild(arrow);

        // Define o novo estado de ordenação da coluna
        header.setAttribute('data-sort-order', sortOrder);

        // Ordena as linhas com base na coluna clicada e no estado de ordenação
        rows.sort(function(a, b) {
          const cellA = a.children[columnIndex];
          const cellB = b.children[columnIndex];
          const valueA = cellA ? cellA.innerText.trim() : '';
          const valueB = cellB ? cellB.innerText.trim() : '';

          if (isNumeric) {
            if (sortOrder === 'asc') {
              return parseFloat(valueA) - parseFloat(valueB);
            } else {
              return parseFloat(valueB) - parseFloat(valueA);
            }
          } else {
            if (sortOrder === 'asc') {
              return valueA.localeCompare(valueB);
            } else {
              return valueB.localeCompare(valueA);
            }
          }
        });

        // Remove as linhas existentes
        rows.forEach(function(row) {
          tbody.removeChild(row);
        });

        // Adiciona as linhas reordenadas à tabela
        rows.forEach(function(row) {
          tbody.appendChild(row);
        });
      });
    });
  });
</script>

<body <?php if($_SESSION['modo_de_visualizacao']==2){ echo 'class="sidebar-icon-only"';} ?>>
    <div class="container-scroller">
<?php include("menu.php"); ?>
      <div class="container-fluid page-body-wrapper">
        <!-- partial:partials/_navbar.html -->
        <nav class="navbar p-0 fixed-top d-flex flex-row">
          <div class="navbar-menu-wrapper flex-grow d-flex align-items-stretch">
            <button onclick="funcaoSetarModoVisualizacao()" class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
              <span class="mdi mdi-menu"></span>
            </button>
            <ul class="navbar-nav w-100">
              <li class="nav-item w-100">
                <form class="nav-link mt-2 mt-md-0 d-none d-lg-flex search">
                  <input type="text" class="form-control" placeholder="Procurar no Sistema">
                </form>
              </li>
            </ul>
            <ul class="navbar-nav navbar-nav-right">
<?php include("menu_rapido.php"); ?>

              <li class="nav-item nav-settings d-none d-lg-block">
                <a class="nav-link" href="#">
                  <i class="mdi mdi-view-grid"></i>
                </a>
              </li>

              <li class="nav-item dropdown border-left">
                <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#" data-toggle="dropdown">
                  <i class="mdi mdi-bell"></i>
                  <span class="count bg-danger"></span>
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown">
                  <h6 class="p-3 mb-0">Notificações</h6>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item preview-item">
                    <div class="preview-thumbnail">
                      <div class="preview-icon bg-dark rounded-circle">
                        <i class="mdi mdi-calendar text-success"></i>
                      </div>
                    </div>
                    <div class="preview-item-content">
                      <p class="preview-subject mb-1">Agendamento de Faturas</p>
                      <p class="text-muted ellipsis mb-0"> Hoje vence boletos agendados... </p>
                    </div>
                  </a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item preview-item">
                    <div class="preview-thumbnail">
                      <div class="preview-icon bg-dark rounded-circle">
                        <i class="mdi mdi-settings text-danger"></i>
                      </div>
                    </div>
                    <div class="preview-item-content">
                      <p class="preview-subject mb-1">Configurações Alteradas</p>
                      <p class="text-muted ellipsis mb-0"> Forma de pagamento Cartão Visa adicionada... </p>
                    </div>
                  </a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item preview-item">
                    <div class="preview-thumbnail">
                      <div class="preview-icon bg-dark rounded-circle">
                        <i class="mdi mdi-link-variant text-warning"></i>
                      </div>
                    </div>
                    <div class="preview-item-content">
                      <p class="preview-subject mb-1">Relatório Gerado</p>
                      <p class="text-muted ellipsis mb-0"> Novo reltário disponível... </p>
                    </div>
                  </a>
                  <div class="dropdown-divider"></div>
                  <p class="p-3 mb-0 text-center">Ver Todas</p>
                </div>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link" id="profileDropdown" href="#" data-toggle="dropdown">
                  <div class="navbar-profile">
                    <img class="img-xs rounded-circle" src="assets/images/faces-clipart/pic-8.png" alt="">
                    <p class="mb-0 d-none d-sm-block navbar-profile-name"><?php echo $_SESSION["usuario_nome"]; ?></p>
                    <i class="mdi mdi-menu-down d-none d-sm-block"></i>
                  </div>
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="profileDropdown">
                  <h6 class="p-3 mb-0">Profile</h6>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item preview-item">
                    <div class="preview-thumbnail">
                      <div class="preview-icon bg-dark rounded-circle">
                        <i class="mdi mdi-settings text-success"></i>
                      </div>
                    </div>
                    <div class="preview-item-content">
                      <p class="preview-subject mb-1">Configurações</p>
                    </div>
                  </a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item preview-item" onclick="java:window.location='logout.php';">
                    <div class="preview-thumbnail" >
                      <div class="preview-icon bg-dark rounded-circle">
                        <i class="mdi mdi-logout text-danger"></i>
                      </div>
                    </div>
                    <div class="preview-item-content">
                    <p class="preview-subject mb-1">Sair</p>
                    </div>
                  </a>
                
              </li>
            </ul>
            <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
              <span class="mdi mdi-format-line-spacing"></span>
            </button>
          </div>
        </nav>
        <!-- partial -->
        <div class="main-panel">
          <div class="content-wrapper">
           

            <div class="row">
    

            <div class="container-fluid">
   <div class="grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                   <center> <h3 class="card-title">Cadastros</h3> </center>
                   <table width="100%" border="0" cellspacing="0" cellpadding="0">
		          <tr>
		            <td width="24%" height="50"><span style="font-size:16px;" class="m-0 font-weight-bold text-primary"><span class="h3 mb-4 text-gray-800"><img src="icons/Pessoas_32441.png" alt="" width="50" height="50" /></span> Pessoa: <select style="width:100px; height:	25px;"  name="tipo" id="tipo" onChange="this.options[this.selectedIndex].value && (window.location = 'clientes.php?tipo='+ this.options[this.selectedIndex].value);">
                    <option <?php if ($tipo=='Juridica'){ echo 'selected'; } ?> value="Juridica">Jurídica</option>
		                <option <?php if ($tipo=='Fisica'){ echo 'selected'; } ?> value="Fisica">Física</option>
</select>
		                </span></td>
		            <td width="56%"><form name="form1" method="post" action="">

		              <span class="m-0 font-weight-bold text-primary" style="font-size:16px;">Busca Avançada:
                      <select style="width:160px; height:	25px;"  name="busca_filtro" id="busca_filtro">
                        <option <?php if ($busca_filtro=='nome_rasao_social'){ echo 'selected'; } ?> value="nome_rasao_social">Nome/Razão Social</option>
                         <option <?php if ($busca_filtro=='id'){ echo 'selected'; } ?> value="id">Código Cliente</option>
                        <option <?php if ($busca_filtro=='cpf_cnpj'){ echo 'selected'; } ?> value="cpf_cnpj">CPF/CNPJ</option>
                        <option <?php if ($busca_filtro=='endereco_rua'){ echo 'selected'; } ?> value="endereco_rua">Rua</option>
                        <option <?php if ($busca_filtro=='endereco_bairro'){ echo 'selected'; } ?> value="endereco_bairro">Bairro</option>
                          <option <?php if ($busca_filtro=='cidade'){ echo 'selected'; } ?> value="cidade">Cidade</option>
                          <option <?php if ($busca_filtro=='estado'){ echo 'selected'; } ?> value="cidade">Estado</option>
                          <option <?php if ($busca_filtro=='tel'){ echo 'selected'; } ?> value="tel">Telefone</option>
                          <option <?php if ($busca_filtro=='cel/whats'){ echo 'selected'; } ?> value="cel/whats">Cel/Whats</option>
                      </select>
       
		     
		                <input  style="width:160px; height:	25px;" type="text" name="pesquisa" id="pesquisa">
		         
            
                        <input  style="width:80px;  height:25px; font-size:16px;" type="submit" name="button" id="button" value="BUSCAR">
                  </span>
		            </form></td>
	              </tr>
	            </table>
                    <div class="table-responsive">
                      <form id="alterar" action="alterar.php" method="post">
                      <table class="table" >
                        <thead>
                          <tr>
                          <th style="color:white;" data-sortable="true"> id </th>
                            <th style="color:white;" data-sortable="true"> Nome  </th>
                            <th style="color:white;" data-sortable="true"> CPF/RG </th>
                            <th style="color:white;" data-sortable="true"> Cidade</th> 
                            <th style="color:white;" data-sortable="true"> Estado </th>
                            <th style="color:white;" data-sortable="true"> Cel/Whats </th>
                            <th style="color:white;" data-sortable="true"> Opções </th>
                          </tr>
                        </thead>
                        <tbody>

                        <?php if ($row_datas['id'] != ''){ ?>
              <?php do { 

		?>
                          <tr onmouseover="this.style.backgroundColor='darkblue'" onmouseout="this.style.backgroundColor=''">
                          <td> #<?php echo $dados['id']; ?> </td>
                          <td> <?php echo $dados['nome_rasao_social']; ?> </td>
                            <td> <?php echo $dados['cpf_cnpj']; ?>  </td>
                            <td> <?php echo $dados['cidade']; ?>  </td>
                            <td> <?php echo $dados['estado']; ?>  </td>

                            <td> <?php echo $dados['estado']; ?>  </td>
                          
                            <td style="color:white;"> opcoes </td>
                          </tr>

                          <?php }while ($dados = mysqli_fetch_array($limite)); 
		  
		  // agora vamos criar os botões "Anterior e próximo"
$anterior = $pc -1;
$proximo = $pc +1;

echo "<center><h3>";
if ($pc>1) {
	
echo " <a href='?pagina=$anterior'><- Anterior</a> ";
}
if ($pc<$tp) {
echo "|";
echo " <a href='?pagina=$proximo'>Próxima -></a>";
}
echo "</h3></center>";
		  ?>
              <?php }
		  ?>

            </tbody>

          </table>
          <!--<span style="fonte-size:10; color:darkorange;">Marcar Selecionados como:</span> 
          <button type="submit" class="btn btn-inverse-success btn-fw" formaction="marcar_pago.php">PAGO</button>
          <button type="submit" class="btn btn-inverse-danger btn-fw" onclick="return confirm('Tem certeza que deseja cancelar todas as contas selecionadas?')" formaction="marcar_cancelado.php">CANCELADO</button>
-->
          <?php
				  if ($pagina == ''){
					  $pagina = '1';
					  }
				  echo "<strong style='font-size:15px;'>$tr Resultados encontrados</strong> <p style='font-size:14px;'>(Pagina: $pagina, Mostrando: " .($total_reg - 1)." registros de: ".$tr." Resultados)</p><br><br>"; ?>
          <?php
				  // agora vamos criar os botões "Anterior e próximo"
$anterior = $pc -1;
$proximo = $pc +1;

echo "<center><h3>";
if ($pc>1) {
	
echo " <a href='?pagina=$anterior'><- Anterior</a> ";
}
if ($pc<$tp) {
echo "|";
echo " <a href='?pagina=$proximo'>Próxima -></a>";
}
echo "</h3></center>";
				  ?>
            
                        </tbody>
                      </table>
</form>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            </div>




          </div>
          <!-- content-wrapper ends -->
          <!-- partial:partials/_footer.html -->
          <footer class="footer">
            <div class="d-sm-flex justify-content-center justify-content-sm-between">
              <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Copyright © Accolita 2023</span>
              <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center"> Desenvolvido por: <a href="https://hdrhospedagem.com.br" target="_blank">HDR Hospedagem</a></span>
            </div>
          </footer>
          <!-- partial -->
        </div>
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="assets/vendors/js/vendor.bundle.base.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="assets/vendors/chart.js/Chart.min.js"></script>
    <script src="assets/vendors/progressbar.js/progressbar.min.js"></script>
    <script src="assets/vendors/jvectormap/jquery-jvectormap.min.js"></script>
    <script src="assets/vendors/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
    <script src="assets/vendors/owl-carousel-2/owl.carousel.min.js"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="assets/js/off-canvas.js"></script>
    <script src="assets/js/hoverable-collapse.js"></script>
    <script src="assets/js/misc.js"></script>
    <script src="assets/js/settings.js"></script>
    <script src="assets/js/todolist.js"></script>
    <!-- endinject -->
    <!-- Custom js for this page -->
    <script src="assets/js/dashboard.js"></script>
    <!-- End custom js for this page -->

  
  </body>
</html>
