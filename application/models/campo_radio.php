<?php
require_once('campo.php');
class CampoRadio extends Campo {

    protected function display($modo, $dato) {
        $display = '<div class="control-group">';
        $display.= '<span class="control-label" data-fieldset="'.$this->fieldset.'">' . $this->etiqueta . (in_array('required', $this->validacion) ? '*:' : ':') . '</span>';
        $display.='<div class="controls" data-fieldset="'.$this->fieldset.'">';

        $dato_selected = null;
        if ($dato){
          $dato_selected = $dato->valor;
        }else{
          $dato_selected  = $this->valor_default;
        }

        $opcion = 1;
        foreach ($this->datos as $d) {
          if($this->ayuda_ampliada && $opcion == 1) {

            $valor_id = $name = str_replace(' ', '_', $d->valor);
            $display.='<label class="radio" for="'.$this->id.'_'.$valor_id.'">';
            $display.='<input id="'.$this->id.'_'.$valor_id.'" ' . ($modo == 'visualizacion' ? 'readonly' : '') . ' type="radio" name="' . $this->nombre . '" value="' . $d->valor . '" ' . ($dato_selected && $d->valor == $dato_selected ? 'checked' : '') . ' />';
            $display.= $d->etiqueta;
            $display .= '<button title="'. strip_tags($this->ayuda_ampliada) .'" class="tooltip_help" onclick="return false;"><span class="icn icn-circle-help"></span><span class="hide-read">Ayuda</span></button>';
          }
          else {
            $valor_id = $name = str_replace(' ', '_', $d->valor);
            $display.='<label class="radio" for="'.$this->id.'_'.$valor_id.'">';
            $display.='<input id="'.$this->id.'_'.$valor_id.'" ' . ($modo == 'visualizacion' ? 'readonly' : '') . ' type="radio" name="' . $this->nombre . '" value="' . $d->valor . '" ' . ($dato_selected && $d->valor == $dato_selected ? 'checked' : '') . ' />';
            $display.= $d->etiqueta;
          }

          $opcion++;

          $display .= '</label>';
        }

        if($this->ayuda)
            $display.='<span class="help-block">'.$this->ayuda.'</span>';

        $display.='</div>';
        $display.='</div>';
        return $display;
    }


    public function formValidate($etapa_id = null){
        $CI=& get_instance();

        $validacion=$this->validacion;
        if($etapa_id){
            $regla = new Regla($this->validacion);
            $validacion = $regla->getExpresionParaOutput($etapa_id);
        }
        if ($this->nombre == 'terminos_de_la_clausula' || $this->fieldset == 'ccccc.clausula_de_consentimiento_informado'){
          $validacioStr = implode('|', $validacion);
          $validacioStr = str_replace('required','callback_required_clausula',$validacioStr);
          $CI->form_validation->set_rules($this->nombre, ucfirst($this->etiqueta), $validacioStr);
        }else{
          $CI->form_validation->set_rules($this->nombre, ucfirst($this->etiqueta), implode('|', $validacion));
        }
    }

    public function backendExtraValidate(){
          $CI=&get_instance();
          $CI->form_validation->set_rules('datos','Datos','required');
    }

}
