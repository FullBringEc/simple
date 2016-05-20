<?php
class UsuarioManagerSesion {

    private static $user;

    private function __construct() {

    }

    public static function usuario() {
        if (!isset(self::$user)) {

            $CI = & get_instance();

            if (!$user_id = $CI->session->userdata('usuario_manager_id')) {
                return FALSE;
            }

            if (!$u = Doctrine::getTable('UsuarioManager')->find($user_id)) {
                return FALSE;
            }

            self::$user = $u;
        }

        return self::$user;
    }

    public static function force_login(){
        $CI = & get_instance();

        if(!self::usuario()){
            $CI->session->set_flashdata('redirect',current_url());
            redirect('/manager/autenticacion/login');
        }

    }

    public static function login($usuario, $password) {
        $CI = & get_instance();

        $autorizacion = self::validar_acceso($usuario, $password);

        if ($autorizacion) {
            $u = Doctrine::getTable('UsuarioManager')->findOneByUsuario($usuario);

            $CI->session->set_userdata('usuario_manager_id', $u->id);
            self::$user = $u;

            return TRUE;
        }

        return FALSE;
    }

    public static function login_saml($usuario) {
        $CI = & get_instance();
        $u = self::validar_acceso_saml($usuario);

        if ($u) {
          //Logueamos al usuario
          $CI->session->set_userdata('usuario_manager_id', $u->id);
          self::$user = $u;

          return true;
        }

        return false;
    }

    public static function validar_acceso_saml($usuario) {
        $usuario = Doctrine::getTable('UsuarioBackend')->findOneByUsuario($usuario);

        if (!$usuario) {
          return false;
        }
        else {
          return $usuario;
        }
    }

    public static function validar_acceso($usuario, $password) {
        $u = Doctrine::getTable('UsuarioManager')->findOneByUsuario($usuario);
        if ($u) {
            // this mutates (encrypts) the input password
            $u_input = new UsuarioManager();
            $u_input->setPasswordWithSalt($password,$u->salt);

            // password match (comparing encrypted passwords)
            if ($u->password == $u_input->password) {
                unset($u_input);


                return TRUE;
            }

            unset($u_input);
        }

        // login failed
        return FALSE;
    }

    public static function logout() {
        $CI = & get_instance();
        self::$user = NULL;
        $CI->session->unset_userdata('usuario_manager_id');
    }

    public function __clone() {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }

    public function registrado_saml() {
      if(isset($_COOKIE['simple_bpm_saml_session_ref_k'])) {
        return true;
      }
      else {
        return false;
      }
    }

    function registrar_acceso() {
      if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
          isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
        $protocolo = 'https://';
      }
      else {
        $protocolo = 'http://';
      }

      $uri = $protocolo.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
      $uri_array = explode('/autenticacion', $uri);

      setcookie('simple_bpm_query', base64_encode('manager'), 0, '/', HOST_SISTEMA_DOMINIO);
      setcookie('simple_bpm_location', base64_encode($uri_array[0]), 0, '/', HOST_SISTEMA_DOMINIO);
    }
}
