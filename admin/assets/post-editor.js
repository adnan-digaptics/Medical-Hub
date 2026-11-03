(function () {
  function ready(fn) {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', fn);
    } else {
      fn();
    }
  }

  ready(function () {
    var textarea = document.getElementById('mmh-content');
    var form = document.getElementById('mmh-post-form');
    if (!textarea || !window.tinymce) {
      return;
    }

    tinymce.init({
      selector: '#mmh-content',
      base_url: 'assets/vendor/tinymce',
      suffix: '.min',
      height: 580,
      menubar: false,
      branding: false,
      promotion: false,
      statusbar: true,
      resize: true,
      plugins: 'lists advlist link image table autoresize code wordcount autolink searchreplace fullscreen quickbars',
      toolbar: 'undo redo | blocks | bold italic underline strikethrough | bullist numlist outdent indent | blockquote | alignleft aligncenter alignright | link image table | removeformat | fullscreen',
      quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote',
      quickbars_insert_toolbar: false,
      block_formats: 'Paragraph=p; Heading 2=h2; Heading 3=h3; Heading 4=h4',
      formats: {
        alignleft: { selector: 'p,h2,h3,h4,td,th,div,ul,ol,li,table,img,figure', classes: 'alignleft' },
        aligncenter: { selector: 'p,h2,h3,h4,td,th,div,ul,ol,li,table,img,figure', classes: 'aligncenter' },
        alignright: { selector: 'p,h2,h3,h4,td,th,div,ul,ol,li,table,img,figure', classes: 'alignright' }
      },
      content_style: [
        'body{font-family:Georgia,"Times New Roman",serif;font-size:18px;line-height:1.75;color:#1c2a3a;max-width:760px;margin:28px auto;padding:0 20px;background:#fff}',
        'p{margin:0 0 1.15em}',
        'h2,h3,h4{font-family:"Plus Jakarta Sans",Arial,Helvetica,sans-serif;color:#002261;line-height:1.3}',
        'h2{font-size:1.6em;margin:1.4em 0 .6em}',
        'h3{font-size:1.3em;margin:1.2em 0 .5em}',
        'img{max-width:100%;height:auto;border-radius:12px}',
        'blockquote{border-left:4px solid #2ea6f7;margin:0 0 1.2em;padding:8px 16px;background:#f4f7fb}',
        'ul,ol{margin:0 0 1.2em;padding-left:1.4em}',
        'a{color:#125d99}'
      ].join(''),
      forced_root_block: 'p',
      entity_encoding: 'raw',
      convert_urls: false,
      relative_urls: false,
      remove_script_host: true,
      document_base_url: new URL('../', window.location.href).href,
      automatic_uploads: true,
      images_upload_handler: function (blobInfo) {
        return new Promise(function (resolve, reject) {
          var fd = new FormData();
          fd.append('file', blobInfo.blob(), blobInfo.filename());
          var token = document.querySelector('input[name=csrf_token]');
          if (token) {
            fd.append('csrf_token', token.value);
          }
          fetch('media-upload.php', { method: 'POST', body: fd })
            .then(function (res) { return res.json(); })
            .then(function (json) {
              if (json && json.location) {
                resolve(json.location);
              } else {
                reject((json && json.error) || 'Upload failed');
              }
            })
            .catch(function () { reject('Upload failed'); });
        });
      }
    });

    if (form) {
      form.addEventListener('submit', function () {
        if (window.tinymce) {
          tinymce.triggerSave();
        }
      });
    }

    document.querySelectorAll('[data-editor-tab]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var tab = btn.getAttribute('data-editor-tab');
        var editor = tinymce.get('mmh-content');
        document.querySelectorAll('[data-editor-tab]').forEach(function (item) {
          var active = item === btn;
          item.classList.toggle('is-active', active);
          item.setAttribute('aria-selected', active ? 'true' : 'false');
        });
        if (!editor) {
          return;
        }
        if (tab === 'code') {
          editor.save();
          editor.hide();
          textarea.classList.add('is-code');
          textarea.focus();
        } else {
          editor.show();
          editor.setContent(textarea.value);
          textarea.classList.remove('is-code');
          editor.focus();
        }
      });
    });
  });
})();
