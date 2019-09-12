<?php

/*
 * sortable_table.inc.php
 *
 * ソートテーブルプラグイン
 *
 * 作成者：オヤジ戦隊ダジャレンジャー(Twitter:@dajya_ranger_)
 * サイト：SEの良心（https://dajya-ranger.com/）
 *
 * ベース：sortabletable.inc.php 0.3 '06.11.18 taru
 * 　　　　http://taru.s223.xrea.com/index.php?diary%2F2006-11-08
 *
 * 2019/08/29 sortable-table.jsでソート種別の日付指定部分のロジックを修正
 * 2019/08/31 引数にnumstep及びnumnameを追加・実装
 *
 * Version 0.5.2
 * Date    2019/08/27
 * Update  2019/08/31
 * License The same as PukiWiki
 *
 */

function plugin_sortable_table_params($args) {
	// ソートキー引数チェック用
	$sort_kinds = array(
		'Number'	=> '',		// 数値
		'Date'		=> '',		// 日付
		'String'	=> ''		// 文字列
	);

	// 引数チェック＆パラメータ設定用
	$params = array(
		'filter'	=> FALSE,	// フィルタ処理
		'autonum'	=> -999999,	// 自動採番No
		'numstep'	=> 0,		// 採番増分
		'numname'	=> '',		// 自動採番列名称
		'head'		=> '',		// ヘッダ行カラー
		'odd'		=> '',		// 奇数行カラー
		'even'		=> '',		// 偶数行カラー
		'_error'	=> ''		// エラー内容
	);

	// 引数チェック
	$sort_arg = array_shift($args);
	if ( isset($sort_arg[0]) && ($sort_arg[0] != '') ) {
		$sort_keys = explode("|", $sort_arg);
		foreach ($sort_keys as $sort_key) {
			if (! (isset($sort_kinds[$sort_key])) ) {
				// 引数のソート種別が一致しない場合
				$params['_error'] = '引数の指定にエラーがあります: ' .  $sort_key;
				return $params;
			}
		}
	}
	if ( isset($args[0]) && ($args[0] != '') ) {
		// 第2引数以降が存在する
		foreach ($args as $arg) {
			$val = explode("=", $arg);
			if (! (isset($params[$val[0]])) ) {
				// オプション名と一致しない場合
				$params['_error'] = '引数の指定にエラーがあります: ' .  $val[0];
				return $params;
			}
		}
	}

	// 引数セット
	if (! empty($args)) {
		foreach ($args as $arg) {
			$val = explode("=", $arg);
			if (isset($params[$val[0]])) {
				// 指定された引数がオプション名と一致する場合
				switch ($val[0]) {
				case 'filter':
					$params[$val[0]] = TRUE;
					break;
				case 'autonum':
					if (isset($val[1]) && ($val[1] != '')) {
						// 開始番号が指定されている場合
						$params[$val[0]] = intval($val[1]);
					} else {
						// 開始番号が指定されている場合
						$params[$val[0]] = 1;
					}
					// 自動採番の場合は、増分と列名称の初期値をセットしておく
					$params['numstep'] = 1;
					$params['numname'] = 'No';
					break;
				case 'numstep':
				case 'numname':
				case 'head':
				case 'odd':
				case 'even':
					if (isset($val[1]) && ($val[1] != '')) {
						// 採番増分・自動採番列名称・カラー指定がある場合
						$params[$val[0]] = $val[1];
						break;
					} else {
						// 採番増分・自動採番列名称・カラー指定がある場合
						$params['_error'] = '引数の指定にエラーがあります: ' .  $arg;
						return $params;
					}
				}
			}
		}
	}

	return $params;
}

