# pukiwiki-plugin-sortable_table

PukiWiki用ソートテーブル（表）プラグイン

- 暫定公開版です（[PukiWiki1.5.2](https://pukiwiki.osdn.jp/?PukiWiki/Download/1.5.2)及び[PukiWiki1.5.3](https://pukiwiki.osdn.jp/?PukiWiki/Download/1.5.3)で動作確認済）
- [PukiWiki](https://ja.wikipedia.org/wiki/PukiWiki)のテーブル（表）をソート可能なテーブルにするプラグインです（フィルタ処理も可能）
- 設置と使い方に関しては自サイトの記事「[PukiWiki用ソートテーブル（表）プラグインをバージョンアップしてみた！](https://dajya-ranger.com/pukiwiki/sortable-table-plugin-verup/)」を参照して下さい
- Ver0.6.0からの変更点は次の通り
	- テーブルの奇数行・偶数行の背景色をJavaScriptへ移行（ソートさせても正しく奇数行・偶数行の背景色が設定される）
	- ヘッダ行の文字選択を不可に修正
	- 動的にCSSが完全に置き換わらない不具合を修正（CSS及びソート状態表示画像も修正）
- バージョンアップに際し、アーカイブ内容の画像・CSS・JavaScript・PHPプラグインを置き換えるのみで、従来ページの修正等はありません（新規に当プラグインを導入する場合も、アーカイブがフルパッケージとなってます）
