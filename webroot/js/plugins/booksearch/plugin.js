/**
 * Books - plugin
 */
// add tinymce plugin
tinymce.PluginManager.add('booksearch', function(editor, url) {
  var win;
  var itemsOnPage = 5;            // 1ページあたりの表示件数
  var currentInsertType = '';     //挿入タイプ
  // 各要素のセレクタ
  var selectors = {
    form: '#wysiwyg-books-search',        // フォームid
    iptKeyword: '#keyword',       // キーワード入力領域
    panelResult: '#wysiwyg-books-result', // 検索結果表示用パネル
    resultWrap: '#wysiwyg-result-items',  //結果リスト表示領域
    linkBtn: '.link-btn'          // リンク作成ボタン
  };
  var vals = {
    googleBooksUrl: 'https://books.google.co.jp/books'
  };

  // 検索処理
  var searchBooks = function(btn, e) {
    var $form = $(selectors.form);
    var keyword = $form.find(selectors.iptKeyword).val();
    NC3_APP.searchBooks({keyword: keyword},
        function(res) {
          // onsucceurlss
          setResultItems(res.items, function(page) {
            // paginatinon init
            $('#wysiwyg-pagination').pagination({
              items: page,
              cssStyle: 'light-theme',
              onPageClick: function(pageNumber) {
                var page = '#page-' + pageNumber;
                $('.selection').hide();
                $(page).show();
                $('.result-items-wrap').scrollTop(0);
              }
            });
            showResult();
            setResultEvent();
          });
        },
        function(res) {}
    );
  };

  // 結果一覧の出力
  var setResultItems = function(items, callback) {
    var domTxt = '', page = 1;
    $(selectors.resultWrap).empty();
    var $selection = $(booksTemplate.selection(page));
    for (var i = 0; i < items.length; i++) {
      if (i != 0 && i % itemsOnPage == 0) {
        $(selectors.resultWrap).append($selection);
        page++;
        $selection = $(booksTemplate.selection(page));
        $selection.css('display', 'none');
      }
      $selection.append(booksTemplate.resultItem(items[i]));
    }
    $(selectors.resultWrap).append($selection);
    $.isFunction(callback) && callback(page);
  };

  // 検索結果パネル表示
  var showResult = function() {
    $el = $(selectors.panelResult);
    // tinymceが付与するスタイルの補正
    $el.css('background-color', '#fff');
    $el.css('min-height', '300px');
    $el.show();
  };

  // 結果画面の非表示
  var hideResult = function() {
    $(selectors.panelResult).hide();
  };

  // 結果一覧内イベント処理
  var setResultEvent = function() {
    $(selectors.panelResult)
        .find(selectors.linkBtn)
        .on('click', function() {
              var id = $(this).attr('data-bid');
              writeItem(id);
            });
  };

  // 挿入タイプ別domの構築/wysiwygへの挿入
  var writeItem = function(id) {
    // エラー処理TODO
    if (!id) { alert('データが見つかりません'); return false }
    if (!currentInsertType) {
      alert('リンク挿入方法が選択されていません');
      return false;
    }

    var dom;
    var url = vals.googleBooksUrl + '?id=' + id;
    var $item = $(selectors.form).find('.book-item[data-bid="' + id + '"]');
    if (currentInsertType == 'detail') {
      var $el = $item.children().clone(false);
      // 不要な要素の削除
      $el.find('.btn-area').remove();
      var $div = $('<div class="wysiwyg-books">').append($el);
      dom = $div.prop('outerHTML');
    }
    else if (currentInsertType == 'text') {
      var title = $item.find('.book-title > a').text();
      dom = editor.dom.createHTML('a', {
        class: 'book-link',
        href: url,
        target: '_blank'
      }, title);
    }
    else if (currentInsertType == 'smallImg') {
      var $el = $item.find('.book-item-img').children().clone(false);
      $el.find('img').removeAttr('data-bthumb');
      dom = $el.prop('outerHTML');
    }
    else if (currentInsertType == 'bigImg') {
      var $el = $item.find('.book-item-img').children().clone(false);
      var url = $el.find('img').attr('data-thumb');
      $el.find('img')
          .attr('src', url)
          .attr('width', '128')
          .attr('height', '164')
          .removeAttr('data-bthumb');
      dom = $el.prop('outerHTML');
    }
    // wysiwygへの挿入
    editor.execCommand('mceInsertContent', false, dom);
    // dialog close
    top.tinymce.activeEditor.windowManager.close();
  };

  // "画像の表示"セレクトボックス設定
  // TODO 多言語化
  var insert_type_vals = [{
    text: '選択してください',
    value: ''
  }, {
    text: '詳細情報',
    value: 'detail'
  }, {
    text: 'テキストのみ',
    value: 'text'
  }, {
    text: '小画像',
    value: 'smallImg'
  }, {
    text: '大画像',
    value: 'bigImg'
  }];

  // 検索画面部品
  var bookFormItems = [
    // 検索アイテム
    {
      id: 'keyword',
      type: 'textbox',
      label: 'Keyword',
      name: 'keyword'
    },
    // 画像の表示(TODO 多言語)
    {
      type: 'listbox',
      label: 'Link-Insert-Type',
      id: 'insert_type',
      name: 'insert_type',
      onselect: function(e) {
        currentInsertType = this.value();
      },
      values: insert_type_vals
    },
    //検索ボタン
    {
      type: 'button',
      text: 'Search',
      maxWidth: 100,
      maxHeight: 200,
      align: 'center',
      onclick: searchBooks
    },
    // 検索結果表示パネル
    {
      type: 'panel',
      id: 'wysiwyg-books-result',
      html: booksTemplate.resultWrapper(),
      minHeight: 300,
      maxHeight: 300
    }
  ];

  // ダイアログ表示
  var showDialog = function() {
    win = editor.windowManager.open({
      title: '書籍検索',
      id: 'wysiwyg-books-search',
      body: bookFormItems,
      width: 600,
      height: 500,
      scrollbars: true,
      buttons: [],
      onsubmit: function(e) {
        e.preventDefault();
        e.stopPropagation();
        return false;
      }
    });
  };


  // コマンド登録
  editor.addCommand('mceBooks', showDialog);

  // windowへのボタン登録
  editor.addButton('booksearch', {
    tooltip: 'Book search',
    image: editor.settings.nc3Configs.book_icon,
    // icon: 'newdocument',
    onclick: showDialog
  });

});