function sortable_table_main($table_id, $table_html, $sort_key, $params) {
	global $script;
	static $st_count = 0;

	if ($st_count == 0) {
		// ページ初回出力のみJavaScript定義コード出力
		global $head_tags;
		$head_tags[] = ' <script type="text/javascript" charset="utf-8" src="' . SKIN_DIR . 'sortable-table.js"></script>';
		$head_tags[] = ' <script type="text/javascript" charset="utf-8" src="' . SKIN_DIR . 'filterable-table.js"></script>';
	}
	$st_count++;

	$table_html = preg_replace('/<table class="style_table"/', '<table id="' . $table_id . '" class="style_table"', $table_html);

	if ($params['filter']) {
		// フィルタ処理が有効の場合
		$js = <<<EOD
<script type="text/javascript">
<!-- <![CDATA[
var tableid = document.getElementById('{$table_id}');
var st = new SortableTable(tableid, [{$sort_key}]);
var ft = new FilterableTable(tableid);
//]]>-->
</script>
EOD;
	} else {
		// フィルタ処理が無効の場合
		$js = <<<EOD
<script type="text/javascript">
<!-- <![CDATA[
var st = new SortableTable(document.getElementById('{$table_id}'),[{$sort_key}]);
//]]>-->
</script>
EOD;
	}

	return $table_html . $js;
}

function plugin_sortable_table_convert() {
	static $number = array();

	$page = isset($vars['page']) ? $vars['page'] : '';

	if (! isset($number[$page])) $number[$page] = 1;
	$count = $number[$page]++;

	$args = func_get_args();

	$param_source = array_pop($args);
	$param_source = str_replace("\r", "\n", str_replace("\r\n", "\n", $param_source));

	// パラメータセット
	$params = plugin_sortable_table_params($args);
	if (isset($params['_error']) && $params['_error'] != '') {
		// パラメータエラーがある場合
		return '#sortable_table: ' . $params['_error'];
	}

	// ソートキーセット
	if (isset($args[0]) && $args[0] != '') {
		// 引数にソートキー定義カラム情報？がある場合
		$sort_cols = explode("|", $args[0]);
		if ($params['autonum'] != -999999) {
			// 自動採番Noあり
			$sort_key = "'Number','" . array_shift($sort_cols) . "'";
		} else {
			// 自動採番Noなし
			$sort_key = "'" . array_shift($sort_cols) . "'";
		}
		foreach ($sort_cols as $sort_col) {
			$sort_key .= ",'" . $sort_col . "'";
		}
	} else {
		// 引数が省略されている（ソートキー定義カラム情報がない）場合
		$table_rows = explode("\n", $param_source);
		foreach ($table_rows as $table_row) {
			if ( preg_match('/^\|(.+)\|([hHfFcC]?)$/', $table_row, $match) ){
				// テーブル定義行（ヘッダ・フッタ・カラム定義行）の場合
				$table_cells = explode("|", $match[1]);
				$cols = count($table_cells);
				break;
			}
		}
		$sort_key = join(',', array_fill(0, $cols, "'String'"));
	}

	// 自動採番No・行カラー追加
	$row = 0;
	$autonum = $params['autonum'];
	$table_source = array();
	$table_rows = explode("\n", $param_source);
	foreach ($table_rows as $table_row) {
		preg_match('/^\|(.+)\|([hHfFcC]?)$/', $table_row, $match);
		if ($match[2] != "") {
			// ヘッダ・フッタ・カラム定義行の場合
			if (strtolower($match[2]) == "h") {
				// ヘッダ行の場合
				if ($params['head'] != "") {
					// ヘッダ行カラー指定がある
					if ($params['autonum'] != -999999) {
						// 自動採番No追加
						$cell = '|BGCOLOR(' . $params['head'] . '):~' . $params['numname'];
					} else {
						// 自動採番Noなし
						$cell = '';
					}
					$cell_color = 'BGCOLOR(' . $params['head'] . '):';
				} else {
					// ヘッダ行カラー指定がない
					if ($params['autonum'] != -1) {
						// 自動採番No追加
						$cell = '|~'  . $params['numname'];
					} else {
						// 自動採番Noなし
						$cell = '';
					}
					$cell_color = '';
				}
			} else if (strtolower($match[2]) == "c") {
				// カラム定義行の場合
				if ($params['autonum'] != -999999) {
					// 自動採番No追加
					$cell = '|RIGHT:42';
				} else {
					// 自動採番Noなし
					$cell = '';
				}
				$cell_color = '';
			} else {
				// フッタ行の場合
				$cell_color = '';
				if ($params['autonum'] != -999999) {
					// 自動採番No追加
					$cell = '|';
				} else {
					// 自動採番Noなし
					$cell = '';
				}
			}
			$sufix = $match[2];
		} else {
			// 通常行の場合
			$cell_color = '';
			$row = $row + 1;
			if ( (($row % 2) != 0) && ($params['odd'] != "") ) {
				// 奇数行で奇数行カラー指定がある場合
				$cell_color = 'BGCOLOR(' . $params['odd'] . '):';
			}
			if ( (($row % 2) == 0) && ($params['even'] != "") ) {
				// 偶数行で偶数行カラー指定がある場合
				$cell_color = 'BGCOLOR(' . $params['even'] . '):';
			}
			if ($params['autonum'] != -999999) {
				// 自動採番No追加
				$cell = '|' . $cell_color . 'RIGHT:' . strval($autonum);
				$autonum = $autonum + $params['numstep'];
			} else {
				// 自動採番Noなし
				$cell = '';
			}
			$sufix = '';
		}

		// テーブル（1行）編集
		$table_cells = explode("|", $match[1]);
		if ( isset($table_cells[0]) && ($table_cells[0] != '') ) {
			// テーブルデータが存在する場合のみ編集
			foreach ($table_cells as $table_cell) {
				$cell = $cell . '|' . $cell_color . $table_cell;
			}
			$table_source[] = $cell . '|' . $sufix;
		}
	}

	// テーブルID（ユニークID）セット
	$table_id = 'sortable_table' . $count;
	// HTML変換
	$table_html = convert_html($table_source);
	// HTML＋JavaScriptコード編集
	$body = sortable_table_main($table_id, $table_html, $sort_key, $params);

	return $body;
}

?>
