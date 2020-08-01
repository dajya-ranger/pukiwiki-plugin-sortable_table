# pukiwiki-plugin-sortable_table

PukiWiki用ソートテーブル（表）プラグイン

- 暫定公開版です（[PukiWiki1.5.2](https://pukiwiki.osdn.jp/?PukiWiki/Download/1.5.2)及び[PukiWiki1.5.3](https://pukiwiki.osdn.jp/?PukiWiki/Download/1.5.3)で動作確認済）
- [PukiWiki](https://ja.wikipedia.org/wiki/PukiWiki)のテーブル（表）をソート可能なテーブルにするプラグインです（フィルタ処理も可能）
- 設置と使い方に関しては自サイトの記事「[PukiWiki用ソートテーブル（表）プラグインをバージョンアップしてみた！](https://dajya-ranger.com/pukiwiki/sortable-table-plugin-verup/)」を参照して下さい
- Ver0.7.0からの変更点は次の通り
	- 本プラグイン（PHP）でJavaScriptコード出力時にCDATAセクションを削除
- すでにVer0.7.0を導入済みの場合は、sortable_table.inc.php（pluginフォルダ）とsortable-table.js（skinフォルダ）を置き換えるだけでバージョンアップが可能です（新規導入の場合や、Ver0.7.0未満のバージョンを導入している場合は、上記自サイトの記事を参照して下さい）
