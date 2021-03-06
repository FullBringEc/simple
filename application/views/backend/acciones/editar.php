<ul class="breadcrumb">
    <li>
        <a href="<?= site_url('backend/procesos') ?>">Listado de Procesos</a> <span class="divider">/</span>
    </li>
    <li>
        <a href="<?= site_url('backend/acciones/listar/' . $proceso->id) ?>"><?= $proceso->nombre ?></a> <span class="divider">/</span>
    </li>
    <li class="active"><?= $accion->nombre ?></li>
</ul>
<?php $this->load->view('backend/proceso_descripcion') ?>
<ul class="nav nav-tabs">
    <li><a href="<?= site_url('backend/procesos/editar/' . $proceso->id) ?>">Diseñador</a></li>
    <li><a href="<?= site_url('backend/formularios/listar/comun/' . $proceso->id) ?>">Formularios</a></li>
    <li><a href="<?= site_url('backend/formularios/listar/obn/' . $proceso->id) ?>">Formularios para Tablas de Datos</a></li>
    <li><a href="<?= site_url('backend/documentos/listar/' . $proceso->id) ?>">Documentos</a></li>
    <li ><a href="<?= site_url('backend/validaciones/listar/' . $proceso->id) ?>">Validaciones</a></li>
    <li class="active"><a href="<?= site_url('backend/acciones/listar/' . $proceso->id) ?>">Acciones</a></li>
    <li><a href="<?= site_url('backend/trazabilidad/listar/' . $proceso->id) ?>">Trazabilidad</a></li>
    <li><a href="<?= site_url('backend/procesos/editar_codigo_tramite_ws_grep/' . $proceso->id) ?>">Código tramites.gub.uy</a></li>
    <li><a href="<?= site_url('backend/procesos/editar_api/' . $proceso->id) ?>">API</a></li>
</ul>


<form class="ajaxForm" method="POST" action="<?= site_url('backend/acciones/editar_form/' . ($edit ? $accion->id : '')) ?>">
    <div class="titulo-form">
        <h3><?= $edit ? $accion->nombre : 'Acción' ?></h3>
    </div>
    <fieldset>
        <legend>Datos generales</legend>
        <div class="validacion validacion-error"></div>
        <?php if (!$edit): ?>
            <input type="hidden" name="proceso_id" value="<?= $proceso->id ?>" />
            <input type="hidden" name="tipo" value="<?= $tipo ?>" />
        <?php endif; ?>
        <div class="form-horizontal">
            <div class="control-group">
                <label for="nombre" class="control-label">Nombre de la acción</label>
                <div class="controls">
                    <input id="nombre" type="text" name="nombre" value="<?= $edit ? $accion->nombre : '' ?>" />
                </div>
            </div>
            <div class="control-group">
                <label for="tipo" class="control-label">Tipo</label>
                <div class="controls">
                    <input id="tipo" type="text" readonly value="<?= $edit ? $accion->tipo : $tipo ?>" />
                </div>
            </div>
            <?php if ( isset($tipo)): ?>
            <?php if ( $tipo=="variable_obn"): ?>
            <div class="control-group">
                <label for="tipo" class="control-label">OBN</label>
                <div class="controls">
                    <input id="tipo" type="text" readonly value="<?= $accion->extra->obn ? $accion->extra->obn : "" ?>" />
                </div>
            </div>
            <?php endif; ?>
             <?php endif; ?>
        </div>
    </fieldset>
    <fieldset>
        <legend>Otros datos</legend>
        <?php if (isset($operacion) && $tipo="pasarela_pago"): ?>
            <?= $accion->displayForm($operacion,$proceso->id) ?>
         <?php elseif (isset($operacion)): ?>
            <?= $accion->displayForm($operacion) ?>
        <?php else: ?>
            <?= $accion->displayForm() ?>
        <?php endif; ?>
    </fieldset>
    <ul class="form-action-buttons">
        <li class="action-buttons-primary">
            <ul>
                <li>
                    <input class="btn btn-primary btn-lg" type="submit" value="Guardar" />
                </li>
            </ul>
        </li>
        <li class="action-buttons-second">
            <ul>
                <li class="float-left">
                    <a class="btn btn-link btn-lg" href="<?= site_url('backend/acciones/listar/' . $proceso->id) ?>">Cancelar</a>
                </li>
            </ul>
        </li>
    </ul>
</form>
