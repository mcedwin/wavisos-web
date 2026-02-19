var baseurl;

function replaceAll(str, find, replace) {
    return str.replace(new RegExp(find, 'g'), replace);
}

$(function () {
    $.gs_loader = $('<div>').hide();
    $.gs_loader.append($('<div>', {
        'class': 'ui-widget-overlay',
    })).append = ($('<div>').html('<img src="' + base_url + '/sys/assets/img/cubo-loader.gif"/>').css({
        'position': 'fixed',
        'font': 'bold 12px Verdana, Arial, Helvetica, sans-serif',
        'left': '50%',
        'top': '50%',
        'z-index': '10001',
        'margin-left': '-32px',
        'margin-top': '-32px'
    })).appendTo($.gs_loader);
    $.gs_loader.appendTo($('body'));
});

var getScript = jQuery.getScript;
jQuery.getScriptA = function (resources, callback) {
    var scripts = [];

    if (typeof (resources) === 'string') { scripts.push(resources) } else { scripts = resources; }

    var length = scripts.length,
        handler = function () { counter++; },
        deferreds = [],
        counter = 0,
        idx = 0;

    $.ajaxSetup({ async: false });
    for (; idx < length; idx++) {
        deferreds.push(
            getScript(scripts[idx], handler)
        );
    }

    jQuery.when.apply(null, deferreds).then(function () {
        callback();
    });
};

