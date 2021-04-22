# pukiwiki-plugin-sortable_table

PukiWiki用ソートテーブル（表）プラグイン

- 暫定公開版です（[PukiWiki1.5.2](https://pukiwiki.osdn.jp/?PukiWiki/Download/1.5.2)及び[PukiWiki1.5.3](https://pukiwiki.osdn.jp/?PukiWiki/Download/1.5.3)で動作確認済）
- [PukiWiki](https://ja.wikipedia.org/wiki/PukiWiki)のテーブル（表）をソート可能なテーブルにするプラグインです（フィルタ処理も可能）
- 設置と使い方に関しては自サイトの記事「[PukiWiki用ソートテーブル（表）プラグインを導入する！](https://dajya-ranger.com/pukiwiki/sortable-table-plugin/)」及び「[PukiWiki用ソートテーブル（表）プラグインをバージョンアップしてみた！](https://dajya-ranger.com/pukiwiki/sortable-table-plugin-verup/)」を参照して下さい
- **Ver0.7.2**からの変更点は次の通り
	- 任意のカラムをソート対象にしない仕様を追加
		- テーブルヘッダが「日付・長さ・尺貫法・メートル法」で、「尺貫法」（ソートキー「Number」）のみソート対象にしない場合は次の通りに設定する（カラムのソートキーを省略する）
		- #sortable_table(Date|String||Number, ･･･
	- ヘッダ行カラー指定がない＆自動採番指定がない場合に表が崩れるバグを対処
	- テーブル定義行中にコメント行があった場合に自動採番値が狂うバグを対処
- Ver**0.7.1**からの変更点は次の通り
	- オプションにヘッダ行折返し禁止指定「nowrap」を追加
		- #sortable_table([Number|Date|String][,autonum[=1]][,numstep=1][,numname=No][,head=#f0f0f0][,odd=#ffffff][,even=#f6f9fb][,filter][,width=100][,nowrap]){{
- すでに**Ver0.7.0**以上のバージョンを導入している場合は、sortable_table.inc.php（pluginフォルダ）とsortable-table.js（skinフォルダ）を置き換えるだけでバージョンアップが可能です（新規導入の場合や、**Ver0.7.0**未満のバージョンを導入している場合は、上記自サイトの記事を参照して下さい）
