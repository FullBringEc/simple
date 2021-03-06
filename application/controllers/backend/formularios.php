<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Formularios extends MY_BackendController {

    public function __construct() {
        parent::__construct();

        UsuarioBackendSesion::force_login();

        if (!UsuarioBackendSesion::has_rol('super') && !UsuarioBackendSesion::has_rol('modelamiento')) {
            redirect('backend');
        }

        $this->load->helper('auditoria_helper');
    }

    public function listar($tipo = "comun", $proceso_id) {
        $proceso = Doctrine::getTable('Proceso')->find($proceso_id);

        if ($proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para listar los formularios de este proceso';
            exit;
        }

        $formularios = Doctrine::getTable('Formulario')->findByProcesoIdAndTipo($proceso->id, $tipo);

        $data['proceso'] = $proceso;
        $procesosArchivados = $proceso->findProcesosArchivados((($proceso->root) ? $proceso->root : $proceso->id));
        $data['procesos_arch'] = $procesosArchivados;

        $data['formularios'] = $formularios;
        $data['tipo'] = $tipo;

        $data['title'] = 'Formularios';
        $data['content'] = 'backend/formularios/index';

        $this->load->view('backend/template', $data);
    }

    public function crear($proceso_id, $tipo = "comun") {
        $proceso = Doctrine::getTable('Proceso')->find($proceso_id);

        if ($proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para crear un formulario dentro de este proceso.';
            exit;
        }

        $formulario = new Formulario();
        $formulario->proceso_id = $proceso->id;
        $formulario->nombre = 'Formulario-' . $this->generar_codigo_formulario();
        $formulario->tipo = $tipo;
        $formulario->save();

        auditar('Formulario', "insert", $formulario->id, UsuarioBackendSesion::usuario()->usuario);

        redirect('backend/formularios/editar/' . $formulario->id);
    }

    public function eliminar($formulario_id) {
        $formulario = Doctrine::getTable('Formulario')->find($formulario_id);

        if ($formulario->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para eliminar este formulario.';
            exit;
        }

        $proceso = $formulario->Proceso;
        $tipo = $formulario->tipo;
        auditar('Formulario', "delete", $formulario->id, UsuarioBackendSesion::usuario()->usuario);
        $formulario->delete();

        redirect('backend/formularios/listar/' . $tipo . '/' . $proceso->id);
    }

    public function editar($formulario_id) {
        $formulario = Doctrine::getTable('Formulario')->find($formulario_id);

        if ($formulario->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para editar este formulario.';
            exit;
        }

        $data['formulario'] = $formulario;
        $data['tipo'] = $formulario->tipo;
        $data['proceso'] = $formulario->Proceso;
        $procesosArchivados = $formulario->Proceso->findProcesosArchivados((($formulario->Proceso->root) ? $formulario->Proceso->root : $formulario->Proceso->id));
        $data['procesos_arch'] = $procesosArchivados;
        $data['title'] = $formulario->nombre;
        $data['content'] = 'backend/formularios/editar';

        $this->load->view('backend/template', $data);
    }

    public function ajax_editar($formulario_id) {
        $formulario = Doctrine::getTable('Formulario')->find($formulario_id);

        if ($formulario->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para editar este formulario.';
            exit;
        }

        $data['formulario'] = $formulario;

        $this->load->view('backend/formularios/ajax_editar', $data);
    }

    public function editar_form($formulario_id) {
        $formulario = Doctrine::getTable('Formulario')->find($formulario_id);

        if ($formulario->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para editar este formulario.';
            exit;
        }

        $this->form_validation->set_rules('nombre', 'Nombre', 'required');

        if ($this->input->post('contenedor')) {
            $this->form_validation->set_rules('leyenda', 'Leyenda', 'required');
        }

        $respuesta = new stdClass();
        if ($this->form_validation->run() == TRUE) {
            $formulario->nombre = $this->input->post('nombre');
            $formulario->leyenda = $this->input->post('leyenda');
            $formulario->contenedor = $this->input->post('contenedor');
            $formulario->tipo = $this->input->post('tipo_form');
            $formulario->save();
            auditar('Formulario', "update", $formulario->id, UsuarioBackendSesion::usuario()->usuario);
            $respuesta->validacion = TRUE;
            $respuesta->redirect = site_url('backend/formularios/editar/' . $formulario->id);
        } else {
            $respuesta->validacion = FALSE;
            $respuesta->errores = validation_errors();
        }

        echo json_encode($respuesta);
    }

    public function ajax_editar_campo($campo_id) {
        $campo = Doctrine::getTable('Campo')->find($campo_id);

        if ($campo->Formulario->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para editar este campo.';
            exit;
        }

        $pagos = array();
        foreach ($campo->Formulario->Proceso->Acciones as $accion) {
            if ($accion->tipo == 'pasarela_pago') {
                array_push($pagos, $accion);
            }
        }

        $bloques = Doctrine_Query::create()->from('Bloque')->execute();

        $data['edit'] = TRUE;
        $data['campo'] = $campo;
        $data['formulario'] = $campo->Formulario;
        $data['pagos'] = $pagos;
        $data['bloques'] = $bloques;
        $data['acciones'] = Doctrine::getTable('Accion')->findByProcesoId($campo->Formulario->Proceso->id);

        $this->load->view('backend/formularios/ajax_editar_campo', $data);
    }

    public function editar_campo_form($campo_id = NULL) {

        $campo = NULL;
        $respuesta = new stdClass();
        if ($campo_id) {
            $campo = Doctrine::getTable('Campo')->find($campo_id);

            if ($campo->Formulario->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
                echo 'Usuario no tiene permisos para editar este campo.';
                exit;
            }

            $formulario_id = $campo->Formulario->id;
        } else {
            $formulario = Doctrine::getTable('Formulario')->find($this->input->post('formulario_id'));

            if ($this->input->post('tipo') != 'bloque') {
                $campo = Campo::factory($this->input->post('tipo'));
                $campo->formulario_id = $formulario->id;
                $campo->posicion = 1 + $formulario->getUltimaPosicionCampo();
            } else {
                $this->form_validation->set_rules('valor_default', 'valor_default', 'required');

                $this->form_validation->set_rules('nombre', 'Nombre', 'required');
                $this->form_validation->set_rules('etiqueta', 'Etiqueta', 'required');
                if ($this->form_validation->run() == FALSE) {
                    $respuesta->validacion = FALSE;
                    $respuesta->errores = validation_errors();
                    echo json_encode($respuesta);
                    exit;
                }
            }

            $formulario_id = $formulario->id;
        }

        $this->form_validation->set_rules('nombre', 'Nombre', 'required');
        $this->form_validation->set_rules('etiqueta', 'Etiqueta', 'required');
        if ($this->input->post('tipo') != 'bloque') {
            if ($campo->Formulario->tipo != "obn" && $campo->tipo == "tabla_datos") {
                $this->form_validation->set_rules('variable_obn', 'variable OBN asociado', 'variable_obn|required');
            } else {
                $this->form_validation->set_rules('variable_obn', 'variable OBN asociado', 'variable_obn');
            }
        }

        if ($this->input->post('tipo') == "file") {
            $this->form_validation->set_rules('extra[tamanio_max]', 'Tamaño máximo permitido', 'callback_check_tamanio_max_permitido');
        }
        $this->form_validation->set_rules('validacion', 'Validación', 'callback_clean_validacion');

        if (!$campo_id) {
            $this->form_validation->set_rules('formulario_id', 'Formulario', 'required|callback_check_permiso_formulario');
            $this->form_validation->set_rules('tipo', 'Tipo de Campo', 'required');
        }

        if (isset($campo)) {
            $campo->backendExtraValidate();
        }


        if ($this->form_validation->run() == TRUE) {
            if (!$campo) {
                
            } else if ($this->input->post('tipo') != 'bloque') {
                $campo->nombre = str_replace(" ", "_", $this->input->post('nombre'));

                if ($campo->tipo == 'paragraph') {
                    $tags = '';
                    $html = trim($this->input->post('etiqueta', false));

                    preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', $html, $tags);

                    $tags_permitidas = array('<strong>', '</strong>',
                        '<em>', '</em>',
                        '<br>', '</br>',
                        '<a', '</a>'
                    );
                    $tags_invalidas = '';

                    if (count($tags) > 0) {
                        foreach ($tags[0] as $tag) {

                            $tag_ok = false;
                            $tag_min = trim(strtolower($tag));

                            foreach ($tags_permitidas as $tag_permitida) {
                                if ($tag_min == $tag_permitida || strpos($tag_min, '<a') !== false) {
                                    //es una tag permitida
                                    $tag_ok = true;
                                    break;
                                }
                            }

                            if (!$tag_ok) {
                                $tags_invalidas .= $tag;
                            }
                        }
                    }

                    if ($tags_invalidas !== '') {
                        $respuesta->validacion = FALSE;
                        $respuesta->errores = 'Etiquetas HTML inválidas <pre><code>' . htmlentities($tags_invalidas) . '</code></pre>';
                        echo json_encode($respuesta);
                        return;
                    } else {
                        $campo->etiqueta = $this->input->post('etiqueta', false);
                    }
                } else {
                    $campo->etiqueta = $this->input->post('etiqueta', false);
                }

                $campo->readonly = $this->input->post('readonly');
                $campo->valor_default = $this->input->post('valor_default', false);
                $campo->ayuda = $this->input->post('ayuda');
                $campo->ayuda_ampliada = $this->input->post('ayuda_ampliada');
                $campo->validacion = explode('|', $this->input->post('validacion'));
                $campo->dependiente_tipo = $this->input->post('dependiente_tipo');
                $campo->dependiente_campo = $this->input->post('dependiente_campo');
                $campo->dependiente_valor = $this->input->post('dependiente_valor');
                $campo->dependiente_relacion = $this->input->post('dependiente_relacion');
                $campo->datos = $this->input->post('datos');
                $campo->documento_id = $this->input->post('documento_id');
                $campo->fieldset = $this->input->post('fieldset');

                $campo->requiere_accion = $this->input->post('requiere_accion');
                $campo->requiere_accion_id = $this->input->post('requiere_accion_id');
                $campo->requiere_accion_boton = $this->input->post('requiere_accion_boton');
                $campo->requiere_accion_var_error = $this->input->post('requiere_accion_var_error');
                $campo->variable_obn = $this->input->post('variable_obn');


                if ($campo->tipo == 'pagos') {
                    $campo->pago_online = $this->input->post('check_pago_online');
                }


                if ($campo->tipo == 'agenda' || $campo->tipo == 'agenda_sae') {
                    $campo->requiere_agendar = $this->input->post('check_requiere_agendar');
                }

                if ($campo->tipo == 'documento') {
                    $campo->firma_electronica = $this->input->post('check_firma_electronica');
                }

                //si el tipo de campo es un tabla responsive
                //cuando se quitan las columnas intermedias de la tabla//el array queda con indices vacios
                //por ejemplo extra[0], extra[3] esto hace que el json_decode lo haga mal
                //se recrea el array para que los indices queden correlativos.
                //se procesa los extras de forma distinta a los demas campos
                if ($campo->tipo == 'tabla-responsive') {
                    $newarray = array();
                    $cols = array();
                    $arra = $this->input->post('extra')["columns"];
                    foreach ($arra as $element) {
                        array_push($cols, $element);
                    }
                    $newarray["columns"] = $cols;
                    $newarray["accion_id"] = $this->input->post('extra')["accion_id"];
                    $newarray["accion_error"] = $this->input->post('extra')["accion_error"];
                    $newarray["generar_fila_automatica"] = $this->input->post('extra')["generar_fila_automatica"];

                    $campo->extra = $newarray;
                } else {
                    $campo->extra = $this->input->post('extra');
                }
                if ($campo->tipo == 'domicilio_ica') {
                    $newarray = array();
                    $newarray["tamanio_limite"] = $this->input->post('tamanio_limite');
                    $campo->extra = $newarray;
                }

                if ($campo->tipo == 'fieldset') {
                    $campo->readonly = true;
                }

                $campo->documento_tramite = $this->input->post('documento_tramite');
                $campo->email_tramite = $this->input->post('email_tramite');
                $campo->save();
                auditar('Campo', "update", $campo->id, UsuarioBackendSesion::usuario()->usuario);
            }
        } else {
            $respuesta->validacion = FALSE;
            $respuesta->errores = validation_errors();
        }

        if ($this->input->post('tipo') == 'bloque') {
            $formulario_bloque = Doctrine_Query::create()->from('Formulario f')
                    ->where('f.bloque_id = ?', $this->input->post('valor_default'))
                    ->execute();
            $formulario_bloque = $formulario_bloque[0];
            $campos_bloque = Doctrine_Query::create()->from('Campo c')
                    ->where('c.formulario_id = ?', $formulario_bloque->id)
                    ->orderBy('c.posicion')
                    ->execute();

            foreach ($campos_bloque as $campo_bloque) {
                $campo_nuevo = Campo::factory($campo_bloque->tipo);
                $campo_nuevo->formulario_id = $formulario_id;

                if (($campo_bloque->tipo == 'fieldset') || ($campo_bloque->tipo == 'encuesta')) {
                    $campo_nuevo->nombre = 'BLOQUE_' . $this->input->post('nombre') . '.' . $campo_bloque->nombre;
                } else {
                    $campo_nuevo->nombre = $campo_bloque->nombre;
                }

                $campo_nuevo->etiqueta = $campo_bloque->etiqueta;
                $campo_nuevo->readonly = $campo_bloque->readonly;
                $campo_nuevo->valor_default = $campo_bloque->valor_default;
                $campo_nuevo->ayuda = $campo_bloque->ayuda;
                $campo_nuevo->ayuda_ampliada = $campo_bloque->ayuda_ampliada;
                $campo_nuevo->validacion = $campo_bloque->validacion;
                $campo_nuevo->dependiente_tipo = $campo_bloque->dependiente_tipo;
                $campo_nuevo->dependiente_campo = $campo_bloque->dependiente_campo;
                $campo_nuevo->dependiente_valor = $campo_bloque->dependiente_valor;
                $campo_nuevo->dependiente_relacion = $campo_bloque->dependiente_relacion;
                $campo_nuevo->datos = $campo_bloque->datos;
                $campo_nuevo->documento_id = $campo_bloque->documento_id;
                $campo_nuevo->fieldset = $this->input->post('nombre') . '.' . $campo_bloque->fieldset;
                $campo_nuevo->posicion = $campo_bloque->posicion;
                $campo_nuevo->extra = $campo_bloque->extra;
                $campo_nuevo->save();
                auditar('Campo', "insert", $campo_nuevo->id, UsuarioBackendSesion::usuario()->usuario);
            }
        }

        if ($this->form_validation->run() == TRUE) {
            $respuesta->validacion = TRUE;
            $respuesta->redirect = site_url('backend/formularios/editar/' . $formulario_id);
        }

        echo json_encode($respuesta);
    }

    public function ajax_agregar_campo($formulario_id, $tipo) {
        $formulario = Doctrine::getTable('Formulario')->find($formulario_id);

        if ($formulario->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para agregar campos a este formulario.';
            exit;
        }

        $campo = Campo::factory($tipo);
        $campo->formulario_id = $formulario_id;

        $pagos = array();
        foreach ($formulario->Proceso->Acciones as $accion) {
            if ($accion->tipo == 'pasarela_pago') {
                array_push($pagos, $accion);
            }
        }

        $bloques = Doctrine_Query::create()->from('Bloque')->execute();
        //auditar('Campo', "insert", $campo->id, UsuarioBackendSesion::usuario()->usuario);
        $data['edit'] = false;
        $data['formulario'] = $formulario;
        $data['campo'] = $campo;
        $data['pagos'] = $pagos;
        $data['bloques'] = $bloques;
        $data['acciones'] = Doctrine::getTable('Accion')->findByProcesoId($campo->Formulario->Proceso->id);

        $this->load->view('backend/formularios/ajax_editar_campo', $data);
    }

    public function eliminar_campo($campo_id) {
        $campo = Doctrine::getTable('Campo')->find($campo_id);

        if ($campo->Formulario->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para eliminar este campo.';
            exit;
        }

        $formulario = $campo->Formulario;
        auditar('Campo', "delete", $campo->id, UsuarioBackendSesion::usuario()->usuario);
        $campo->delete();

        redirect('backend/formularios/editar/' . $formulario->id);
    }

    public function editar_posicion_campos($formulario_id) {
        $formulario = Doctrine::getTable('Formulario')->find($formulario_id);

        if ($formulario->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para editar este formulario.';
            exit;
        }

        $json = $this->input->post('posiciones');
        $formulario->updatePosicionesCamposFromJSON($json);
    }

    public function check_permiso_formulario($formulario_id) {
        $formulario = Doctrine::getTable('Formulario')->find($formulario_id);

        if ($formulario->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            $this->form_validation->set_message('check_permiso_formulario', 'Usuario no tiene permisos para agregar campos a este formulario.');
            return FALSE;
        }

        return TRUE;
    }

    public function check_tamanio_max_permitido($tamanio) {
        $tamanio_max_parametro = Doctrine::getTable('Parametro')->findOneByCuentaIdAndClave(UsuarioBackendSesion::usuario()->cuenta_id, "tamanio_maximo_archivo");
        if ($tamanio_max_parametro) {
            if ($tamanio > $tamanio_max_parametro->valor) {
                $this->form_validation->set_message('check_tamanio_max_permitido', 'El tamaño máximo excede el tamaño permitido en la configuración.');
                return FALSE;
            }
        }
        return TRUE;
    }

    function clean_validacion($validacion) {
        return preg_replace('/\|\s*$/', '', $validacion);
    }

    public function generar_codigo_formulario($length = 6) {
        $arr = array('A', 'B', 'C', 'D', 'E', 'F',
            'G', 'H', 'I', 'J', 'K', 'L',
            'M', 'N', 'O', 'P', 'R', 'S',
            'T', 'U', 'V', 'X', 'Y', 'Z',
            '1', '2', '3', '4', '5', '6',
            '7', '8', '9', '0');
        $token = "";
        for ($i = 0; $i < $length; $i++) {
            $index = rand(0, count($arr) - 1);
            $token .= $arr[$index];
        }
        return $token;
    }

}
