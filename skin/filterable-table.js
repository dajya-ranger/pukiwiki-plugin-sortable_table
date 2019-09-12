/*----------------------------------------------------------------------------\
|                             Filterable Table 0.03                           |
|-----------------------------------------------------------------------------|
| original code : http://www.codeproject.com/jscript/filter.asp (by WoutL)    |
|-----------------------------------------------------------------------------|
| 2005-10-15 | multiple tHead (and related tBody) rows support panda@arino.jp |
| 2006-09-05 | cleanup / bugfix / textbox / safari support     panda@arino.jp |
|-----------------------------------------------------------------------------|
| Created 2005-07-27 | All changes are in the log above. | Updated 2006-09-05 |
|-----------------------------------------------------------------------------|
| Modify by オヤジ戦隊ダジャレンジャー(Twitter:@dajya_ranger_)                |
|           SEの良心（https://dajya-ranger.com/）                             |
|-----------------------------------------------------------------------------|
| 2019/08/21 | ファイル名を filterable-table.js に変更                        |
| 2019/08/26 | 「Enable Filter」を「フィルタ処理」に変更                      |
\----------------------------------------------------------------------------*/
function FilterableTable(oTable)
{
	while (oTable.tagName.toUpperCase() != 'TABLE') {
		oTable = oTable.parentNode;
	}
	this.table = oTable;
	this.tHead = oTable.tHead;
	this.tBody = oTable.tBodies[0];
	this.document = oTable.ownerDocument || oTable.document;
	var oThis = this;
	this._optChange = function (e) {
		oThis.filter(e);
	}
	this._toggle = function (e) {
		oThis.toggleFilter(e);
	}
	this.filterEnabled = false;

	// enabler
	var form = document.createElement('FORM');
	form.action = '#';
	var input = document.createElement('INPUT');
	input.type = 'checkbox';
	if (typeof input.addEventListener != 'undefined') {
		input.addEventListener('click', this._toggle, false);
	} else if (typeof input.attachEvent != 'undefined') {
		input.attachEvent('onclick', this._toggle);
	} else {
		input.onclick = this._toggle;
	}
	var text = document.createElement('LABEL');
	text.appendChild(input);
	// 2019/08/26 文言変更
	//text.appendChild(document.createTextNode('Enable Filter'));
	text.appendChild(document.createTextNode('フィルタ処理'));
	form.appendChild(text);
	oTable.parentNode.insertBefore(form, oTable);
}
FilterableTable.gecko = navigator.product == "Gecko";
FilterableTable.safari = (navigator.userAgent.indexOf("Safari") != -1);
FilterableTable.msie = /msie/i.test(navigator.userAgent);
FilterableTable.prototype.toggleFilter = function (e)
{
	var sel = e.target || e.srcElement;
	sel.checked
		? this.attachFilter()
		: this.detachFilter();
}
FilterableTable.prototype.detachFilter = function ()
{
	if (! this.filterEnabled) { return; }

	// Remove the filter
	this.showAll();
	for (var row = 0; row < this.filterRows.length; row++) {
		this.tHead.removeChild(this.filterRows[row]);
	}
	this.filterEnabled = false;
}
FilterableTable.prototype.attachFilter = function ()
{
	if (this.filterEnabled) { return; }

	// Check if the table has any rows. If not, do nothing
	if (this.tBody.rows.length == 0) { return; }
	if (this.table.style.display == 'none') { return; }

	// Insert the filterrow and add cells whith drowdowns.
	this.filterRows = new Array();
	this.step = this.tHead.rows.length;
	for (var i = 0; i < this.step; i++) {
		this.filterRows[i] = this.tHead.insertRow(this.tHead.rows.length);
	}
	this.filterObjects = new Array();
	for (var j = 0; j < this.step; j++) {
		var row = new Array();
		for (var i = 0; i < this.tHead.rows[j].cells.length; i++) {
			var cell = this.tHead.rows[j].cells[i];
			var c = document.createElement('TH');
			c.rowSpan = cell.rowSpan;
			c.colSpan = cell.colSpan;
			this.filterRows[j].appendChild(c);

			var text = document.createElement('INPUT');
			text.className = 'filter-box';
			text.type = 'text';

			if (typeof text.addEventListener != 'undefined') {
				text.addEventListener('change', this._optChange, false);
				text.addEventListener('keyup', this._optChange, false);
			} else if (typeof text.attachEvent != 'undefined') {
				text.attachEvent('onchange', this._optChange);
				text.attachEvent('onkeyup', this._optChange);
			} else {
				text.onchange = this._optChange;
				text.onkeyup = this._optChange;
			}

			c.appendChild(text);
			c.appendChild(document.createElement('BR'));

			var opt = document.createElement('SELECT');
			opt.size = 5;
			opt.multiple = true;

			if (typeof opt.addEventListener != 'undefined') {
				opt.addEventListener('change', this._optChange, false);
			} else if (typeof opt.attachEvent != 'undefined') {
				opt.attachEvent('onchange', this._optChange);
			} else {
				opt.onchange = this._optChange;
			}

			c.appendChild(opt);

			row[i] = {columnIndex: i, rowIndex: j, opt: opt, text: text, filter: {}, regexp: false, enable: false};
		}
		this.filterObjects[j] = row;
	}
	// Fill the filters
	this.fillFilters();
	this.filterEnabled = true;
}
// Checks if a column is filtered
FilterableTable.prototype.inFilter = function (row, column)
{
	return this.filterObjects[row][column].enable;
}

