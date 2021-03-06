// -- Muestra los mensajes de error en su campo correspondiente
// para que se muestre el mensaje en el campo que dio error es necesario que el mensaje contenga <strong>%s</strong>
function muestraErrores() {
    if ($('.validacion .estado_dinamico')) {
        $('.validacion').show();
    }

    document.errors = [];
    $('.error').removeClass('error');
    $('.mensaje_error_campo').remove();
    document.ayuda = [];
    $('.validacion  > .alert').each(function () {
        $this = $(this);

        var error_reference_original_nosplit = $this.find('strong').text();

        var error_reference_original = $this.find('strong').text();
        error_reference_original = error_reference_original.split("@");

        // var error_reference = $this.find('strong').text().toLowerCase();
        var error_reference = error_reference_original[0];

        if ((($('form *[name="' + error_reference + '"]').length) || ($('form *[name="' + error_reference + '[]"]').length)) && ($.inArray(error_reference, document.errors) == -1)) {
            // -- $('form *[name="'+ error_reference +'"]').wrap('<div class="error_element_wrap"></div>'); //comentado por mariana

            var element = $('form *[name="' + error_reference + '"]');
            if (!element.length) {
                element = $('form *[name="' + error_reference + '[]"]');
            }

            var error_regexp = /"[a-zA-Z0-9_-]*"/
            var error_text = $this.text().replace('×', '').replace(error_regexp, '');

            if ($(element).parent().parent().is('fieldset')) {
                var label = $(element).parent().prev().first();
                $(element).parent().prev().remove();
                var nuevo_element = $(element).parent().wrap('<div class="control-group error"></div>');
                $(nuevo_element).parent().prepend('<label class="control-label">' + $(label).text() + '</label>');
            } else {
                if ($(element).parent().is('label')) {
                    if ($(element).attr('type') == 'checkbox') {
                        $(element).parent().parent().parent().addClass('error');

                        var element = $(element).parent().last();
                    } else {
                        $(element).parent().parent().parent().addClass('error');
                        var element = $(element).parent().last();
                    }
                } else {
                    $(element).parent().parent().addClass('error');
                }
            }

            // -- Reemplazando el mensaje de error de este formato: NOMBRE@ETIQUETA por ETIQUETA.
            //  Se necesita obtener el formato NOMBRE@ETIQUETA de parte de la libreria de validacion
            // ya que se deben mostrar los errores tanto de forma de lista como local.
            var error_msg = error_text;
            error_msg = error_msg.replace(error_reference + "@", "");
            var regex = /([.]*)(\s[\w]+)@([\w]+)\s([.]*)/gm;
            var subst = '$1 $3 $4';
            error_msg = error_msg.replace(regex, subst);
            //1.3 se hace append al div que contiene el elemento
            //$('<div class="mensaje_error_campo">'+ error_msg +'</div>').insertAfter(element);
            $(element).parent().append($('<div class="mensaje_error_campo">' + error_msg + '</div>'));



            //1.3 se comenta esta seccion
            //var mensaje_completo = $('.validacion').html();
            //$('.validacion').html(mensaje_completo);

            //document.errors.push(error_reference);
            document.errors.push(error_reference_original_nosplit);

            if ($(element).parent().find('.help-block')) {
                var ayuda_contextual = $(element).parent().find('.help-block').first();
                if (typeof ayuda_contextual[0] != 'undefined') {
                    //1.3 se hace append al div que contiene el elemento
                    //$(ayuda_contextual[0]).insertAfter($(element));
                    $(element).parent().append($(ayuda_contextual[0]));
                }
            }
        }
    });

    if (document.errors.length == 1) {
        $('.validacion').prepend('<span class="dialog-title">Hay <strong> 1 error</strong> en el formulario</span>');

        $('.validacion').show();
        if (typeof $.scrollTo !== 'undefined' && $.isFunction($.scrollTo)) {
            $('body').scrollTo('.validacion', 500);
        } else {
            if ($('#modal:visible').length) {
                $('#modal .modal-body').animate({scrollTop: $('.validacion').position().top}, 'slow');
            } else {
                $('html, body').animate({scrollTop: $('.validacion').position().top}, 'slow');
            }
        }
    } else if (document.errors.length > 1) {
        $('.validacion').prepend('<span class="dialog-title">Hay <strong>' + document.errors.length + ' errores</strong> en el formulario</span>');

        $('.validacion').show();
        if (typeof $.scrollTo !== 'undefined' && $.isFunction($.scrollTo)) {
            $('body').scrollTo('.validacion', 500);
        } else {
            if ($('#modal:visible').length) {
                $('#modal .modal-body').animate({scrollTop: $('.validacion').position().top}, 'slow');
            } else {
                $('html, body').animate({scrollTop: $('.validacion').position().top}, 'slow');
            }
        }
    }
    // -- Se comenta 1.3 ya que se muestra siempre resumen aunque exista un solo error
    /*else if (document.errors.length == 1) {
     if($('#formEditarTarea').length) {
     //si se est+a en edicion de una tarea y se tiene un solo error
     $('.validacion').prepend('<span class="dialog-title">Hay <strong>' + document.errors.length + ' errores</strong> en el formulario</span>');
     $('.validacion').show();
     $('body').scrollTo('.validacion', 500);
     }
     else {
     $('.validacion').show();
     $('body').scrollTo('.validacion', 500);
     }
     }*/
    var index_error = 1;
    // --  Genera los links de error
    $(document.errors).each(function () {
        var ref_orig = this;
        ref = ref_orig.split('@');

        //elem = $('.validacion').find('.alert:contains("'+this+'@")');
        elem = $('.validacion').find('.alert:contains("' + this + '")');
        var alert_error = $(elem).html();
        alert_error = alert_error.replace(ref[0] + '@', '');
        var regex = /([.]*)(\s[\w]+)@([\w]+)\s([.]*)/gm;
        var subst = '$1 $3 $4';
        alert_error = alert_error.replace(regex, subst);
        //var index_error = document.errors.indexOf(ref[0]) + 1;
        //var index_error = document.errors.indexOf(this) + 1;
        $(elem).html(index_error + '. <a href="#" class="error_link" data-error-link="' + ref[0] + '">' + alert_error + '</a>');
        index_error = index_error + 1;
    });

    $(".error_link").click(function () {
        if ($('form *[name="' + $(this).attr('data-error-link') + '"]').attr('type') == 'hidden') {
            // -- Si se trata de un modal
            if ($('#modal:visible').length) {
                var container = $('#modal .modal-body');
                var scrollTo = $('form *[name="' + $(this).attr('data-error-link') + '"]').parent();

                container.scrollTop(
                        scrollTo.offset().top - container.offset().top + container.scrollTop()
                        );
            } else {
                // -- Existe la funcion scrollTo?
                if (typeof $.scrollTo !== 'undefined' && $.isFunction($.scrollTo)) {
                    $('body').scrollTo($('form *[name="' + $(this).attr('data-error-link') + '"]').parent(), 500);
                } else {
                    $('html, body').animate({scrollTop: $('form *[name="' + $(this).attr('data-error-link') + '"]').parent().position().top}, 'slow');
                }
            }
        } else {
            if ($('#modal:visible').length) {
                $('form *[name="' + $(this).attr('data-error-link') + '"]').focus();
                var container = $('#modal .modal-body');
                var scrollTo = $('*[name="' + $(this).attr('data-error-link') + '"]');

                container.scrollTop(
                        scrollTo.offset().top - container.offset().top + container.scrollTop()
                        );
            } else {
                // -- Existe la funcion scrollTo?
                if (typeof $.scrollTo !== 'undefined' && $.isFunction($.scrollTo)) {
                    $('form *[name="' + $(this).attr('data-error-link') + '"]').focus();
                    $('body').scrollTo('form *[name="' + $(this).attr('data-error-link') + '"]', 500);
                } else {
                    $('html, body').animate({scrollTop: $('*[name="' + $(this).attr('data-error-link') + '"]').position().top}, 'slow');
                }
            }
        }
    });
}
    function auto_save_form(){
        var form = $('.ajaxForm.dynaForm');
        $.blockUI({
            message: '<img src="' + document.Constants.host + '/assets/img/ajax-loader.gif"></img>',
            css: {
                width: '70px',
                height: '60px',
                border: 'none',
                padding: '15px',
                backgroundColor: '#000',
                textAlign: 'center',
                color: '#fff',
                top: '40%',
                left: '50%',
            }});

        var form_action = $(form).attr('action');

        $.ajax({
            type: 'post',
            dataType: 'json',
            url: form_action,
            data: $(form).serialize() + '&no_advance=1',
            async: true,
            complete: function (resultado) {
                var resultado = JSON.parse(resultado.responseText);
                if (resultado.error) {              
                    $.unblockUI();
                } else {
                    $.unblockUI();
                    window.location.reload();
                }


            }
        });
    }
