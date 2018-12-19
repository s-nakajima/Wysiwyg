/**
 * @fileoverview Wysiwyg Javascript
 * @author nakajimashouhei@gmail.com (Shohei Nakajima)
 */

NetCommonsApp.requires.push('ui.tinymce');


/**
 * NetCommonsWysiwyg factory
 */
NetCommonsApp.factory('NetCommonsWysiwyg',
    ['nc3Configs', 'colorPaletteBaseColors', 'colorPaletteDefaultColors',
      function(nc3Configs, colorPaletteBaseColors, colorPaletteDefaultColors) {

        // textclor で使用するためのカラーパレット情報を構築
        // textcolor_map の設定値をココで構築する内容で設定する
        //
        var colors = function(baseColors, defaultColors) {
          var _colors = baseColors.concat(defaultColors);

          // TinyMCEのカラーパレット情報の
          // ['色コード', '色名', '色コード', '色名', ... ]
          // と言う形式に colors 配列を作成する
          //
          var colors = [];
          for (var i = 0; i < _colors.length; i++) {
            colors.push(_colors[i].replace(/#/g, ''));
            colors.push('');
          }
          return colors;
        };

        var toolbarPc = [
          'fontselect fontsizeselect formatselect ' +
              '| bold italic underline strikethrough ' +
              '| subscript superscript | forecolor backcolor ' +
              '| removeformat ' +
              '| undo redo | alignleft aligncenter alignright ' +
              '| bullist numlist | indent outdent blockquote ' +
              '| table | hr | titleicons | tex | link unlink ' +
              '| media booksearch nc3Image file | pastetext code nc3Preview'
        ];
        var toolbarMobile = [
          'styleselect forecolor backcolor titleicons nc3Image'
        ];

        var toolbar = nc3Configs.is_mobile ? toolbarMobile : toolbarPc;

        /**
         * tinymce optins
         *
         * @type {{mode: string, menubar: string, plugins: string, toolbar: string}}
         */
        var options = {
          mode: 'exact',
          menubar: false,
          plugins: 'lists advlist nc3_textcolor nc3_colorpicker table hr titleicons ' +
              'charmap link media nc3Image code nc3Preview searchreplace ' +
              'paste tex file booksearch',
          toolbar: toolbar,

          font_formats: 'ゴシック=Arial, Roboto, “Droid Sans”, ' +
                        '“游ゴシック”, "Yu Gothic", "YuGothic", ' +
                        '“ヒラギノ角ゴ ProN W3”, “Hiragino Kaku Gothic ProN”, ' +
                        '“メイリオ”, Meiryo, sans-serif;' +
                        '明朝=“Times New Roman”, “游明朝”, "Yu Mincho", "YuMincho", ' +
                        '“ヒラギノ明朝 ProN W3”, “Hiragino Mincho ProN”, ' +
                        '"ＭＳ Ｐ明朝", "MS PMincho", serif;' +
                        'メイリオ=“メイリオ”, Meiryo, sans-serif;' +
                        'ヒラギノ角ゴ=“ヒラギノ角ゴ ProN W3”, ' +
                        '“Hiragino Kaku Gothic ProN”, sans-serif;' +
                        'ヒラギノ明朝=“ヒラギノ明朝 ProN W3”, “Hiragino Mincho ProN”, ' +
                        '“游明朝”, "Yu Mincho", "YuMincho", ' +
                        '"ＭＳ Ｐ明朝", "MS PMincho", serif;' +
                        'MS Pゴシック= "MS PGothic", Osaka, Arial, sans-serif;' +
                        'MS P明朝= "ＭＳ Ｐ明朝", "MS PMincho", serif;',
          fontsize_formats: '8pt 10pt 11pt 12pt 14pt 18pt 24pt 36pt',

          paste_as_text: true,
          convert_urls: false,
          resize: 'both',
          content_css: nc3Configs.content_css,

          nc3Configs: nc3Configs,

          // colorpicker 関連
          textcolor_cols: 10,
          textcolor_rows: 6,
          textcolor_map: colors(
              colorPaletteBaseColors,
              colorPaletteDefaultColors
          ),

          // 言語設定
          language: nc3Configs.lang,
          language_url: nc3Configs.lang_js,

          // 許可するタグの設定
          extended_valid_elements: nc3Configs.extended_valid_elements,
          valid_children: nc3Configs.valid_children,
          cleanup: nc3Configs.cleanup,

          // テーブル関連
          // テーブル配置の属性
          table_default_attributes: {
            class: 'table table-bordered table-hover table-responsive'
          },
          // テーブルのclassリスト
          table_class_list: [
            {title: 'None', value: ''},
            {title: 'table-bordered', value: 'table table-bordered table-hover table-responsive'},
            {title: 'table-bordered(border-top only)', value: 'table table-hover table-responsive'},
            {title: 'table-striped', value: 'table table-striped table-hover table-responsive'}
            // なぜか色きかない
            //{title: 'table-inverse', value: 'table table-inverse table-hover table-responsive'}
          ],
          // なぜか色きかない
          // table_cell_class_list: [
          //   {title: 'None', value: ''},
          //   {title: 'table-active', value: 'table-active'},
          //   {title: 'table-success', value: 'table-success'},
          //   {title: 'table-info', value: 'table-info'},
          //   {title: 'table-warning', value: 'table-warning'},
          //   {title: 'table-danger', value: 'table-danger'}
          // ],
          // table_row_class_list: [
          //   {title: 'None', value: ''},
          //   {title: 'table-active', value: 'table-active'},
          //   {title: 'table-success', value: 'table-success'},
          //   {title: 'table-info', value: 'table-info'},
          //   {title: 'table-warning', value: 'table-warning'},
          //   {title: 'table-danger', value: 'table-danger'}
          // ],
          // テーブルのドラッグ＆ドロップリサイズ無効（レスポンシブ効かなくなるため）
          table_resize_bars: false,
          object_resizing: 'img'
        };

        /**
         * variables
         *
         * @type {Object.<string>}
         */
        var variables = {
          options: options
        };

        /**
         * functions
         *
         * @type {Object.<function>}
         */
        var functions = {
          /**
           * new method
           */
          new: function(extendOptions) {
            if (extendOptions) {
              variables['options'] = angular.extend(variables['options'], extendOptions);
            }
            return angular.extend(variables, functions);
          }
        };

        return functions.new();
      }]);
