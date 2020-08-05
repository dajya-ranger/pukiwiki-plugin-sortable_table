# pukiwiki-plugin-sortable_table

PukiWiki用ソートテーブル（表）プラグイン

- 暫定公開版です（[PukiWiki1.5.2](https://pukiwiki.osdn.jp/?PukiWiki/Download/1.5.2)及び[PukiWiki1.5.3](https://pukiwiki.osdn.jp/?PukiWiki/Download/1.5.3)で動作確認済）
- [PukiWiki](https://ja.wikipedia.org/wiki/PukiWiki)のテーブル（表）をソート可能なテーブルにするプラグインです（フィルタ処理も可能）
- 設置と使い方に関しては自サイトの記事「[PukiWiki用ソートテーブル（表）プラグインをバージョンアップしてみた！](https://dajya-ranger.com/pukiwiki/sortable-table-plugin-verup/)」及び「[PukiWiki用ソートテーブル（表）プラグインを導入する！]」(https://dajya-ranger.com/pukiwiki/sortable-table-plugin/)を参照して下さい
- Ver0.7.1からの変更点は次の通り
	- オプションにヘッダ行折返し禁止指定「nowrap」を追加
		- #sortable_table([Number|Date|String],[autonum[=1]],[numstep=1],[numname=No],[head=#f0f0f0],[odd=#ffffff],[even=#f6f9fb],[filter],[width=100],[nowrap]){{
- すでにVer0.7.0またはVer0.7.1を導入済みの場合は、sortable_table.inc.php（pluginフォルダ）とsortable-table.js（skinフォルダ）を置き換えるだけでバージョンアップが可能です（新規導入の場合や、Ver0.7.0未満のバージョンを導入している場合は、上記自サイトの記事を参照して下さい）
