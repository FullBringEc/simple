<script src="<?= base_url() ?>assets/js/modelador-acciones.js" type="text/javascript"></script>

<ul class="breadcrumb">
    <li><a href="<?=site_url('backend/seguimiento/pagos')?>">Seguimiento de Pagos</a> <span class="divider">/</span></li>
    <li><a href="<?=site_url('backend/seguimiento/ver_pago/'. $registro->id.'')?>" class="active">Registro #<?=$registro->id?></a></li>
</ul>
<h2>Registro de Pago</h2>
<div class="well">
    <dl  class="dl-horizontal">
      <dt>Última actualización</dt>
      <dd><?=$registro->fecha_actualizacion?></dd>
    </dl>
    <dl  class="dl-horizontal">
      <dt>ID de trámite interno</dt>
      <dd><a href="<?=site_url('backend/seguimiento/ver/'.$registro->id_tramite_interno)?>"><?=$registro->id_tramite_interno?></a></dd>
    </dl>
    <dl  class="dl-horizontal">
      <dt>ID de trámite externo</dt>
      <dd><?=$registro->id_tramite?></dd>
    </dl>
    <dl  class="dl-horizontal">
      <dt>ID de solicitud</dt>
      <dd><?=$registro->id_solicitud?></dd>
    </dl>
    <dl  class="dl-horizontal">
      <dt>Estado</dt>
      <dd><span class="estado badge <?= ($registro->estado == 'realizado' ? 'badge-success' : ($registro->estado == 'error' ? 'badge-important' : ($registro->estado == 'pendiente' ? 'badge-warning' :  ($registro->estado == 'rechazado' ? 'badge-important' : 'badge-secondary')))) ?>"><?=$registro->estado?></span></dd>
    </dl>
    <dl  class="dl-horizontal">
      <dt>Pasarela ID</dt>
      <dd><?=$registro->pasarela?></dd>
    </dl>
    <dl  class="dl-horizontal">
      <dt>Nombre de pasarela</dt>
      <dd><?=$pasarela_nombre?></dd>
    </dl>
    <dl  class="dl-horizontal">
      <dt>Usuario</dt>
      <dd><?=$registro->usuario?></dd>
    </dl>
</div>

<div class="modal hide fade" id="modal"></div>
