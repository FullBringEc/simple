<!DOCTYPE html>
<html lang="es">
  <head>
      <meta charset="utf-8">
      <meta name="viewport" content="widpth=device-width, initial-scale=1.0">
      <title>Tramitador</title>

      <!-- Le styles -->
      <link href="<?= base_url() ?>assets/css/bootstrap.css" rel="stylesheet">
      <link href="<?= base_url() ?>assets/css/responsive.css" rel="stylesheet">
      <link href="<?= base_url() ?>assets/js/file-uploader/fileuploader.css" rel="stylesheet">
      <link href="<?= base_url() ?>assets/css/common.css" rel="stylesheet">

      <script src="<?= base_url() ?>assets/js/jquery/jquery-1.8.3.min.js" type="text/javascript"></script>
      <script src="<?= base_url() ?>assets/js/bootstrap.min.js" type="text/javascript"></script> <?php //JS base       ?>
      <script src="<?= base_url() ?>assets/js/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
      <script src="<?= base_url() ?>assets/js/bootstrap-datepicker/js/locales/bootstrap-datepicker.es.js"></script>
      <script src="<?= base_url() ?>assets/js/jquery.chosen/chosen.jquery.min.js"></script> <?php //Soporte para selects con multiple choices     ?>
      <script src="<?= base_url() ?>assets/js/file-uploader/fileuploader.js"></script> <?php //Soporte para subir archivos con ajax     ?>

      <link href="<?= base_url() ?>assets/css/font-awesome-3.2.1.min.css" rel="stylesheet" />
      <link href="<?= base_url() ?>assets/css/estilos_extendidos.css" rel="stylesheet" />
      <link rel="shortcut icon" href="<?= base_url() ?>assets/img/favicon.png" />
      <script type="text/javascript">
          var site_url = "<?= site_url() ?>";
          var base_url = "<?= base_url() ?>";
      </script>
  </head>
  <body>
    <ul id="skip">
      <li><a href="#main">Ir al contenido</a></li>
      <li><a href="#sideMenu">Ir al menú de navegación</a></li>
    </ul>
    <div class="contenedorGeneral">
      <header class="header-publico">
        <div class="container">
          <div class="row-fluid">
            <div class="span5">
              <div id="logo">
                <h1>Gestión</h1>
              </div>
            </div>
            <div class="span7">
              <div class="logosSecundarios">
                <ul class="listaHorizontal">
                  <li>
                    <a href="https://www.presidencia.gub.uy/" title="Ir al sitio de Presidencia">
                      <img src="<?= base_url() ?>assets/img/logoPresidencia.png" alt="Presidencia">
                    </a>
                  </li>
                </ul>
              </div>
              <?php if (UsuarioManagerSesion::registrado_saml()): ?>
                <div id="userMenu" class="pull-right userMenu">
                  <span class="btn-small">Bienvenido,</span>
                  <div class="btn-group">
                    <a class="btn btn-small btn-link dropdown-toggle" data-toggle="dropdown" href="#"><span><?= UsuarioManagerSesion::usuario()->usuario ?></span> <span class="caret"></span></a>
                    <ul class="dropdown-menu pull-right">
                      <?php if (strtoupper(TIPO_DE_AUTENTICACION) == 'CDA'): ?>
                        <li><a href="<?= site_url('autenticacion/logout_saml') ?>"><span class="icon-off"></span> Cerrar sesión</a></li>
                      <?php elseif (strtoupper(TIPO_DE_AUTENTICACION) == 'LDAP'): ?>
                        <li><a href="<?= site_url('manager/autenticacion/logout_ldap') ?>"><span class="icon-off"></span> Cerrar sesión</a></li>
                      <?php else: ?>
                        <li><a href="<?= site_url('manager/autenticacion/logout') ?>"><span class="icon-off"></span> Cerrar sesión</a></li>
                      <?php endif; ?>
                    </ul>
                  </div>
                </div>
              <?php else: ?>
                <div id="userMenu" class="pull-right userMenu">
                  <span class="btn-small">Bienvenido,</span>
                  <div class="btn-group">
                    <a class="btn btn-small btn-link dropdown-toggle" data-toggle="dropdown" href="#"><span><?= UsuarioManagerSesion::usuario()->usuario ?></span> <span class="caret"></span></a>
                    <ul class="dropdown-menu pull-right">
                        <li><a href="<?= site_url('manager/autenticacion/logout') ?>"><span class="icon-off"></span> Cerrar sesión</a></li>
                    </ul>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </header>

      <div id="main" tabindex="-1">
        <div class="container">
          <div class="row-fluid">
            <div class="span3">
              <ul id="sideMenu" class="nav nav-list" tabindex="-1">
                <li><a href="<?= site_url('manager') ?>">Portada</a></li>
                <li class="nav-header">Administración</li>
                <li><a href="<?= site_url('manager/cuentas') ?>">Cuentas</a></li>
                <li><a href="<?= site_url('manager/usuarios') ?>">Usuarios Backend</a></li>
                <li><a href="<?= site_url('manager/plugins') ?>">Plugins</a></li>
                <li class="nav-header">Estadisticas</li>
                <li><a href="<?= site_url('manager/estadisticas/cuentas') ?>">Trámites en curso</a></li>
              </ul>
            </div>
            <div class="span9 contenido-publico">
              <?=$this->session->flashdata('message')?'<div class="alert alert-success">'.$this->session->flashdata('message').'</div>':''?>
              <?php $this->load->view($content) ?>
            </div>
          </div>
        </div>
      </div>
    </div>
    <footer class="row-fluid">
      <div class="area2">
        <div class="container-fluid">
      	<span class="version"><?=SIMPLE_VERSION?></span>
          <div class="pull-right">
              <img src="<?= base_url() ?>assets/img/logoTramites.png" alt="tramites.gub.uy">
          </div>
        </div>
      </div>
      <!-- Google Analytics -->
      <?php get_instance()->load->helper('analytics_helper'); echo display_codigo_analytics(); ?>
    </footer>
    <script src="<?= base_url() ?>assets/js/common.js"></script>
  </body>
</html>
