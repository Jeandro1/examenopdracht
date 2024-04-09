function openFormMedewerkers(rowId) {
    var row = document.getElementById('voornaam_' + rowId).parentNode.parentNode;
    var inputs = row.querySelectorAll('input[type=text], select');
    var spans = row.querySelectorAll('span');

    for (var i = 0; i < inputs.length; i++) {
        inputs[i].style.display = 'block';
        spans[i].style.display = 'none';
    }

    document.getElementById('saveButton_' + rowId).style.display = 'inline-block';
    document.getElementById('deleteButton_' + rowId).style.display = 'inline-block';
    document.getElementById('aanpassenButton_' + rowId).style.display = 'none';
}

function saveChangesMedewerkers(rowId) {

    var row = document.getElementById('voornaam_' + rowId).parentNode.parentNode;
    var inputs = row.querySelectorAll('input[type=text], select');
    var spans = row.querySelectorAll('span');

    for (var i = 0; i < inputs.length; i++) {
        inputs[i].style.display = 'none';
        spans[i].style.display = 'block';
        spans[i].textContent = inputs[i].value;
    }

    document.getElementById('saveButton_' + rowId).style.display = 'none';
    document.getElementById('deleteButton_' + rowId).style.display = 'none';
}

function openFormLeveranciers(rowId) {
    var row = document.getElementById('bedrijfsnaam_' + rowId).parentNode.parentNode;
    var inputs = row.querySelectorAll('input[type=text], input[type=datetime-local]');
    var spans = row.querySelectorAll('span');

    for (var i = 0; i < inputs.length; i++) {
        inputs[i].style.display = 'block';
        spans[i].style.display = 'none';
    }

    document.getElementById('saveButton_' + rowId).style.display = 'inline-block';
    document.getElementById('deleteButton_' + rowId).style.display = 'inline-block';
}

function saveChangesLeveranciers(rowId) {

    var row = document.getElementById('bedrijfsnaam_' + rowId).parentNode.parentNode;
    var inputs = row.querySelectorAll('input[type=text], input[type=datetime-local]');
    var spans = row.querySelectorAll('span');

    for (var i = 0; i < inputs.length; i++) {
        inputs[i].style.display = 'none';
        spans[i].style.display = 'block';
        if (inputs[i].type === 'datetime-local') {
            spans[i].textContent = new Date(inputs[i].value).toLocaleString();
        } else {
            spans[i].textContent = inputs[i].value;
        }
    }

    document.getElementById('saveButton_' + rowId).style.display = 'none';
    document.getElementById('deleteButton_' + rowId).style.display = 'none';
}

function openFormProduct(rowId) {
    var row = document.getElementById('product_' + rowId).parentNode.parentNode;
    var inputs = row.querySelectorAll('input[type=text], select');
    var spans = row.querySelectorAll('span');

    for (var i = 0; i < inputs.length; i++) {
        inputs[i].style.display = 'block';
        spans[i].style.display = 'none';
    }

    document.getElementById('saveButton_' + rowId).style.display = 'inline-block';
    document.getElementById('deleteButton_' + rowId).style.display = 'inline-block';
    document.getElementById('aanpassenButton_' + rowId).style.display = 'none';
}

function saveChangesProduct(rowId) {

    var row = document.getElementById('product_' + rowId).parentNode.parentNode;
    var inputs = row.querySelectorAll('input[type=text], select');
    var spans = row.querySelectorAll('span');

    for (var i = 0; i < inputs.length; i++) {
        inputs[i].style.display = 'none';
        spans[i].style.display = 'block';
        spans[i].textContent = inputs[i].value;
    }

    document.getElementById('saveButton_' + rowId).style.display = 'none';
    document.getElementById('deleteButton_' + rowId).style.display = 'none';
}

function openFormGezin(rowId) {
    var row = document.getElementById('gezinsnaam_' + rowId).parentNode.parentNode;
    var inputs = row.querySelectorAll('input[type=text], input[type=number], input[type=checkbox], textarea');
    var spans = row.querySelectorAll('span');

    for (var i = 0; i < inputs.length; i++) {
        inputs[i].style.display = 'block';
        spans[i].style.display = 'none';
    }

    document.getElementById('saveButton_' + rowId).style.display = 'inline-block';
    document.getElementById('deleteButton_' + rowId).style.display = 'inline-block';
    document.getElementById('aanpassenButton_' + rowId).style.display = 'none';
}

function saveChangesGezin(rowId) {
    var row = document.getElementById('gezinsnaam_' + rowId).parentNode.parentNode;
    var inputs = row.querySelectorAll('input[type=text], input[type=number], input[type=checkbox], textarea');
    var spans = row.querySelectorAll('span');

    for (var i = 0; i < inputs.length; i++) {
        inputs[i].style.display = 'none';
        spans[i].style.display = 'block';
        if (inputs[i].type === 'checkbox') {
            spans[i].textContent = inputs[i].checked ? 'Ja' : 'Nee';
        } else {
            spans[i].textContent = inputs[i].value;
        }
    }

    document.getElementById('saveButton_' + rowId).style.display = 'none';
    document.getElementById('deleteButton_' + rowId).style.display = 'none';
}