/**
 * 要素作成用
 */
var booksTemplate = (function() {
  // 結果一覧のラッパー
  var resultWrapper = function() {
    return '' +
        '<div class="result-items-wrap">' +
        '<ul class="wysiwyg-books" id="wysiwyg-result-items"></ul>' +
        '<div id="wysiwyg-pagination"></div>' +
        '</div>';
  };

  // 一覧の各アイテムテンプレート
  var resultItem = function(d) {
    var _DATA_ = d;
    var _VINFO_ = d.volumeInfo;
    var title = '', authors = '', sThumb = '', thumb = '',
        isbn = '', description = '', publishedDate = '';
    //タイトル
    if (_VINFO_.title) title += _VINFO_.title + '&nbsp;';
    if (_VINFO_.subtitle) title += _VINFO_.subtitle;
    // 詳細飛び先
    if (_VINFO_.previewLink) {
      previewLink = _VINFO_.previewLink;
    }    // 著者
    if (_VINFO_.authors) {
      for (var i = 0; i < _VINFO_.authors.length; i++) {
        authors += _VINFO_.authors[i] + '&nbsp';
      }
    }
    // 画像
    if (_VINFO_.imageLinks) {
      sThumb = _VINFO_.imageLinks.smallThumbnail;
      thumb = _VINFO_.imageLinks.thumbnail;
    }
    // isbn [0]->ISBN_10 [1]->ISBN_13
    if (_VINFO_.industryIdentifiers &&
        _VINFO_.industryIdentifiers[0].identifier) {
      isbn = _VINFO_.industryIdentifiers[0].identifier;
    }
    // 出版日
    if (_VINFO_.publishedDate) {
      publishedDate = _VINFO_.publishedDate;
    }
    // 説明
    if (_DATA_.searchInfo && _DATA_.searchInfo.textSnippet) {
      description = _DATA_.searchInfo.textSnippet;
    }

    // .itemと.link-btn共にdata-bidが存在するのは関連付けの為
    return '' +
        '<li class="book-item" data-bid="' + _DATA_.id + '">' +
            '<div class="book-title"><a href="' + previewLink + '"' +
            ' target="_blank">' +
                title +
            '</a></div>' +
            '<div class="book-item-detail">' +
                '<div class="book-item-img">' +
                    '<a href="' + previewLink + '"' +
                    ' target="_blank">' +
                        '<img src="' + sThumb + '"' +
                        'data-bthumb="' + thumb + '"' +
                        ' alt="' + title + '" height="76" width="60">' +
                    '</a>' +
                '</div>' +
                '<div class="book-item-inner">' +
                    '<div class="authors">' + authors + '&nbsp;&nbsp;' +
                        publishedDate +
                    '</div>' +
                    '<div class="book-item-description">' + description +
                    '</div>' +
                    '<div class="btn-area"><button class="btn link-btn"' +
                    ' data-bid="' + _DATA_.id + '"' +
                    '">リンク作成</button></div>' +
                '</div>' +
            '</div>' +
        '</li>';
  };

  // ページャ用
  var selection = function(num) {
    return '<div class="selection" id="page-' + num + '"></div>';
  };

  return {
    resultWrapper: resultWrapper,
    resultItem: resultItem,
    selection: selection
    // 挿入時テンプレート
  };
})();