// Fills the filters for columns which are not fiiltered
FilterableTable.prototype.fillFilters = function ()
{
	for (var row = 0; row < this.filterRows.length; row++) {
		for (var column = 0; column < this.filterRows[row].cells.length; column++) {
			if (! this.inFilter(row, column)) {
				this.buildFilter(row, column, {'(all)':true});
			}
		}
	}
}

// Fills the columns dropdown box.
// setValue is the value which the dropdownbox should have one filled.
// If the value is not suplied, the first item is selected
FilterableTable.prototype.buildFilter = function (rowIndex, columnIndex, setValue)
{
	// Get a reference to the selectbox.
	var filterObject = this.filterObjects[rowIndex][columnIndex];
	var opt = filterObject.opt;

	// remove all existing items
	while (opt.length > 0) {
		opt.remove(0);
	}
	opt.options[0] = new Option('(all)', '(all)');
//	opt.options.add(new Option('(all)', '(all)'), 0);
	if (setValue['(all)']) { opt.options[0].selected = true; }

	var values = new Array();

	// put all relevant strings in the values array.
	for (var i = 0; i < this.tBody.rows.length; i += this.step) {
		var r = this.tBody.rows[i + rowIndex];
		if (r.style.display != 'none' && r.className != 'noFilter') {
			values.push(this.getInnerText(r.cells[columnIndex]).toLowerCase());
		}
	}
	values.sort();

	//add each unique string to the selectbox
	var value = '';
	for (var i = 0; i < values.length; i++) {
		if (values[i].toLowerCase() != value) {
			value = values[i].toLowerCase();
			var option = new Option(values[i], value);
			if (setValue[value]) { option.selected = true; }
			opt.options[opt.options.length] = option;
//			opt.options.add(option);
		}
	}
}
FilterableTable.prototype.getInnerText = function (oNode)
{
	var s = '';
	var cs = oNode.childNodes;
	var l = cs.length;
	for (var i = 0; i < l; i++) {
		if (cs[i].style && cs[i].style.display == 'none') { continue; }
		switch (cs[i].nodeType) {
			case 1: //ELEMENT_NODE
				s += this.getInnerText(cs[i]);
				break;
			case 3:	//TEXT_NODE
				s += cs[i].nodeValue;
				break;
		}
	}
	return s;
}
FilterableTable.getCellIndex = function (cell) {
	var cells = cell.parentNode.childNodes;
	for (var column = 0; cells[column] != cell && column < cells.length; column++)
		;
	return column;
}
// This function is called when a dropdown box changes
FilterableTable.prototype.filter = function (e) {
	var sel = e.target || e.srcElement;
	// The column number of the column which should be filtered
	var columnIndex = FilterableTable.safari
		? FilterableTable.getCellIndex(sel.parentNode)
		: sel.parentNode.cellIndex;
	var rowIndex = sel.parentNode.parentNode.rowIndex - this.step;
	var filterObject = this.filterObjects[rowIndex][columnIndex];

	var filterText  = {};
	var regexp = false;
	if (sel.options) {
		for (var i = 0; i < sel.options.length; i++) {
			filterText[sel.options[i].value] = sel.options[i].selected;
			if (i > 0 && sel.options[i].selected) {
				filterText['(all)'] = false;
			}
		}
		filterObject.text.value = '';
	} else {
		if (sel.value == '') {
			filterText['(all)'] = true;
		} else {
			try {
				regexp = new RegExp(sel.value, 'i');
			} catch (e) {
				return;
			}
			filterText[sel.value] = true;
		}
	}

	filterObject.enable = (! filterText['(all)']);
	filterObject.filter = filterText;
	filterObject.regexp = regexp;

	// first set all rows to be displayed
	this.showAll();

	// the filter ou the right rows.
	var hideRows = {};
	for (var rowIndex in this.filterObjects) {
		var r = parseInt(rowIndex);
		for (var columnIndex in this.filterObjects[rowIndex]) {
			var n = parseInt(columnIndex);
			var obj = this.filterObjects[rowIndex][columnIndex];
			if (! obj.enable) { continue; }
			// First fill the select box for this column
			this.buildFilter(obj.rowIndex, obj.columnIndex, obj.filter);
			// Apply the filter
			for (var i = 0; i < this.tBody.rows.length; i += this.step) {
				if (hideRows[i]) { continue; }
				var row = this.tBody.rows[i + r];
				var cell = row.cells[n];
				var text = this.getInnerText(cell).toLowerCase();
				if (row.className != 'noFilter') {
					if (obj.regexp) {
						if (! text.match(obj.regexp)) {
							hideRows[i] = true;
						}
					} else {
						if (! obj.filter[text]) {
							hideRows[i] = true;
						}
					}
				}
			}
		}
	}

	for (var i in hideRows) {
		var r = parseInt(i);
		for (var j = 0; j < this.step; j++) {
			this.tBody.rows[r + j].style.display = 'none';
		}
	}
	// Fill the dropdownboxes for the remaining columns.
	this.fillFilters();
}

FilterableTable.prototype.showAll = function () {
	for (var i = 0; i < this.tBody.rows.length; i++) {
		this.tBody.rows[i].style.display = '';
	}
}
