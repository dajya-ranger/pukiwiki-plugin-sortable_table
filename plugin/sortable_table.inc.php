<?php

/**
 * sortable_table.inc.php
 *
 * ソートテーブルプラグイン
 *
 * @author		オヤジ戦隊ダジャレンジャー <red@dajya-ranger.com>
 * @copyright	Copyright © 2019-2020, dajya-ranger.com
 * @link		https://dajya-ranger.com/pukiwiki/sortable-table-plugin-verup/
 * @example		@linkの内容を参照
 * @license		Apache License 2.0
 * @version		0.7.1
 * @since 		0.7.1 2020/08/01 HTMLのscriptタグ出力からCDATAセクションを削除
 * @since 		0.7.0 2020/07/30 JavaScript定義コードを必ず出力し、テーブル背景色をJavaScriptへ移行（JS側の不具合も対応）
 * @since 		0.6.0 2020/05/21 引数にwidthを追加・実装
 * @since 		0.5.2 2019/08/31 引数にnumstep及びnumnameを追加・実装
 * @since 		0.5.1 2019/08/29 sortable-table.jsでソート種別の日付指定部分のロジックを修正
 * @since 		0.5.0 2019/08/27 暫定初公開（独自拡張・バグFix等）
 * @since 		0.3.0 2006/11/18 sortabletable.inc.php 0.3（ベースプログラム）
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
		'width'		=> -1,		// テーブル幅（％）
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
				$params['_error'] = '引数の指定にエラーがあります: ' . $sort_key;
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
				$params['_error'] = '引数の指定にエラーがあります: ' . $val[0];
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
						// 開始番号が指定されていない場合
						$params[$val[0]] = 1;
					}
					// 自動採番の場合は、増分と列名称の初期値をセットしておく
					$params['numstep'] = 1;
					$params['numname'] = 'No';
					break;
				case 'width':
				case 'numstep':
				case 'numname':
				case 'head':
				case 'odd':
				case 'even':
					if (isset($val[1]) && ($val[1] != '')) {
						// テーブル幅・採番増分・自動採番列名称・カラー指定がある場合
						$params[$val[0]] = $val[1];
						break;
					} else {
						// テーブル幅・採番増分・自動採番列名称・カラー指定がない場合
						$params['_error'] = '引数の指定にエラーがあります: ' . $arg;
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
	global $head_tags;

	// JavaScript定義コードを必ず出力するように修正 v0.7.0
	$head_tags[] = ' <script type="text/javascript" src="' . SKIN_DIR . 'sortable-table.js"></script>';
	$head_tags[] = ' <script type="text/javascript" src="' . SKIN_DIR . 'filterable-table.js"></script>';

	// テーブル幅指定 v0.6.0
	if ($params['width'] > 0) {
		// テーブル幅（％）指定がある場合
		$table_html = preg_replace('/<table class="style_table"/', '<table id="' . $table_id . '" class="style_table" width="' . $params['width'] . '%"', $table_html);
	} else {
		// テーブル幅（％）指定がない場合
		$table_html = preg_replace('/<table class="style_table"/', '<table id="' . $table_id . '" class="style_table"', $table_html);
	}

	// テーブル背景色をJavaScriptへ移行 v0.7.0
	$odd_color = $params['odd'];
	$even_color = $params['even'];
	$back_color = "'$odd_color','$even_color'";

	// フィルタ指定
	if ($params['filter']) {
		// フィルタ処理が有効の場合（テーブル背景色をJavaScriptへ移行 v0.7.0）
		// CDATAセクション出力削除 v0.7.1
		$js = <<<EOD
<script type="text/javascript">
	var tableid = document.getElementById('{$table_id}');
	var st = new SortableTable(tableid,[{$sort_key}],[{$back_color}]);
	var ft = new FilterableTable(tableid);
</script>
EOD;
	} else {
		// フィルタ処理が無効の場合（テーブル背景色をJavaScriptへ移行 v0.7.0）
		// CDATAセクション出力削除 v0.7.1
		$js = <<<EOD
<script type="text/javascript">
	var st = new SortableTable(document.getElementById('{$table_id}'),[{$sort_key}],[{$back_color}]);
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
			if ($params['autonum'] != -999999) {
				// 自動採番No追加
				//$cell = '|' . $cell_color . 'RIGHT:' . strval($autonum);
				$cell = '|' . 'RIGHT:' . strval($autonum);
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
