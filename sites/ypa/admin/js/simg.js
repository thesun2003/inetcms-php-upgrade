var total_checked = 0;

function flip(img) {
	opener.document.cont.simg.value = img;
	self.close();
}

function renamefile(fname, hash) {
	var s = prompt('������������� ' + fname + ' �:');
	if (s) window.location.href = '?action=rename&hash=' + hash + '&to=' + s;
}

function update_count(checkbox) {
	if (checkbox)
	if (checkbox.checked) total_checked++; else total_checked--;
}

function confirmmassdelete() {

	if (0 == total_checked) return false;

	var x = total_checked % 10;
	var s = '';

	switch (x) {
		case 0:
		case 5:
		case 6:
		case 7:
		case 8:
		case 9:
		s = '������';
		break;

		case 1:
		s = '����';
		break;

		case 2:
		case 3:
		case 4:
		s = '�����';
		break;
	}

	var result = confirm("������������� ������� " + total_checked + " " + s + "?");

	if (result) document.forms.massdelete.submit(); /*else alert("�� ������� �� ������ ����� ��� ��������");*/

	return result;
}
