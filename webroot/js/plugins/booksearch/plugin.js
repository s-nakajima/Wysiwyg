/**
 * Books - plugin
 */
// add tinymce plugin
tinymce.PluginManager.add('nc3Books', function(editor, url) {
  var win;
  var itemsOnPage = 4; // 1ページあたりの表示件数
  var currentDialog = 'top'; //ダイアログ内の表示状態(top,result,detail)
  // 各要素のセレクタ
  var selectors = {
    form: '#books-search', // フォームid
    iptKeyword: '#keyword', // キーワード入力領域
    panelResult: '#books-result', // 検索結果表示用パネル
    panelDetail: '#books-detail', // 詳細表示用パネル
    resultWrap: '#result-items', //結果リスト表示領域
    detailLink: '.for-detail', //　結果リストでの詳細遷移用クラス
    detailWrap: '#detail-items', // 詳細ラッパー
    linkBtn: '.link-btn', // リンク作成ボタン
    backBtn: '#books-backbtn' //戻るボタン
  };


  // 検索処理
  var searchBooks = function(btn, e) {
    var $form = $(selectors.form);
    var keyword = $form.find(selectors.iptKeyword).val();
    if (!keyword) keyword = 'NetCommons'; // forDEBUG
    NC3_APP.searchBooks({keyword: keyword},
        function(res) {
          // onsucceurlss
          setResultItems(res.items, function(page) {
            // paginatinon init
            $('#pagination').pagination({
              items: page,
              cssStyle: 'light-theme',
              onPageClick: function(pageNumber) {
                var page = '#page-' + pageNumber;
                $('.selection').hide();
                $(page).show();
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
    $el.css('top', '0');
    $el.css('background-color', '#fff');
    $el.css('min-height', '300px');
    $el.show();
    // 戻るボタンの表示
    $(selectors.backBtn).css('visibility', 'visible');
    currentDialog = 'result';
  };
  // 結果画面の非表示
  var hideResult = function() {
    $(selectors.panelResult).hide();
  };
  // 結果一覧内イベント処理
  var setResultEvent = function() {
    // リンククリック時の詳細表示
    $(selectors.detailLink).on('click', function() {
      var id = $(this).attr('data-bid');
      // get Detail
      if (id) {
        showDetail(id);
      }
    });
    $(selectors.panelResult)
    .find(selectors.linkBtn)
    .on('click', function() {
          var id = $(this).attr('data-bid');
          var title = $(this).attr('data-title');
          if (id) {
            writeLink(id, title);
          }
        });
  };
  //　詳細情報のセット
  var setDetailItems = function(data, callback) {
    $(selectors.detailWrap).empty();
    var domTxt = '';
    domTxt += booksTemplate.detailItem(data);
    $(selectors.detailWrap).append(domTxt);
    $.isFunction(callback) && callback();
  };
  // 詳細の表示
  var showDetail = function(id) {
    var book_id = (id) ? id : 'S2txMAEACAAJ'; // for DEBUG
    // 詳細情報の取得
    NC3_APP.getBookDetail(book_id,
        function(res) {
          // onsuccess
          setDetailItems(res, function() {
            $el = $(selectors.panelDetail);
            // tinymceが付与するスタイルの補正
            $el.css('top', '0');
            $el.css('background-color', '#fff');
            $el.css('min-height', '300px');
            $el.show();
            setDetailEvent();
            currentDialog = 'detail';
          });
        },
        function(res) {}
    );
  };
  // 詳細の非表示
  var hideDetail = function() {
    $(selectors.panelDetail).hide();
  };
  // 詳細内イベント
  var setDetailEvent = function() {
    $(selectors.panelDetail)
    .find(selectors.linkBtn)
    .on('click', function() {
          var id = $(this).attr('data-bid');
          var title = $(this).attr('data-title');
          if (id) {
            writeLink(id, title);
          }
        });

  };

  // wysiwygへの挿入
  var writeLink = function(id, title) {
    var url = 'https://books.google.co.jp/books?id=' + id;
    var dom = editor.dom.createHTML('a', {
      class: 'book-link',
      href: url,
      target: '_blank'
    }, title);
    editor.execCommand('mceInsertContent', false, dom);
    // dialog close
    top.tinymce.activeEditor.windowManager.close();
  };

  // 検索画面部品
  var bookFormItems = [
    // 検索アイテム
    // キーワード
    {
      type: 'label',
      text: 'キーワード',
      forId: 'keyword'
    },
    {
      id: 'keyword',
      type: 'textbox',
      name: 'keyword'
      // get key event
      // onKeyDown: function(e){
      //     console.log(e);
      //   if((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13)){
      //     e.preventDefault();
      //     e.stopPropagation();
      //     return false;
      //   }
      // }
    },
    // 書籍名
    // {
    //   type: 'label',
    //   text: '書籍名',
    //   forId: 'book_title'
    // },
    // {
    //   id: 'book_title',
    //   type: 'textbox',
    //   name: 'book_title',
    // },
    // 著者
    // {
    //   type: 'label',
    //   text: '著者',
    //   forId: 'writeran_author'
    // },
    // {
    //   id: 'writeran_author',
    //   type: 'textbox',
    //   name: 'writeran_author',
    // },
    //検索ボタン
    {
      type: 'button',
      text: 'Books Search',
      maxWidth: 100,
      align: 'center',
      onclick: searchBooks
    },
    // 検索結果表示パネル
    {
      type: 'panel',
      id: 'books-result',
      html: booksTemplate.resultWrapper(),
      minHeight: 400,
      maxHeight: 400
    },
    // 詳細表示パネル
    {
      type: 'panel',
      id: 'books-detail',
      html: booksTemplate.detailWrapper(),
      minHeight: 400,
      maxHeight: 400
    }
  ];

  // ダイアログ表示
  var showDialog = function() {
    win = editor.windowManager.open({
      title: '書籍検索',
      id: 'books-search',
      body: bookFormItems,
      width: 600,
      height: 400,
      buttons: [{
        text: 'Back',
        id: 'books-backbtn',
        style: 'visibility:hidden;',
        onclick: function(e) {
          if (currentDialog == 'result') {
            hideResult();
            $(selectors.backBtn).css('visibility', 'hidden');
            currentDialog = 'top';
          }
          else if (currentDialog == 'detail') {
            hideDetail();
            currentDialog = 'result';
          }
        }
      }],
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
  editor.addButton('nc3Books', {
    tooltip: 'Book Search',
    image: './img/title_icons/book.svg',
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
        '<ul class="books" id="result-items"></ul>' +
        '<div id="pagination"></div>' +
        '</div>';
  };

  // 一覧の各アイテムテンプレート
  var resultItem = function(d) {
    var _DATA_ = d;
    var _VINFO_ = d.volumeInfo;
    var title = '', authors = '', sThumb = '',
        isbn = '', description = '', publishedDate = '';
    //タイトル
    if (_VINFO_.title) title += _VINFO_.title + '&nbsp;';
    if (_VINFO_.subtitle) title += _VINFO_.subtitle;
    // 著者
    if (_VINFO_.authors) {
      for (var i = 0; i < _VINFO_.authors.length; i++) {
        authors += _VINFO_.authors[i] + '&nbsp';
      }
    }
    // 画像
    if (_VINFO_.imageLinks) {
      sThumb = _VINFO_.imageLinks.smallThumbnail;
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

    return '' +
        '<li class="item">' +
            '<div class="title"><a href="javascript:void(0);"' +
            ' class="for-detail" data-bid="' + _DATA_.id + '">' +
                title +
            '</a></div>' +
            '<div class="item-detail">' +
                '<div class="item-img">' +
                    '<a href="javascript:void(0);"' +
                    ' class="for-detail" data-bid="' + _DATA_.id + '">' +
                        '<img src="' + sThumb + '"' +
                        ' alt="" height="76" width="60">' +
                    '</a>' +
                '</div>' +
                '<div class="item-inner">' +
                    //'<div>https://books.google.com/books?isbn='+isbn+'</div>'+
                    '<div class="authors">' +
                        '<a href="" title="">' + authors + '</a>&nbsp;&nbsp;' +
                        publishedDate +
                    '</div>' +
                    '<div class="item-description">' + description + '</div>' +
                    '<div class="btn-area"><button class="btn link-btn"' +
                    ' data-bid="' + _DATA_.id + '" data-title="' + title +
                    '">リンク作成</button></div>' +
                '</div>' +
            '</div>' +
        '</li>';
  };

  // 詳細表示用のラッパー
  var detailWrapper = function() {
    return '' +
        '<div class="result-items-wrap">' +
            '<div class="books" id="detail-items"></div>' +
        '</div>';
  };

  // 詳細用テンプレート
  var detailItem = function(d) {
    var _DATA_ = d;
    var _VINFO_ = d.volumeInfo;
    var title = '', authors = '', sThumb = '', isbn = '',
        description = '', previewLink;
    //タイトル
    if (_VINFO_.title) title += _VINFO_.title + '&nbsp;';
    if (_VINFO_.subtitle) title += _VINFO_.subtitle;
    // 詳細飛び先
    if (_VINFO_.previewLink) {
      previewLink = _VINFO_.previewLink;
    }
    // 著者
    if (_VINFO_.authors) {
      for (var i = 0; i < _VINFO_.authors.length; i++) {
        authors += _VINFO_.authors[i] + '&nbsp';
      }
    }
    // 画像
    if (_VINFO_.imageLinks) {
      sThumb = _VINFO_.imageLinks.smallThumbnail;
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
    if (_VINFO_.description) {
      description = _VINFO_.description;
    }

    return '' +
        '<div class="item detail">' +
            '<div class="title"><a href="' + previewLink +
            '" target="_blank">' +
                title +
            '</a></div>' +
            '<div class="item-detail">' +
                '<div class="item-img">' +
                    '<img src="' + sThumb + '" alt="" height="76" width="60">' +
                '</div>' +
                '<div class="item-inner">' +
                    //'<div>https://books.google.com/books?isbn='+isbn+'</div>'+
                    '<div class="authors">' +
                        '<a href="" title="">' + authors +
                        '</a>&nbsp;&nbsp;' + publishedDate +
                    '</div>' +
                    '<div class="item-description">' + description + '</div>' +
                    '<div class="btn-area"><button class="btn link-btn"' +
                    ' data-bid="' + _DATA_.id + '"' +
                    ' data-title="' + title + '">リンク作成</button></div>' +
                '</div>' +
            '</div>' +
        '</div>';
  };
  // ページャ用
  var selection = function(num) {
    return '<div class="selection" id="page-' + num + '"></div>';
  };

  return {
    resultWrapper: resultWrapper,
    resultItem: resultItem,
    detailWrapper: detailWrapper,
    detailItem: detailItem,
    selection: selection
  };
})();
