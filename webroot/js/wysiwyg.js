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
            'paste tex file booksearch',
        toolbar: [
                  'fontselect fontsizeselect formatselect ' +
                  '| bold italic underline strikethrough ' +
                  '| subscript superscript | forecolor backcolor ' +
                  '| removeformat' +
                  '| undo redo | alignleft aligncenter alignright ' +
                  '| bullist numlist | outdent indent blockquote ' +
                  '| table | hr | titleicons | tex | link unlink' +
                  '| media booksearch nc3Image file | pastetext code nc3Preview'
        ],

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

        paste_as_text: true,
        convert_urls: false,
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