/**
 * @fileoverview Wysiwyg Javascript
 * @author nakajimashouhei@gmail.com (Shohei Nakajima)
 */

NetCommonsApp.requires.push('ui.tinymce');


/**
 * NetCommonsWysiwyg factory
 */
NetCommonsApp.factory('NetCommonsWysiwyg', function(nc3Configs,
    colorPaletteBaseColors, colorPaletteDefaultColors) {

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

      /**
       * tinymce optins
       *
       * @type {{mode: string, menubar: string, plugins: string, toolbar: string}}
       */
      var options = {
        mode: 'exact',
        menubar: false,
        plugins: 'advlist nc3_textcolor colorpicker table hr titleicons ' +
            'charmap link media nc3Image code nc3Preview searchreplace ' +
            'paste tex file',
        toolbar: [
                  'fontselect fontsizeselect formatselect ' +
                  '| bold italic underline strikethrough ' +
                  '| subscript superscript | forecolor backcolor ' +
                  '| removeformat' +
                  '| undo redo | alignleft aligncenter alignright ' +
                  '| bullist numlist | outdent indent blockquote ' +
                  '| table | hr | titleicons | tex | link unlink' +
                  '| media books nc3Image file | pastetext code nc3Preview'
        ],
        paste_as_text: true,
        convert_urls: false,

        nc3Configs: nc3Configs,

        // タイトルアイコンのサイズ指定
        titleIconSize: 18,

        // colorpicker 関連
        textcolor_cols: 10,
        textcolor_rows: 6,
        textcolor_map: colors(
            colorPaletteBaseColors,
            colorPaletteDefaultColors
        ),

        setup: function(editor) {
          editor.addButton('books', {
            tooltip: 'BookSearch',
            image: '/wysiwyg/img/title_icons/book.svg',
            onclick: function() {
              editor.windowManager.alert('書籍検索');
            }
          });
        },

        // 言語設定
        language: nc3Configs.lang,
        language_url: '/wysiwyg/js/langs/' + nc3Configs.lang + '.js'
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
        new: function() {
          return angular.extend(variables, functions);
        }
      };

      return functions.new();
    });
