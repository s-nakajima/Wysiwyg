/**
 * nc3Image - plugin
 */
// ad tinymce plugin
tinymce.PluginManager.add('nc3Image', function(editor, url) {
  //　セレクタなど
  var vals = {
    dispThumArea: 'image-thumb',
    img_elm_class: 'nc3-img',
    img_basepath: '/wysiwyg/image/download/'
  };
  var position_form_vals = [{
    text: 'Select Position',
    value: ''
  }, {
    text: 'center',
    value: 'center'
  }, {
    text: 'left',
    value: 'left'
  }, {
    text: 'right',
    value: 'right'
  }];
  var size_form_vals = [{
    text: 'Select Size',
    value: ''
  }, {
    text: 'big',
    value: 'big'
  }, {
    text: 'medium',
    value: 'medium'
  }, {
    text: 'small',
    value: 'small'
  }, {
    text: 'thumb',
    value: 'thumb'
  }];

  // 新規登録時のサムネイル表示
  var __setImageThumb = function(e) {
    if (!$(e).prop('files')) return;
    var file = $(e).prop('files')[0],
        fileRdr = new FileReader(),
        thumbEl = $('#thumb-wrap'),
        thumbImg = thumbEl.find('.image-thumb');
    if (!e.files.length) {
      if (0 < thumbImg.size()) {
        thumbImg.remove();
        return;
      }
    } else {
      if (file.type.match('image.*')) {
        if (!(0 < thumbImg.size())) {
          thumbEl.append(
              $('<img alt="thumb" class="image-thumb">')
              .height('50px')
              .width('50px')
          );
        }
        var prevElm = thumbEl.find('.image-thumb');
        fileRdr.onload = function() {
          prevElm.attr('src', fileRdr.result);
        };
        fileRdr.readAsDataURL(file);
      } else {
        if (0 < thumbImg.size()) {
          thumbImg.remove();
          return;
        }
      }
    }
  };

  // submit時動作(insert)
  var onSubmitForm_Insert = function(data) {
    if (data.src) {
      // formオブジェクト作成
      var files = $('#uploadForm')
        .find('input[type="file"]')[0]
        .files[0];
      var formData = new FormData();
      formData.append('data[Wysiwyg][file]', files);
      formData.append('data[Block][key]', 'block_1'); // ひとまずダミー送信
      formData.append('src', data.src);
      formData.append('alt', data.alt);
      formData.append('size', data.size);
      formData.append('position', data.position);
      NC3_APP.uploadImage(formData,
          function(res) {
            // onsuccess
            if (res.result) {
              editor.selection.collapse(true);
              editor.execCommand('mceInsertContent', false,
                  editor.dom.createHTML('img', {
                    src: vals.img_basepath + res.file.id + '/' + data.size,
                    alt: data.alt,
                    class: vals.img_elm_class,
                    'data-size': data.size,
                    'data-position': data.position,
                    'data-imgid': res.file.id
                  })
              );
            } // if
          },
          function(res) {
            // onerror
          },
          editor.settings.isDEBUG
      ); // uploadfile()
    } // if(src)
  };
  // submit時動作(update)
  var onSubmitForm_Update = function(data, selectedNode) {
    // domの更新
    $el = $(selectedNode);
    $el.attr('alt', data.alt_edit);
    $el.attr('data-size', data.size_edit);
    $el.attr('data-position', data.position_edit);
  };

  // ダイアログ内要素(新規用タブ)
  var generalFormItems = [
    // 登録用領域
    {
      name: 'src',
      type: 'textbox',
      subtype: 'file',
      label: 'File',
      autofocus: true,
      onchange: function(e) {
        __setImageThumb(e.target);
      }
    },
    // サムネイル表示用パネル
    {
      type: 'panel',
      id: 'thumb-wrap',
      style: 'height:60px;text-align:right;background-color:#fff;',
      html: ''
    },
    // alt
    {
      name: 'alt',
      type: 'textbox',
      label: 'Image description',
      name: 'alt'
    },
    // 位置(left, right, center)
    {
      type: 'listbox',
      label: 'Image-Position',
      name: 'position',
      onselect: function(e) {},
      values: position_form_vals
    },
    // 画像サイズ
    {
      type: 'listbox',
      label: 'Image-Size',
      name: 'size',
      onselect: function(e) {},
      values: size_form_vals
    }
  ];
  // ダイアログ内要素(編集用タブ)
  var advancedFormItems = function(data) {
    return [
      // 登録用領域
      {
        name: 'src_edit',
        type: 'textbox',
        label: 'Url',
        autofocus: true,
        value: data.src
      },
      // サムネイル表示用パネル
      {
        type: 'panel',
        id: 'image-thumb-ed',
        style: 'text-align:right;background-color:#fff;',
        html: function() {
          if (data.id) {
            return '<img src="' + vals.img_basepath + data.id + '/thumb">';
          } else {
            return '';
          }
        }
      },
      // alt
      {
        name: 'alt_edit',
        type: 'textbox',
        label: 'Image description',
        value: data.alt
      },
      // 位置(left, right, center)
      {
        type: 'listbox',
        label: 'Image-Position',
        name: 'position_edit',
        value: data.position,
        onselect: function(e) {},
        values: position_form_vals
      },
      // 画像サイズ
      {
        type: 'listbox',
        label: 'Image-Size',
        name: 'size_edit',
        value: data.size,
        onselect: function(e) {},
        values: size_form_vals
      }
    ];
  };

  // ダイアログ表示
  var showDialog = function() {
    var win;
    var data = {
      src: '',
      alt: '',
      w: '',
      h: '',
      size: '',
      position: ''
    }; // init
    var selectedNode = editor.selection.getNode();
    var isTarget = selectedNode.tagName == 'IMG' &&
        editor.dom.hasClass(selectedNode, vals.img_elm_class) == true;
    if (isTarget) {
      var re_src = editor.dom.getAttrib(selectedNode, 'src');
      re_src = re_src.replace(/\/(big|middle|small|thumb)$/, '');
      data.src = re_src;
      data.alt = editor.dom.getAttrib(selectedNode, 'alt');
      data.w = editor.dom.getAttrib(selectedNode, 'width');
      data.h = editor.dom.getAttrib(selectedNode, 'height');
      data.size = editor.dom.getAttrib(selectedNode, 'data-size');
      data.position = editor.dom.getAttrib(selectedNode, 'data-position');
      data.id = editor.dom.getAttrib(selectedNode, 'data-imgid');
    }
    win = editor.windowManager.open({
      title: 'Insert/edit image',
      bodyType: 'tabpanel',
      body: [
        // タブ1(新規投稿)
        {
          title: 'Insert',
          type: 'form',
          id: 'uploadForm',
          items: generalFormItems
        },
        // タブ2(nc3より画像取得)
        {
          title: 'Edit',
          type: 'form',
          pack: 'start',
          items: advancedFormItems(data)
        }
      ],
      onsubmit: function(e) {
        var d = tinymce.extend(e.data, win.toJSON());
        if (isTarget) {
          onSubmitForm_Update(d, selectedNode);
        } else {
          onSubmitForm_Insert(d);
        }
      }
    }); // open()

    __setImageThumb();
  }; // showDialog

  // コマンド登録
  editor.addCommand('mceImage', showDialog);

  // windowへのボタン登録
  editor.addButton('nc3Image', {
    icon: 'image',
    id: 'image-btn',
    stateSelector: '.' + vals.img_elm_class,
    onclick: showDialog
  });
});
