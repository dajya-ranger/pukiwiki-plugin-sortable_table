# pukiwiki-plugin-sortable_table

PukiWiki用ソートテーブル（表）プラグイン

- 暫定公開版です（[PukiWiki1.5.2](https://pukiwiki.osdn.jp/?PukiWiki/Download/1.5.2)及び[PukiWiki1.5.3](https://pukiwiki.osdn.jp/?PukiWiki/Download/1.5.3)で動作確認済）
- [PukiWiki](https://ja.wikipedia.org/wiki/PukiWiki)のテーブル（表）をソート可能なテーブルにするプラグインです（フィルタ処理も可能）
- 設置と使い方に関しては自サイトの記事「[PukiWiki用ソートテーブル（表）プラグインを導入する！](https://dajya-ranger.com/pukiwiki/sortable-table-plugin/)」を参照して下さい
- Ver0.5.2からの変更点は次の通り
	- オプションにテーブル幅（％指定）widthを追加
		- #sortable_table([Number|Date|String],[autonum[=1]],[numstep=1],[numname=No],[head=#f0f0f0],[odd=#ffffff],[even=#f6f9fb],[filter],[width=100]){{