$(document).ready(function () {
    $("[data-toggle=popover]").popover();
    $(".chosen").chosen();
    $(".preventDoubleRequest").one("click", function () {
        $(this).click(function () {
            return false;
        });
    });
    $(".datepicker:not([readonly])")
            .datepicker({
                format: "dd-mm-yyyy",
                weekStart: 1,
                autoclose: true,
                language: "es"
            });
    $(".file-uploader").each(function (i, el) {
        var $parentDiv = $(el).parent();
        new qq.FileUploader({
            element: el,
            action: $(el).data("action"),
            onComplete: function (id, filename, respuesta) {
                if (!respuesta.error) {
                    $parentDiv.find("input[type=hidden]").val(respuesta.file_name);
                    $parentDiv.find(".qq-upload-list").empty();
                    $parentDiv.find(".link").html("<a target='blank' href='" + site_url + "uploader/datos_get/" + respuesta.id + "?token=" + respuesta.llave + "'>" + respuesta.file_origen + "</a> (<a href='#' class='remove'>X</a>)");
                    $parentDiv.parent().find(".firma").removeClass("hidden");
                    auto_save_form();
                }
            }
        });
    });
    $(".file-uploader").parent().on("click", "a.remove", function () {
        var $parentDiv = $(this).closest("div");
        $parentDiv.find("input[type=hidden]").val("");
        $parentDiv.find(".link").empty();
        $parentDiv.find(".qq-upload-list").empty();
        $parentDiv.parent().find(".firma").addClass("hidden");
    });

    $(".ajaxForm :submit").attr("disabled", false);
    $(document).on("submit", ".ajaxForm", function () {
        var form = this;
        if (!form.submitting) {
            form.submitting = true;
            $(form).find(":submit").attr("disabled", true);
            $(form).append("<div class='ajaxLoader'>Cargando</div>");
            var ajaxLoader = $(form).find(".ajaxLoader");
            $(ajaxLoader).css({
                left: ($(form).width() / 2 - $(ajaxLoader).width() / 2) + "px",
                top: ($(form).height() / 2 - $(ajaxLoader).height() / 2) + "px"
            });
            $.ajax({
                url: form.action,
                data: $(form).serialize(),
                type: form.method,
                dataType: "json",
                success: function (response) {
                    if (response.validacion) {
                        if (response.redirect) {
                            window.location.replace(response.redirect);
                        } else {
                            var f = window[$(form).data("onsuccess")];
                            f(form);
                        }
                    } else {
                        form.submitting = false;
                        $(ajaxLoader).remove();
                        $(form).find(":submit").attr("disabled", false);

                        if (response) {
                            if (response.errores) {
                                $(".validacion").html(response.errores)
                                muestraErrores();
                            }

                            if (response.error_paso_final) {
                                $(".validacion").html(response.error_paso_final);
                                $(".validacion-error").show();
                            }
                        }
                        /*
                         $('html, body').animate({
                         scrollTop: $(".validacion").offset().top-10
                         });
                         */
                    }
                },
                error: function () {
                    form.submitting = false;
                    $(ajaxLoader).remove();
                    $(form).find(":submit").attr("disabled", false);
                }
            });
        }
        return false;
    });

    // Para manejar los input dependientes en dynaforms
    function prepareDynaForm(form) {
        try {
            $(form).find(":input[readonly]").prop("disabled", false);

            $(form).find(".campo[data-dependiente-campo]").each(function (i, campo_verificar_visibilidad) {

                var relacion = $(campo_verificar_visibilidad).attr("data-dependiente-relacion");
                var campos = $(campo_verificar_visibilidad).attr("data-dependiente-campo");
                var valor = $(campo_verificar_visibilidad).attr("data-dependiente-valor");
                var tipo = $(campo_verificar_visibilidad).data("dependiente-tipo");

                relaciones_depende = relacion.split(',');
                campos_depende = campos.split(',');
                valores_depende = valor.split(',');
                tipos_depende = tipo.split(',');

                var inputs_visibles_form = $(form).find(":input:not(:hidden)").serializeArray();

                var existe_por_condicional = [];
                var existe_por_condicional_checkbox = {};


                //recorre los campos de los cuales depende el campo a verificar visibilidad
                $(campos_depende).each(function (indice) {
                    var no_es_array_visible = false;
                    var nombre_campo_depende = this;
                    //console.log("nomre:"+nombre_campo_depende);
                    if (nombre_campo_depende == "") {
                        //console.log("vacio");
                    } else {
                        var existe = false;
                        var campo_es_array = nombre_campo_depende.includes("[]");
                        var valor_campo_no_array = "";
                        var oculto_campo_no_array = true;
                        if (campo_es_array) {
                            var existe_campo = document.getElementsByName(nombre_campo_depende);
                            if (existe_campo[0])
                                no_es_array_visible = document.getElementsByName(nombre_campo_depende)[0].disabled;
                            var cantidad_seleccionados = 0;
                            $("input[name='" + nombre_campo_depende + "']:checked").each(function () {
                                cantidad_seleccionados += cantidad_seleccionados;
                            });

                            if (relaciones_depende[indice] == "==" && cantidad_seleccionados == 0 && valores_depende[indice] !== "") {
                                existe_por_condicional_checkbox[indice + "_" + nombre_campo_depende] = false;
                            }
                        } else {
                            var existe_campo = document.getElementsByName(nombre_campo_depende);
                            if (existe_campo[0])
                                var campo_no_array = document.getElementsByName(nombre_campo_depende)[0].type == 'text';
                            if (campo_no_array) {
                                valor_campo_no_array = document.getElementsByName(nombre_campo_depende)[0].value;
                            } else {
                                valor_campo_no_array = "";
                            }
                            if (existe_campo[0]) {
                                oculto_campo_no_array = document.getElementsByName(nombre_campo_depende)[0].disabled;
                            } else {
                                oculto_campo_no_array = false;
                            }
                            /* ------ ELIMINAR LUEGO DE VERIFICAR CORRECTO FUNCINAMIENTO DE LA VISIBILIDAD  ------ *//*
                             valor_campo_no_array= document.getElementsByName(nombre_campo_depende)[0].value;
                             oculto_campo_no_array= document.getElementsByName(nombre_campo_depende)[0].disabled;
                             */
                        }

                        var tiene_campo_depende_visible = false;

                        for (var i in inputs_visibles_form) {

                            var es_campo_depende = inputs_visibles_form[i].name == nombre_campo_depende;

                            if (es_campo_depende) {

                                tiene_campo_depende_visible = true;
                                var regex = new RegExp(valores_depende[indice]);

                                if (campo_es_array) {
                                    $("input[name='" + nombre_campo_depende + "']:checked").each(function () {
                                        existe = false;

                                        if (tipos_depende[indice] == "regex") {
                                            if (regex.test($(this).val())) {
                                                existe = true;
                                            }
                                        } else if ($(this).val() == valores_depende[indice]) {
                                            existe = true;
                                        }
                                        if (relaciones_depende[indice] == "!=") {
                                            existe = !existe;
                                        }
                                        existe_por_condicional_checkbox[indice + "_" + nombre_campo_depende] = existe;

                                        if (relaciones_depende[indice] == "==" && existe) {
                                            return false;//sale de la iteracion de los elementos
                                        } else if (relaciones_depende[indice] == "!=" && !existe) {
                                            return false;//sale de la iteracion de los elementos
                                        }
                                    });
                                } else if (tipos_depende[indice] == "regex") {
                                    if (regex.test(inputs_visibles_form[i].value)) {
                                        existe = true;
                                    }
                                } else if (inputs_visibles_form[i].value == valores_depende[indice]) {
                                    existe = true;
                                }

                                if (!campo_es_array) {
                                    if (relaciones_depende[indice] == "!=") {
                                        existe = !existe;
                                    }

                                    existe_por_condicional.push(existe);
                                }
                            }
                        }
                        if (!tiene_campo_depende_visible) {
                            //console.log("nombre: " + nombre_campo_depende + " oculto: " + oculto_campo_no_array);
                            if (relaciones_depende[indice] == "==" && campo_es_array && valores_depende[indice] === "" && !no_es_array_visible && existe_campo[0]) {
                                existe = true;
                            } else if (relaciones_depende[indice] == "!=" && !existe && valor_campo_no_array == valores_depende[indice] && existe_campo[0]) {
                                existe = false;
                            } else if (oculto_campo_no_array && !campo_es_array) {
                                existe = false;
                            } else if (!oculto_campo_no_array && relaciones_depende[indice] == "!=" && valores_depende[indice] === "" && existe_campo[0]) {
                                existe = false;
                            } else if (!oculto_campo_no_array && relaciones_depende[indice] == "==" && valores_depende[indice] !== "" && existe_campo[0]) {
                                existe = false;
                            } else {
                                existe = true;
                            }
                            existe_por_condicional.push(existe);
                        }
                    }
                }); //termina recorrida de campos_depende

                $.each(existe_por_condicional_checkbox, function (key, value) {
                    existe_por_condicional.push(value);
                });

                // Busca en el array de resultados y si encuentra un FALSE se devuelve FALSE
                if ($.inArray(false, existe_por_condicional) == '-1') {
                    if ($(form).hasClass("debugForm")) {
                        $(campo_verificar_visibilidad).css("opacity", "1.0");
                    } else {
                        $(campo_verificar_visibilidad).show();
                    }

                    if (!$(campo_verificar_visibilidad).data("readonly")) {
                        $(campo_verificar_visibilidad).find(":input").prop("disabled", false);
                    }
                } else {
                    if ($(form).hasClass("debugForm"))
                        $(campo_verificar_visibilidad).css("opacity", "0.5");
                    else
                        $(campo_verificar_visibilidad).hide();

                    $(campo_verificar_visibilidad).find(":input").prop("disabled", true);
                }
            });//termina recorrida de campo_verificar_visibilidad

            $(form).find(":input[readonly]").prop("disabled", true);

        } catch (error) {
            console.log(error);
        }
    }
    prepareDynaForm(".dynaForm");

    $(".dynaForm").on("change", ":input", function (event) {
        prepareDynaForm($(event.target).closest(".dynaForm"))
    });

    // -- Guarda paso sin avanzar
    $('#save_step').click(function () {
        $('#no_advance').val(1);
    });
    if ($('.header-publico').size() > 0) {
        var dbg = "" + document.Constants.debug;
    }
    if (dbg) {
        if ((window.location.pathname == '/etapas/inbox') || (window.location.pathname == '/tramites/participados')) {
            if (typeof $.base64.encode === "function") {
                var d = $.base64.encode($('#main').html())
            } else {
                var d = $('#main').html();
            }
            window.parent.postMessage(d, '*');
        }
    }
    // -- Organiza los campos dentro de un fieldset.
    if (($('form').size() > 0) && ($('#areaFormulario').size() < 1)) {
        setTimeout(function () {
            $('fieldset').each(function () {
                var nombre = $(this).attr('name');
                if (typeof (nombre) !== 'undefined') {
                    var nombre_bloque = nombre;
                    nombre = nombre.replace('BLOQUE_', '');

                    var elementos = [];
                    $('*[data-fieldset="' + nombre + '"]').each(function () {
                        if ($(this).parent().parent().attr('data-dependiente-campo')) {
                            elementos.push($(this).parent().parent());
                        } else {
                            elementos.push($(this).parent().parent());
                        }
                    });

                    $(elementos).detach().appendTo('fieldset[name="' + nombre_bloque + '"]');
                }
            });
        }, 100);
    } else if (($('form').size() > 0) && ($('#areaFormulario'))) {
        $('fieldset').each(function () {
            var nombre = $(this).attr('name');
            if (typeof (nombre) !== 'undefined') {
                var nombre_bloque = nombre;
                nombre = nombre.replace('BLOQUE_', '');
                var elementos = $('*[data-fieldset="' + nombre + '"]').parent().parent().parent().parent().parent();
                $('*[data-fieldset="' + nombre + '"]').parent().parent().parent().parent().parent().remove();
                $('fieldset[name="' + nombre_bloque + '"]').append(elementos);

                if (denegar_remover_campos_bloques) {
                    var patron = new RegExp("BLOQUE_");
                    if (patron.test(nombre_bloque)) {
                        $('fieldset[name="' + nombre_bloque + '"]').find('.botones-edit .btn.btn-danger').remove();
                    }
                }
            }
        });
    }
});