(function ($) {

    $.fn.load_img = function () {
        $(this).find('.changephoto').click(function () {
            $(".inputfile").click();
            return false;
        })

        $(this).find(".inputfile").change(function () {
            mostrarImagen(this);
        });
    }

    function mostrarImagen(input) {
        if (input.files && input.files[0]) {
          var file = input.files[0];
          if($(input).hasClass('mp3')){
            if (file.type.match('audio.*')) {
              $('#viewfoto').text(file.name);
            }else{
              alert("No es audio");
              $(".inputfile").val("");
            }
            return;
          }

            var reader = new FileReader();
            reader.onload = function (e) {
                $('#viewfoto').attr('src', e.target.result);
            }
     

            if (file.type.match('image.*')) {
                reader.readAsDataURL(input.files[0]);
            } else {
                alert("No es imagen");
                $(".inputfile").val("");
            }
        }
    }



    $.fn.load_img1 = function () {
      $(this).find('.changephoto').click(function () {
          $(this).next(".inputfile").click();
          return false;
      })

      $(this).find(".inputfile").change(function () {
          mostrarImagen1(this);
      });
  }

  function mostrarImagen1(input) {
      $padre = $(input).closest('.row');
      if (input.files && input.files[0]) {
        var file = input.files[0];
        if($(input).hasClass('mp3')){
          if (file.type.match('audio.*')) {
            $padre.find('#viewfoto').text(file.name);
          }else{
            alert("No es audio");
            $padre.find(".inputfile").val("");
          }
          return;
        }

          var reader = new FileReader();
          reader.onload = function (e) {
            $padre.find('#viewfoto').attr('src', e.target.result);
          }
   

          if (file.type.match('image.*')) {
              reader.readAsDataURL(input.files[0]);
          } else {
              alert("No es imagen");
              $padre.find(".inputfile").val("");
          }
      }
  }




    $.fn.serializeJSON = function (obj) {
        var json = {};
        if (typeof (obj) !== 'undefined')
            for (var k in obj)
                json[obj[k]] = [];
        $.each($(this).serializeArray(), function () {
            if (typeof (json[this.name]) == 'undefined')
                json[this.name] = this.value;
            else if (typeof (json[this.name]) == 'object')
                json[this.name].push(this.value);
        });
        return json;
    };
    $.fn.load_simpleTable = function (config) {
        var $table = $(this);
        var wch = $table.attr('wch');
        var cols = Array();

        $table.find('tr .ths').each(function (i, item) {
            cols.push({ "data": $(item).text() });
        })

        if (wch) {
            cols.push({
                "data": null,
                "orderable": false,
                "width": "30",
                'render': function (data, type, full, meta) {
                    return config.onrow.call(this, data)
                }
            })
        }

        var table_config = {
            "processing": true,
            "serverSide": true,
            responsive: true,
            "bResetDisplay": true,
            "order": config.order,
            "ajax": {
                "url": config.data_source,
                "type": "POST",
                "data": function (data) {
                    return $.extend(data, $('' + config.cactions).serializeJSON());
                }
            },

            "lengthChange": false,
            "searching": false,
            "columns": cols,
        };
        var table = $table.DataTable(table_config)
        return table;
    }

    function formatRepo(repo) {
        var markup = repo.text;
        return markup;
    }

    function formatRepoSelection(repo) {
        return markup = repo.text;
    }

    $.fn.Seleccion2 = function (title, url, calle) {
        $(this).select2({
            placeholder: title,
            theme: "bootstrap-5",
            // width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? 'auto' : 'style',
            //allowClear: true,
            // width: '100%',
            language: "es",
            minimumInputLength: Infinity,
            ajax: {
                url: base_url + url,
                dataType: 'json',
                data: function (params) {
                    return {
                        q: params.term,
                        p: params.page
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.items,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) {
                return markup;
            },
            minimumInputLength: 0,
            templateResult: formatRepo,
            templateSelection: formatRepoSelection
        }).on("select2:select", function (e) {
            calle(e.params.data);
        }).on('select2:unselect', function (e) {

        });
    }

    $.fn.load_dialog = function (config) {
        var $contenedor;
        if (config.content !== undefined)
            $contenedor = config.content.appendTo($('#modales'));
        else
            $contenedor = $('<div class="modal fade" tabindex="-1"  data-bs-backdrop="static">').appendTo($('#modales'));

        var set_dialog = function () {
            var ftmp = config.close;
            config.close = function () {
                if (ftmp !== undefined)
                    ftmp();
                $contenedor.remove();
            }
            $contenedor.find('.modal-title').text(config.title);
            $contenedor.modal('show');
            $contenedor.on('hidden.bs.modal', function (e) {
                $contenedor.remove();
            })
            $.gs_loader.hide();
            if (config.loaded !== undefined)
                config.loaded($contenedor);
        }
        $.gs_loader.show();
        var url = $(this).attr('href');
        if (config.custom_url !== undefined)
            url = config.custom_url;
        if (url !== undefined) {
            $contenedor.load(url, config.data, function () {
                if (typeof (config.script) != 'undefined')
                    $.getScriptA(config.script, set_dialog);
                else
                    set_dialog();
            });
        } else {
            if (typeof (config.script) != 'undefined')
                $.getScriptA(config.script, set_dialog);
            else
                set_dialog();
        }
        return $contenedor;
    }

    $.bsAlert = {
        alertTitle: "Alerta",
        confirmTitle: "Confirmación",
        closeDisplay: "Cancelar",
        sureDisplay: "Aceptar",
        isConfirm: false,
        init: function (w) {
            this.width = w;
        },
        createAlertWin: function () {
            var $h1 = "";
            $h1 += "<div class=\"bsAlert alert alert-danger alert-dismissible\" role=\"alert\">";
            $h1 += "    <span class=\"alert-msg\">warning message</span>";
            $h1 += "</div>";
            //console.log($h1);
            $("#alerta").append($h1);
        },
        alert: function (msg) {
            $.bsAlert.createAlertWin();
            $(".alert").fadeIn();
            $(".alert-msg").text(msg);
            setTimeout(function () {
                $(".alert").alert('close')
            }, 5000);
        },
        createConfirmWin: function (msg) {
            var $h1 = "";
            $h1 += "<div class='modal fade' id='bsAlertModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>";
            $h1 += "    <div class='modal-dialog'>";
            $h1 += "        <div class='modal-content'>";
            $h1 += "            <div class='modal-header'>";
            $h1 += "                <h5 class='modal-title' id='myModalLabel'>" + this.confirmTitle + "</h5>";
            $h1 += "                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>";
            $h1 += "            </div>";
            $h1 += "            <div class='modal-body'>";
            $h1 += "                <p>" + msg + "</p>";
            $h1 += "            </div>";
            $h1 += "            <div class='modal-footer'>";
            $h1 += "                <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>" + this.closeDisplay + "</button>";
            $h1 += "                <button type='button' id='bsSaveBtn' class='btn btn-primary'>" + this.sureDisplay + "</button>";
            $h1 += "            </div>";
            $h1 += "        </div>";
            $h1 += "    </div>";
            $h1 += "</div>";
            $("#modales").append($h1);
        },
        confirm: function (msg, fun) {
            $.bsAlert.createConfirmWin(msg);
            $('#bsAlertModal').modal('show');

            $("#bsSaveBtn").click(function () {

                fun();
                $('#bsAlertModal').modal('hide')
            });
            $("#bsAlertModal").on("hidden.bs.modal", function () {
                $(this).remove();
            });
        }
    }


    $.fn.onlydialog = function (callload, onsubmit) {
        $(this).load_dialog({
            title: $(this).attr('title'),
            loaded: function (dlg) {
                if (callload !== undefined) callload(dlg);
                $(dlg).find('form').submit(function () {
                    onsubmit(dlg);
                    return false;
                });
            }
        });
    }

    $.fn.mydialog = function (callload, onsave) {
        $(this).load_dialog({
            title: $(this).attr('title'),
            loaded: function (dlg) {
                if (callload !== undefined) callload(dlg);
                $(dlg).find('form').submit(function () {
                    $(this).mysave(function (data) {
                        dlg.modal('hide');
                        if (onsave != undefined) onsave(data);
                    });
                    return false;
                });
            }
        });
    }

    $.fn.mysave = function (onsucces) {

        $(this)[0].classList.add('was-validated');
        if ($(this)[0].checkValidity() === false) {
            $('html,body').animate({ scrollTop: $('.was-validated :invalid').first().offset().top - 50 }, 'slow');
            return false;
        }
        var fd = new FormData(this[0]);
        for (var p of fd) {
            let name = p[0];
            let value = p[1];

            console.log(name, value)
        }
        $.gs_loader.show();
        $.ajax({
            url: $(this).attr('action'),
            type: 'post',
            data: fd,
            dataType: "json",
            contentType: false,
            processData: false,
        }).done(function (data) {
            $.gs_loader.hide();
            if (onsucces !== undefined) onsucces(data);

        }).fail(function (data) {
            $.gs_loader.hide();
            if (data.status == 200) alert("Mensaje del servidor incorrecto.")
            else alert("Error en respuesta: " + data.statusText)
        });
    }




    $.fn.myprocess = function (onsucces) {
        $.gs_loader.show();
        $.ajax({
            type: "POST",
            dataType: "json",
            url: $(this).attr('href'),
        }).done(function (data) {
            $.gs_loader.hide();
            console.log(data)
            if (data.mensaje == 'user' && data.redirect.length > 0) window.location.href = data.redirect;
            else if (onsucces !== undefined) onsucces(data);
        }).fail(function (data) {
            $.gs_loader.hide();
            alert("Error en respuesta :" + data.statusText);
        });
    }



    $.fn.mysend = function (url, onsucces) {
        el = this
        $.gs_loader.show();
        $.ajax({
            type: "POST",
            dataType: "json",
            url: url,
            data: { 'data': $(this).data('data') },
        }).done(function (data) {
            $.gs_loader.hide();
            if (data.mensaje == 'user' && data.redirect.length > 0) window.location.href = data.redirect;
            else if (onsucces !== undefined) onsucces(el, data);
        }).fail(function (data) {
            $.gs_loader.hide();
            alert("Error en respuesta :" + data.statusText);
        });
    }

})(jQuery);


function myloaddata(url, onsucces) {
    $.gs_loader.show();
    $.ajax({
        type: "GET",
        dataType: "json",
        url: url,
    }).done(function (data) {
        $.gs_loader.hide();
        onsucces(data);
    }).fail(function (data) {
        $.gs_loader.hide();
        alert("Error en respuesta :" + data.statusText);
    });
}





function pre_wpautop(content) {
  // Funcion de Wordpress
  // We have a TON of cleanup to do. Line breaks are already stripped.

  // Protect pre|script tags
  content = content.replace(/<(pre|script)[^>]*>[\s\S]+?<\/\1>/g, function(a) {
    a = a.replace(/<br ?\/?>[\r\n]*/g, '<wp_temp>');
    return a.replace(/<\/?p( [^>]*)?>[\r\n]*/g, '<wp_temp>');
  });

  // Pretty it up for the source editor
  var blocklist1 = 'blockquote|ul|ol|li|table|thead|tbody|tr|th|td|div|h[1-6]|p';
  content = content.replace(new RegExp('\\s*</(' + blocklist1 + ')>\\s*', 'mg'), '</$1>\n');
  content = content.replace(new RegExp('\\s*<((' + blocklist1 + ')[^>]*)>', 'mg'), '\n<$1>');

  // Mark </p> if it has any attributes.
  content = content.replace(new RegExp('(<p [^>]+>.*?)</p>', 'mg'), '$1</p#>');

  // Sepatate <div> containing <p>
  content = content.replace(new RegExp('<div([^>]*)>\\s*<p>', 'mgi'), '<div$1>\n\n');

  // Remove <p> and <br />
  content = content.replace(new RegExp('\\s*<p>', 'mgi'), '');
  content = content.replace(new RegExp('\\s*</p>\\s*', 'mgi'), '\n\n');
  content = content.replace(new RegExp('\\n\\s*\\n', 'mgi'), '\n\n');
  content = content.replace(new RegExp('\\s*<br ?/?>\\s*', 'gi'), '\n');

  // Fix some block element newline issues
  content = content.replace(new RegExp('\\s*<div', 'mg'), '\n<div');
  content = content.replace(new RegExp('</div>\\s*', 'mg'), '</div>\n');
  content = content.replace(new RegExp('\\s*\\[caption([^\\[]+)\\[/caption\\]\\s*', 'gi'), '\n\n[caption$1[/caption]\n\n');
  content = content.replace(new RegExp('caption\\]\\n\\n+\\[caption', 'g'), 'caption]\n\n[caption');

  var blocklist2 = 'blockquote|ul|ol|li|table|thead|tr|th|td|h[1-6]|pre';
  content = content.replace(new RegExp('\\s*<((' + blocklist2 + ') ?[^>]*)\\s*>', 'mg'), '\n<$1>');
  content = content.replace(new RegExp('\\s*</(' + blocklist2 + ')>\\s*', 'mg'), '</$1>\n');
  content = content.replace(new RegExp('<li([^>]*)>', 'g'), '\t<li$1>');

  if (content.indexOf('<object') != -1) {
    content = content.replace(new RegExp('\\s*<param([^>]*)>\\s*', 'mg'), "<param$1>");
    content = content.replace(new RegExp('\\s*</embed>\\s*', 'mg'), '</embed>');
  }

  // Unmark special paragraph closing tags
  content = content.replace(new RegExp('</p#>', 'g'), '</p>\n');
  content = content.replace(new RegExp('\\s*(<p [^>]+>.*</p>)', 'mg'), '\n$1');

  // Trim whitespace
  content = content.replace(new RegExp('^\\s*', ''), '');
  content = content.replace(new RegExp('[\\s\\u00a0]*$', ''), '');

  // put back the line breaks in pre|script
  content = content.replace(/<wp_temp>/g, '\n');

  // Hope.
  return content;
}

function wpautop(pee) {
  // Funcion de Wordpress
  var blocklist = 'table|thead|tfoot|caption|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|blockquote|address|math|p|h[1-6]';

  pee = pee + "\n\n";
  pee = pee.replace(new RegExp('<br />\\s*<br />', 'gi'), "\n\n");
  pee = pee.replace(new RegExp('(<(?:' + blocklist + ')[^>]*>)', 'gi'), "\n$1");
  pee = pee.replace(new RegExp('(</(?:' + blocklist + ')>)', 'gi'), "$1\n\n");
  pee = pee.replace(new RegExp("\\r\\n|\\r", 'g'), "\n");
  pee = pee.replace(new RegExp("\\n\\s*\\n+", 'g'), "\n\n");
  pee = pee.replace(new RegExp('([\\s\\S]+?)\\n\\n', 'mg'), "<p>$1</p>\n");
  pee = pee.replace(new RegExp('<p>\\s*?</p>', 'gi'), '');
  pee = pee.replace(new RegExp('<p>\\s*(</?(?:' + blocklist + ')[^>]*>)\\s*</p>', 'gi'), "$1");
  pee = pee.replace(new RegExp("<p>(<li.+?)</p>", 'gi'), "$1");
  pee = pee.replace(new RegExp('<p>\\s*<blockquote([^>]*)>', 'gi'), "<blockquote$1><p>");
  pee = pee.replace(new RegExp('</blockquote>\\s*</p>', 'gi'), '</p></blockquote>');
  pee = pee.replace(new RegExp('<p>\\s*(</?(?:' + blocklist + ')[^>]*>)', 'gi'), "$1");
  pee = pee.replace(new RegExp('(</?(?:' + blocklist + ')[^>]*>)\\s*</p>', 'gi'), "$1");
  pee = pee.replace(new RegExp('\\s*\\n', 'gi'), "<br />\n");
  pee = pee.replace(new RegExp('(</?(?:' + blocklist + ')[^>]*>)\\s*<br />', 'gi'), "$1");
  pee = pee.replace(new RegExp('<br />(\\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)>)', 'gi'), '$1');
  pee = pee.replace(new RegExp('(?:<p>|<br ?/?>)*\\s*\\[caption([^\\[]+)\\[/caption\\]\\s*(?:</p>|<br ?/?>)*', 'gi'), '[caption$1[/caption]');
  // pee = pee.replace(new RegExp('^((?:&nbsp;)*)\\s', 'mg'), '$1&nbsp;');

  // Fix the pre|script tags
  pee = pee.replace(/<(pre|script)[^>]*>[\s\S]+?<\/\1>/g, function(a) {
    a = a.replace(/<br ?\/?>[\r\n]*/g, '\n');
    return a.replace(/<\/?p( [^>]*)?>[\r\n]*/g, '\n');
  });

  return pee;
}