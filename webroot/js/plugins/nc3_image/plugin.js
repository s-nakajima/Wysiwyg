/**
 * nc3Image - plugin
 */
// ad tinymce plugin
tinymce.PluginManager.add('nc3Image', function(editor, url) {
  //　セレクタなど
  var vals = {
    dispThumArea: 'image-thumb',
    img_elm_class: 'nc3-img',
    img_elm_block_class: 'nc3-img-block'
  };
  var positionFormVals = [{
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
  // 画像の位置のformValueと挿入時クラス
  var positionClass = {
    center: 'center-block',
    left: 'pull-left',
    right: 'pull-right'
  };
  var sizeFormVals = [{
    text: 'Default size(800px)',
    value: 'big'
  }, {
    text: 'original',
    value: ''
  }, {
    text: 'biggest(1200px)',
    value: 'biggest'
  }, {
    text: 'medium(400px)',
    value: 'medium'
  }, {
    text: 'small(200px)',
    value: 'small'
  }, {
    text: 'thumb(80px)',
    value: 'thumb'
  }];

  // 新規登録時のサムネイル表示
  var setImageThumb = function(e) {
    if (!$(e).prop('files')) return;
    var file = $(e).prop('files')[0],
        fileRdr = new FileReader(),
        thumbEl = $('#thumb-wrap'),
        thumbImg = thumbEl.find('.image-thumb');

    if (!e.files.length) {
      if (0 < thumbImg.length) {
        thumbImg.remove();
        return;
      }
    } else {
      if (file.type.match('image.*')) {
        if (!(0 < thumbImg.length)) {
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
        if (0 < thumbImg.length) {
          thumbImg.remove();
          return;
        }
      }
    }
  };

  // submit時動作(insert)
  var onSubmitFormInsert = function(data) {
    var d = data;
    if (d.src) {
      // formオブジェクト作成
      var files = $('#uploadForm')
          .find('input[type="file"]')[0]
          .files[0];
      var formData = new FormData();
      formData.append('data[Wysiwyg][file]', files);
      formData.append('data[Block][key]', editor.settings.nc3Configs.blockKey);
      formData.append('data[Block][room_id]', editor.settings.nc3Configs.roomId);
      formData.append('data[Room][id]', editor.settings.nc3Configs.roomId);
      formData.append('data[_Token][fields]', editor.settings.nc3Configs.imageSecure);
      formData.append('data[_Token][unlocked]', '');
      if (editor.settings.nc3Configs.debug !== '0') {
        formData.append('data[_Token][debug]', editor.settings.nc3Configs.debug);
      }

      var loading = $('#loading');
      loading.removeClass('ng-hide');

      NC3_APP.uploadImage(editor.settings.nc3Configs.roomId, formData,
          function(res) {
            // onsuccess
            if (res && res.result) {
              var imgSrc = (d.size) ?
                           (res.file.path + '/' + d.size) :
                           (res.file.path);
              var imgClass = 'img-responsive ' + vals.img_elm_class;
              if (d.size === 'biggest' || d.size === 'big') {
                imgClass = imgClass + ' ' + vals.img_elm_block_class;
              }
              if (positionClass[d.position]) {
                imgClass = imgClass + ' ' + positionClass[d.position];
              }
              editor.selection.collapse(true);
              editor.execCommand('mceInsertContent', false,
                  editor.dom.createHTML('img', {
                    src: imgSrc,
                    alt: d.alt,
                    title: d.alt,
                    class: imgClass,
                    'data-size': d.size,
                    'data-position': d.position,
                    'data-imgid': res.file.id
                  })
              );
              // dialog close
              top.tinymce.activeEditor.windowManager.close();
            } // if
            loading.addClass('ng-hide');
          },
          function(res) {
            // onerror
            alert(res.responseJSON.message);
            loading.addClass('ng-hide');
          },
          editor.settings.isDEBUG
      ); // uploadfile()
    } // if(src)
  };
  // submit時動作(update)
  var onSubmitFormUpdate = function(d, selectedNode) {
    var imgSrc = (d.size_edit) ? (d.src_edit + '/' + d.size_edit) : (d.src_edit);

    // domの更新
    $el = $(selectedNode);
    $el.attr('alt', d.alt_edit);
    $el.attr('title', d.alt_edit);
    $el.attr('data-size', d.size_edit);
    $el.attr('src', imgSrc);
    $el.attr('data-mce-src', imgSrc);
    $el.attr('data-position', d.position_edit);
    $el.attr('class', ''); // クラス初期化
    var imgClass = 'img-responsive ' + vals.img_elm_class;
    if (d.size_edit === 'biggest' || d.size_edit === 'big') {
      imgClass = imgClass + ' ' + vals.img_elm_block_class;
    }
    if (positionClass[d.position_edit]) {
      imgClass = imgClass + ' ' + positionClass[d.position_edit];
    }
    $el.attr('class', imgClass);
    // dialog close
    top.tinymce.activeEditor.windowManager.close();
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
        setImageThumb(e.target);
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
      label: 'Image description'
    },
    // 位置(left, right, center)
    {
      type: 'listbox',
      label: 'Image-Position',
      name: 'position',
      onselect: function(e) {},
      values: positionFormVals
    },
    // 画像サイズ
    {
      type: 'listbox',
      label: 'Image-Size',
      name: 'size',
      onselect: function(e) {},
      values: sizeFormVals
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
        multiline: true,
        value: data.src,
        disabled: false
      },
      // サムネイル表示用パネル
      {
        type: 'panel',
        id: 'image-thumb-ed',
        style: 'height:60px;text-align:right;background-color:#fff;',
        html: function() {
          if (data.id) {
            return '<img src="' + data.src + '/thumb">';
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
        values: positionFormVals
      },
      // 画像サイズ
      {
        type: 'listbox',
        label: 'Image-Size',
        name: 'size_edit',
        value: data.size,
        onselect: function(e) {},
        values: sizeFormVals
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
      re_src = re_src.replace(/(\/biggest|\/big|\/medium|\/small|\/thumb)$/, '');
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
      // activeTab: 2, // TODO: setActiveTab
      body: [
        // タブ1(新規投稿)
        {
          title: 'Insert',
          type: 'form',
          id: 'uploadForm',
          items: generalFormItems
        },
        // タブ2(既存画像編集)
        {
          title: 'Edit',
          type: 'form',
          pack: 'start',
          items: advancedFormItems(data)
        }
      ],
      onsubmit: function(e) {
        e.preventDefault();
        e.stopPropagation();
        var d = tinymce.extend(e.data, win.toJSON());
        if (isTarget) {
          onSubmitFormUpdate(d, selectedNode);
        } else {
          onSubmitFormInsert(d);
        }
        return false;
      }
    }); // open()
    // TODO: setActiveTab
    // console.log(tinymce.activeEditor);
    setImageThumb();
  }; // showDialog

  // コマンド登録
  editor.addCommand('mceImage', showDialog);

  // windowへのボタン登録
  editor.addButton('nc3Image', {
    icon: 'image',
    tooltip: 'Insert/edit image',
    id: 'image-btn',
    stateSelector: '.' + vals.img_elm_class,
    onclick: showDialog
  });
});
